<?php
use PhotoDb\Photo;
use WebsiteTemplate\Menu;

require_once __DIR__.'/../../scripts/php/inc_script.php';
$i18n = require_once __DIR__.'/nls/'.$lang->get().'/photo.php';

$query = new \WebsiteTemplate\QueryString();
$photo = new Photo($web->getWebRoot());
$params = $photo->createObjectFromPost((object) $_GET);

// generate filter and sorting menus
$arrDel = ['pg'];
$arrVal = [14, 28, 56, 112];
$mRecPp = new Menu();
$mRecPp->cssClass .= ' menu2 mRecPp';
$mRecPp->add(['a', 'b', $params->numRecPerPage]);
foreach ($arrVal as $key => $val) {
	$url = $web->page.$query->withString(['numRecPp' => $val], $arrDel);
	$mRecPp->add([$key, 'a', $val, $url]);
	if ($params->numRecPerPage == $val) {
		$mRecPp->arrItem[$key]->setActive();
	}
}

$arrVal = [
	4 => $i18n['by title'],
	1 => $i18n['date added'],
	2 => $i18n['date created'],
	3 => $i18n['last changed']
];
$mSort = new Menu();
$mSort->cssClass .= ' menu2 mSort';
$mSort->add(['a', 'b', $arrVal[$params->sort]]);
foreach ($arrVal as $key => $val) {
	$url = $web->page.$query->withString(['sort' => $key], $arrDel);
	$mSort->add([$key, 'a', $val, $url]);
	if ($params->sort == $key) {
		$mSort->arrItem[$key]->setActive();
	}
}

$star = '<img class="imgRatingStar" src="'.$web->getWebRoot().'layout/images/ratingstar.gif" alt="'.$i18n['star icon'].'">';
$arrVal = [
	2 => $star.$star.$star,
	1 => $star.$star,
	0 => $star
];
$mRating = new Menu();
$mRating->cssId = 'mRating';
$mRating->cssClass .= ' menu2 mRating';
$mRating->add(['a', 'b', $arrVal[$params->qual], null, null, 'rating '.$params->qual]);
foreach ($arrVal as $key => $val) {
	$url = $web->page.$query->withString(['qual' => $key], $arrDel);
	$mRating->add([$key, 'a', $val, $url, null, 'rating '.$key]);
	if ($params->qual == $key) {
		$mRating->arrItem[$key]->setActive();
	}
}
