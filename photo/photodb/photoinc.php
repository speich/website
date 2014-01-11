<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\Menu;
use WebsiteTemplate\Website;


require_once __DIR__.'/../../library/inc_script.php';
require_once 'PhotoDb.php';

$db = new PhotoDb($web->getWebRoot());
$db->connect();
$lastPage = $web->getLastPage();	// to check if we need to reset caching of number of records further below
$web->setLastPage();

$pageTitle = $sideNav->getActive('linkTxt');
$pageTitle = $pageTitle[count($pageTitle) - 1];

// paged nav
$pgNav = isset($_GET['pgNav']) ? $_GET['pgNav'] : $pgNav = 1;
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
$arrDel = array('pgNav');
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
$arrVal = array(4 => 'Titel', 1 => 'hinzugefügt', 2 => 'erstellt', 3 => 'zuletzt geändert');
$mSort = new Menu(null, 'ulMenu1 mSort');
$mSort->add(array('a', 'b', $arrVal[$sort]));
foreach ($arrVal as $key => $val) {
	$url = $web->page.$web->getQuery(array('sort' => $key), $arrDel);
	$mSort->add(array($key, 'a', $val, $url));
	if ($sort == $key) {
		$mSort->arrItem[$key]->setActive();
	}
}
$star = '<img class="imgRatingStar" src="'.$web->getWebRoot().'layout/images/ratingstar.gif" alt="star icon for rating image"/>';
$arrVal = array(3 => $star.$star.$star, 2 => $star.$star, 1 => $star);
$mQuality = new Menu(null, 'ulMenu1 mQuality');
$mQuality->add(array('a', 'b', $arrVal[$qual], null, null, 'rating '.$qual));
foreach ($arrVal as $key => $val) {
	$url = $web->page.$web->getQuery(array('qual' => $key), $arrDel);
	$mQuality->add(array($key, 'a', $val, $url, null, 'rating '.$key));
	if ($qual == $key) {
		$mQuality->arrItem[$key]->setActive();
	}
}

/**
 * @param PhotoDb $db
 * @param array $arrData
 * @param Website $web
 * @return bool
 */
function renderData($db, $arrData, $web) {
	if (count($arrData) == 0) {
		echo '<p>Mit diesen Einstellungen wurden keine Datensätze gefunden.</p>';
		return false;
	}
	$c = 0;
	$num = count($arrData) - 1;
	foreach ($arrData as $row) {
		// image dimensions
		$imgFile = $db->webroot.$db->getPath('img').'thumbs/'.$row['imgFolder'].'/'.$row['imgName'];
		$imgSize = getimagesize($web->getDocRoot().$db->getPath('img').$row['imgFolder'].'/'.$row['imgName']);
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
		echo '<div class="slideCanvas'.($c == $num ? ' slideLast' : '').' '.$css.'" style="background-image: url('.$imgFile.')">';
		echo '<a href="'.$link.'" title="'.$imgTitle.'"><img class="'.$cssImg.'" src="'.$imgFile.'" alt="Foto" title="Thumbnail of '.$imgTitle.'"></a>';
		echo '</div>';
		echo '<div class="slideText"><a title="Foto \''.$imgTitle.'\' anzeigen" href="'.$link.'">Zoom</a> | ';
		echo '<a title="Details zu Foto \''.$imgTitle.'\' anzeigen" href="'.$detailLink.'">Details</a></div>';
		echo '</li>';	// end slide
		$c++;
	}
	return true;
}