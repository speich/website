<?php
namespace PhotoDb\Map;

use PDO;
use PDOStatement;
use PhotoDb\PhotoDb;
use PhotoDb\PhotoDbQuery;
use stdClass;

require_once 'PhotoDb.php';
require_once 'PhotoDbQuery.php';

/**
 * This class is used to define the bind parameters for the SQL statement used to load marker data.
 * This class is only for convenience. The properties will show in autocomplete of an IDE. It allows the programmer
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
}


/**
 * Class Map
 */
class Map extends PhotoDb implements PhotoDbQuery {

	/**
	 * @param string $webroot path to root folder
	 */
	public function __construct($webroot) {
		parent::__construct($webroot);
		$this->connect();
	}

	/**
	 * Create property bag from posted data.
	 * @param $postData
	 * @return stdClass
	 */
	public function createObjectFromPost($postData) {
		if (is_null($postData) || !is_object($postData)) {
			$postData = new stdClass();
		}
		$params = new stdClass();
		$params->qual = property_exists($postData, 'qual') ? $postData->qual : 2;
		$params->theme = property_exists($postData, 'theme') ? (int) $postData->theme : null;
		$params->country = property_exists($postData, 'country') ? (int) $postData->country : null;
		$params->lat1 = property_exists($postData, 'lat1') ? (float) $postData->lat1 : null;
		$params->lng1 = property_exists($postData, 'lng1') ? (float) $postData->lng1 : null;
		$params->lat2 = property_exists($postData, 'lat2') ? (float) $postData->lat2 : null;
		$params->lng2 = property_exists($postData, 'lng2') ? (float) $postData->lng2 : null;

		return $params;
	}

	/**
	 * Return SQL to query database for marker data.
	 * @param stdClass $params
	 * @return String SQL
	 */
	public function getSql($params) {
			// normal case
		if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
			if ($params->lng2 < $params->lng1) {
				$sqlLatLng = " AND (i.ImgLat >= :lat2 AND i.ImgLng >= :lng2) AND (i.ImgLat <= :lat1 AND i.ImgLng <= :lng1)";	// parentheses are just for readability
			}
			// special case lng2|lng1 <-> 180|-180
			else {
				$sqlLatLng = " AND ((i.ImgLat >= :lng2 AND i.ImgLng >= :lng2) OR (i.ImgLat <= :lat1 AND i.ImgLng <= :lng1))";
			}
		}
		else {
			$sqlLatLng = '';
		}

		$sql = "SELECT i.Id id, i.ImgFolder||'/'||i.ImgName img, ROUND(i.ImgLat, 6) lat, ROUND(i.ImgLng, 6) lng FROM Images i";
		if ($params->theme) {
			$sql.= "	INNER JOIN Images_Themes it ON i.Id = it.ImgId";
		}
		else if ($params->country) {
			$sql.= " INNER JOIN Images_Locations il ON i.Id = il.ImgId
				INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId";
		}
		$sql.= " WHERE i.RatingId > :qual
			AND i.ImgLat NOT NULL AND i.ImgLng NOT NULL AND i.ImgLat != '' AND i.ImgLng != ''"
			.$sqlLatLng;

		// filtering
		// to avoid confusion only allow one restriction at a time, e.g. either theme or country. Filter by Rating or
		// coordinates (bounds) is always possible
		if ($params->theme) {
			$sql.= " AND it.ThemeId = :theme";
		}
		else if ($params->country) {
			$sql.= " AND lc.CountryId = :country";
		}

		return $sql;
	}

	/**
	 * Bind variables to SQL query.
	 * @param PDOStatement $stmt
	 * @param stdClass $params
	 */
	public function bind($stmt, $params) {
		$bind = new Bindings();

		if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
			$stmt->bindValue($bind->lat1, $params->lat1);
			$stmt->bindValue($bind->lng1, $params->lng1);
			$stmt->bindValue($bind->lat2, $params->lat2);
			$stmt->bindValue($bind->lng2, $params->lng2);
		}
		if ($params->theme) {
			$stmt->bindValue($bind->theme, $params->theme);
		}
		else if ($params->country) {
			$stmt->bindValue($bind->country, $params->country);
		}
		$stmt->bindValue($bind->qual, $params->qual);
	}

	/**
	 * Load marker data from database
	 * @param stdClass $params
	 * @return string json
	 */
	public function loadMarkerData($params) {
		$sql = $this->getSql($params);
		$stmt = $this->db->prepare($sql);
		$this->bind($stmt, $params);
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return json_encode($res, JSON_NUMERIC_CHECK);
	}

	/**
	 * @param $country
	 * @return string|bool json
	 */
	public function loadCountry($country = null) {
		if (is_null($country)) {
			return false;
		}

		$sql = "SELECT c.Id, c.NameEn FROM Countries c WHERE c.Id = :country";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':country', $country);
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return json_encode($res, JSON_NUMERIC_CHECK);
	}
} 