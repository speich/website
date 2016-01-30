<?php
use WebsiteTemplate\Menu;
use WebsiteTemplate\Website;

require_once 'Website.php';
require_once __DIR__.'/../photo/photodb/scripts/php/Menu.php';

/**************************
 * main navigation on top *
 *************************/
$path = $web->getWebRoot();
$arrNav = [];
$arrNav['de'] = [
	[1, 'N','Fotografie', $path.'photo/photodb/photo.php'],
	[2, 'N','Artikel', $path.'articles/?lang=de'],
	[3, 'N','Projekte', $path.'projects/programming/progs.php'],
	[5, 'N','Person', $path.'about/cv.php'.$web->getQuery()],
	[6, 'N','Kontakt', $path.'contact/contact.php'.$web->getQuery()]
];

$arrNav['en'] = [
	[1, 'N','Photography', $path.'photo/photodb/photo.php'],
	[2, 'N','Articles', $path.'articles/?lang=en'],
	[3, 'N','Projects', $path.'projects/programming/progs.php'],
	[5, 'N','Person', $path.'about/cv.php'.$web->getQuery()],
	[6, 'N','Contact', $path.'contact/contact-en.php'.$web->getQuery()]
];

$mainNav = new Menu('menuMain', null, $arrNav[$web->getLang()]);

// set main menu active according to first (top) directory
foreach ($mainNav->arrItem as $item) {
	// to find top (first) folder we need to remove webroot folder (if in web subdir) first
	$arrUrl = parse_url($_SERVER['REQUEST_URI']);
	$arrPath = pathinfo($arrUrl['path']);
	$dir = $web->getDir();
	$dir = ltrim($dir, '/');
	$arrDir = explode('/', $dir);
	$dir = $arrDir[0];
	if ($dir !== '' && strpos($item->linkUrl, $web->getWebRoot().$dir) !== false) {
		$item->setActive();
	}
}
$mainNav->setActive();


/******************************
 * sub navigation to the left *
 *****************************/
$path = $web->getWebRoot().'photo/photodb/';
$arrQueryDel = ['lat1', 'lng1', 'lat1', 'lat2', 'lng2'];
$arrPhotoNav['de'] = [
	[1, 'f', 'Bildarchiv', $path.'photo.php'.$web->getQuery(['lang'])],
	[2, 1, 'Alle Fotos', $path.'photo.php'],
	[3, 'f', 'Geografische Suche', $path.'photo-mapsearch.php'.$web->getQuery($arrQueryDel, 2)],
	[4, 'f', 'Ausrüstung', $web->getWebRoot().'photo/ausruestung.php'.$web->getQuery($arrQueryDel, 2)]
];
$arrPhotoNav['en'] = [
	[1, 'f', 'Photo Database', $path.'photo.php'.$web->getQuery()],
	[2, 1, 'All Photos', $path.'photo.php'],
	[3, 'f', 'Search on Map', $path.'photo-mapsearch.php'.$web->getQuery($arrQueryDel, 2)],
	[4, 'f', 'Equipment', $web->getWebRoot().'photo/ausruestung-en.php'.$web->getQuery($arrQueryDel, 2)]
];

$path = $web->getWebRoot().'articles/';
$arrArticleNav['de'] = [
	[1, 'f', 'Alle Artikel', $path]
];
$arrArticleNav['en'] = [
	[1, 'f', 'All Articles', $path]
];

$path = $web->getWebRoot().'projects/';
$arrProjectNav['de'] = [
	[1, 'f', 'Programmierung', $path.'programming/progs.php'.$web->getQuery()],
	[2, 'f', 'Musik', $path.'music/music.php'.$web->getQuery()]
];
$arrProjectNav['en'] = [
	[1, 'f', 'Programming', $path.'programming/progs.php'.$web->getQuery()],
	[2, 'f', 'Music', $path.'music/music.php'.$web->getQuery()]
];

$path = $web->getWebRoot().'about/';
$arrPersonNav['de'] = [
	[1, 'f', 'Lebenslauf', $path.'cv.php'.$web->getQuery()],
	[5, 'f', 'Diplomarbeit', $path.'diplomarbeit.php'.$web->getQuery()]
];
$arrPersonNav['en'] = [
	[1, 'f', 'Curriculum Vitae', $path.'cv.php'.$web->getQuery()],
	[5, 'f', 'Diploma Thesis', $path.'diplomarbeit.php'.$web->getQuery()]
];


$sideNav = new Menu();
$sideNav->cssClass = 'sideMenu';
$sideNav->setAutoActiveMatching(3);
$photoNav = new \PhotoDb\Menu($web->getWebRoot());
$photoNav->connect();


switch($mainNav->getActive()) {
	case 1:
		$photoNav->create($sideNav, $arrPhotoNav[$web->getLang()], $web);
		break;
	case 2:
		createMenuArticles($web, $sideNav, $arrArticleNav[$web->getLang()]);
		break;
	case 3:
		foreach ($arrProjectNav[$web->getLang()] as $item) {
			$sideNav->add($item);
		}
		break;
	case 5:
		foreach ($arrPersonNav[$web->getLang()] as $item) {
			$sideNav->add($item);
		}
		break;
}


/**
 * Create sub navigation for main menu articles.
 * @param Website $web
 * @param Menu $sideNav
 * @param array $arrArticleNav
 */
function createMenuArticles($web, $sideNav, $arrArticleNav) {
	$sideNav->autoActive = false;
	$path = $web->getWebRoot().'articles/';
	foreach ($arrArticleNav as $item) {
		$sideNav->add($item);
	}
	$count = 2;
	if (function_exists('get_categories')) {
		$categories = get_categories('orderby=name');
		foreach ($categories as $cat) {
			$sideNav->add([$count, 'f', $cat->cat_name, $path.'?cat='.$cat->cat_ID]);
			$count++;
		}
		if (isset($_GET['p'])) {
			$postId = $_GET['p'];
			$categories = get_the_category($postId);
			foreach ($categories as $cat) {
				$sideNav->setActive($path.'?cat='.$cat->cat_ID);
			}
		}
		else if (isset($_GET['cat'])) {
			$sideNav->setActive($path.'?cat='.$_GET['cat']);
		}
		else {
			$sideNav->setActive($path);
		}
	}
}