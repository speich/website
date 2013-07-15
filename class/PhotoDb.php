<?php
/**
 * Class to work with SQLite databases.
 * 
 * Creates the photo DB.
 *
 */
class PhotoDb extends Website {
	/** @var PDO|null */
	public $db = null;
	// paths are always appended to webroot ('/' or a subfolder) and start therefore with a foldername
	// and not with a slash, but end with a slash
	private $dbName = "photodb.sqlite";
	private $dbUserPrefs = 'user.sqlite'; 
	private $pathDb = 'photo/photodb/dbfiles/';
	private $pathImg = 'photo/photodb/images/';
	private $execTime = 300;
	protected $hasActiveTransaction = false;	// keep track of open transactions
	
	/**
	 * @constructor
	 */
	public function __construct() {
		parent::__construct();
		set_time_limit($this->execTime);
	}
	
	/**
	 * Connect to the SQLite photo database.
	 * 
	 * If you set the argument $UseNativeDriver to true the native SQLite driver
	 * is used instead of PDO.
	 * @param bool $useNativeDriver
	 */
	public function connect($useNativeDriver = false) {
		$path = $_SERVER['DOCUMENT_ROOT'].$this->getWebRoot().$this->pathDb;
		if (is_null($this->db)) {	// check if not already connected to db
			try {
				$this->db = new PDO('sqlite:'.$path.'/'.$this->dbName);
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
			case 'webRoot': $path = $this->getWebRoot(); break;	// redudant, but for convenience
			case 'db': $path = $this->pathDb; break;
			case 'img': $path = $this->pathImg; break;
			default: $path = '/';
		}
		return $path;	// pdo functions need full path to work with subfolders on windows
	}
	
	/**
	 * Insert new image data from form and from exif data.
	 * 
	 * This method is only called once, when the image is selected by the user for the first time.
	 * @param string $Img image file including web root path
	 * @return string XML file
	 */
	public function insert($Img) {
		$ImgFolder = str_replace($this->getWebRoot().$this->getPath('Img'), '', $Img);	// remove web images folder path part
		$ImgName = substr($ImgFolder, strrpos($ImgFolder, '/') + 1);
		$ImgFolder = trim(str_replace($ImgName, '', $ImgFolder), '/');
		$Sql = "INSERT INTO Images (Id, ImgFolder, ImgName, DateAdded, LastChange)
			VALUES (NULL, :ImgFolder, :ImgName,".time().",".time().")
		";
		$this->beginTransaction();
		$stmt = $this->db->prepare($Sql);
		$stmt->bindParam(':ImgName', $ImgName);
		$stmt->bindParam(':ImgFolder', $ImgFolder);
		$stmt->execute();
		$ImgId = $this->db->lastInsertId();
		// insert exif data
		if (!$this->InsertExif($ImgId, $Img)) {
			echo 'failed';
			return false;
		}
		$Sql = "SELECT Id, ImgFolder, ImgName, ImgDate,	ImgTechInfo, FilmTypeId, RatingId,
			DateAdded, LastChange, ImgDesc,	ImgTitle, Public, DatePublished, ImgDateOriginal, ImgLat, ImgLng
			FROM Images WHERE Id = :ImgId";
		$stmt = $this->db->prepare($Sql);
		$stmt->bindParam(':ImgId', $ImgId);
		$stmt->execute();
		$strXml = '<?xml version="1.0" encoding="UTF-8"?>';
		$strXml .= '<HtmlFormData xml:lang="de-CH">';
		// image data
		$strXml .= '<Image';
		foreach ($stmt->fetch(PDO::FETCH_ASSOC) as $Key => $Val) {
			// each col in db is attribute of xml element Image
			if (strpos($Key, 'Date') !== false &&	!is_null($Val) && $Val != '' && $Key != 'ImgDate') {
				$strXml .= ' '.$Key.'="'.date("d.m.Y", $Val).'"';
			}
			else if ($Key == 'LastChange' &&	!is_null($Val) && $Val != '') {
				$strXml .= ' '.$Key.'="'.date("d.m.Y", $Val).'"';
			}			
			else {  
				$strXml .= ' '.$Key.'="'.$Val.'"';
			}
		}
		$strXml .= '/>';
		$strXml .= '</HtmlFormData>';
		header('Content-Type: text/xml; charset=UTF-8');
		echo $strXml;
	}
	
	/**
	 * Edit image data.
	 * 
	 * Data is selected from database and posted back as an xml page.
	 * Response is returned as an XML to the ajax request to fill form fields.
	 * XML attribute names must have the same name as the HTML form field names.
	 * 
	 * @param integer $ImgId image id
	 */
	public function edit($ImgId) {
		// TODO: use DOM functions instead of string to create xml
		$Sql = "SELECT Id, ImgFolder, ImgName, ImgDate, ImgTechInfo, FilmTypeId, RatingId,
			DateAdded, LastChange, ImgDesc, ImgTitle, Public, DatePublished, 
			ImgDateOriginal, ImgLat, ImgLng	
			FROM Images	WHERE Id = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$strXml = '<?xml version="1.0" encoding="UTF-8"?>';
		$strXml .= '<HtmlFormData xml:lang="de-CH">';
		// image data
		$strXml .= '<Image';
		foreach ($Stmt->fetch(PDO::FETCH_ASSOC) as $Key => $Val) {
			// each col in db is attribute of xml element Image
			if (strpos($Key, 'Date') !== false && $Key != 'ImgDate' &&	!is_null($Val) && $Val != '') {
				$strXml .= ' '.$Key.'="'.date("d.m.Y", $Val).'"';
			}
			else if ($Key == 'LastChange' &&	!is_null($Val) && $Val != '') {
				$strXml .= ' '.$Key.'="'.date("d.m.Y", $Val).'"';
			}			
			else {  
				$strXml .= ' '.$Key.'="'.htmlspecialchars($Val, ENT_QUOTES, 'UTF-8').'"';
			}
		}
		$strXml .= '/>';
		// themes
		$Sql = "SELECT ThemeId FROM Images_Themes WHERE ImgId = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$strXml .= '<Themes Id="'.$ImgId.'">';
		foreach ($Stmt->fetchAll(PDO::FETCH_ASSOC) as $Row) {
			$strXml .= '<Theme Id="'.$Row['ThemeId'].'"/>';
		}
		$strXml .= '</Themes>';
		// keywords
		$Sql = "SELECT Name, KeywordId FROM Images_Keywords IK
			INNER JOIN Keywords ON IK.KeywordId = Keywords.Id
			WHERE ImgId = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$strXml .= '<Keywords Id="'.$ImgId.'">';
		foreach ($Stmt->fetchAll(PDO::FETCH_ASSOC) as $Row) {
			$strXml .= '<Keyword Id="'.$Row['KeywordId'].'" Name="'.$Row['Name'].'"/>';
		}
		$strXml .= '</Keywords>';
		// species
		$Sql = "SELECT ScientificNameId, NameDe, NameEn, NameLa, SexId, ss.Name SexText 
			FROM Images_ScientificNames isn 
			INNER JOIN ScientificNames sn ON isn.ScientificNameId = sn.Id
			INNER JOIN Sexes ss ON isn.SexId = ss.Id
			WHERE ImgId = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$strXml .= '<ScientificNames Id="'.$ImgId.'">';
		foreach ($Stmt->fetchAll(PDO::FETCH_ASSOC) as $Row) {
			$strXml .= '<ScientificName Id="'.$Row['ScientificNameId'].'" NameDe="'.htmlspecialchars($Row['NameDe'], ENT_QUOTES, 'UTF-8');
			$strXml .= '" NameEn="'.htmlspecialchars($Row['NameEn'], ENT_QUOTES, 'UTF-8').'" NameLa="'.htmlspecialchars($Row['NameLa'], ENT_QUOTES, 'UTF-8').'"';
			$strXml .= ' SexId="'.$Row['SexId'].'" SexText="'.htmlspecialchars($Row['SexText'], ENT_QUOTES, 'UTF-8').'"/>';
		}
		$strXml .= '</ScientificNames>';
		// locations
		// TODO: find solution to locname might occur twice but in a different country
		$Sql = "SELECT il.LocationId LocId, l.Name LocName, CountryId FROM Images_Locations il
			INNER JOIN Locations l ON il.LocationId = l.Id
			INNER JOIN Locations_Countries lc ON l.Id = lc.LocationId
			WHERE ImgId = :ImgId";
		// AND CountryId = ???
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$strXml .= '<Locations Id="'.$ImgId;
		$arrData = $Stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!count($arrData)) {
			$strXml .= '" CountryId="">';	
		}
		foreach ($arrData as $Row) {
			if (!isset($CountryId)) {
				$CountryId = $Row['CountryId'];
				$strXml .= '" CountryId="'.$CountryId.'">';	
			}			
			$strXml .= '<Location Id="'.$Row['LocId'].'" Name="'.htmlspecialchars($Row['LocName'], ENT_QUOTES, 'UTF-8').'"/>';
		}
		$strXml .= '</Locations>';		
		$strXml .= '</HtmlFormData>';
		header('Content-Type: text/xml; charset=UTF-8');
		echo $strXml;
	}
	
