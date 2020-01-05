<?php
namespace PhotoDb;

use PDOStatement;
use stdClass;


/**
 * Interface PhotoDbQuery
 */
interface PhotoDbQuery {

	/**
	 * @param $webroot
	 */
	public function __construct($webroot);

	/**
	 * Create property bag from posted data.
	 * @param $postData
	 * @return stdClass
	 */
	public function createObjectFromPost($postData);

    /**
     * Return SQL string used for query.
     * @param stdClass $params
     * @return String SQL
     */
	public function getSql($params): string;

    /**
     * Bind variables to SQL query.
     * @param PDOStatement $stmt
     * @param stdClass $params
     */
	public function bind($stmt, $params): void;
} 