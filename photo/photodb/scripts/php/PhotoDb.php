<?php
namespace PhotoDb;

use PDO;
use PDOException;


/**
 * Class to work with SQLite databases.
 * Creates the photo database.
 */
class PhotoDb {
	/** @var PDO|null */
	public $db;
	// paths are always appended to webroot ('/' or a subfolder) and start therefore with a foldername
	// and not with a slash, but end with a slash
    // TODO: use json config file instead as in fotodb
	private $dbName = 'photodb.sqlite';
	private $dbUserPrefs = 'user.sqlite'; 
	private $pathDb = 'photo/photodb/dbfiles/';
	private $pathImg = 'photo/photodb/images/';
	private $execTime = 300;
	public $webroot;
	protected $hasActiveTransaction = false;	// keep track of open transactions
	
	/**
	 * @param string $webroot path to root folder
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
		$path = __DIR__.'/../../../../'.$this->pathDb;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
		if ($this->db === null) {	// check if not already connected to db
			try {
				$this->db = new PDO('sqlite:'.$path.$this->dbName, null, null, $options);
				// Do every time you connect since they are only valid during connection (not permanent)
				$this->db->exec('PRAGMA full_column_names = 1');
				$this->db->exec('PRAGMA short_column_names = 0');	// green hosting's sqlite older driver version does not support short column names = off
				$this->db->sqliteCreateAggregate('GROUP_CONCAT', [$this, 'groupConcatStep'], [$this, 'groupConcatFinalize']);
				//$this->db->sqliteCreateFunction('STRTOTIME', array($this, 'strToTime'));
			}
			catch (PDOException $error) {
				echo $error->getMessage();
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
	 * Adds a SQL GROUP_CONCAT function
	 * Method used in the SQLite createAggregate function to implement SQL GROUP_CONCAT
	 * which is not supported by PDO.
	 *
	 * @param string $context
	 * @param $rowId
	 * @param string $str
	 * @param bool $unique
	 * @param string $separator
	 * @return string
	 * @internal param $bool [$Unique]
	 * @internal param $string [$Separator]
	 */
	public function groupConcatStep($context, $rowId, $str, $unique = false, $separator = ", ") {
		if ($context) {
			if ($unique) {
				if (strpos($context, $str) === false) {
					return $context.$separator.$str;
				}
				else {
					return $context;
				}
			}
			else {
				return $context.$separator.$str;
			}
		}
		else {
			return $str;
		}
	}

	/**
	 * @param $context
	 * @return mixed
	 */
	public function groupConcatFinalize($context) {
		return $context;
	}

	/**
	 * Adds the PHP strtotime function to PDO SQLite.
	 * @return string 
	 * @param string $context
	 */
  public function strToTime($context) {
  	if (strlen($context) > 4) {
  		return strtotime($context);
		}
		else if (preg_match('/[0-9]{4}/', $context)) {
			return strtotime($context."-01-01");
		}
		else {
			return null;
		}
  }
}