<?php
require_once 'PhotoDb.php';

class PhotoDbNav extends PhotoDb {

	public function getThemes($lang) {
		$sql = "SELECT s.Id SubjectAreaId, s.Name".$lang." SubjectArea, t.Id ThemeId, t.Name".$lang." Theme FROM SubjectAreas s
		 INNER JOIN Themes t ON t.SubjectAreaId = s.Id
		 INNER JOIN Images_Themes It ON t.Id = It.ThemeId
		 ORDER BY s.Name".$lang." ASC, t.Name".$lang." ASC";
		$rst = $this->db->query($sql);
		return $rst;
	}

	public function getCountries($lang) {
		$sql = "SELECT DISTINCT Id countryId, Name".$lang." country FROM Countries c
			--INNER JOIN Location_Countries lc ON c.Id = lc.CountryId	// only countries which are used
			ORDER BY country ASC";
		$rst = $this->db->query($sql);
		return $rst;
	}
}