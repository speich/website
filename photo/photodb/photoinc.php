<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\Language;
use WebsiteTemplate\Menu;

require_once __DIR__.'/../../library/inc_script.php';
require_once 'PhotoDb.php';

$i180n = array(
	'de' => array(
		'photo' => 'Foto',
		'photos' => 'Fotos',
		'per page' => 'pro Seite',
		'sorting' => 'Sortierung',
		'rating' => 'Bewertung',
		'by title' => 'Titel',
		'date added' => 'hinzugefügt',
		'date created' => 'erstellt',
		'last changed' => 'zuletzt geändert',
		'not found' => 'Mit diesen Einstellungen wurden keine Datensätze gefunden.'
	),
	'en' => array(
		'photo' => 'photo',
		'photos' => 'photos',
		'per page' => 'per page',
		'sorting' => 'sorting',
		'rating' => 'rating',
		'by title' => 'by title',
		'date added' => 'date added',
		'date created' => 'date created',
		'last changed' => 'last changed',
		'not found' => 'No records found with these settings.'
	)
);


$db = new PhotoDb($web->getWebRoot());
$db->connect();
$lastPage = $web->getLastPage();	// to check if we need to reset caching of number of records further below
$web->setLastPage();

$pageTitle = $sideNav->getActive('linkTxt');
$pageTitle = $pageTitle[count($pageTitle) - 1];

// paged nav
$pg = isset($_GET['pg']) ? $_GET['pg'] : $pg = 1;
$numRecPerPage = isset($_GET['numRecPp']) ? $_GET['numRecPp'] : 14;

// filtering
$sqlFilter = '';
if (isset($_GET['theme'])) {
	$themeId = preg_replace("/\D*/", '', $_GET['theme']);	// sanitize for security reasons
	$sqlFilter = " WHERE themeId = $themeId AND";
}
else if (isset($_GET['country'])) {
	$countryId = preg_replace("/\D*/", '', $_GET['country']);	// sanitize for security reasons
	$sqlFilter = " WHERE countryId = $countryId AND";
}
else {
	//$theme = null;
	$sqlFilter.= " WHERE";
}
$qual = isset($_GET['qual']) ? preg_replace("/\D*/", '', $_GET['qual']) : 3;	// sanitize for security reasons
$sqlFilter.= " ratingId > ".($qual - 1);

// sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 1;
switch($sort) {
	case 1:
		$sqlSort = ' ORDER BY dateAdded DESC';
		break;
	case 2:
		$sqlSort = ' ORDER BY date DESC';
		break;
	case 3:
		$sqlSort = ' ORDER BY lastChange DESC';
		break;
	case 4:
		$sqlSort = ' ORDER BY imgTitle ASC';
		break;
}

$numRec = !isset($numRec) ? 0 : $numRec;

// generate filter and sorting menus
$arrDel = array('pg');
$arrVal = array(7, 14, 21, 28, 56);
$mRecPp = new Menu(null, 'ulMenu1 mRecPp');
$mRecPp->add(array('a', 'b', $numRecPerPage));
foreach ($arrVal as $key => $val) {
	$url = $web->page.$web->getQuery(array('numRecPp' => $val), $arrDel);
	$mRecPp->add(array($key, 'a', $val, $url));
	if ($numRecPerPage == $val) {
		$mRecPp->arrItem[$key]->setActive();
	}
}

$arrVal = array(
	4 => $i180n[$web->getLang()]['by title'],
	1 => $i180n[$web->getLang()]['date added'],
	2 => $i180n[$web->getLang()]['date created'],
	3 => $i180n[$web->getLang()]['last changed']
);
$mSort = new Menu(null, 'ulMenu1 mSort');
$mSort->add(array('a', 'b', $arrVal[$sort]));
foreach ($arrVal as $key => $val) {
	$url = $web->page.$web->getQuery(array('sort' => $key), $arrDel);
	$mSort->add(array($key, 'a', $val, $url));
	if ($sort == $key) {
		$mSort->arrItem[$key]->setActive();
	}
}
$star = '<img class="imgRatingStar" src="'.$web->getWebRoot().'layout/images/ratingstar.gif" alt="star icon for rating image">';
$arrVal = array(3 => $star.$star.$star, 2 => $star.$star, 1 => $star);
$mRating = new Menu('mRating', 'ulMenu1 mRating');
$mRating->add(array('a', 'b', $arrVal[$qual], null, null, 'rating '.$qual));
foreach ($arrVal as $key => $val) {
	$url = $web->page.$web->getQuery(array('qual' => $key), $arrDel);
	$mRating->add(array($key, 'a', $val, $url, null, 'rating '.$key));
	if ($qual == $key) {
		$mRating->arrItem[$key]->setActive();
	}
}

/**
 * @param PhotoDb $db
 * @param array $arrData
 * @param Language $web
 * @param array $i18n
 * @return bool
 */
function renderData($db, $arrData, $web, $i18n) {
	if (count($arrData) == 0) {
		echo '<p>'.$i18n[$web->getLang()]['not found'].'</p>';
		return false;
	}
	$c = 0;
	$num = count($arrData) - 1;
	foreach ($arrData as $row) {
		// image dimensions
		$imgFile = $db->webroot.$db->getPath('img').'thumbs/'.$row['imgFolder'].'/'.$row['imgName'];
		$imgSize = getimagesize(__DIR__.'/../..'.$web->getWebRoot().$db->getPath('img').$row['imgFolder'].'/'.$row['imgName']);
		$imgTitle = $row['imgTitle'];
		$link = str_replace('thumbs/', '', $imgFile).'?w='.$imgSize[0].'&h='.$imgSize[1];
		$detailLink = 'photo-detail.php'.$web->getQuery(array('imgId' => $row['imgId']));

		if ($imgSize[0] > $imgSize[1]) {
			$css = 'slideHorizontal';
			$cssImg = 'slideImgHorizontal';

		}
		else if ($imgSize[0] < $imgSize[1]) {
			$css = 'slideVertical';
			$cssImg = 'slideImgVertical';
		}
		else {
			$css = 'slideQuadratic';
			$cssImg = 'slideImgQuadratic';
		}
		echo '<li class="slide">';
		echo '<div class="slideCanvas'.($c == $num ? ' slideLast' : '').' '.$css.'">';
		echo '<a href="'.$link.'" title="'.$imgTitle.'"><img class="'.$cssImg.'" src="'.$imgFile.'" alt="Foto" title="Thumbnail of '.$imgTitle.'"></a>';
		echo '</div>';
		echo '<div class="slideText"><a title="Foto \''.$imgTitle.'\' anzeigen" href="'.$link.'">Zoom</a> | ';
		echo '<a title="Details zu Foto \''.$imgTitle.'\' anzeigen" href="'.$detailLink.'">Details</a></div>';
		echo '</li>';	// end slide
		$c++;
	}
	return true;
}