	/**
	 * Updata image data.
	 * @param string $XmlData
	 */
	public function updateAll($XmlData) {
		// TODO: more generic update
		// TODO: after UpdateAll send updated data back to browser (like Edit), for ex. LocationId would be updated
		$Xml = new DOMDocument();
		$Xml->loadXML($XmlData);
		$this->beginTransaction();
		// update images
		$Attributes = $Xml->getElementsByTagName('Image')->item(0)->attributes;
		$ImgId = $Attributes->getNamedItem('Id')->nodeValue;
		$Count = 0;
		$Sql = "UPDATE Images SET";
		foreach ($Attributes as $Attr) {
			$Sql .= " ".sqlite_escape_string($Attr->nodeName)." = :Val$Count,";
			$Count++;
		}
		$Sql = rtrim($Sql, ',');
		$Sql .= " WHERE Id = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Count = 0;
		foreach ($Attributes as $Attr) {
			if (strpos($Attr->nodeName, 'Date') !== false && $Attr->nodeName != 'ImgDate') {
				if ($Attr->nodeValue != '' && strtotime($Attr->nodeValue)) {
					$Stmt->bindParam(":Val$Count", strtotime($Attr->nodeValue));
				}
			}
			else {
				$Stmt->bindParam(":Val$Count", $Attr->nodeValue);
			}
			$Count++;
		}
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$Sql = "UPDATE Images SET LastChange = ".time()." WHERE Id = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		// update images_keywords		
		$Sql = "DELETE FROM Images_Keywords WHERE ImgId = :ImgId";	// Delete all first -> add current keywords back. User might have deleted keyword, which would not be transmitted
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		if ($Xml->getElementsByTagName('Keywords')->length > 0) {
			$El = $Xml->getElementsByTagName('Keywords')->item(0);
			$ImgId = $El->getAttribute('Id');
			$Children = $El->childNodes;
			$Sql1 = "INSERT INTO Images_Keywords (ImgId, KeywordId) VALUES (:ImgId, :KeywordId)";
			$Stmt1 = $this->db->prepare($Sql1);
			$Stmt1->bindParam(':ImgId', $ImgId);
			$Stmt1->bindParam(':KeywordId', $KeywordId);
			$Sql2 = "INSERT INTO Keywords (Id, Name) VALUES (NULL, :Name)";
			$Stmt2 = $this->db->prepare($Sql2);
			$Stmt2->bindParam(':Name', $Keyword);
			$Sql3 = "SELECT KeywordId FROM Images_Keywords WHERE ImgId = :ImgId AND KeywordId = :KeywordId";
			$Stmt3 = $this->db->prepare($Sql3);
			$Stmt3->bindParam(':ImgId', $ImgId);
			$Stmt3->bindParam(':KeywordId', $KeywordId);
			$Sql4 = "SELECT Id FROM Keywords WHERE Name = :Name";
			$Stmt4 = $this->db->prepare($Sql4);
			$Stmt4->bindParam(':Name', $Keyword);
			foreach ($Children as $Child) {
				$KeywordId = $Child->getAttribute('Id');
				$Keyword = $Child->getAttribute('Name');
				// 1. Insert into keyword table first if new keyword,e.g no id. and
				// use (returned) id for table Images_Keywords.
				// Note: Its possible that there is no id posted, but keyword is already in db -> check name first
				if ($KeywordId == '') { // new?
					$Stmt4->execute();
					if ($Row = $Stmt4->fetch(PDO::FETCH_ASSOC)) {
						$KeywordId = $Row['Id']; 
					}
					else {
						$Stmt2->execute();
						$KeywordId = $this->db->lastInsertId();
					}
					$Stmt4->closeCursor();
				}
				// 2. Check if keyword was not inserted previously
				// because user might have the same keyword twice in the div-list. 
				$Stmt3->execute();
			 	// 3. Insert keyword id into table Images_Keywords
			 	if (!$Row = $Stmt3->fetch(PDO::FETCH_ASSOC)) {
			 		$Stmt3->closeCursor();
					$Stmt1->execute();
			 	}
			}
		}
		// update images_themes
		$El = $Xml->getElementsByTagName('Themes')->item(0);
		$Sql = "DELETE FROM Images_Themes WHERE ImgId = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		$Children = $El->childNodes;
		$Sql = "INSERT INTO Images_Themes (ImgId, ThemeId) VALUES (:ImgId, :ThemeId)";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->bindParam(':ThemeId', $ThemeId);
		foreach ($Children as $Child) {
			$ThemeId = $Child->getAttribute('Id');
			$Stmt->execute();
		}
		// update Images_ScientificNames. Note: not every image has a species.
		$Sql = "DELETE FROM Images_ScientificNames WHERE ImgId = :ImgId";
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		if ($Xml->getElementsByTagName('ScientificNames')->length > 0) {
			$El = $Xml->getElementsByTagName('ScientificNames')->item(0);		
			$Children = $El->childNodes;
			$Sql = "INSERT INTO Images_ScientificNames (ImgId, ScientificNameId, SexId) VALUES (:ImgId, :SpeciesId, :SexId)";
			$Stmt = $this->db->prepare($Sql);
			$Stmt->bindParam(':ImgId', $ImgId);
			$Stmt->bindParam(':SpeciesId', $SpeciesId);
			$Stmt->bindParam(':SexId', $SexId);
			foreach ($Children as $Child) {
				$SpeciesId = $Child->getAttribute('Id');
				$SexId = $Child->getAttribute('SexId');
				$Stmt->execute();
			}
		}
		// update locations
		// An image can have several locations. A location name can occur once per country
		// -> enables filtering locations by country
		// All queries have to be executed singledly, because resulting records are used as input. -> do not put in one transaction
		// TODO: consequences for multiuser ?
		$Sql = "DELETE FROM Images_Locations WHERE ImgId = :ImgId";	// always remove first before setting new locs, maybe user simply wants to remove locs
		$Stmt = $this->db->prepare($Sql);
		$Stmt->bindParam(':ImgId', $ImgId);
		$Stmt->execute();
		if ($Xml->getElementsByTagName('Locations')->length > 0) {
			$El = $Xml->getElementsByTagName('Locations')->item(0);		
			$CountryId = $El->getAttribute('CountryId');
			$Children = $El->childNodes;
			foreach ($Children as $Child) {
				$LocationId = $Child->getAttribute('Id');
				// 1. Check if location name is already in table locations, if not, insert it.
				// Use (returned) id for table Images_Locations and Locations_Countries.
				if ($LocationId == '') { // new? location from map or input field
					$Name = $Child->getAttribute('Name');
					$Sql = "
						SELECT Id FROM Locations l
						INNER JOIN Locations_Countries lc ON lc.LocationId = l.Id
						WHERE Name = :Name AND CountryId = :CountryId
					";
					$Stmt = $this->db->prepare($Sql);
					$Stmt->bindParam(':Name', $Name);
					$Stmt->bindParam(':CountryId', $CountryId);
					$Stmt->execute();
				 	if ($Row = $Stmt->fetch(PDO::FETCH_ASSOC)) {
				 		$LocationId = $Row['Id']; 
				 	}
				 	else {
						$Sql = "INSERT INTO Locations (Id, Name) VALUES (NULL, :Name)";
						$Stmt = $this->db->prepare($Sql);
						$Stmt->bindParam(':Name', $Name);
						$Stmt->execute();
						$LocationId = $this->db->lastInsertId();
						$Sql = "INSERT INTO Locations_Countries (LocationId, CountryId) VALUES (:LocationId, :CountryId)";
						$Stmt = $this->db->prepare($Sql);
						$Stmt->bindParam(':LocationId', $LocationId);
						$Stmt->bindParam(':CountryId', $CountryId);
						$Stmt->execute();
				 	}				
				}
				// 2. Check if location was not inserted previously into Images_Locations,
				// because user might have the same location twice in the list. 
				$Sql = "SELECT LocationId FROM Images_Locations WHERE ImgId = :ImgId AND LocationId = :LocationId";
				$Stmt = $this->db->prepare($Sql);
				$Stmt->bindParam(':ImgId', $ImgId);
				$Stmt->bindParam(':LocationId', $LocationId);
				$Stmt->execute();
			 	// 3. Insert location into table Images_Locations
			 	if (!$Row = $Stmt->fetch(PDO::FETCH_ASSOC)) {
			 		$Sql = "INSERT INTO Images_Locations (ImgId, LocationId) VALUES (:ImgId, :LocationId)";
			 		$Stmt = $this->db->prepare($Sql);
					$Stmt->bindParam(':ImgId', $ImgId);
					$Stmt->bindParam(':LocationId', $LocationId);
					$Stmt->execute();
			 	}
			}
		}
		if ($this->Commit()) {
			echo 'success';
		}
		else {
			print_r($this->db->ErrorInfo());
			$this->RollBack();
			echo 'failed';
		}
	}
	
