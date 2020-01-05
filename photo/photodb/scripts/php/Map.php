<?php

namespace PhotoDb;

use PDO;
use PDOStatement;
use stdClass;


require_once 'PhotoDb.php';
require_once 'PhotoDbQuery.php';

/**
 * Class Map
 */
class Map extends PhotoDb implements PhotoDbQuery
{

    /**
     * @param string $webroot path to root folder
     */
    public function __construct($webroot)
    {
        parent::__construct($webroot);
        $this->connect();
    }

    /**
     * Create property bag from posted data.
     * @param $postData
     * @return stdClass
     */
    public function createObjectFromPost($postData)
    {
        if ($postData === null || !is_object($postData)) {
            $postData = new stdClass();
        }
        $params = new stdClass();
        $params->qual = property_exists($postData, 'qual') ? filter_var($postData->qual, FILTER_SANITIZE_NUMBER_INT) : 2;
        $params->theme = property_exists($postData, 'theme') ? filter_var($postData->theme, FILTER_SANITIZE_NUMBER_INT) : null;
        $params->country = property_exists($postData, 'country') ? filter_var($postData->country, FILTER_SANITIZE_NUMBER_INT) : null;
        $params->lat1 = property_exists($postData, 'lat1') ? filter_var($postData->lat1, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $params->lng1 = property_exists($postData, 'lng1') ? filter_var($postData->lng1, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $params->lat2 = property_exists($postData, 'lat2') ? filter_var($postData->lat2, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $params->lng2 = property_exists($postData, 'lng2') ? filter_var($postData->lng2, FILTER_SANITIZE_NUMBER_FLOAT) : null;

        return $params;
    }

    /**
     * Return SQL to query database for marker data.
     * @param stdClass $params
     * @return String SQL
     */
    public function getSql($params): string
    {
        // normal case
        if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
            if ($params->lng2 < $params->lng1) {
                $sqlLatLng = ' AND (i.ImgLat >= :lat2 AND i.ImgLng >= :lng2) AND (i.ImgLat <= :lat1 AND i.ImgLng <= :lng1)';    // parentheses are just for readability
            } // special case lng2|lng1 <-> 180|-180
            else {
                $sqlLatLng = ' AND ((i.ImgLat >= :lng2 AND i.ImgLng >= :lng2) OR (i.ImgLat <= :lat1 AND i.ImgLng <= :lng1))';
            }
        } else {
            $sqlLatLng = '';
        }

        $sql = "SELECT i.Id id, i.ImgFolder||'/'||i.ImgName img, ROUND(i.ImgLat, 6) lat, ROUND(i.ImgLng, 6) lng FROM Images i";
        if ($params->theme) {
            $sql .= ' INNER JOIN Images_Themes it ON i.Id = it.ImgId';
        } elseif ($params->country) {
            $sql .= ' INNER JOIN Images_Locations il ON i.Id = il.ImgId
				INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId';
        }
        $sql .= " WHERE i.RatingId > :qual
			AND i.ImgLat NOT NULL AND i.ImgLng NOT NULL AND i.ImgLat != '' AND i.ImgLng != ''"
            .$sqlLatLng;

        // filtering
        // to avoid confusion only allow one restriction at a time, e.g. either theme or country. Filter by Rating or
        // coordinates (bounds) is always possible
        if ($params->theme) {
            $sql .= ' AND it.ThemeId = :theme';
        } elseif ($params->country) {
            $sql .= ' AND lc.CountryId = :country';
        }

        return $sql;
    }

    /**
     * Bind variables to SQL query.
     * @param PDOStatement $stmt
     * @param stdClass $params
     */
    public function bind($stmt, $params): void
    {
        $bind = new MapBindings();

        if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
            $stmt->bindValue($bind->lat1, $params->lat1);
            $stmt->bindValue($bind->lng1, $params->lng1);
            $stmt->bindValue($bind->lat2, $params->lat2);
            $stmt->bindValue($bind->lng2, $params->lng2);
        }
        if ($params->theme) {
            $stmt->bindValue($bind->theme, $params->theme);
        } elseif ($params->country) {
            $stmt->bindValue($bind->country, $params->country);
        }
        $stmt->bindValue($bind->qual, $params->qual);
    }

    /**
     * Load marker data from database
     * @param stdClass $params
     * @return string json
     */
    public function loadMarkerData($params): string
    {
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
    public function loadCountry($country = null)
    {
        if ($country === null) {
            return false;
        }

        $sql = 'SELECT c.Id, c.NameEn FROM Countries c WHERE c.Id = :country';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':country', $country);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($res, JSON_NUMERIC_CHECK);
    }
} 