<?php

namespace PhotoDb;

use PDO;
use PDOStatement;



/**
 * Class Map
 */
class Map extends PhotoDb
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
     * Return SQL to query database for marker data.
     * @param PhotoQueryString $params
     * @return String SQL
     */
    public function getSql(PhotoQueryString $params): string
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
     * @param PhotoQueryString $params
     */
    public function bind($stmt, PhotoQueryString $params): void
    {
        if ($params->lat1 && $params->lng1 && $params->lat2 && $params->lng2) {
            $stmt->bindValue(':lat1', $params->lat1);
            $stmt->bindValue(':lng1', $params->lng1);
            $stmt->bindValue(':lat2', $params->lat2);
            $stmt->bindValue(':lng2', $params->lng2);
        }
        if ($params->theme) {
            $stmt->bindValue(':theme', $params->theme);
        } elseif ($params->country) {
            $stmt->bindValue(':country', $params->country);
        }
        $stmt->bindValue(':qual', $params->qual);
    }

    /**
     * Load marker data from database
     * @param PhotoQueryString $params
     * @return string json
     */
    public function loadMarkerData(PhotoQueryString $params): string
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