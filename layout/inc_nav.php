<?php

use speich\LanguageMenu;
use speich\WebsiteSpeich;
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
    [5, 'N', 'Person', $path.'about/simon-speich.php'],
    [6, 'N', 'Kontakt', $path.'contact/contact.php']
];

$arrNav['en'] = [
    [1, 'N', 'Photography', $path.'photo/photodb/photo-en.php'],
    [2, 'N', 'Articles', $path.'articles/en/'],
    [3, 'N', 'Projects', $path.'projects/programming/progs.php'],
    [5, 'N', 'Person', $path.'about/simon-speich-en.php'],
    [6, 'N', 'Contact', $path.'contact/contact-en.php']
];

$mainNav = new Menu($arrNav[$language->get()]);
$mainNav->cssClass = 'menu';

// set main menu items active according to first (top) directory for related pages such as /photo/ausruestung.php
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


$langNav = new LanguageMenu($language, $web);
$langNav->useLabel = true;
$langNav->setWhitelist($web->getWhitelistQueryString());


/******************************
 * Create different sub navigation to the left
 * and set them active
 *****************************/
$query = new QueryString();
$path = $web->getWebRoot().'photo/photodb/';
$arrPhotoNav['de'] = [
    [1, 'f', 'Datenbank', $path.'photo.php'],
    [2, 1, 'Alle Fotos', $path.'photo.php'],
    [4, 'f', 'Ausrüstung', $web->getWebRoot().'about/ausruestung.php'],
    [5, 'f', 'Auszeichnungen', $web->getWebRoot().'about/auszeichnungen.php']
];
$arrPhotoNav['en'] = [
    [1, 'f', 'Database', $path.'photo-en.php'],
    [2, 1, 'All Photos', $path.'photo-en.php'],
    [4, 'f', 'Equipment', $web->getWebRoot().'about/ausruestung-en.php'],
    [5, 'f', 'Awards', $web->getWebRoot().'about/auszeichnungen-en.php']
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
    [1, 'f', 'Über mich', $path.'simon-speich.php'],
    [3, 'f', 'Auszeichungen', $path.'auszeichnungen.php'],
    [4, 'f', 'Ausrüstung', $path.'ausruestung.php'],
    [2, 'f', 'Diplomarbeit', $path.'diplomarbeit.php']
];
$arrPersonNav['en'] = [
    [1, 'f', 'About me', $path.'simon-speich-en.php'],
    [3, 'f', 'Awards', $path.'auszeichnungen-en.php'],
    [4, 'f', 'Equipment', $path.'ausruestung-en.php'],
    [2, 'f', 'Diploma Thesis', $path.'diplomarbeit-en.php']
];


/* render different side navigation depending on active main navigation */
$activeNavId = $mainNav->getActive();
$items = [];
$sideNav = new Menu();
$sideNav->setAutoActiveMatching(Menu::MATCH_FULL);
$sideNav->allChildrenOpen = true;
$sideNav->allChildrenRendered = true;
$sideNav->cssClass = 'sideMenu';
switch ($activeNavId) {
    case 1:
        $items = createSideMenuPhoto($web, $arrPhotoNav, $language);
        $sideNav->addAll($items);
        if ($web->page === $language->createPage('photo-detail.php')) {
            $url = $web->getDir().$language->createPage('photo.php').$query->withString(null, ['imgId', 'lang']);
            $sideNav->setActive($url);
        }
        if (isset($_GET['theme']) || isset($_GET['country'])) {
            // unset item ('Alle Fotos'), otherwise it would always be active
            $sideNav->arrItem['2']->setActive(false);
        }
        break;
    case 3:
        $items = $arrProjectNav[$language->get()];
        $sideNav->addAll($items);
        break;
    case 5:
        $items = $arrPersonNav[$language->get()];
        $sideNav->addAll($items);
        break;
}


/**
 * Creates the side menu photography for the main menu.
 * @param WebsiteSpeich $web
 * @param array $menuItems
 * @param Language $lang
 * @return array
 */
function  createSideMenuPhoto(WebsiteSpeich $web, array $menuItems, Language $lang): array
{
    // TODO: move to a (new) photo db class
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

    // The query string of the links of the side menu should only contain the theme={id} (or other non-photo database related query variables).
    // Note: we treat country as a theme, so do not allow country and theme vars in the query string at the same time. Also,
    //       country and theme will be added back in the loop
    $query = new QueryString($web->getWhitelistQueryString());
    $path = $web->getWebRoot().$lang->createPage('photo/photodb/photo.php');
    $arrQueryDelMaster = ['pg', 'q', 'numRec', 'lang', 'imgId', 'theme', 'country'];
    $lastMenuId = null;
    $items = $menuItems[$lang->get()];
    $row = $themes->fetch(PDO::FETCH_ASSOC);
    while ($row) {
        // remove query variable of current db record from query string variables to delete
        $arrQueryDel = $arrQueryDelMaster;
        $key = array_search($row['queryField'], $arrQueryDel, true);
        if ($key !== false) {
            unset($arrQueryDel[$key]);
        }
        $arrQueryAdd = [$row['queryField'] => $row['queryValue']];
        $link = $path.$query->withString($arrQueryAdd, $arrQueryDel);

        // main subject areas (parent menu)
        if ($row['menuId'] !== $lastMenuId) {
            $items[] = [$row['menuId'], 1, htmlspecialchars($row['menuLabel']), $link];
        }
        // sub menu
        $items[] = [$row['submenuId'], $row['menuId'], htmlspecialchars($row['submenuLabel']), $link];
        $lastMenuId = $row['menuId'];
        $row = $themes->fetch(PDO::FETCH_ASSOC);
    }

    return $items;
}
