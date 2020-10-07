<?php
require_once __DIR__.'/FileExplorer.php';

/**
 * Module for the class FileExplorer.
 * Allows you to store a virtual file system as an array in a session variable.
 * Directories are directly referenced for fast lookup.
 * @author Simon Speich
 */

class ModuleSession extends FileExplorer
{

    /** @var int limit number of items that can be in filesystem */
    private int $numItemLimit = 100;

    /**
     * Array storing the file system (fsArray).
     * Provides a default file system prefilled with some files and folders.
     * @var array
     */
    private array $fsDefault = [
        'root' => ['id' => 'root', 'name' => 'web root', 'size' => 0, 'mod' => '21.03.2010', 'dir' => true],

        1 => [
            'id' => 1,
            'parId' => 'root',
            'name' => 'folder 1',
            'size' => 0,
            'mod' => '21.03.2010',
            'dir' => true
        ],
        2 => [
            'id' => 2,
            'parId' => 'root',
            'name' => 'folder 2',
            'size' => 0,
            'mod' => '23.03.2010',
            'dir' => true
        ],
        24 => [
            'id' => 24,
            'parId' => 'root',
            'name' => 'folder 3',
            'size' => 0,
            'mod' => '23.03.2010',
            'dir' => true
        ],
        3 => ['id' => 3, 'parId' => 'root', 'name' => 'texts', 'size' => 0, 'mod' => '20.04.2010', 'dir' => true],
        4 => ['id' => 4, 'parId' => 'root', 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        5 => ['id' => 5, 'parId' => 'root', 'name' => 'fenalet.jpg', 'size' => 321, 'mod' => '09.10.2002'],

        6 => ['id' => 6, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        7 => ['id' => 7, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        8 => ['id' => 8, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        26 => ['id' => 26, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        27 => ['id' => 27, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        28 => ['id' => 28, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        29 => ['id' => 29, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        30 => ['id' => 30, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        31 => ['id' => 31, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        32 => ['id' => 32, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        33 => ['id' => 33, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        34 => ['id' => 34, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        35 => ['id' => 35, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        36 => ['id' => 36, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        37 => ['id' => 37, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        38 => ['id' => 38, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        39 => ['id' => 39, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        40 => ['id' => 40, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],
        41 => ['id' => 41, 'parId' => 1, 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'],
        42 => ['id' => 42, 'parId' => 1, 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'],
        43 => ['id' => 43, 'parId' => 1, 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'],

        9 => [
            'id' => 9,
            'parId' => 2,
            'name' => 'subfolder 21',
            'size' => 0,
            'mod' => '27.03.2010',
            'dir' => true
        ],
        10 => ['id' => 10, 'parId' => 2, 'name' => 'file5.txt', 'size' => 1631, 'mod' => '06.11.1973'],
        11 => ['id' => 11, 'parId' => 2, 'name' => 'file1.txt', 'size' => 9638, 'mod' => '27.01.2010'],
        12 => [
            'id' => 12,
            'parId' => 2,
            'name' => 'subfolder 22',
            'size' => 0,
            'mod' => '27.03.2010',
            'dir' => true
        ],
        25 => ['id' => 25, 'parId' => 2, 'name' => 'test dnd 1', 'size' => 1631, 'mod' => '06.11.1973'],
        44 => ['id' => 44, 'parId' => 2, 'name' => 'test dnd 2', 'size' => 9638, 'mod' => '27.01.2010'],

        13 => ['id' => 13, 'parId' => 3, 'name' => 'file3.pdf', 'size' => 8923, 'mod' => '27.03.2001'],
        14 => ['id' => 14, 'parId' => 3, 'name' => 'file1.pdf', 'size' => 8925, 'mod' => '13.02.2002'],
        15 => ['id' => 15, 'parId' => 3, 'name' => 'file2.pdf', 'size' => 8923, 'mod' => '01.03.2003'],

        16 => ['id' => 16, 'parId' => 9, 'name' => 'test 21', 'size' => 30, 'mod' => '27.03.2010'],
        17 => [
            'id' => 17,
            'parId' => 9,
            'name' => 'subfolder 22',
            'size' => 0,
            'mod' => '21.05.2010',
            'dir' => true
        ],

        18 => ['id' => 18, 'parId' => 12, 'name' => 'file4.xls', 'size' => 128923, 'mod' => '27.03.2010'],
        19 => ['id' => 19, 'parId' => 12, 'name' => 'file5.xls', 'size' => 428925, 'mod' => '27.03.2010'],
        20 => ['id' => 20, 'parId' => 12, 'name' => 'file6.xls', 'size' => 448927, 'mod' => '27.03.2010'],

        21 => ['id' => 21, 'parId' => 17, 'name' => 'test.doc', 'size' => 128923, 'mod' => '12.12.2011'],
        22 => ['id' => 22, 'parId' => 17, 'name' => 'some.xls', 'size' => 428925, 'mod' => '17.03.1990'],
        23 => ['id' => 23, 'parId' => 17, 'name' => 'any.xls', 'size' => 448927, 'mod' => '08.03.2010']
    ];

    /**
     * Instantiates the session based filesystem.
     * The string root dir is used as the session name.
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        parent::__construct($rootDir);
        // I came across a really nasty bug in php 5.2.15: If the session name is the same as the
        // instance variable of this class, e.g. $rfe = new ModuleSession() and $_SESSION['rfe'] then
        // the session is unserialized to the instance instead of the array $this->fsDefault
        if (!isset($_SESSION['fs'])) {
            $_SESSION['fs'][$rootDir] = serialize($this->fsDefault);
            $_SESSION['fs']['lastUsedItemId'] = count($this->fsDefault);    // this is a very simple way which is not very robust // TODO: better way to create an id
        }
    }

    /**
     * Reads requested resource from file system array.
     * @param string $resource REST resource
     * @return string|false json
     */
    public function get($resource)
    {
        $json = false;
        $fs = unserialize($_SESSION['fs'][$this->getRoot()], ['allawed_classes' => false]);
        if (substr($resource, -1) === '/') {   // query for children of $resource
            $json = $this->getChildren(rtrim($resource, '/'), $fs);
        } elseif (array_key_exists($resource, $fs)) { // get item
            $item = $fs[$resource];
            $json = json_encode($item);
            $json = preg_replace('/"([\d]+)"/', '$1', $json);
        }

        return $json;
    }

    /**
     * Returns the children of a directory.
     * @param string $resource
     * @param array $fs array with files
     * @return string json
     */
    public function getChildren($resource, $fs)
    {
        $arr = [];
        foreach ($fs as $row) {
            if (array_key_exists('parId', $row) && $row['parId'] == $resource) {
                $arr[] = $row;
            }
        }
        // sort array to display folders first, then alphabetically
        // Obtain a list of columns for multisort
        if (count($arr) > 0) {
            $dirs = [];
            $names = [];
            foreach ($arr as $key => $row) {
                $dirs[$key] = array_key_exists('dir', $row) ? 'a' : 'b';
                $names[$key] = $row['name'];
            }
            array_multisort($dirs, SORT_ASC, $names, SORT_ASC, $arr);
            $json = json_encode($arr);
            $json = preg_replace('/"([\d]+)"/', '$1', $json);
        } else {
            $json = '[]';
        }

        return $json;
    }

    /**
     * Update item located at resource.
     * @param string $resource REST resource
     * @param object $data request data
     * @return string|false json
     */
    public function update($resource, $data)
    {
        $json = false;
        $fs = unserialize($_SESSION['fs'][$this->getRoot()], ['allowed_classes' => false]);

        if (array_key_exists($resource, $fs)) {
            $fs[$resource]['name'] = $data->name;
            $fs[$resource]['mode'] = $data->mod;
            $fs[$resource]['parId'] = $data->parId;

            $_SESSION['fs'][$this->getRoot()] = serialize($fs);
            $json = json_encode($fs[$resource]);
            $json = preg_replace('/"([\d]+)"/', '$1', $json);
        }

        return $json;
    }

    /**
     * Create item located at resource.
     * @param string $resource REST resource
     * @param object $data request data
     * @return string|false resource location as json or false
     */
    public function create(string $resource, $data)
    {
        $json = false;
        if (count($this->fsDefault) <= $this->numItemLimit) {
            // number of items in filesystem is limited
            // TODO: raise error instead of $json = false
            $fs = unserialize($_SESSION['fs'][$this->getRoot()], ['allowed_classes' => false]);
            $id = $this->getId(); // $ref/id do not start with a slash
            $item = [
                'id' => $id,
                'parId' => $data->parId,
                'name' => $data->name,
                'mod' => $data->mod,
                'size' => 0,
            ];
            if (property_exists($data, 'dir')) {
                $item['dir'] = true;
            }
            $fs[$id] = $item;
            $_SESSION['fs'][$this->getRoot()] = serialize($fs);
            $json = json_encode($item);
            $json = preg_replace('/"([\d]+)"/', '$1', $json);
        }

        return $json;
    }

    /**
     * Delete resource from filesystem.
     * @param string $resource REST resource
     * @return string|false
     */
    public function delete($resource)
    {
        $json = false;
        $fs = unserialize($_SESSION['fs'][$this->getRoot()], ['allowed_classes' => false]);
        if (array_key_exists($resource, $fs)) {
            // if $item has children, delete all children too
            if (array_key_exists('dir', $fs[$resource])) {
                for ($i = 0, $len = count($fs); $i < $len; $i++) {
                    if (isset($fs[$i]['parId']) && $fs[$i]['parId'] == $resource) {
                        unset($fs[$i]);
                    }
                }
            }
            unset($fs[$resource]);
            $_SESSION['fs'][$this->getRoot()] = serialize($fs);
            $json = '[{"msg": "item deleted"}]';
        }

        return $json;
    }

    /**
     * Returns a new unused id to use as a resource.
     * @return int
     */
    private function getId()
    {
        return $_SESSION['fs']['lastUsedItemId']++;
    }
}