	/**
	 * Delete image data from database.
	 *
	 * @param integer $ImgId image id
	 * @return string message
	 */
	public function delete($ImgId) {
		$ImgId = preg_replace("/\D/",'', $ImgId);	// allow only numbers
		$this->beginTransaction();
		$this->db->exec("DELETE FROM Images WHERE Id = $ImgId");
		$this->db->exec("DELETE FROM Exif WHERE ImgId = $ImgId");
		$this->db->exec("DELETE FROM Images_ScientificNames WHERE ImgId = $ImgId");
		$this->db->exec("DELETE FROM Images_Keywords WHERE ImgId = $ImgId");
	 	$this->db->exec("DELETE FROM Images_Themes WHERE ImgId = $ImgId");
		$this->db->exec("DELETE FROM Images_Locations WHERE ImgId = $ImgId");
		if ($this->Commit()) {
			echo 'success';
		}
		else {
			$this->RollBack();
			echo 'failed';
		}
	}
	
	/**
	 * Insert exif data read from image into photodb.
	 * Returns true on success or false on failure.
	 * @return bool
	 * @param string $Img image file including web root path
	 * @param integer $ImgId image database id
	 */
	public function insertExif($ImgId, $Img = null) {
		if (is_null($Img)) {
			$Sql = "SELECT ImgFolder, ImgName FROM Images WHERE Id = :ImgId";
			$Stmt = $this->db->prepare($Sql);
			$Stmt->bindParam(':ImgId', $ImgId);
			$Stmt->execute();
			$Row = $Stmt->fetch(PDO::FETCH_ASSOC);
			$Img = $this->getWebRoot().$this->getPath('Img').$Row['ImgFolder'].'/'.$Row['ImgName'];
		}
		// Scanned slides have a lot of empty exif data
		$Exif = new ExifData();
		$Path = $this->getWebRoot().$this->getPath('Img');
		$arrExif = $Exif->ReadArray($Img, $Path, FOTODB_EXIF_READ_ORIGINAL);
		if (count($arrExif) > 0) {
			$this->beginTransaction();
			$SqlTemp = "";
			$Sql = "INSERT OR REPLACE INTO Exif (ImgId, ";	// deletes row first if conflict occurs
			foreach ($arrExif as $Key => $Val) {	// column names
				$SqlTemp .= "$Key,";
			}
			$Sql .= rtrim($SqlTemp, ',').") VALUES (:ImgId, ";
			$SqlTemp = "";
			foreach ($arrExif as $Key => $Val) {	// column data
				if (strpos($Key, 'Date') !== false && $Key != 'ImgDate') {
					if ($Val != '' && strtotime($Val)) {
						$SqlTemp .= "'".sqlite_escape_string(strtotime($Val))."',";
					}
					else {
						$SqlTemp .= "NULL,";
					}
				}
				else {
					$SqlTemp .= "'".sqlite_escape_string($Val)."',";
				}
			}
			$Sql .= rtrim($SqlTemp, ',').");";
			$Stmt = $this->db->prepare($Sql);
			$Stmt->bindParam(':ImgId', $ImgId);
			$Stmt->execute();
			// Use exif date also as column value in Images table. Scanned slides have only
			// date of scanning, which should not be inserted of course.
			if ($arrExif['Model'] != 'Nikon SUPER COOLSCAN 5000 ED' && $arrExif['DateOrig'] != '') {	
				$Sql = 'UPDATE Images SET ImgDateOriginal = :Date WHERE Id = :ImgId';
				$Stmt = $this->db->prepare($Sql);
				$Stmt->bindParam(':ImgId', $ImgId);
				$Stmt->bindParam(':Date', strtotime($arrExif['DateOrig']));
				$Stmt->execute();
			}
			if ($this->Commit()) {
				return true;
			}
			else {
				$this->RollBack();
				return false;
			}
		}
	}

