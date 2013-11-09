<?php
require_once ('PhotoDb.php');

/**
 * Provides full text search capabilites to photodb.
 */
class Search extends PhotoDb {
	private $sql = '';	// store sql used for searching and getting number of records
	private $arrQuery = array(); 
	
	/**
	 * Constructs the Search object.
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Load markers (images) that fall within the bounding box. 
	 * @param integer $lat1 latitude SW
	 * @param integer $lng1 longitude SW
	 * @param integer $lat2 latitude NE
	 * @param integer $lng2 longitude NE
	 * @return array
	 */
	public function loadGMapsData($lat1, $lng1, $lat2, $lng2, $qual) {
		$sql = "SELECT Id id, imgFolder||'/'||imgName img, ROUND(imgLat, 6) lat, ROUND(imgLng, 6) lng FROM Images";
		if ($lat1 < $lat2) {
			$sql.= " WHERE imgLat > :lat1 AND imgLat < :lat2 
			AND imgLng > :lng1 AND imgLng < :lng2";
		}
		else {	// bounding box contains international date line
			$sql.= " WHERE imgLat > :lat1 AND imgLat < :lat2
				AND (imgLng > :lng1 OR imgLng < lng2)";
		}
		$sql.= " AND ratingId > :qual";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':lat1', $lat1);
		$stmt->bindValue(':lng1', $lng1);
		$stmt->bindValue(':lat2', $lat2);
		$stmt->bindValue(':lng2', $lng2);
		$stmt->bindValue(':qual', $qual - 1);
		$stmt->execute();
		$arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $arrData;
	}
}

?>