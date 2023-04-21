<?php

use PhotoDb\PhotoDb;
use speich\WebsiteSpeich;
use WebsiteTemplate\Header;
use WebsiteTemplate\Language;
use PhotoDb\SearchQuery;

date_default_timezone_set('Europe/Zurich');
setlocale(LC_COLLATE, 'de_CH');

require_once __DIR__.'/../../../library/vendor/autoload.php';


$language = new Language();
$language->set($_GET['lang'] ?? 'de');
$web = new WebsiteSpeich();
$db = new PhotoDb($web->getWebRoot());

$words = SearchQuery::extractWords($_GET['q'], 4, 0);
$query = SearchQuery::createQuery($words, $language->get());



// re-format array to object
if ($words) {   // can be false or 0 records
    $arr2 = [];
    foreach ($words as $label) {
        $arr2[] = ['q' => $label];
    }
    $response = json_encode($arr2);
} else {
    $response = json_encode([]);
}
header('Content-Type: '.$header->getContentType().'; '.$header->getCharset());
echo $response;