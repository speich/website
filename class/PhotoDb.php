<?php
namespace PhotoDb;

use DOMDocument;
use PDO;
use PDOException;
use WebsiteTemplate\Website;

require_once 'Website.php';

/**
 * Class to work with SQLite databases.
 * 
 * Creates the photo DB.
 *
 */
class PhotoDb {
	/** @var PDO|null */
	public $db = null;
	// paths are always appended to webroot ('/' or a subfolder) and start therefore with a foldername
	// and not with a slash, but end with a slash
	private $dbName = "photodb.sqlite";
	private $dbUserPrefs = 'user.sqlite'; 
	private $pathDb = 'photo/photodb/dbfiles/';
	private $pathImg = 'photo/photodb/images/';
	private $execTime = 300;
	public $webroot;
	protected $hasActiveTransaction = false;	// keep track of open transactions
	
	/**
	 * @constructor
	 */
	public function __construct($webroot) {
		$this->webroot = $webroot;
		set_time_limit($this->execTime);
	}
	
	/**
	 * Connect to the SQLite photo database.
	 * 
	 * If you set the argument $UseNativeDriver to true the native SQLite driver
	 * is used instead of PDO.
	 */
	public function connect() {
		$path = __DIR__.'/../'.$this->pathDb;
		if (is_null($this->db)) {	// check if not already connected to db
			try {
				$this->db = new PDO('sqlite:'.$path.$this->dbName);
				// Do every time you connect since they are only valid during connection (not permanent)
				$this->db->exec('PRAGMA full_column_names = 0');
				$this->db->exec('PRAGMA short_column_names = 1');	// green hosting's sqlite older driver version does not support short column names = off
				$this->db->sqliteCreateAggregate('GROUP_CONCAT', array($this, 'groupConcatStep'), array($this, 'groupConcatFinalize'));
				//$this->db->sqliteCreateFunction('STRTOTIME', array($this, 'strToTime'));
			}
			catch (PDOException $Error) {
				echo $Error->getMessage();
			}
		}
	}
	
	/**
	 * Open transaction with a flag that you can check if it is already started.
	 * PDO whould throw an error if you opend a transaction which is already open
	 * and does not provide a means of checking status. So use this method instead
	 * together with Commit and RollBack.
	 * @return bool
	 */
	public function beginTransaction() {
		if ($this->hasActiveTransaction === true) {
			return false;
		} else {
			$this->hasActiveTransaction = $this->db->beginTransaction();
			return $this->hasActiveTransaction;
		}
	}
	
	/**
	 * Comit transaction and set flag to false.
	 * @return bool
	 */
	public function commit() {
		$this->hasActiveTransaction = false;
		return $this->db->commit();
   }
	
	/**
	 * Rollback transaction and set flag to false.
	 * @return bool
	 */
	function rollback() {
		$this->hasActiveTransaction = false;
		return $this->db->rollback();
   }	
	
	/**
	 * Returns the file name of the database.
	 * @return string
	 */	
	public function getDbName() { return $this->dbName; }


	/**
	 * Provides access to the different paths in the PhotoDB project.
	 * @param string $name
	 * @return string
	 */
	public function getPath($name) {
		switch($name) {
			case 'webroot': $path = $this->webroot; break;	// redundant, but for convenience
			case 'db': $path = $this->pathDb; break;
			case 'img': $path = $this->pathImg; break;
			default: $path = '/';
		}
		return $path;	// pdo functions need full path to work with subfolders on windows
	}


	/**
	 * Query the number of records with given sql.
	 * If you provide the optional parameter lastPage, then this query is cached in a session variable as long as the
	 * query variables with the exception of: pg, sort, numRecPp
	 * @param string $sql sql query
	 * @param string $colName name of db column to count
	 * @param array $arrBind array of bind variables
	 * @param string $lastPage [optional] url of last page
	 * @param Website $web
	 * @return integer number of records
	 */
	public function getNumRec($sql, $colName, $arrBind, $lastPage = null, $web) {
		$colName = preg_replace('/[^a-zA-Z0-9_\.]/', '', $colName);	// sanitize
		$expr = "/&?pg=[0-9]*|&?sort=[0-9]*|&?numRecPp=[0-9]*/";	// these query vars can change without having to reset numRec
		$queryLast = parse_url($lastPage);
		if (array_key_exists('query', $queryLast)) {
			$queryLast = preg_replace($expr, '', $queryLast['query']);
		}
		else {
			$queryLast = '';
		}
		$queryCurr = parse_url($_SERVER['REQUEST_URI']);
		if (array_key_exists('query', $queryCurr)) {
			$queryCurr = preg_replace($expr, '', $queryCurr['query']);
		}
		else {
			$queryCurr = '';
		}
		if (!isset($_SESSION[$web->namespace]['numRec']) || $queryLast != $queryCurr) {
			$pattern = '/^SELECT (.|\s)*? FROM/';
			$sql = preg_replace($pattern, 'SELECT COUNT('.$colName.') numRec FROM', $sql);
			$stmt = $this->db->prepare($sql);
			foreach ($arrBind as $key => $val) {
				$stmt->bindValue(':'.$key, $val);
			}
			$stmt->execute();			
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) {
				$_SESSION[$web->namespace]['numRec'] = $row['numRec'];
			}
			else {
				$_SESSION[$web->namespace]['numRec'] = 0;
			}
		}
		return $_SESSION[$web->namespace]['numRec'];
	}


	/**
	 * Adds a SQL GROUP_CONCAT function
	 * Method used in the SQLite createAggregate function to implement SQL GROUP_CONCAT
	 * which is not supported by PDO. 
	 * @return string
	 * @param string $Context
	 * @param string $RowId
	 * @param string $String
	 * @param bool [$Unique]
	 * @param string [$Separator]
	 */
	public function groupConcatStep($Context, $RowId, $String, $Unique = false, $Separator = ", ") {
		if ($Context) {
			if ($Unique) {
				if (strpos($Context, $String) === false) {
					return $Context.$Separator.$String;
				}
				else {
					return $Context;
				}
			}
			else {
				return $Context.$Separator.$String;
			}
		}
		else {
			return $String;
		}
	}

	/**
	 * @param $Context
	 * @return mixed
	 */
	public function groupConcatFinalize($Context) {
		return $Context;
	}

	/**
	 * Adds the PHP strtotime function to PDO SQLite.
	 * @return string 
	 * @param string $Context
	 */
  public function strToTime($Context) {
  	if (strlen($Context) > 4) {
  		return strtotime($Context);
		}
		else if (preg_match('/[0-9]{4}/', $Context)) {
			return strtotime($Context."-01-01");
		}
		else {
			return null;
		}
  }
}