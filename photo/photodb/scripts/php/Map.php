<?php
namespace PhotoDb;

use PDO;
use stdClass;


/**
 * Class Map
 * @package PhotoDb
 */
class Map extends PhotoDb {

	/**
	 * @param string $webroot path to root folder
	 */
	public function __construct($webroot) {
		parent::__construct($webroot);
		$this->connect();
	}

	/**
	 * Create property bag from posted data.
	 * @param $data
	 * @return stdClass
	 */
	protected function createObjectFromPost($data) {
		if (is_null($data) || !is_object($data)) {
			$data = new stdClass();
		}
		$params = new stdClass();
		$params->qual = property_exists($data, 'qual') ? $data->qual - 1 : 2;
		$params->theme = property_exists($data, 'theme') ? $data->theme : null;
		$params->country = property_exists($data, 'country') ? $data->country : null;
		$params->lat1 = property_exists($data, 'lat1') ? $data->lat1 : null;
		$params->lng1 = property_exists($data, 'lng1') ? $data->lng1 : null;
		$params->lat2 = property_exists($data, 'country') ? $data->lat2 : null;
		$params->lng2 = property_exists($data, 'country') ? $data->lng2 : null;

		return $params;
	}

	/**
	 * Load marker data from database
	 * @param object $data object with properties [rating], [theme] and [country]
	 * @return string json
	 */
	public function loadMarkerData($data = null) {

		$params = $this->createObjectFromPost($data);

		// normal case
		if ($params->lng2 < $params->lng1) {
			$sqlLatLng = " WHERE imgLat > :query0 AND imgLat < :query1
			AND imgLng > :query2 AND imgLng < :query3";
		}
		// special case lng2|lng1 <-> 180|-180
		else {
			$sqlLatLng = " WHERE imgLat > :query0 AND imgLat < :query1
			AND (imgLng > :query2 OR imgLng < :query3)";
		}



		// Currently we just load all markers
		$sql = "SELECT i.Id id, i.ImgFolder||'/'||i.ImgName img, ROUND(i.ImgLat, 6) lat, ROUND(i.ImgLng, 6) lng FROM Images i";
		if (!is_null($params->theme)) {
			$sql.= "	INNER JOIN Images_Themes it ON i.Id = it.ImgId";
		}
		else if (!is_null($params->country)) {
			$sql.= " INNER JOIN Images_Locations il ON i.Id = il.ImgId
				INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId";
		}
		$sql.= " WHERE i.RatingId > :rating
			AND (i.ImgLat NOT NULL OR i.ImgLng NOT NULL) -- exclude images that don't have any gps info
			AND i.ImgLat != '' AND i.ImgLng != ''";
			if (!is_null($params->theme)) {
				$sql.= " AND it.ThemeId = :theme";
			}
			if (!is_null($params->country)) {
				$sql.= " AND lc.CountryId = :country";
			}


		$stmt->bindValue(':query0', $lat1);
		$stmt->bindValue(':query1', $lat2);
		$stmt->bindValue(':query2', $lng1);
		$stmt->bindValue(':query3', $lng2);


		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':rating', $params->qual);
		if (!is_null($params->theme)) {
			$stmt->bindValue(':theme', $params->theme);
		}
		if (!is_null($params->country)) {
			$stmt->bindValue(':country', $params->country);
		}
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