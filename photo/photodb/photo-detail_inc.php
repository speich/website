<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\Language;
use WebsiteTemplate\Website;

require_once __DIR__.'/../../library/inc_script.php';
require_once 'photoinc.php';

if (isset($_GET['imgId'])) {
	$imgId = $_GET['imgId'];
}
else {
	header('Location: http://www.speich.net/photo/photodb/photo.php');
}


$ucLang = ucfirst($lang->get());

$db = new PhotoDb($web->getWebRoot());
$db->connect();
$sql = "SELECT I.id imgId, I.imgFolder imgFolder, I.imgName imgName, I.imgDate imgDate, I.imgTechInfo imgTechInfo,
	I.dateAdded dateAdded, I.lastChange lastChange, I.datePublished datePublished, I.imgDesc imgDesc, I.imgTitle imgTitle, I.imgDateOriginal imgDateOriginal,
	I.imgLat imgLat, I.imgLng imgLng, I.showLoc showLoc,
	CASE WHEN F.code NOT NULL THEN F.name ||' ('||F.code||')' ELSE F.name END film,
	R.Name rating,
	E.make make, E.model model, E.fileSize fileSize, E.exposureTime exposureTime, E.fNumber fNumber, E.iso iso,
	E.focalLength focalLength, E.exposureProgram exposureProgram,
	E.meteringMode meteringMode, E.flash flash, E.focusDistance focusDistance, E.imageWidth imageWidth,
	E.imageHeight imageHeight, E.createDate createDate, E.dateTimeOriginal dateTimeOriginal, E.bitsPerSample bitsPerSample,
	E.gpsLatitude gpsLatitude, E.gpsLongitude gpsLongitude, E.gpsAltitude gpsAltitude, E.gpsAltitudeRef gpsAltitudeRef,
	E.lensSpec lensSpec, E.lens lens, E.fileType fileType, E.vibrationReduction vibrationReduction,
	GROUP_CONCAT(DISTINCT T.name".$ucLang.") themes,
	GROUP_CONCAT(DISTINCT K.name) categories,
	N.nameDe wissNameDe, N.nameEn wissNameEn, N.nameLa wissNameLa,
	S.name sex,
	GROUP_CONCAT(DISTINCT L.name) locations,
	GROUP_CONCAT(DISTINCT C.name".$ucLang.") countries
	FROM Images I
	LEFT JOIN FilmTypes F ON I.filmTypeId = F.id
	LEFT JOIN Rating R ON I.ratingId = R.id
	LEFT JOIN Exif E ON I.id = E.imgId
	LEFT JOIN Images_Themes IT ON I.id = IT.imgId
	LEFT JOIN Themes T ON IT.themeId = T.id
	LEFT JOIN Images_Keywords IK ON I.id = IK.imgId
	LEFT JOIN Keywords K ON IK.keywordId = K.id
	LEFT JOIN Images_ScientificNames ISc ON I.id = ISc.imgId
	LEFT JOIN ScientificNames N ON ISc.scientificNameId = N.id
	LEFT JOIN Sexes S ON ISc.sexId = S.id
	LEFT JOIN Images_Locations IL ON I.id = IL.imgId
	LEFT JOIN Locations L ON IL.locationId = L.id
	LEFT JOIN Locations_Countries LC ON L.id = LC.locationId
	LEFT JOIN Countries C ON LC.countryId = C.id
	WHERE I.id = :imgId";
$stmt = $db->db->prepare($sql);
$stmt->bindValue(':imgId', $imgId);
$stmt->execute();
$photo = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * Print HTML to display photo detail.
 * @param $data
 * @param PhotoDb $db
 * @param Website $web
 * @param Language $lang
 * @param array $i18n internationalization
 */
