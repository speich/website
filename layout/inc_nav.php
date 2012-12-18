<?php
$mainNav = new Menu();
$mainNav->cssId = 'menuMain';
$mainNav->autoActive = true;
if ($web->getLang() == 'de') {
	$mainNav->add(array(1, 'N','Fotografie', $web->getWebRoot().'photo/photodb/photo.php'));
	$mainNav->add(array(2, 'N','Artikel', $web->getWebRoot().'articles/index.php'));
	$mainNav->add(array(3, 'N','Projekte', $web->getWebRoot().'projects/programming/progs.php'));
//$mainNav->add(array(4, 'N','Sonstiges', $web->getWebRoot().'other'.$web->getQuery()));
	$mainNav->add(array(5, 'N','Person', $web->getWebRoot().'about/cv.php'.$web->getQuery()));
	$mainNav->add(array(6, 'N','Kontakt', $web->getWebRoot().'contact/contact.php'.$web->getQuery()));
}
else {
	$mainNav->add(array(1, 'N','Photography', $web->getWebRoot().'photo/photodb/photo.php'));
	$mainNav->add(array(2, 'N','Articles', $web->getWebRoot().'articles/index.php'));
	$mainNav->add(array(3, 'N','Projects', $web->getWebRoot().'projects/programming/progs.php'));
	$mainNav->add(array(5, 'N','Person', $web->getWebRoot().'about/cv.php'.$web->getQuery()));
	$mainNav->add(array(6, 'N','Contact', $web->getWebRoot().'contact/contact.php'.$web->getQuery()));
}

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

$sideNav = new Menu();
$sideNav->cssClass = 'sideMenu';
$sideNav->setAutoActiveMatching(3);
switch($mainNav->getActive()) {
	case 1:
		createMenuPhoto($sideNav);
		break;
	case 2:
		$sideNav->autoActive = false;
		$sideNav->add(array(1, 'f', 'alle Artikel', $web->getWebRoot().'articles/'));
		$count = 2;
		if (function_exists('get_categories')) {
			$categories = get_categories('orderby=name');
			foreach ($categories as $cat) {
				$sideNav->add(array($count, 'f', $cat->cat_name, $web->getWebRoot().'articles/?cat='.$cat->cat_ID));
				$count++;
			}
			if (isset($_GET['p'])) {
				$postId = $_GET['p'];
				$categories = get_the_category($postId);
				foreach ($categories as $cat) {
					$sideNav->setActive($web->getWebRoot().'articles/?cat='.$cat->cat_ID);
				}
			}
			else if (isset($_GET['cat'])) {
				$sideNav->setActive($web->getWebRoot().'articles/?cat='.$_GET['cat']);
			}
			else {
				$sideNav->setActive($web->getWebRoot().'articles/');
			}
		}
		break;
	case 3:
		$sideNav->add(array(1, 'f', 'Programmierung', $web->getWebRoot().'projects/programming/progs.php'.$web->getQuery()));
		$sideNav->add(array(2, 'f', 'Musik', $web->getWebRoot().'projects/music/music.php'.$web->getQuery()));
		break;
	case 5:
		$sideNav->add(array(1, 'f', 'Lebenslauf', $web->getWebRoot().'about/cv.php'.$web->getQuery()));
		$sideNav->add(array(5, 'f', 'Diplomarbeit', $web->getWebRoot().'about/diplomarbeit.php'.$web->getQuery()));		break;
}

/**
 * Creates the sidemenu for the main menu 'Fotografie'
 * @param Menu $sideNav
 */
function createMenuPhoto($sideNav) {
	$db = new PhotoDb();
	$db->connect();
	$sideNav->autoActive = false;
	// note: theme id' are used as menu ids
	$sideNav->add(array(1, 'f', 'Bildarchiv', $db->getWebRoot().'photo/photodb/photo.php'));
	$sideNav->add(array(2, 1,'Alle Fotos', $db->getWebRoot().'photo/photodb/photo.php'.$db->getQuery(array('theme', 'pgNav'), 2)));
	$sideNav->add(array(3, 'f', 'Bildsuche', $db->getWebRoot().'photo/photodb/photo-search.php'.$db->getQuery(array('qt' => 'full'))));
		$sideNav->Add(array(4, 3, 'Volltext-Suche', $db->getWebRoot().'photo/photodb/photo-search.php'.$db->getQuery(array('qt' => 'full'))));
		$sideNav->Add(array(5, 3, 'Geografische Suche', $db->getWebRoot().'photo/photodb/photo-mapsearch.php'.$db->getQuery(array('qt' => 'geo', 'showMap' => 1), array('q', 'pgNav'))));
//		$sideNav->Add(array(6, 3, 'Wissenschaftliche Namen', $db->getWebRoot().'photo/photodb/photo-suche.php'.$db->getQuery(array('qt' => 'sci'))));
//	$sideNav->add(array(7, 'f', 'Bildrechte', $db->getWebRoot().'photo/bildrechte.php'.$db->getQuery(array('theme', 'PgNav'), 2)));
//	$sideNav->add(array(8, 'f', 'Gallerie', $db->getWebRoot().'photo/photodb/gallerie.php'.$db->getQuery(array('theme', 'PgNav'), 2)));
	$sideNav->add(array(9, 'f', 'Ausrüstung', $db->getWebRoot().'photo/ausruestung.php'.$db->getQuery(array('theme', 'gNav'), 2)));

	$sql = "SELECT s.Id SubjectAreaId, s.Name SubjectArea, t.Id ThemeId, t.Name Theme FROM SubjectAreas s
	 INNER JOIN Themes t ON t.SubjectAreaId = s.Id
	 INNER JOIN Images_Themes It ON t.Id = It.ThemeId
	 ORDER BY s.Name ASC, t.Name ASC";
	$rst = $db->db->query($sql);
	$arrDel = array('pgNav', 'numRec');
	$lastSubjectArea = null;
	while ($row = $rst->fetch(PDO::FETCH_ASSOC)) {
		if ($row['SubjectArea'] != $lastSubjectArea) {
			$arrAdd = array('theme' => $row['ThemeId']);
			$link = $db->getWebRoot().'photo/photodb/photo.php'.$db->getQuery($arrAdd, $arrDel);
			$sideNav->add(array('s'.$row['SubjectAreaId'], 1, htmlspecialchars($row['SubjectArea']), $link));
		}
		$arrAdd = array('theme' => $row['ThemeId']);
		$link = $db->getWebRoot().'photo/photodb/photo.php'.$db->getQuery($arrAdd, $arrDel);
		$sideNav->add(array('t'.$row['ThemeId'], 's'.$row['SubjectAreaId'], htmlspecialchars($row['Theme']), $link));
		$lastSubjectArea = $row['SubjectArea'];
	}
	$sideNav->setActive();
	if (isset($_GET['theme'])) {
		// unset item ('Alle Fotos'), otherwise it would always be active
		$sideNav->arrItem[2]->setActive(null); // 
	}
}


?>