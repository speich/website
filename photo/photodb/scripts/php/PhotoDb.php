<?php
namespace PhotoDb;

use PDO;
use Pdo\Sqlite;
use PDOException;
use PhotoDb\FtsFunctions;
use function strlen;


/**
 * Class to work with SQLite databases.
 * Creates the photo database.
 */
class PhotoDb
{
    /** @var Sqlite|null */
    public ?Sqlite $db = null;
    // paths are always appended to webroot ('/' or a subfolder) and start therefore with a foldername
    // and not with a slash, but end with a slash
    // TODO: use json config file instead as in fotodb
    private string $dbName = 'photodb.sqlite';
    private string $dbUserPrefs = 'user.sqlite';
    private string $pathDb = 'photo/photodb/dbfiles/';
    private string $pathImg = 'photo/photodb/images/';
    private int $execTime = 300;
    public string $webroot;
    protected bool $hasActiveTransaction = false;    // keep track of open transactions

    /**
     * @param string $webroot path to root folder
     */
    public function __construct(string $webroot)
    {
        $this->webroot = $webroot;
        set_time_limit($this->execTime);
    }

    /**
     * Connect to the SQLite photo database.
     *
     * If you set the argument $UseNativeDriver to true the native SQLite driver
     * is used instead of PDO.
     */
    public function connect(): void
    {
        $path = __DIR__.'/../../../../'.$this->pathDb;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        if ($this->db === null) {    // check if not already connected to db
            try {
                $this->db = new Sqlite('sqlite:'.$path.$this->dbName, null, null, $options);
                // Do every time you connect since they are only valid during connection (not permanent)
                $this->db->exec('PRAGMA full_column_names = 0');
                $this->db->exec('PRAGMA short_column_names = 1');    // green hosting's sqlite older driver version does not support short column names = off
                $this->db->createAggregate('GROUP_CONCAT', [$this, 'groupConcatStep'], [$this, 'groupConcatFinalize']);
                $this->db->createFunction('SCORE', [FtsFunctions::class, 'tfIdfWeighted']);
            } catch (PDOException $error) {
                echo $error->getMessage();
            }
        }
    }

    /**
     * Open a transaction with a flag that you can check if it is already started.
     * PDO would throw an error if you open a transaction which is already open
     * and does not provide a means of checking status. So use this method instead
     * together with Commit and RollBack.
     * @return bool
     */
    public function beginTransaction(): bool
    {
        $result = false;
        if ($this->hasActiveTransaction === false) {
            $this->hasActiveTransaction = $this->db->beginTransaction();
            $result = $this->hasActiveTransaction;
        }

        return $result;
    }

    /**
     * Commit transaction and set the flag to false.
     * @return bool
     */
    public function commit(): bool
    {
        $this->hasActiveTransaction = false;
        return $this->db->commit();
    }

    /**
     * Roll back transaction and set the flag to false.
     * @return bool
     */
    public function rollback(): bool
    {
        $this->hasActiveTransaction = false;
        return $this->db->rollBack();
    }

    /**
     * Returns the file name of the database.
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }


    /**
     * Provides access to the different paths in the PhotoDB project.
     * Path is returned without the leading slash, but with a trailing slash
     * @param string $name
     * @return string
     */
    public function getPath(string $name): string
    {
        switch ($name) {
            case 'webroot':
                $path = $this->webroot;
                break;    // redundant, but for convenience
            case 'db':
                $path = $this->pathDb;
                break;
            case 'img':
                $path = $this->pathImg;
                break;
            default:
                $path = '/';
        }

        return $path;    // pdo functions need full path to work with subfolders on windows
    }

    /**
     * Adds a SQL GROUP_CONCAT function
     * Method used in the SQLite createAggregate function to implement SQL GROUP_CONCAT
     * which is not supported by PDO.
     *
     * @param string|null $context
     * @param int $rowId
     * @param string|null $str
     * @param bool $unique
     * @param string $separator
     * @return string|null
     * @internal param $bool [$Unique]
     * @internal param $string [$Separator]
     */
    public function groupConcatStep(?string $context, int $rowId, ?string $str, bool $unique = false, string $separator = ', '): ?string
    {
        $result = $str;

        if ($context) {
            if ($unique && str_contains($context, $str)) {
                $result = $context;
            } else {
                $result = $context.$separator.$str;
            }
        }

        return $result;
    }

    /**
     * @param $context
     * @return mixed
     */
    public function groupConcatFinalize($context): mixed
    {
        return $context;
    }

    /**
     * Adds the PHP strtotime function to PDO SQLite.
     * @param string $context
     * @return string|null
     */
    public function strToTime($context): ?string
    {
        $result = null;
        if (strlen($context) > 4) {
            $result = strtotime($context);
        } elseif (preg_match('/[0-9]{4}/', $context)) {
            $result = strtotime($context.'-01-01');
        }

        return $result;
    }
}