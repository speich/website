<?php

use speich\WebsiteSpeich;
use speich\LanguageMenu;
use WebsiteTemplate\Language;
use WebsiteTemplate\Menu;
use WebsiteTemplate\QueryString;


/**************************
 * main navigation on top *
 *************************/
$path = $web->getWebRoot();
$arrNav = [];
$arrNav['de'] = [
    [1, 'N', 'Fotografie', $path.'photo/photodb/photo.php'],
    [2, 'N', 'Artikel', $path.'articles/de/'],
    [3, 'N', 'Projekte', $path.'projects/programming/progs.php'],
    [5, 'N', 'Person', $path.'about/cv.php'],
    [6, 'N', 'Kontakt', $path.'contact/contact.php']
];

$arrNav['en'] = [
    [1, 'N', 'Photography', $path.'photo/photodb/photo-en.php'],
    [2, 'N', 'Articles', $path.'articles/en/'],
    [3, 'N', 'Projects', $path.'projects/programming/progs.php'],
    [5, 'N', 'Person', $path.'about/cv-en.php'],
    [6, 'N', 'Contact', $path.'contact/contact-en.php']
];

$mainNav = new Menu($arrNav[$language->get()]);
$mainNav->cssClass = 'menu';


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
 * Create different sub navigation to the left
 * and set them active
 *****************************/
$query = new QueryString();
$path = $web->getWebRoot().'photo/photodb/';
$arrQueryDel = ['lat1', 'lng1', 'lat1', 'lat2', 'lng2'];
$arrPhotoNav['de'] = [
    [1, 'f', 'Bildarchiv', $path.'photo.php'],
    [2, 1, 'Alle Fotos', $path.'photo.php'],
    [3, 'f', 'Geografische Suche', $path.'photo-mapsearch.php'.$query->withString(null, $arrQueryDel)],
    [4, 'f', 'Ausrüstung', $web->getWebRoot().'photo/ausruestung.php'],
    [5, 'f', 'Auszeichnungen', $web->getWebRoot().'photo/auszeichnungen.php']
];
$arrPhotoNav['en'] = [
    [1, 'f', 'Photo Database', $path.'photo-en.php'],
    [2, 1, 'All Photos', $path.'photo-en.php'],
    [3, 'f', 'Search on Map', $path.'photo-mapsearch-en.php'.$query->withString(null, $arrQueryDel)],
    [4, 'f', 'Equipment', $web->getWebRoot().'photo/ausruestung-en.php'],
    [5, 'f', 'Awards', $web->getWebRoot().'photo/auszeichnungen-en.php']
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
    [1, 'f', 'Programmierung', $path.'programming/progs.php'],
    [2, 'f', 'Musik', $path.'music/music.php']
];
$arrProjectNav['en'] = [
    [1, 'f', 'Programming', $path.'programming/progs.php'],
    [2, 'f', 'Music', $path.'music/music.php']
];

$path = $web->getWebRoot().'about/';
$arrPersonNav['de'] = [
    [1, 'f', 'Lebenslauf', $path.'cv.php'],
    [3, 'f', 'Auszeichungen', $web->getWebRoot().'photo/auszeichnungen.php'],
    [2, 'f', 'Diplomarbeit', $path.'diplomarbeit.php']
];
$arrPersonNav['en'] = [
    [1, 'f', 'Curriculum Vitae', $path.'cv-en.php'],
    [3, 'f', 'Awards', $web->getWebRoot().'photo/auszeichnungen-en.php'],
    [2, 'f', 'Diploma Thesis', $path.'diplomarbeit-en.php']
];


$sideNav = new Menu();
$sideNav->cssClass = 'sideMenu';
$sideNav->setAutoActiveMatching(3);

$langNav = new LanguageMenu($language, $web);
$langNav->useLabel = true;
$langNav->setWhitelist($web->getWhitelistQueryString());

