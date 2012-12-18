<?php
/**
 * Module for the class FileExplorer.
 * Allows you to store a virtual file system as an array in a session variable.
 * Directories are directly referenced for fast lookup.
 * @author Simon Speich
 */

require_once 'FileExplorer.php';

class SessionModule extends FileExplorer {

	/** @var int limit number of items that can be in filesystem */
	private $numItemLimit = 100;

	/** @var int current number of items */
	private $currNumItem = 41;

	/** @var int last used item id for new items	 */
	private $lastUsedItemId = 0;

	/** @var string REST resource */
	private $resource = '/library/rfe/rfefnc.php';
		
	/**
	 * Array storing the file system (fsArray).
	 * Provides a default file system prefilled with some files and folders.
	 * Note: References ($ref) never start with a slash since this would overwrite the JsonRestStore's target
	 * and send the GET as www.mysite.ch/resource instead of www.mysite.ch/target/resource
	 * @var array
	 */
	private $fsDefault = array(
		// NOTE: $ref can not start with / if so it would set a new target path e.g. add a /
		'/' => array('id' => '/', 'name' => '/', 'size' => 0, 'mod' => '21.03.2010', 'dir' => array(
			array('$ref' => 'folder1', 'dir' => true),
			array('$ref' => 'folder2', 'dir' => true),
			array('$ref' => 'folder3', 'dir' => true),
			array('id' => 'DSC0029_test.jpg', 'name' => 'photo.jpg', 'size' => 134507, 'mod' => '27.05.2010')
		)),
 		'/folder1' => array('id' => 'folder1', 'name' => 'folder 1', 'size' => 0, 'mod' => '21.03.2010', 'dir' => array(
			array('id' => 'folder1/photo01.jpg', 'name' => 'photo01.jpg', 'size' => 129631, 'mod' => '27.03.2010'),
			array('id' => 'folder1/photo02.jpg', 'name' => 'photo02.jpg', 'size' => 29634, 'mod' => '27.03.2010'),
			array('id' => 'folder1/photo03.jpg', 'name' => 'photo03.jpg', 'size' => 79308, 'mod' => '27.07.2009'),
			array('id' => 'folder1/photo04.jpg', 'name' => 'photo04.jpg', 'size' => 12962, 'mod' => '27.03.2010')
		)),
		'/folder2' => array('id' => 'folder2', 'name' => 'folder 2', 'size' => 0, 'mod' => '23.03.2010','dir' => array(
			array('$ref' => 'folder2/subfolder21', 'dir' => true),
			array('$ref' => 'folder2/subfolder22', 'dir' => true),
			array('id' => 'folder2/file1.txt', 'name' => 'file1.txt', 'size' => 9638, 'mod' => '27.01.2010'),
			array('id' => 'folder2/file2.txt', 'name' => 'file2.txt', 'size' => 3639, 'mod' => '24.02.2010'),
			array('id' => 'folder2/file3.txt', 'name' => 'file3.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file4.txt', 'name' => 'file4.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file5.txt', 'name' => 'file5.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file6.txt', 'name' => 'file6.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file7.txt', 'name' => 'file7.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file8.txt', 'name' => 'file8.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file9.txt', 'name' => 'file9.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file10.txt', 'name' => 'file10.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file11.txt', 'name' => 'file11.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file12.txt', 'name' => 'file12.txt', 'size' => 7630, 'mod' => '29.03.2010'),
			array('id' => 'folder2/file13.txt', 'name' => 'file13.txt', 'size' => 7630, 'mod' => '29.03.2010'),
		)),
		'/folder3' => array('id' => 'folder3', 'name' => 'folder 3', 'size' => 0, 'mod' => '20.04.2010', 'dir' => array(
			array('id' => 'folder3/file3.pdf', 'name' => 'file3.pdf', 'size' => 8923, 'mod' => '27.03.2001'),
			array('id' => 'folder3/file1.pdf', 'name' => 'file1.pdf', 'size' => 8925, 'mod' => '13.02.2002'),
			array('id' => 'folder3/file2.pdf', 'name' => 'file2.pdf', 'size' => 8923, 'mod' => '01.03.2003'),
		)),
		'/folder2/subfolder21' => array('id' => 'folder2/subfolder21', 'name' => 'subfolder 21', 'size' => 0, 'mod' => '27.03.2010', 'dir' => array(
			array('$ref' => 'folder2/subfolder21/subsubfolder1', 'dir' => true),
			array('id' => 'folder2/subfolder21/file4.xls', 'name' => 'file4.xls', 'size' => 128923, 'mod' => '27.03.2010'),
			array('id' => 'folder2/subfolder21/file5.xls', 'name' => 'file5.xls', 'size' => 428925, 'mod' => '27.03.2010'),
			array('id' => 'folder2/subfolder21/file6.xls', 'name' => 'file6.xls', 'size' => 448927, 'mod' => '27.03.2010')
		)),
		'/folder2/subfolder22' => array('id' => 'folder2/subfolder22', 'name' => 'subfolder 22', 'size' => 0, 'mod' => '21.05.2010', 'dir' => array(
			array('id' => 'folder2/subfolder22/simon4.jpg', 'name' => 'simon4.jpg', 'size' => 122921, 'mod' => '20.05.2010'),
			array('id' => 'folder2/subfolder22/simon5.jpg', 'name' => 'simon5.jpg', 'size' => 428925, 'mod' => '21.05.2010'),
			array('id' => 'folder2/subfolder22/simon6.jpg', 'name' => 'simon6.jpg', 'size' => 448927, 'mod' => '21.05.2010')
		)),		
		'/folder2/subfolder21/subsubfolder1' => array('id' => 'folder2/subfolder21/subsubfolder1', 'name' => 'subsubfolder 1', 'size' => 0, 'mod' => '27.03.2010', 'dir' => array(
			array('$ref' => 'folder2/subfolder21/subsubfolder1/lastsub1', 'dir' => true),
			array('id' => 'folder2/subfolder21/subsubfolder1/file3.doc', 'name' => 'file3.doc', 'size' => 1123, 'mod' => '27.03.2010'),
			array('id' => 'folder2/subfolder21/subsubfolder1/file1.doc', 'name' => 'file1.doc', 'size' => 8323, 'mod' => '27.03.2010'),
			array('id' => 'folder2/subfolder21/subsubfolder1/file2.doc', 'name' => 'file2.doc', 'size' => 6963, 'mod' => '27.03.2010')
		)),
		'/folder2/subfolder21/subsubfolder1/lastsub1' => array('id' => 'folder2/subfolder21/subsubfolder1/lastsub1', 'name' => 'last subfolder 1', 'size' => 0, 'mod' => '21.05.2010', 'dir' => array(
			array('id' => 'folder2/subfolder22/test.jpg', 'name' => 'test.jpg', 'size' => 122921, 'mod' => '20.05.2010'),
			array('id' => 'folder2/subfolder22/anothertest.jpg', 'name' => 'anothertest.jpg', 'size' => 428925, 'mod' => '21.05.2010'),
			array('id' => 'folder2/subfolder22/moretest.jpg', 'name' => 'moretest.jpg', 'size' => 448927, 'mod' => '21.05.2010')
		)),
	);
	