	/**
	 * Load form field data and output it.
	 */
	public function loadData() {
		switch ($_POST['FldName']) {
			case 'Location':
				$CountryId = (isset($_POST['CountryId']) && $_POST['CountryId'] != '') ? $_POST['CountryId'] : NULL;
				$Sql = "SELECT L.Id, L.Name LocName FROM Locations L";
				if (!is_null($CountryId)) {
					$Sql .= " LEFT JOIN Locations_Countries LC ON L.Id = LC.LocationId
					WHERE CountryId = :CountryId";
				}
				$Sql .=	" ORDER BY Name ASC";
				$Stmt = $this->db->prepare($Sql);
				if (!is_null($CountryId)) {
					$Stmt->bindParam(':CountryId', $CountryId);
				}
				$Stmt->execute();
				while ($Row = $Stmt->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value="'.$Row['Id'].'">'.$Row['LocName'].'</option>';
				}
				break;
			case 'KeywordName':
				$Query = (isset($_POST['Val']) && $_POST['Val'] != '') ? $_POST['Val'] : '';
				$Limit = (isset($_POST['count']) && preg_match('/^[0-9]+$/', $_POST['count']) === 1) ? $_POST['count'] : 50;
				$Offset = (isset($_POST['start']) && preg_match('/^[0-9]+$/', $_POST['start']) === 1) ? $_POST['start'] : 0;
				$Sql = "SELECT Id, Name FROM Keywords WHERE Name LIKE '%'||:Query||'%' ORDER BY Name ASC
					LIMIT :Limit OFFSET :Offset";				
				$Stmt = $this->db->prepare($Sql);
				$Stmt->bindParam(':Query', $Query);
				$Stmt->bindParam(':Limit', $Limit);
				$Stmt->bindParam(':Offset', $Offset);
				$Stmt->execute();
				// { identifier: Id, items: [Id: 3, Name: 'Flug'] }
				$arr = $Stmt->fetchAll(PDO::FETCH_ASSOC);
				$arr = array('identifier' => 'Id', 'items' => $arr);
				echo json_encode($arr);
				break;
			case 'ScientificName':
				$Query = (isset($_POST['Val']) && $_POST['Val'] != '') ? $_POST['Val'] : '';
				$ColName = (isset($_POST['ColName']) && preg_match('/^\w+$/', $_POST['ColName']) === 1) ? $_POST['ColName'] : 'NameDe';
				$Limit = (isset($_POST['count']) && preg_match('/[0-9]+/', $_POST['count']) !== false) ? $_POST['count'] : 50;
				$Offset = (isset($_POST['start']) && preg_match('/[0-9]+/', $_POST['start']) !== false) ? $_POST['start'] : 0;
				$Sql = "SELECT Id, NameDe, NameEn, NameLa FROM ScientificNames WHERE $ColName LIKE '%'||:Query||'%' LIMIT :Limit OFFSET :Offset";				
				$Stmt = $this->db->prepare($Sql);
				$Stmt->bindParam(':Query', $Query);
				$Stmt->bindParam(':Limit', $Limit);
				$Stmt->bindParam(':Offset', $Offset);
				$Stmt->execute();
				// { identifier: Id, items: [Id: 3, Name: 'Flug'] }
				$arr = $Stmt->fetchAll(PDO::FETCH_ASSOC);
				$arr = array('identifier' => 'Id', 'items' => $arr);
				echo json_encode($arr);
			break;
		}
	}

