<?php
require_once __DIR__.'/library/vendor/autoload.php';
// create the example database
use PhotoDb\Fts4Ranking;
use PhotoDb\FtsFunctions;

try {
  $db = new PDO('sqlite:example.sqlite');
} catch (PDOException $error) {
  echo $error->getMessage();
}


$ranking = new Fts4Ranking();
$db->sqliteCreateFunction('SCORE', [$ranking, 'matchInfoData']);

// create a virtual fts4 table and populate it with example data
try {
  $db->exec("DROP TABLE IF EXISTS images;
    CREATE VIRTUAL TABLE images USING fts4(imgId, title, description, species, speciesEn);
    INSERT INTO images VALUES(1, 'Great Spotted Woodpecker', 'A great spotted woodpecker with a caterpillar in its beak.', 'Dendrocopos major', 'Great Spotted woodpecker');
    INSERT INTO images VALUES(2, 'Woodpecker at the pond', 'A green woodpecker drinks water.', 'Picus viridis', 'Green Woodpecker');
    INSERT INTO images VALUES(3, 'Woodpecker', 'A middle spotted woodpecker is looking for food on an oak tree.', 'Dendrocopos medius', 'Middle Spotted Woodpecker');
    INSERT INTO images VALUES(4, 'Woodpecker', 'A lesser yellownape showing its red wings.', 'Picus chlorolophus', 'Lesser Yellownape');
    /* INSERT INTO images VALUES(5, 'Blackbird', 'Blackbird eating an green apple.', 'Turdus', 'Blackbird'); */
    ");
} catch (PDOException $error) {
  echo $error->getMessage().'<br>';
}


// use matchinfo when searching for green woodpecker using an implicit AND operator
//$data = $db->query("SELECT id, MATCHINFO(images, '".$ranking::MATCHINFO_NUM_TOKEN_AVERAGE."') info FROM images WHERE images MATCH 'green woodpecker'");
$args = $ranking::MATCHINFO_NUM_TOKEN_AVERAGE;
$data = $db->query("SELECT imgId, SCORE(MATCHINFO(images, '$args'), '$args') info FROM images WHERE images MATCH 'green woodpecker'");

// convert the binary output to integers and format integers in groups of three
while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
    echo 'row '.$row['imgId'].': '.$row['info'].'<br>';
}