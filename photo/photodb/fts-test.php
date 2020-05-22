<?php
//phpinfo();

// compile-time options
/*echo "<h2>FTS compile-time options</h2>";
$db = new PDO('sqlite:fts4.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = 'PRAGMA compile_options;';
$stm = $db->query($sql);
$lst = $stm->fetchAll();
var_dump($lst);
*/

/**
 * @param string $dsn
 * @param string $type
 * @return bool|PDO
 */
function createDb($dsn, $type)
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    $sqlCreate = "BEGIN;
        DROP TABLE IF EXISTS Keywords; 
        DROP TABLE IF EXISTS SearchKeywords_fts;
        CREATE TABLE Keywords (id INTEGER PRIMARY KEY, Keyword);
        INSERT INTO Keywords VALUES (1, 'Waldhaus'); 
        INSERT INTO Keywords VALUES (2, 'Wälder'); 
        INSERT INTO Keywords VALUES (3, 'Rehe im Wald'); 
        CREATE VIRTUAL TABLE SearchKeywords_fts USING $type; 
        INSERT INTO SearchKeywords_fts(Keyword) SELECT Keyword FROM Keywords;
        COMMIT;";

    $db = new PDO($dsn, null, null, $options);
    if ($db->exec($sqlCreate) === false) {
        foreach ($db->errorInfo() as $err) {
            echo $err.' ';
        }

        return false;
    }

    return $db;
}

/**
 * @param PDO $db
 * @param string $sqlSearch
 * @param string $chars
 * @return bool|PDOStatement
 */
function search($db, $chars)
{
    $sqlSearch = 'SELECT Keyword FROM SearchKeywords_fts
       WHERE (SearchKeywords_fts MATCH :chars)';
    $stmt = $db->prepare($sqlSearch);
    $stmt->bindParam(':chars', $chars, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt;
}

/**
 * @param PDOStatement $stmt
 * @param string $chars
 */
function renderResult($stmt, $chars)
{
    echo "gesucht nach: $chars<br>gefunden:<br>";
    foreach ($stmt as $row) {
        echo $row['Keyword'].'<br>';
    }
}

/**
 * @param PDO $db
 */
function version($db)
{
    $stmt = $db->query('SELECT sqlite_version() AS version');
    echo 'SQLite version: '.$stmt->fetch()['version'].'<br>';
}

$chars = 'wäl*';
echo '<h2>FTS4</h2>';
$db = createDb('sqlite:fts4.sqlite', 'fts4(Keyword)');
if ($db) {
    version($db);
    $stmt = search($db, $chars);
    renderResult($stmt, $chars);
}

echo '<h2>FTS4 + unicode61</h2>';
$db = createDb('sqlite:fts4-unicode.sqlite', 'fts4(Keyword, tokenize=unicode61)');
if ($db) {
    version($db);
    $stmt = search($db, $chars);
    renderResult($stmt, $chars);
}

echo '<h2>FTS4 + icu</h2>';
$db = createDb('sqlite:fts4-unicode.sqlite', 'fts4(Keyword, tokenize=icu de_CH)');
if ($db) {
    version($db);
    $stmt = search($db, $chars);
    renderResult($stmt, $chars);
}

echo '<h2>FTS5</h2>';
$db = createDb('sqlite:fts5.sqlite', 'fts5(Keyword)');
if ($db) {
    version($db);
    $stmt = search($db, $chars);
    renderResult($stmt, $chars);
}