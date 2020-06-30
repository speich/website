<?php

echo "SQLite v:".SQLite3::version()['versionString']."<br>";
// Test checks for unicode61 support in fts4
try {
	$db = new PDO('sqlite:test.sqlite');
}
catch (PDOException $error) {
	echo $error->getMessage();
}

$db->exec("DROP TABLE data");
//$sql = "CREATE VIRTUAL TABLE data USING fts5(keyword)";
$sql = "CREATE VIRTUAL TABLE data USING fts4(keyword, tokenize=unicode61)";
//$sql = "CREATE VIRTUAL TABLE data USING fts4(keyword)";
try {
    $db->exec($sql);
    echo "1: ";
    var_dump($db->errorInfo());
    echo "<br>";
}
catch (PDOException $error) {
	echo "2: ".$error->getMessage();
    echo "<br>";
}

$db->exec("INSERT INTO data(keyword) VALUES ('Simon gefallen die Wälder im Jura')");
echo "3: ";
var_dump($db->errorInfo());
echo "<br>4: ";
$data = $db->query("SELECT * FROM data where keyword match 'wald*'");
var_dump($data->fetchAll());