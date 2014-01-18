<?php
use WebsiteTemplate\Menu;
use WebsiteTemplate\Website;
use PhotoDb\PhotoDbNav;

require_once 'Website.php';
require_once 'PhotoDbNav.php';

/**************************
 * main navigation on top *
 *************************/
$path = $web->getWebRoot();
$arrNav = array();
$arrNav['de'] = array(
	array(1, 'N','Fotografie', $path.'photo/photodb/photo.php'),
	array(2, 'N','Artikel', $path.'articles/?lang=de'),
	array(3, 'N','Projekte', $path.'projects/programming/progs.php'),
	array(5, 'N','Person', $path.'about/cv.php'.$web->getQuery()),
	array(6, 'N','Kontakt', $path.'contact/contact.php'.$web->getQuery())
);

$arrNav['en'] = array(
	array(1, 'N','Photography', $path.'photo/photodb/photo.php'),
	array(2, 'N','Articles', $path.'articles/?lang=en'),
	array(3, 'N','Projects', $path.'projects/programming/progs.php'),
	array(5, 'N','Person', $path.'about/cv.php'.$web->getQuery()),
	array(6, 'N','Contact', $path.'contact/contact-en.php'.$web->getQuery())
);

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
$arrPhotoNav['de'] = array(
	array(1, 'f', 'Bildarchiv', $path.'photo.php'),
	array(2, 1, 'Alle Fotos', $path.'photo.php'.$web->getQuery(array('theme', 'country', 'pg'), 2)),
	array(3, 'f', 'Geografische Suche', $path.'photo-mapsearch.php'.$web->getQuery(array('qt' => 'geo', 'showMap' => 1), array('q', 'pg'))),
	array(4, 'f', 'Ausrüstung', $web->getWebRoot().'photo/ausruestung.php'.$web->getQuery(array('theme', 'gNav'), 2))
);
$arrPhotoNav['en'] = array(
	array(1, 'f', 'Picture Library', $path.'photo.php'),
	array(2, 1, 'All Photos', $path.'photo.php'.$web->getQuery(array('theme', 'country', 'pg'), 2)),
	array(3, 'f', 'Search on Map', $path.'photo-mapsearch.php'.$web->getQuery(array('qt' => 'geo', 'showMap' => 1), array('q', 'pg'))),
	array(4, 'f', 'Equipment', $web->getWebRoot().'photo/ausruestung-en.php'.$web->getQuery(array('theme', 'gNav'), 2))
);

$path = $web->getWebRoot().'articles/';
$arrArticleNav['de'] = array(
	array(1, 'f', 'Alle Artikel', $path)
);
$arrArticleNav['en'] = array(
	array(1, 'f', 'All Articles', $path)
);

$path = $web->getWebRoot().'projects/';
$arrProjectNav['de'] = array(
	array(1, 'f', 'Programmierung', $path.'programming/progs.php'.$web->getQuery()),
	array(2, 'f', 'Musik', $path.'music/music.php'.$web->getQuery())
);
$arrProjectNav['en'] = array(
	array(1, 'f', 'Programming', $path.'programming/progs.php'.$web->getQuery()),
	array(2, 'f', 'Music', $path.'music/music.php'.$web->getQuery())
);

$path = $web->getWebRoot().'about/';
$arrPersonNav['de'] = array(
	array(1, 'f', 'Lebenslauf', $path.'cv.php'.$web->getQuery()),
	array(5, 'f', 'Diplomarbeit', $path.'diplomarbeit.php'.$web->getQuery())
);
$arrPersonNav['en'] = array(
	array(1, 'f', 'Curriculum Vitae', $path.'cv.php'.$web->getQuery()),
	array(5, 'f', 'Diploma Thesis', $path.'diplomarbeit.php'.$web->getQuery())
);


$sideNav = new Menu();
$sideNav->cssClass = 'sideMenu';
$sideNav->setAutoActiveMatching(3);
$photoNav = new PhotoDbNav($web->getWebRoot());
$photoNav->connect();


switch($mainNav->getActive()) {
	case 1:
		$photoNav->createMenu($sideNav, $arrPhotoNav[$web->getLang()], $web);
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
			$sideNav->add(array($count, 'f', $cat->cat_name, $path.'?cat='.$cat->cat_ID));
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