	/**
	 * Query the number of records with given sql.
	 * If you provide the optional parameter lastPage, then this query is cached in a session variable as long as the
	 * query variables with the exception of: pgNav, sort, numRecPp
	 * @param string $sql sql query
	 * @param string $colName name of db column to count
	 * @param array $arrBind array of bind variables
	 * @param string $lastPage [optional] url of last page
	 * @return integer number of records
	 */
	public function getNumRec($sql, $colName, $arrBind, $lastPage = null) {
		$colName = preg_replace('/[^a-zA-Z0-9_\.]/', '', $colName);	// sanitize
		$expr = "/&?pgNav=[0-9]*|&?sort=[0-9]*|&?numRecPp=[0-9]*/";	// these query vars can change without having to reset numRec
		$queryLast = parse_url($lastPage);
		if (array_key_exists('query', $queryLast)) {
			$queryLast = preg_replace($expr, '', $queryLast['query']);
		}
		else {
			$queryLast = '';
		}
		$queryCurr = parse_url($this->getUrl());
		if (array_key_exists('query', $queryCurr)) {
			$queryCurr = preg_replace($expr, '', $queryCurr['query']);
		}
		else {
			$queryCurr = '';
		}
		if (!isset($_SESSION[$this->getNamespace()]['numRec']) || $queryLast != $queryCurr) {
			$pattern = '/^SELECT (.|\s)*? FROM/';
			$sql = preg_replace($pattern, 'SELECT COUNT('.$colName.') numRec FROM', $sql);
			$stmt = $this->db->prepare($sql);
			foreach ($arrBind as $key => $val) {
				$stmt->bindValue(':'.$key, $val);
			}
			$stmt->execute();			
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) {
				$_SESSION[$this->getNamespace()]['numRec'] = $row['numRec'];
			}
			else {
				$_SESSION[$this->getNamespace()]['numRec'] = 0;
			}
		}
		return $_SESSION[$this->getNamespace()]['numRec'];
	}

	/**
	 * Store user preference.
	 * @param string $Name preference
	 * @param string $Value value
	 * @param integer $UserId user id
	 * @return bool
	 */
	public function savePref($Name, $Value, $UserId) {
		if ($this->DbType == 'private') {
			$Path = $this->getWebRoot().$this->PathDb;
		}
		else {
			$Path = $this->getWebRoot().$this->PathDbPubl;
		}
		$Path = $_SERVER['DOCUMENT_ROOT'].$Path;
		try {
			$Db = new PDO('sqlite:'.$Path.'/'.$this->DbUserPrefs);
		}
		catch (PDOException $Error) {
			echo $Error->getMessage();
		}
		// get setting id
		$Sql = "SELECT Id FROM Settings WHERE Name = :Name";
		$Stmt = $Db->prepare($Sql);
		$Stmt->bindParam(':Name', $Name);
		$Stmt->execute();
		$Row = $Stmt->fetch(PDO::FETCH_ASSOC);
		$SettingId = $Row['Id'];
		// check if this setting was already set once if not insert it otherwise update
		$Sql = "SELECT Id FROM Prefs WHERE SettingId = :SettingId AND UserId = :UserId";
		$Stmt = $Db->prepare($Sql);
		$Stmt->bindParam(':SettingId', $SettingId);
		$Stmt->bindParam(':UserId', $UserId);
		$Stmt->execute();
		$Row = $Stmt->fetch(PDO::FETCH_ASSOC);
		if (!is_null($Row['Id'])) {
			$Sql = "UPDATE Prefs SET Value = :Value WHERE SettingId = :SettingId AND UserId = :UserId";
		}
		else {
			$Sql = "INSERT INTO Prefs (SettingId, UserId, Value) VALUES (:SettingId, :UserId, :Value)";
		}
		$Stmt = $Db->prepare($Sql);
		$Stmt->bindParam(':SettingId', $SettingId);
		$Stmt->bindParam(':UserId', $UserId);
		$Stmt->bindParam(':Value', $Value);
		return $Stmt->execute();
	}
	
	/**
	 * Load a user preference.
	 * @return mixed
	 * @param string $Name preference
	 * @param integer $UserId
	 */
	public function loadPref($Name, $UserId) {
		if ($this->DbType == 'private') {
			$Path = $this->getWebRoot().$this->PathDb;
		}
		else {
			$Path = $this->getWebRoot().$this->PathDbPubl;
		}
		$Path = $_SERVER['DOCUMENT_ROOT'].$Path;
		try {
			$Db = new PDO('sqlite:'.$Path.'/'.$this->DbUserPrefs);
		}
		catch (PDOException $Error) {
			echo $Error->getMessage();
		}
		// get setting id
		$Sql = "SELECT Value FROM Prefs WHERE SettingId = (SELECT Id FROM Settings WHERE Name = :Name) AND UserId = :UserId";
		$Stmt = $Db->prepare($Sql);
		$Stmt->bindParam(':Name', $Name);
		$Stmt->bindParam(':UserId', $UserId);
		$Stmt->execute();
		$Row = $Stmt->fetch(PDO::FETCH_ASSOC);
		return $Row['Value'];
	}
	
	/**
	 * Adds a SQL GROUP_CONCAT function
	 * Method used in the SQLite createAggregate function to implement SQL GROUP_CONCAT
	 * which is not supported by PDO. 
	 * @return 
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



?>