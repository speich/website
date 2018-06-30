<?php
use PhotoDb\PhotoDb;

require_once 'scripts/php/inc_script.php';
require_once 'photo/photodb/scripts/php/PhotoDb.php';

$db = new PhotoDb($web->getWebRoot());
$db->connect();
$sql = "DROP TABLE data";
$db->db->exec($sql);
$sql = "CREATE VIRTUAL TABLE data using fts4(keyword, tokenize=unicode61)";
$db->db->exec($sql);
var_dump($db->db->errorInfo());
$db->db->exec("INSERT INTO data(keyword) VALUES ('Simon geht nach Hause einkaufen')");
var_dump($db->db->errorInfo());
$data = $db->db->query("SELECT * FROM data where keyword match 'haus'");
var_dump($data->fetchAll());