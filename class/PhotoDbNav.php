<?php
namespace PhotoDb;

use WebsiteTemplate\Menu;
use WebsiteTemplate\Language;
use PDO;
use PDOStatement;

require_once 'PhotoDb.php';

/**
 * Class PhotoDbNav
 */
class PhotoDbNav extends PhotoDb {

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
				UNION -- add country
				SELECT * FROM (
					SELECT 's' || s.Id menuId, s.Name".$lang." menuLabel,
					'c' || c.Id submenuId, c.Name".$lang." submenuLabel, c.Id queryValue, 'country' queryField
					FROM SubjectAreas s
					CROSS JOIN (
						SELECT DISTINCT Id, Name".$lang." FROM Countries c
						INNER JOIN Locations_Countries lc ON c.Id = lc.CountryId
					) c
					WHERE s.Id = 7	-- id of country in SubjectArea
				) t
				ORDER BY menuLabel ASC, submenuLabel ASC";
		return $this->db->query($sql);
	}

	/**
	 * Creates the side menu photography for the main menu.
	 * @param Menu $sideNav
	 * @param array $items menu items to add
	 * @param Language $web
	 */
	public function createMenu($sideNav, $items, $web) {
		$sideNav->autoActive = false;
		foreach($items as $item) {
			$sideNav->add($item);
		}

		$lang = ucfirst($web->getLang());
		$themes = $this->get($lang);
		$arrQueryDel = array('country', 'theme', 'pg', 'numRec', 'lang');
		$path = $web->getWebRoot().'photo/photodb/photo.php';
		$lastMenuId = null;
		while ($row = $themes->fetch(PDO::FETCH_ASSOC)) {
			$arrQueryAdd = array($row['queryField'] => $row['queryValue']);
			// subject areas
			if ($row['menuId'] != $lastMenuId) {
				$link = $path.$web->getQuery($arrQueryAdd, $arrQueryDel);
				$sideNav->add(array($row['menuId'], 1, htmlspecialchars($row['menuLabel']), $link));
			}
			$arrQueryAdd = array($row['queryField'] => $row['queryValue']);
			$link = $path.$web->getQuery($arrQueryAdd, $arrQueryDel);
			$sideNav->add(array($row['submenuId'], $row['menuId'], htmlspecialchars($row['submenuLabel']), $link));

			$lastMenuId = $row['menuId'];
		}

		$sideNav->setActive();
		if (isset($_GET['theme']) ||isset($_GET['country'])) {
			// unset item ('Alle Fotos'), otherwise it would always be active
			$sideNav->arrItem[2]->setActive(null); //
		}
	}
}