function renderPhoto($data, $db, $web, $lang, $i18n) {

	$backPage = $lang->createPage('photo.php').$web->getQuery(['imgId'], 2);
	if (strpos($backPage, $lang->createPage('photo-mapsearch.php')) !== false) {
		$backPage = $lang->createPage('photo-mapsearch.php').'?'.$_SERVER['QUERY_STRING'];	// when coming from map via js lastPage was not set with latest query vars, use current
	}
	$imgFile = $db->webroot.$db->getPath('img').$data['imgFolder'].'/'.$data['imgName'];

	$star = '';
	$str = '<img id="ratingStar" src="../../layout/images/ratingstar.gif" alt="star icon for rating image"/>';
	for ($i = 0; $i < strlen($data['rating']); $i++) {
		$star.= $str;
	}
	if ($data['dateTimeOriginal']) {
		$datum = date("d.m.Y H:i:s", $data['dateTimeOriginal']);
	}
	else {
		$datum = $data['imgDate'];
	}
	echo '<h1>'.ucfirst($i18n['photo']).': '.$data['imgTitle']."</h1>\n";
	echo '<p>'.$data['imgDesc'].'</p>';
	echo '<div class="col colLeft">';

	echo '<p><span class="photoTxtLabel">'.$i18n['keywords'].':</span> '.($data['categories'] != '' ? $data['categories'].'<br/>' : '').'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['scientific name'].':</span> <i>'.$data['wissNameLa'].'</i><br/>';
	echo '<span class="photoTxtLabel">'.$i18n['name'].':</span> '.$data['wissNameDe'].' - '.$data['wissNameEn'].'</p>';
	echo '<p class="photoTxtSeparator"><span class="photoTxtLabel">'.$i18n['rating'].':</span> '.$star.'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['date'].':</span> '.$datum.'</p>';
	echo '<p class="photoTxtSeparator"><span class="photoTxtLabel">'.$i18n['original size'].':</span> '.$data['imageWidth'].' x '.$data['imageHeight'].' '.$i18n['pixel'];
	echo '<p><span class="photoTxtLabel">'.$i18n['order number'].':</span> '.$data['imgId'].'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['file name'].':</span> '.$data['imgName'].'<p>';
	echo '</div>';

	echo '<div class="col colRight">';
	echo '<div id="map">';
	if ($data['showLoc'] === '0') {
		echo '<div id="mapNote">'.$i18n['Coordinates are not shown'].'</div>';
	}
	echo '</div>';
	echo '<p><span class="photoTxtLabel">'.$i18n['place'].':</span> '.$data['locations'].'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['country'].':</span> '.$data['countries'].'</p>';
	echo '</div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';

	echo '<div id="contPhoto">';
	echo '<p><a title="'.$data['imgTitle'].' '.$i18n['photo'].'" href="'.$imgFile.'">';
	echo '<img src="'.$imgFile.'" id="'.$i18n['photo'].'" alt="'.$data['imgTitle'].'"/></a><br>';
	echo '© '.$i18n['photo'].' Simon Speich, www.speich.net';
	echo '</p>';
	echo '</div>';

	echo '<div id="exifInfo"><div class="col">';
	echo '<p><strong>'.$i18n['technical information'].' (Exif)</strong></p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].', '.$data['make']."</p>\n";
	echo '<p><span class="photoTxtLabel">'.$i18n['original size'].':</span> '.$data['imageWidth'].' x '.$data['imageHeight'].'px @ ';
	if ($data['bitsPerSample'] != '') {
		echo $data['bitsPerSample'];
	}
	else {
		echo '8bit';
	}
	echo "</p>\n";
	echo '<p><span class="photoTxtLabel">'.$i18n['file format'].':</span> '.$data['fileType']." (".$data['fileSize'].")</p>\n";
	if ($data['model'] == 'Nikon SUPER COOLSCAN 5000 ED') {
		echo '<p><span class="photoTxtLabel">'.$i18n['date of scan'].': </span>'.date("d.m.Y H:i:s", $data['createDate'])."</p>\n";
		echo '<p class="PhotoTxtSeparator"><span class="photoTxtLabel">'.$i18n['type of film'].':</span> '.$data['film'].'</p>';
	}
	else {
		echo '<p><span class="photoTxtLabel">'.$i18n['exposure'].':</span> '.$data['exposureTime']." @ f/".$data['fNumber'];
		echo ' mit '.$data['meteringMode']."</p>\n";
		echo '<p><span class="photoTxtLabel">'.$i18n['program'].':</span> '.$data['exposureProgram']."</p>\n";
		echo '<p><span class="photoTxtLabel">ISO:</span> '.$data['iso']."</p>\n";
		echo '<p><span class="photoTxtLabel">'.$i18n['lens'].':</span> '.($data['lensSpec'] != '' ? $data['lensSpec'] : ($data['lens'] != '' ? $data['lens'] : '')).', VR: '.$data['vibrationReduction'].'</p>';
		echo '<p><span class="photoTxtLabel">'.$i18n['focal length'].':</span> '.$data['focalLength'].', '.$i18n['distance'].' : '.$data['focusDistance'].'</p>';
		echo '<p><span class="photoTxtLabel">'.$i18n['flash'].':</span> '.$data['flash']."</p>\n";
	}
	echo '</div>';

	echo '<div class="col">';
	echo '<p><span class="photoTxtLabel">'.$i18n['position'].' (GPS):</span> ';
	if ($data['showLoc'] === '1') {
		echo $data['gpsLatitude'].' / '.$data['gpsLongitude'];
	}
	echo '<p><span class="photoTxtLabel">'.$i18n['hight'].' (GPS):</span> '.$data['gpsAltitude'].' m '.($data['gpsAltitudeRef'] == '1' ? 'b.s.l.' : 'a.s.l.');
	echo '<p><strong>'.$i18n['database information'].'</strong></p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['added'].':</span> '.(!empty($data['dateAdded']) ? date("d.m.Y H:i:s", $data['dateAdded']) : '').'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['changed'].':</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['lastChange']) : '').'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="photoTxtLabel">'.$i18n['published'].':</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['datePublished']) : '').'</p>';
	echo '</div></div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
}