	/**
	 * Instantiates the session based filesystem.
	 * The string root dir is used as the session name.
	 * @param string $rootDir
	 * @return void
	 */
	public function __construct($rootDir) {
		parent::__construct($rootDir);
		if (!isset($_SESSION['rfe'])) {
			$_SESSION['rfe'][$rootDir] = serialize($this->fsDefault);
			$_SESSION['rfe']['lastUsedItemId'] = $this->lastUsedItemId;
			$_SESSION['rfe']['curNumItem'] = $this->currNumItem;
		}
	}
	
	/**
	 * Reads requested directory from fsArray.
	 * @param string $resource REST resource
	 * @see library/phdb/rfe/FileExplorer#read($resource)
	 * @return array
	 */
	public function read($resource) {
		$fs = unserialize($_SESSION['rfe'][$this->getRoot()]);
		$item = $fs[$resource];
		//sleep(1); // for testing async
		return $resource != '/' ? $item : array($item);
	}
			
	/**
	 * Update item located at resource.
	 * @param string $resource REST resource
	 * @param object $data request data
	 */
	public function update($resource, $data) {
		// only change the name but not ref/id
		$updated = false;
		$fs = unserialize($_SESSION['rfe'][$this->getRoot()]);

		// resource is directory
		if (array_key_exists($resource, $fs)) {
			$fs[$resource]['name'] = $data->name;
			$fs[$resource]['mod'] = $data->mod;
			$_SESSION['rfe'][$this->getRoot()] = serialize($fs);
			$updated = true;
		}
		// resource is file
		else if ($parent = $this->getParent($resource)) {
			$arr = &$fs[$parent]['dir'];
			foreach ($arr as $key => &$row) {
				if (array_key_exists('id', $row) && $row['id'] == ltrim($resource, '/')) {
					$row['name'] = $data->name;
					$row['mode'] = $data->mod;
					$_SESSION['rfe'][$this->getRoot()] = serialize($fs);
					$updated = true;
					break;
				}
			}
		}
		return $updated;
	}
	
