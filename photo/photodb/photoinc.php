<?php
require_once '../../library/inc_script.php';
require_once 'PhotoDb.php';

$db = new PhotoDb();
$db->connect();
$lastPage = $db->getLastPage();	// to check if we need to reset caching of number of records further below
$db->setLastPage();

// searching
if (isset($_GET['q']) && strlen($_GET['q']) > 2) {
	$query = mb_ereg_replace('/\W/', '', $_GET['q']);
	// reset pagedNav to first page and rating to 1 if new query
	$lastQuery = parse_url($lastPage);
	$lastQuery = array_key_exists('query', $lastQuery) ? $lastQuery['query'] : '';
	parse_str($lastQuery, $arrQuery);
	if (array_key_exists('q', $arrQuery) && $arrQuery['q'] != $query) {
		$_GET['pgNav'] = 1;
		$_GET['qual'] = 1;
	}
	$query = preg_replace('/(^\s+)|(\s+$)/', '', $query);
	$arrQuery = preg_split('/\s+/', $query);
	array_splice($arrQuery, 5);
}

// paged nav
$pgNav = isset($_GET['pgNav']) ? $_GET['pgNav'] : $pgNav = 1;
$numRecPerPage = isset($_GET['numRecPp']) ? $_GET['numRecPp'] : 14;

// filtering
$sqlFilter = '';
if (isset($_GET['theme'])) {
	$lang = ucfirst($web->getLang());
	$themeId = preg_replace("/\D*/", '', $_GET['theme']);	// sanitize for security reasons
	$sql = "SELECT Id, Name".$lang." Name FROM Themes WHERE Id = :themeId";
	$db = new PhotoDb();
	$db->connect();
	$stmt = $db->db->prepare($sql);
	$stmt->bindValue(':themeId', $themeId);
	$stmt->execute();
	$theme = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$theme = $theme[0]['Name'];
	$sqlFilter = " WHERE themeId = $themeId AND";
}
else {
	$theme = null;
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
	$url = $db->getPage().$db->getQuery(array('numRecPp' => $val), $arrDel);
	$mRecPp->add(array($key, 'a', $val, $url));
	if ($numRecPerPage == $val) {
		$mRecPp->arrItem[$key]->setActive();
	}
}
$arrVal = array(4 => 'Titel', 1 => 'hinzugefügt', 2 => 'erstellt', 3 => 'zuletzt geändert');
$mSort = new Menu(null, 'ulMenu1 mSort');
$mSort->add(array('a', 'b', $arrVal[$sort]));
foreach ($arrVal as $key => $val) {
	$url = $db->getPage().$db->getQuery(array('sort' => $key), $arrDel);
	$mSort->add(array($key, 'a', $val, $url));
	if ($sort == $key) {
		$mSort->arrItem[$key]->setActive();
	}
}
$star = '<img class="imgRatingStar" src="'.$db->getWebRoot().'layout/images/ratingstar.gif" alt="star icon for rating image"/>';
$arrVal = array(3 => $star.$star.$star, 2 => $star.$star, 1 => $star);
$mQuality = new Menu(null, 'ulMenu1 mQuality');
$mQuality->add(array('a', 'b', $arrVal[$qual], null, null, 'rating '.$qual));
foreach ($arrVal as $key => $val) {
	$url = $db->getPage().$db->getQuery(array('qual' => $key), $arrDel);
	$mQuality->add(array($key, 'a', $val, $url, null, 'rating '.$key));
	if ($qual == $key) {
		$mQuality->arrItem[$key]->setActive();
	}
}

?>