/* render different side navigation depending on active main navigation */
switch ($mainNav->getActive()) {
    case 1:
        // do not render side navigation on map page
        if ($language->createPage($web->page) !== $language->createPage('photo-mapsearch.php')) {
            createSideMenuPhoto($web, $sideNav, $arrPhotoNav[$language->get()], $language);
        }
        break;
    case 3:
        foreach ($arrProjectNav[$language->get()] as $item) {
            $sideNav->add($item);
        }
        break;
    case 5:
        foreach ($arrPersonNav[$language->get()] as $item) {
            $sideNav->add($item);
        }
        break;
}

/**
 * Creates the side menu photography for the main menu.
 * @param WebsiteSpeich $web
 * @param Menu $sideNav
 * @param array $menuItems
 * @param Language $lang
 */
function createSideMenuPhoto($web, $sideNav, $menuItems, $lang)
{
    // TODO: move to a (new) photo db class
    foreach ($menuItems as $item) {
        $sideNav->add($item);
    }

    $db = new PhotoDb\PhotoDb($web->getWebRoot());
    $db->connect();
    $ucLang = ucfirst($lang->get());
    // 't' and 's' are used to make the menu id's unique
    $sql = "SELECT 's' || s.Id menuId, s.Name".$ucLang." menuLabel,
			't' || t.Id submenuId, t.Name".$ucLang." submenuLabel, t.Id queryValue, 'theme' queryField
			FROM SubjectAreas s
			INNER JOIN Themes t ON t.SubjectAreaId = s.Id
			INNER JOIN Images_Themes It ON t.Id = It.ThemeId
			WHERE It.ThemeId != 10
			-- add countries
			UNION
			SELECT * FROM (
				SELECT 's' || s.Id menuId, s.Name".$ucLang." menuLabel,
				'c' || c.Id submenuId, c.Name".$ucLang." submenuLabel, c.Id queryValue, 'country' queryField
				FROM SubjectAreas s
				CROSS JOIN (
					SELECT DISTINCT Id, Name".$ucLang.' FROM Countries c
					INNER JOIN Locations_Countries lc ON c.Id = lc.CountryId
				) c
				WHERE s.Id = 7	-- id of country in SubjectArea table
			) t
			ORDER BY menuLabel ASC, submenuLabel ASC';
    $themes = $db->db->query($sql);

    // side menu links for submenu database should start fresh with only ?theme={id} as query string (or non photo related query variables)
    // treat country as a theme, do not allow country and theme vars in the query string at the same time
    // note: country and theme will be added back in loop
    $query = new QueryString($web->getWhitelistQueryString());
    $path = $web->getWebRoot().$lang->createPage('photo/photodb/photo.php');
    $lastMenuId = null;
    $row = $themes->fetch(PDO::FETCH_ASSOC);
    while ($row) {
        $arrQueryDel = ['pg', 'numRec', 'lang', 'imgId', 'lat1', 'lng1', 'lat2', 'lng2', 'theme', 'country'];
        // remove query variable of current db record from query string variables to delete
        if (($key = array_search($row['queryField'], $arrQueryDel, true)) !== false) {
            unset($arrQueryDel[$key]);
        }
        $arrQueryAdd = [$row['queryField'] => $row['queryValue']];
        $link = $path.$query->withString($arrQueryAdd, $arrQueryDel);
        // main subject areas (parent menu)
        if ($row['menuId'] !== $lastMenuId) {
            $sideNav->add([$row['menuId'], 1, htmlspecialchars($row['menuLabel']), $link]);
        }
        // sub menu
        $sideNav->add([$row['submenuId'], $row['menuId'], htmlspecialchars($row['submenuLabel']), $link]);
        $lastMenuId = $row['menuId'];
        $row = $themes->fetch(PDO::FETCH_ASSOC);
    }
    /*
        if ($web->page === $lang->createPage('photo-detail.php')) {
            $sideNav->setActive($lang->createPage('photo-detail.php').$query->withString(null, $arrQueryDel));
        }
        // unset item ('Alle Fotos'), otherwise it would always be active
        if (isset($_GET['theme']) || isset($_GET['country'])) {
            $sideNav->arrItem[1]->setActive(false);
        }
        if ($web->page === $lang->createPage('ausruestung.php')) {
            $sideNav->arrItem[1]->setActive(false);
            $sideNav->arrItem[2]->setActive(false);
        }
    */
}
