<?php
namespace PhotoDb\Photo;

use PDO;
use PDOStatement;
use stdClass;
use PhotoDb\PhotoDb;
use PhotoDb\PhotoDbQuery;
use WebsiteTemplate\Language;

require_once 'PhotoDb.php';
require_once 'PhotoDbQuery.php';
require_once 'Language.php';

/**
 * This class is used to define the bind parameters for the SQL statement used to load marker data.
 * This class is only for convenience. It will let you know which bind variables are used in the SQL query returned
 * by the getSql method. The properties will show in autocomplete of an IDE. It allows the programmer
 * to know which variable names are required to use with the SQL statement for binding without even knowing the exact
 * bind name.
 */
class Bindings {
	/** @var String latitude Northeast */
	var $lat1 = ':lat1';

	/** @var String longitude Northeast */
	var $lng1 = ':lng1';

	/** @var String latitude Southwest */
	var $lat2 = ':lat2';

	/** @var String longitude Southwest */
	var $lng2 = ':lng2';

	/** @var String quality of the photo */
	var $qual = ':qual';

	/** @var String theme of the photo */
	var $theme = ':theme';

	/** @var String country photo was taken */
	var $country = ':country';

	/** @var string query limit */
	var $limit = ':limit';

	/** @var string query offset */
	var $offset = ':offset';
}


/**
 * Class Photo
 */
class Photo extends PhotoDb implements PhotoDbQuery {
	/** @var int default value for photo quality */
	var $quality = 2;

	/** @var int default sort value */
	var $sort = 1;

	/** @var int default number of records per page */
	var $numRecPerPage = 14;

	/**
	 * @param string $webroot path to root folder
	 */
	public function __construct($webroot) {
		parent::__construct($webroot);
		$this->connect();
	}

	/**
	 * Create property bag from posted data.
	 * If a value is not posted, default value will be set.
	 * @param $postData
	 * @return stdClass
	 */
	public function createObjectFromPost($postData) {
		if (is_null($postData) || !is_object($postData)) {
			$postData = new stdClass();
		}
		$params = new stdClass();
		$params->qual = property_exists($postData, 'qual') ? $postData->qual : $this->quality;
		$params->theme = property_exists($postData, 'theme') ? (int) $postData->theme : null;
		$params->country = property_exists($postData, 'country') ? (int) $postData->country : null;
		$params->lat1 = property_exists($postData, 'lat1') ? (float) $postData->lat1 : null;
		$params->lng1 = property_exists($postData, 'lng1') ? (float) $postData->lng1 : null;
		$params->lat2 = property_exists($postData, 'lat2') ? (float) $postData->lat2 : null;
		$params->lng2 = property_exists($postData, 'lng2') ? (float) $postData->lng2 : null;
		$params->sort = (int) (property_exists($postData, 'sort') ? $postData->sort : $this->sort);
		$params->numRecPerPage = (int) (property_exists($postData, 'numRecPp') ? $postData->numRecPp : $this->numRecPerPage);
		$params->page = property_exists($postData, 'pg') ? $postData->pg - 1 : 0;

		return $params;
	}

	/**
	 * Return SQL to query photos in database.
	 * @param stdClass $params
	 * @return string SQL
	 */
	public function getSql($params) {
		// join only with theme when we can filter by theme, otherwise we have multiple records per theme (group by imgid is to performance expensive)
		// We have to alias all fields since depending on PHP SQLite version short column names is on/off and can't be set.
		$sql = "SELECT * FROM (
			SELECT DISTINCT i.Id imgId, i.ImgFolder imgFolder, i.ImgName imgName, i.ImgTitle imgTitle,
			i.DateAdded dateAdded, i.LastChange lastChange, i.ImgTitle imgTitle,
			i.ImgLat lat, i.ImgLng lng,
			r.Id ratingId, i.ImgDateOriginal date ";
			if ($params->theme) {
				$sql.= ", it.ThemeId themeId";
			}
			else if ($params->country) {
				$sql.= ", lc.CountryId countryId";
			}
			$sql.= ", CASE WHEN i.ImgDateOriginal IS NULL THEN
				(CASE WHEN i.ImgDate IS NOT NULL THEN DATETIME(i.ImgDate, 'unixepoch', 'localtime') END)
				ELSE DATETIME(i.ImgDateOriginal, 'unixepoch', 'localtime') END date
				FROM Images i";
			if ($params->theme) {
				$sql.= " INNER JOIN Images_Themes it ON i.Id = it.ImgId";
			}
			else if ($params->country) {
				$sql.= "	INNER JOIN Images_Locations il ON i.Id = il.ImgId
				INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId";
			}
			$sql.= " INNER JOIN Rating r ON i.RatingId = r.Id
		)";

