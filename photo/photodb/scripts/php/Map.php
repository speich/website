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
     * @param SqlMap $sql
     * @param string $webroot path to root folder
     */
    public function __construct($webroot)
    {
        parent::__construct($webroot);
        $this->connect();
    }

    /**
     * Load marker data from database
     * @param SqlMap $sql
     * @return string json
     */
    public function loadMarkerData(SqlMap $sql): string
    {
        $strSql = $sql->get();
        $stmt = $this->db->prepare($strSql);
        $sql->bind([$stmt, 'bindValue']);
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