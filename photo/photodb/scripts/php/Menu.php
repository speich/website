<?php
namespace PhotoDb;

use WebsiteTemplate\Language;
use PDO;
use PDOStatement;

require_once 'PhotoDb.php';

/**
 * Class Menu
 */
class Menu extends PhotoDb {

	/**
	 * Load and create list of menu and sub menu items.
	 * @param string $lang
	 * @return PDOStatement
	 */
	public function get($lang) {
		// 't' and 's' are used to make the menu id's unique
		$sql = "SELECT 's' || s.Id menuId, s.Name".$lang." menuLabel,
				't' || t.Id submenuId, t.Name".$lang." submenuLabel, t.Id queryValue, 'theme' queryField
				FROM SubjectAreas s
				INNER JOIN Themes t ON t.SubjectAreaId = s.Id
				INNER JOIN Images_Themes It ON t.Id = It.ThemeId
				-- add countries
				UNION
				SELECT * FROM (
					SELECT 's' || s.Id menuId, s.Name".$lang." menuLabel,
					'c' || c.Id submenuId, c.Name".$lang." submenuLabel, c.Id queryValue, 'country' queryField
					FROM SubjectAreas s
					CROSS JOIN (
						SELECT DISTINCT Id, Name".$lang." FROM Countries c
						INNER JOIN Locations_Countries lc ON c.Id = lc.CountryId
					) c
					WHERE s.Id = 7	-- id of country in SubjectArea table
				) t
				ORDER BY menuLabel ASC, submenuLabel ASC";
		return $this->db->query($sql);
	}

	/**
	 * Creates the side menu photography for the main menu.
	 * @param \WebsiteTemplate\Menu $sideNav
	 * @param array $items menu items to add
	 * @param Language $web
	 */
	public function create($sideNav, $items, $web) {
		if ($web->page == $web->createLangPage('photo-mapsearch.php')) {
			// do not render side navigation on map page
			return;
		}

		foreach($items as $item) {
			$sideNav->add($item);
		}

		$lang = ucfirst($web->getLang());
		$themes = $this->get($lang);
		// side menu links should start fresh with only ?theme={id} as query string (or non photo related query variables)
		// treat country as a theme, do not allow country and theme vars in the query string at the same time
		// note: country and theme will be added back in loop
		$arrQueryDel = array('pg', 'numRec', 'country', 'qual', 'lang', 'imgId', 'theme', 'lat1', 'lng1', 'lat2', 'lng2');
		$path = $web->getWebRoot().'photo/photodb/photo.php';
		$lastMenuId = null;
		while ($row = $themes->fetch(PDO::FETCH_ASSOC)) {
			$arrQueryAdd = array($row['queryField'] => $row['queryValue']);
			// main subject areas (parent menu)
			if ($row['menuId'] != $lastMenuId) {
				$link = $path.$web->getQuery($arrQueryAdd, $arrQueryDel);
				$sideNav->add(array($row['menuId'], 1, htmlspecialchars($row['menuLabel']), $link));
			}
			// sub menu
			$arrQueryAdd = array($row['queryField'] => $row['queryValue']);
			$link = $path.$web->getQuery($arrQueryAdd, $arrQueryDel);
			$sideNav->add(array($row['submenuId'], $row['menuId'], htmlspecialchars($row['submenuLabel']), $link));

			$lastMenuId = $row['menuId'];
		}

		$sideNav->setActive($path.$web->getQuery($arrQueryDel, 2));

		if (isset($_GET['theme']) || isset($_GET['country'])) {
			// unset item ('Alle Fotos'), otherwise it would always be active
			$sideNav->arrItem[2]->setActive(false); //
		}
		else if (strpos($web->getLastPage(), $web->createLangPage('photo-mapsearch.php')) !== false) {
			// for photo-details.php
			$sideNav->arrItem[1]->setActive(false);
			$sideNav->arrItem[3]->setActive();
		}

		if ($web->page == $web->createLangPage('ausruestung.php')) {
			$sideNav->arrItem[1]->setActive(false);
			$sideNav->arrItem[2]->setActive(false);
		}
	}
}