		// filtering
		// to avoid confusion only allow one restriction at a time, e.g. either theme or country.
		// Filter by Rating or coordinates (bounds) is always possible
		if ($params->theme) {
			$sqlFilter = " WHERE themeId = :theme AND";
		}
		else if ($params->country) {
			$sqlFilter = " WHERE countryId = :country AND";
		}
		else {
			$sqlFilter = " WHERE";
		}
		$sqlFilter.= " ratingId > :qual";
		if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
			if ($params->lng2 < $params->lng1) {
				$sqlFilter.= " AND (lat >= :lat2 AND lng >= :lng2) AND (lat <= :lat1 AND lng <= :lng1)"; // parentheses are just for readability
			}
			// special case lng2|lng1 <-> 180|-180
			else {
				$sqlFilter.= " AND ((lat >= :lng2 AND lng >= :lng2) OR (lat <= :lat1 AND lng <= :lng1))";
			}
			$sqlFilter.= " AND lat NOT NULL AND lng NOT NULL AND lat != '' AND lng != ''";
		}

		// sorting
		$sqlSort = '';
		switch ($params->sort) {
			case 1:
				$sqlSort = ' ORDER BY dateAdded DESC';
				break;
			case 2:
				$sqlSort = ' ORDER BY date DESC';
				break;
			case 3:
				$sqlSort = ' ORDER BY lastChange DESC';
				break;
			case 4:
				$sqlSort = ' ORDER BY imgTitle ASC';
				break;
		}
		$sql.= $sqlFilter.$sqlSort;

		return $sql;
	}

	/**
	 * Bind variables to SQL query.
	 * @param PDOStatement $stmt
	 * @param stdClass $params
	 */
	public function bind($stmt, $params) {

		$bind = new Bindings();

		if (!is_null($params->theme)) {
			$stmt->bindValue($bind->theme, $params->theme);
		}
		if (!is_null($params->country)) {
			$stmt->bindValue($bind->country, $params->country);
		}
		$stmt->bindValue($bind->qual, $params->qual);
		if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
			$stmt->bindValue($bind->lat1, $params->lat1);
			$stmt->bindValue($bind->lng1, $params->lng1);
			$stmt->bindValue($bind->lat2, $params->lat2);
			$stmt->bindValue($bind->lng2, $params->lng2);
		}
	}

	/**
	 * Load photos from database.
	 * @param stdClass $params
	 * @return array database records of photos or false
	 */
	public function loadPhotos($params) {
		$sql = $this->getSql($params);
		$stmt = $this->db->prepare($sql." LIMIT :limit OFFSET :offset");
		$this->bind($stmt, $params);
		$stmt->bindValue(':limit', $params->numRecPerPage);
		$stmt->bindValue(':offset', $params->page * $params->numRecPerPage);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Return number of records in query.
	 * @param stdClass $params
	 * @return int|bool number of records or false
	 */
	public function getNumRec($params) {
		$pattern = '/^SELECT \* FROM/';
		$sql = $this->getSql($params);
		$sql = preg_replace($pattern, 'SELECT COUNT(imgId) numRec FROM', $sql);
		$stmt = $this->db->prepare($sql);
		$this->bind($stmt, $params);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row ? $row['numRec'] : false;
	}

	/**
	 * Display photos.
	 * @param array $photos database records
	 * @param Language $web
	 * @param array $i18n
	 * @return string HTML
	 */
	public function renderData($photos, $web, $i18n) {
		$str = '';
		if (count($photos) == 0) {
			$str.= '<p>'.$i18n['not found'].'</p>';
			return $str;
		}

		foreach ($photos as $row) {
			// get image dimensions
			$imgFile = $this->webroot.$this->getPath('img').'thumbs/'.$row['imgFolder'].'/'.$row['imgName'];
			$imgSize = getimagesize(__DIR__.'/../../../..'.$web->getWebRoot().$this->getPath('img').$row['imgFolder'].'/'.$row['imgName']);
			$imgTitle = $row['imgTitle'];
			$link = str_replace('thumbs/', '', $imgFile).'?w='.$imgSize[0].'&h='.$imgSize[1];
			$detailLink = 'photo-detail.php'.$web->getQuery(array('imgId' => $row['imgId']));

			if ($imgSize[0] > $imgSize[1]) {
				$css = 'slideHorizontal';
				$cssImg = 'slideImgHorizontal';

			}
			else if ($imgSize[0] < $imgSize[1]) {
				$css = 'slideVertical';
				$cssImg = 'slideImgVertical';
			}
			else {
				$css = 'slideQuadratic';
				$cssImg = 'slideImgQuadratic';
			}

			$str.= '<li class="slide">';
			$str.= '<div class="slideCanvas '.$css.'">';
			$str.= '<a href="'.$link.'" title="'.$imgTitle.'">';
			$str.= '<img class="'.$cssImg.'" src="'.$imgFile.'" alt="'.$i18n['photo'].'" title="'.$i18n['thumbnail of'].' '.$imgTitle.'">';
			$str.= '</a></div>';
			$title = $i18n['zoom photo'].': '.$imgTitle;
			$str.= '<div class="slideText"><a title="'.$title.'" href="'.$link.'">'.$i18n['zoom'].'</a> | ';
			$str.= '<a title="'.$i18n['show details'].'" href="'.$detailLink.'">'.$i18n['details'].'</a></div>';
			$str.= '</li>';
		}
		return $str;
	}
}