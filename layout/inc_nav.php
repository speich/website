<?php
use WebsiteTemplate\Language;
use WebsiteTemplate\Menu;
use WebsiteTemplate\Website;

require_once 'Menu.php';

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

$mainNav = new Menu('menuMain', null, $arrNav[$lang->get()]);

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
 * different sub navigation to the left *
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


$sideNav = new Menu(null, 'sideMenu');
$sideNav->setAutoActiveMatching(3);

/* render different side navigation depending on active main navigation*/
switch($mainNav->getActive()) {
	case 1:
		// do not render side navigation on map page
		if ($lang->createPage($web->page) !== $lang->createPage('photo-mapsearch.php')) {
			createSideMenuPhoto($web, $sideNav, $arrPhotoNav[$lang->get()], $lang);
		}
		break;
	case 2:
		createSideMenuArticles($web, $sideNav, $arrArticleNav[$lang->get()]);
		break;
	case 3:
		foreach ($arrProjectNav[$lang->get()] as $item) {
			$sideNav->add($item);
		}
		break;
	case 5:
		foreach ($arrPersonNav[$lang->get()] as $item) {
			$sideNav->add($item);
		}
		break;
}


/**
 * Create sub navigation for main menu articles.
 * @param Website $web
 * @param Menu $sideNav
 * @param array $menuItems
 */
function createSideMenuArticles($web, $sideNav, $menuItems) {
	$sideNav->autoActive = false;
	$path = $web->getWebRoot().'articles/';
	foreach ($menuItems as $item) {
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

/**
 * Creates the side menu photography for the main menu.
 * @param Website $web
 * @param Menu $sideNav
 * @param array $menuItems
 * @param Language $lang
 */
function createSideMenuPhoto($web, $sideNav, $menuItems, $lang) {
	require_once __DIR__.'/../photo/photodb/scripts/php/PhotoDb.php';
	$db = new PhotoDb\PhotoDb($web->getWebRoot());
	$db->connect();
	$ucLang = ucfirst($lang->get());

	// 't' and 's' are used to make the menu id's unique
	$sql = "SELECT 's' || s.Id menuId, s.Name".$ucLang." menuLabel,
			't' || t.Id submenuId, t.Name".$ucLang." submenuLabel, t.Id queryValue, 'theme' queryField
			FROM SubjectAreas s
			INNER JOIN Themes t ON t.SubjectAreaId = s.Id
			INNER JOIN Images_Themes It ON t.Id = It.ThemeId
			-- add countries
			UNION
			SELECT * FROM (
				SELECT 's' || s.Id menuId, s.Name".$ucLang." menuLabel,
				'c' || c.Id submenuId, c.Name".$ucLang." submenuLabel, c.Id queryValue, 'country' queryField
				FROM SubjectAreas s
				CROSS JOIN (
					SELECT DISTINCT Id, Name".$ucLang." FROM Countries c
					INNER JOIN Locations_Countries lc ON c.Id = lc.CountryId
				) c
				WHERE s.Id = 7	-- id of country in SubjectArea table
			) t
			ORDER BY menuLabel ASC, submenuLabel ASC";
	$themes = $db->db->query($sql);

	foreach($menuItems as $item) {
		$sideNav->add($item);
	}

	// side menu links should start fresh with only ?theme={id} as query string (or non photo related query variables)
	// treat country as a theme, do not allow country and theme vars in the query string at the same time
	// note: country and theme will be added back in loop
	$arrQueryDel = ['pg', 'numRec', 'country', 'qual', 'lang', 'imgId', 'theme', 'lat1', 'lng1', 'lat2', 'lng2'];
	$path = $web->getWebRoot().'photo/photodb/photo.php';
	$lastMenuId = null;
	while ($row = $themes->fetch(PDO::FETCH_ASSOC)) {
		$arrQueryAdd = [$row['queryField'] => $row['queryValue']];
		// main subject areas (parent menu)
		if ($row['menuId'] != $lastMenuId) {
			$link = $path.$web->getQuery($arrQueryAdd, $arrQueryDel);
			$sideNav->add([$row['menuId'], 1, htmlspecialchars($row['menuLabel']), $link]);
		}
		// sub menu
		$arrQueryAdd = [$row['queryField'] => $row['queryValue']];
		$link = $path.$web->getQuery($arrQueryAdd, $arrQueryDel);
		$sideNav->add([$row['submenuId'], $row['menuId'], htmlspecialchars($row['submenuLabel']), $link]);

		$lastMenuId = $row['menuId'];
	}

	$sideNav->setActive($path.$web->getQuery($arrQueryDel, 2));

	if (isset($_GET['theme']) || isset($_GET['country'])) {
		// unset item ('Alle Fotos'), otherwise it would always be active
		$sideNav->arrItem[2]->setActive(false); //
	}
	else if (strpos($web->getLastPage(), $lang->createPage('photo-mapsearch.php')) !== false) {
		// for photo-details.php
		$sideNav->arrItem[1]->setActive(false);
		$sideNav->arrItem[3]->setActive();
	}

	if ($web->page == $lang->createPage('ausruestung.php')) {
		$sideNav->arrItem[1]->setActive(false);
		$sideNav->arrItem[2]->setActive(false);
	}
}