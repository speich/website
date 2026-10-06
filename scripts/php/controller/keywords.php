<?php

use PhotoDb\PhotoDb;
use speich\WebsiteSpeich;
use WebsiteTemplate\Header;
use WebsiteTemplate\Language;


date_default_timezone_set('Europe/Zurich');
setlocale(LC_COLLATE, 'de_CH');

require_once __DIR__.'/../../../library/vendor/autoload.php';


$language = new Language();
$language->set($_GET['lang'] ?? 'de');
$web = new WebsiteSpeich();
$db = new PhotoDb($web->getWebRoot());
$db->connect();

// Sanitize the query: remove FTS control characters that could break the SQLite MATCH syntax
$query = $_GET['q'] ?? '';
$query = preg_replace('/[*"^:\-]/', '', $query);
$query = trim($query);

if ($query === '') {
    echo json_encode([]);
    exit;
}

// Build the FTS MATCH string
// If a user types "rot fuch", we want it to match "KeywordPrefixes:rot* KeywordPrefixes:fuch*"
$words = preg_split('/\s+/', $query);
$matchTerms = [];
foreach ($words as $word) {
    $matchTerms[] = $word . "*";
}
$matchString = implode(' ', $matchTerms);

// FTS4 searches ALL indexed columns (Keyword and KeywordPrefixes).
// Column Lang is ignored by MATCH because of 'notindexed', but filtered in the WHERE clause.
$sql = "SELECT Keyword 
        FROM Keywords_fts 
        WHERE Keywords_fts MATCH :match 
          AND Lang = :lang 
        ORDER BY Keyword ASC
        LIMIT 12";
$stmt = $db->db->prepare($sql);
$stmt->execute([
    ':match' => $matchString,
    ':lang' => $language->get()
]);

// Format for typeahead-standalone
$results = [];
foreach ($stmt as $row) {
    $results[] = [
        'keyword' => $row['Keyword']
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($results, JSON_UNESCAPED_UNICODE);