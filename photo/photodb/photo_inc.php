<?php

use PhotoDb\PhotoDb;
use PhotoDb\PhotoList;
use PhotoDb\PhotoQueryString;
use PhotoDb\SqlPhotoList;
use PhotoDb\SearchQuery;
use WebsiteTemplate\Menu;
use WebsiteTemplate\PagedNav;
use WebsiteTemplate\QueryString;


require_once __DIR__.'/../../scripts/php/inc_script.php';
$i18n = require __DIR__.'/nls/'.$language->get().'/photo.php';

$db = new PhotoDb($web->getWebRoot());
$db->connect();
$query = new QueryString();
$params = new PhotoQueryString($_GET);

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

// TODO: show title or image when sorting by it
$arrVal = [
    SqlPhotoList::SORT_BY_IMGTITLE => $i18n['by title'],
    SqlPhotoList::SORT_BY_DATEADDED => $i18n['date added'],
    SqlPhotoList::SORT_BY_DATECREATED => $i18n['date created'],
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
$photo = new PhotoList($db);
$sql = new SqlPhotoList();
$sql->qual = $params->qual;
$sql->theme = $params->theme;
$sql->country = $params->country;
$sql->lat1 = $params->lat1;
$sql->lng1 = $params->lng1;
$sql->lat2 = $params->lat2;
$sql->lng2 = $params->lng2;
if (isset($params->search)) {
    $search = str_replace('&#34;', '"', $params->search);
    $words = SearchQuery::extractWords($search);
    $sql->search = SearchQuery::createQuery($words, $language->get());
}
$numRec = $photo->getNumRec($sql);
$sql->offset = $params->pg + $params->pg * $params->numPerPg;
$sql->limit = $params->numPerPg;
$sql->setSort($params->sort);
$photos = $photo->loadPhotos($sql);
$pagedNav = new PagedNav($numRec, $params->numPerPg);
$pagedNav->cssClass = 'bar-item pgNav';
$pagedNav->renderText = false;
$pagedNav->setWhitelist($web->getWhitelistQueryString());
$word = 'photo'.($numRec > 1 ? 's' : '');
$pagingBar = '<div class="bar-paging">'.
    '<div class="bar-item">'.$numRec.' '.$i18n[$word].'</div>'.
    '<div class="bar-sep-vert"></div>'.
    '<div class="bar-item">'.$i18n['per page'].'</div>'.
		'<div class="bar-item">'.$mRecPp->render().'</div>'.
    '<div class="bar-sep-vert"></div>'.
    $pagedNav->render($params->pg + 1, $web).
    '</div>';