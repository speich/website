<?php
/**************************
 * main navigation on top *
 *************************/
$arrNav = array();
$arrNav['de'] = array(
	array(1, 'N','Fotografie', $web->getWebRoot().'photo/photodb/photo.php'),
	array(2, 'N','Artikel', $web->getWebRoot().'articles/index.php'),
	array(3, 'N','Projekte', $web->getWebRoot().'projects/programming/progs.php'),
	array(5, 'N','Person', $web->getWebRoot().'about/cv.php'.$web->getQuery()),
	array(6, 'N','Kontakt', $web->getWebRoot().'contact/contact.php'.$web->getQuery())
);

$arrNav['en'] = array(
	array(1, 'N','Photography', $web->getWebRoot().'photo/photodb/photo.php'),
	array(2, 'N','Articles', $web->getWebRoot().'articles/index.php'),
	array(3, 'N','Projects', $web->getWebRoot().'projects/programming/progs.php'),
	array(5, 'N','Person', $web->getWebRoot().'about/cv.php'.$web->getQuery()),
	array(6, 'N','Contact', $web->getWebRoot().'contact/contact.php'.$web->getQuery())
);

$mainNav = new Menu('menuMain', null, $arrNav[$web->getLang()]);
$mainNav->autoActive = true;

// set main menu active according to first (top) directory
foreach ($mainNav->arrItem as $item) {
	// to find top (first) folder we need to remove webroot folder (if in web subdir) first
	$arrUrl = parse_url($_SERVER["REQUEST_URI"]);
	$arrPath = pathinfo($arrUrl['path']);
	$dir = $web->getDir();
	if ($web->getWebRoot() != '/') {
		$dir = str_replace($web->getWebRoot(), '', $dir);
	}
	$dir = ltrim($dir, '/');
	$arrDir = explode('/', $dir);
	$dir = $arrDir[0];
	$firstDir = substr($dir, 0, stripos($dir, '/'));	// unused?
	// special cases
	if (strpos($item->linkUrl, $web->getWebRoot().$dir.'/') !== false) {
		$item->setActive();
	}
}
$mainNav->setActive();


/******************************
 * sub navigation to the left *
 *****************************/
$path = $web->getWebRoot().'photo/photodb';
$arrPhotoNav['de'] = array(
	array(1, 'f', 'Bildarchiv', $path.'/photo.php'),
	array(2, 1, 'Alle Fotos', $path.'/photo.php'.$web->getQuery(array('theme', 'country', 'pgNav'), 2)),
	array(3, 'f', 'Bildsuche', $path.'/photo-search.php'.$web->getQuery(array('qt' => 'full'))),
	array(4, 3, 'Volltext-Suche', $path.'/photo-search.php'.$web->getQuery(array('qt' => 'full'))),
	array(5, 3, 'Geografische Suche', $path.'/photo-mapsearch.php'.$web->getQuery(array('qt' => 'geo', 'showMap' => 1), array('q', 'pgNav'))),
	array(9, 'f', 'Ausrüstung', '/photo/ausruestung.php'.$web->getQuery(array('theme', 'gNav'), 2))
);
$arrPhotoNav['en'] = array(
	array(1, 'f', 'Picture Library', $path.'/photo.php'),
	array(2, 1, 'All Photos', $path.'/photo.php'.$web->getQuery(array('theme', 'country', 'pgNav'), 2)),
	array(3, 'f', 'Photo Search', $path.'/photo-search.php'.$web->getQuery(array('qt' => 'full'))),
	array(4, 3, 'Fulltext Search', $path.'/photo-search.php'.$web->getQuery(array('qt' => 'full'))),
	array(5, 3, 'Search on Map', $path.'/photo-mapsearch.php'.$web->getQuery(array('qt' => 'geo', 'showMap' => 1), array('q', 'pgNav'))),
	array(9, 'f', 'Equipment', '/photo/ausruestung-en.php'.$web->getQuery(array('theme', 'gNav'), 2))
);

$path = $web->getWebRoot().'articles';
$arrArticleNav['de'] = array(
	array(1, 'f', 'Alle Artikel', $path.'/')
);
$arrArticleNav['en'] = array(
	array(1, 'f', 'All Articles', $path.'/')
);

$path = $web->getWebRoot().'projects';
$arrProjectNav['de'] = array(
	array(1, 'f', 'Programmierung', $path.'/programming/progs.php'.$web->getQuery()),
	array(2, 'f', 'Musik', $path.'/music/music.php'.$web->getQuery())
);
$arrProjectNav['en'] = array(
	array(1, 'f', 'Programming', $path.'/programming/progs.php'.$web->getQuery()),
	array(2, 'f', 'Music', $path.'/music/music.php'.$web->getQuery())
);

$path = $web->getWebRoot().'about';
$arrPersonNav['de'] = array(
	array(1, 'f', 'Lebenslauf', $path.'/cv.php'.$web->getQuery()),
	array(5, 'f', 'Diplomarbeit', $path.'/diplomarbeit.php'.$web->getQuery())
);
$arrPersonNav['en'] = array(
	array(1, 'f', 'Curriculum Vitae', $path.'/cv.php'.$web->getQuery()),
	array(5, 'f', 'Diploma Thesis', $path.'/diplomarbeit.php'.$web->getQuery())
);


$sideNav = new Menu();
$sideNav->cssClass = 'sideMenu';
$sideNav->setAutoActiveMatching(3);
$photoNav = new PhotoDbNav();
$photoNav->connect();

switch($mainNav->getActive()) {
	case 1:
		$photoNav->createMenu($sideNav, $arrPhotoNav[$web->getLang()]);
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
	$path = $web->getWebRoot().'articles';
	foreach ($arrArticleNav as $item) {
		$sideNav->add($item);
	}
		$count = 2;
		if (function_exists('get_categories')) {
			$categories = get_categories('orderby=name');
			foreach ($categories as $cat) {
				$sideNav->add(array($count, 'f', $cat->cat_name, $path.'/?cat='.$cat->cat_ID));
				$count++;
			}
			if (isset($_GET['p'])) {
				$postId = $_GET['p'];
				$categories = get_the_category($postId);
				foreach ($categories as $cat) {
					$sideNav->setActive($path.'/?cat='.$cat->cat_ID);
				}
			}
			else if (isset($_GET['cat'])) {
				$sideNav->setActive($path.'/?cat='.$_GET['cat']);
			}
			else {
				$sideNav->setActive($path.'/');
			}
		}
}