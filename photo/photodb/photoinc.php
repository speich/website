<?php

use PhotoDb\PhotoDb;
use PhotoDb\PhotoList;
use PhotoDb\PhotoListBindings;
use PhotoDb\SqlPhotoList;
use WebsiteTemplate\Menu;
use WebsiteTemplate\QueryString;


require_once __DIR__.'/../../scripts/php/inc_script.php';
$i18n = require __DIR__.'/nls/'.$lang->get().'/photo.php';

$db = new PhotoDb($web->getWebRoot());
$db->connect();
$query = new QueryString();
$params = new PhotoListBindings();
$params->sanitizeInput((object)$_GET);

// generate filter and sorting menus
$arrDel = ['pg'];
$arrVal = [
    PhotoList::NUMPERPAGE_LOW,
    PhotoList::NUMPERPAGE_MEDIUM,
    PhotoList::NUMPERPAGE_HIGH,
    PhotoList::NUMPERPAGE_VERYHIGH
];
$mRecPp = new Menu();
$mRecPp->cssClass .= ' menu2 mRecPp';
$mRecPp->add(['a', 'b', $params->numPerPg]);
foreach ($arrVal as $key => $val) {
    $url = $web->page.$query->withString(['numPerPg' => $val], $arrDel);
    $mRecPp->add([$key, 'a', $val, $url]);
    if ($params->numPerPg === $val) {
        $mRecPp->arrItem[$key]->setActive();
    }
}

$arrVal = [
    SqlPhotoList::SORT_BY_IMGTITLE => $i18n['by title'],
    SqlPhotoList::SORT_BY_DATEADDED => $i18n['date added'],
    SqlPhotoList::SORT_BY_DATECEATED => $i18n['date created'],
    SqlPhotoList::SORT_BY_DATECHANGED => $i18n['last changed']
];
$mSort = new Menu();
$mSort->cssClass .= ' menu2 mSort';
$mSort->add(['a', 'b', $arrVal[$params->sort]]);
foreach ($arrVal as $key => $val) {
    $url = $web->page.$query->withString(['sort' => $key], $arrDel);
    $mSort->add([$key, 'a', $val, $url]);
    if ($params->sort === $key) {
        $mSort->arrItem[$key]->setActive();
    }
}

$star = '<svg class="icon"><use xlink:href="/../../layout/images/symbols.svg#star"></use></svg>';
$arrVal = [
    PhotoList::QUALITY_HIGH => $star.$star.$star,
    PhotoList::QUALITY_MEDIUM => $star.$star,
    PhotoList::QUALITY_LOW => $star
];
$mRating = new Menu();
$mRating->cssClass .= ' menu2 mRating';
$mRating->add(['a', 'b', $arrVal[$params->qual]]);
foreach ($arrVal as $key => $val) {
    $url = $web->page.$query->withString(['qual' => $key], $arrDel);
    $mRating->add([$key, 'a', $val, $url]);
    if ($params->qual === $key) {
        $mRating->arrItem[$key]->setActive();
    }
}