	/**
	 * Create item located at resource.
	 * 
	 * @param string $resource REST resource
	 * @param object $data request data
	 * @return string|false resource location or false
	 */
	public function create($resource, $data) {
		/*
		POST - This should create a new object. The URL will correspond to the target store (like /table/)
		and the body should be the properties of the new object. The server's response should include a
		Location header that indicates the id of the newly created object. This id will be used for subsequent
		PUT and DELETE requests. JsonRestStore also includes a Content-Location header that indicates
		the temporary randomly generated id used by client, and this location is used for subsequent
		PUT/DELETEs if no Location header is provided by the server or if a modification is sent prior
		to receiving a response from the server.

		When creating new items, the JsonRestStore will POST to the target URL for the store. If your server wants
		to assign the URL/location for the newly created item, it can do so by including a Location header in the response:

      Location: http://mysite.com/Table/newid

		The server can also assign or change properties of the object (such an id or default values)
		in the response to a POST (or any other request), by simply returning the updated
		JSON representation of the item in the body of the response.

		Note that in PHP, sometimes setting the Location will erroneously trigger a 302 status code
		which will cause JsonRestStore to fail. Per RFC 2616, the correct response to a POST that creates
		a new resource is to return a 201 status code with the Location header. In PHP, you must set the status code
		as well as the Location header if you want to avoid a 302 response.

		 */
		if ($_SESSION['rfe']['curNumItem'] == $this->numItemLimit) {
			// number of items in filesystem is limited
			return false;
		}
		else {
			$fs = unserialize($_SESSION['rfe'][$this->getRoot()]);
			$resource = $data->parentId != '/' ? '/'.$data->parentId.'/' : $data->parentId;
			$id = $this->getId();
			$arrItem = array(
				'id' => ltrim($resource.$id, '/'),
				'name' => $data->name,
				'size' => 0,
				'mod' => $data->mod
			);
			if ($data->dir) {
				// aside from creating a new item, we also need to create a reference in the parent item
				$arrItem['dir'] = array();
				$arrParentItem = array();
				$arrParentItem['$ref'] = ltrim($resource.$id, '/');
				$arrParentItem['dir'] = true;
				$fs[$resource]['dir'][] = $arrParentItem;
				$fs[$resource.$id] = $arrItem;
			}
			else {
				$fs[$resource]['dir'][] = $arrItem;
			}                         			
			$_SESSION['rfe'][$this->getRoot()] = serialize($fs);
			$_SESSION['rfe']['curNumItem']++;
			return $arrItem;
		}
	}
	
	/**
	 * Delete resource from filesystem.
	 * @param string $resource REST resource
	 */
	public function delete($resource) {
		$deleted = false;
		$fs = unserialize($_SESSION['rfe'][$this->getRoot()]);

		// $resource is a directory
		if (array_key_exists($resource, $fs)) {
			unset($fs[$resource]);
			$deleted = $this->deleteReference($resource, $fs);
		}
		// $resource is a file
		else {
			$deleted = $this->deleteFile($resource, $fs);
		}

		if ($deleted) {
			$_SESSION['rfe'][$this->getRoot()] = serialize($fs);
			$_SESSION['rfe']['currNumItem']--;
		}
		return $deleted;
	}
	
	/**
	 * Extracts and returns the parent directory path.
	 * @param string $resource file resource
	 * @return string parent file path
	 */
	public function getParent($resource) {
		if ($resource != '/') {
			$resource = explode('/', $resource);
			if (count($resource) > 2) {
				array_pop($resource);
				$resource = implode('/', $resource);
			}
			else {
				$resource = '/';
			}
		}
		return $resource;
	}

	/**
	 * Deletes a reference from the filesystem.
	 * @param string $resource REST resource
	 * @param array $fs filesystem
	 * @return bool
	 */
	public function deleteReference($resource, &$fs) {
		$deleted = false;
		$parent = $this->getParent($resource);
		$arr = &$fs[$parent]['dir'];
		foreach ($arr as $key => &$row) {
			if (array_key_exists('$ref', $row) && $row['$ref'] == ltrim($resource, '/')) {
				array_splice($arr, $key, 1);	// unset $row does not work, unset($arr[$key]) changes keys from integer to string
				$deleted = true;
				break;
			}
		}
		return $deleted;
	}

	/**
	 * Deletes a file from the filesystem
	 * @param string $resource REST resource
	 * @param array $fs filesystem
	 * @return bool
	 */
	public function deleteFile($resource, &$fs) {
		$deleted = false;
		$parent = $this->getParent($resource);
		$arr = &$fs[$parent]['dir'];
		foreach ($arr as $key => &$row) {
			if (array_key_exists('id', $row) && $row['id'] == ltrim($resource, '/')) {
				array_splice($arr, $key, 1);	// unset $row does not work, unset($arr[$key]) changes keys from integer to string
				$deleted = true;
				break;
			}
		}
		return $deleted;
	}

	/**
	 * Returns a new unused id to use as a resource.
	 * @return integer
	 */
	private function getId() {
		return $_SESSION['rfe']['lastUsedItemId']++;
	}

	/**
	 * Returns the resource where the file system is located.
	 * @return string
	 */
	private function getResource() {
		return $this->resource;
	}

	/**
	 * Prints the whole file system array.
	 * Mainly used for debugging.
	 * @return void
	 */
	public function printAll() {
	   print_r($this->fsDefault);
	}

}
?>