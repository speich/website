<?php

use PhotoDb\Photo\Photo;
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
$sql = "SELECT I.Id imgId, I.ImgFolder imgFolder, I.ImgName imgName, I.ImgDate imgDate, I.ImgTechInfo imgTechInfo,
	I.DateAdded dateAdded, I.LastChange lastChange, I.DatePublished datePublished, I.ImgDesc imgDesc, I.ImgTitle imgTitle, I.ImgDateOriginal imgDateOriginal,
	I.ImgLat imgLat, I.ImgLng imgLng, I.ShowLoc showLoc,
	CASE WHEN F.Code NOT NULL THEN F.Name ||' ('||F.Code||')' ELSE F.Name END film,
	R.Name rating,
	E.Make make, E.Model model, E.FileSize fileSize, E.ExposureTime exposureTime, E.FNumber fNumber, E.Iso iso,
	E.FocalLength focalLength, E.ExposureProgram exposureProgram,
	E.MeteringMode meteringMode, E.Flash flash, E.FocusDistance focusDistance, E.ImageWidth imageWidth,
	E.ImageHeight imageHeight, E.DateTimeOriginal dateTimeOriginal, --E.BitsPerSample bitsPerSample,E.CreateDate createDate
	E.GpsLatitude gpsLatitude, E.GpsLongitude gpsLongitude, E.GpsAltitude gpsAltitude, E.GpsAltitudeRef gpsAltitudeRef,
	E.LensSpec lensSpec, E.Lens lens, E.FileType fileType, E.VibrationReduction vibrationReduction,
	X.CropTop, X.CropLeft, X.CropRight, X.CropBottom, X.CropAngle,
	GROUP_CONCAT(DISTINCT T.Name".$ucLang.") themes,
	GROUP_CONCAT(DISTINCT K.Name) categories,
	N.NameDe wissNameDe, N.NameEn wissNameEn, N.NameLa wissNameLa,
	S.Name sex,
	GROUP_CONCAT(DISTINCT L.Name) locations,
	GROUP_CONCAT(DISTINCT C.Name".$ucLang.") countries,
	C2.Name".$ucLang." country
	FROM Images I
	LEFT JOIN FilmTypes F ON I.FilmTypeId = F.Id
	LEFT JOIN Rating R ON I.RatingId = R.Id
	LEFT JOIN Exif E ON I.Id = E.ImgId
	LEFT JOIN Xmp X ON I.Id = X.ImgId
	LEFT JOIN Images_Themes IT ON I.Id = IT.ImgId
	LEFT JOIN Themes T ON IT.ThemeId = T.Id
	LEFT JOIN Images_Keywords IK ON I.Id = IK.ImgId
	LEFT JOIN Keywords K ON IK.KeywordId = K.Id
	LEFT JOIN Images_ScientificNames ISc ON I.Id = ISc.ImgId
	LEFT JOIN ScientificNames N ON ISc.ScientificNameId = N.Id
	LEFT JOIN Sexes S ON ISc.SexId = S.Id
	LEFT JOIN Images_Locations IL ON I.Id = IL.ImgId
	LEFT JOIN Locations L ON IL.LocationId = L.Id
	LEFT JOIN Locations_Countries LC ON L.Id = LC.LocationId
	LEFT JOIN Countries C ON LC.CountryId = C.Id
	LEFT JOIN Countries C2 ON I.CountryId = C2.Id 
	WHERE I.Id = :imgId";
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
    $photo = new Photo($db->webroot);

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
    echo '<p><span class="photoTxtLabel">'.$i18n['order number'].':</span> '.$data['imgId'].'</p>';
    $dim = $photo->getImageSize($data);
    echo '<p><span class="photoTxtLabel">'.$i18n['image size'].($dim['isCropped'] ? ' ('.$i18n['cropped'].') ' : '').':</span> '.$dim['w'].' x '.$dim['h'].'</p>';
    echo '<p><span class="photoTxtLabel">'.$i18n['file name'].':</span> '.$data['imgName'].'<p>';
    echo '</div>';

    echo '<div class="col colRight">';
	echo '<div id="map">';
	if ($data['showLoc'] === '0') {
		echo '<div id="mapNote">'.$i18n['Coordinates are not shown'].'</div>';
	}
	echo '</div>';
	echo '<p><span class="photoTxtLabel">'.$i18n['place'].':</span> '.$data['locations'].'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['country'].':</span> '.(is_null($data['countries']) ? $data['country'] : $data['countries']).'</p>';
	echo '</div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';

	echo '<div id="contPhoto">';
	echo '<p><a title="'.$data['imgTitle'].' '.$i18n['photo'].'" href="'.$imgFile.'">';
	echo '<img src="'.$imgFile.'" id="'.$i18n['photo'].'" alt="'.$data['imgTitle'].'"/></a><br>';
	echo '© '.$i18n['photo'].' Simon Speich, www.speich.net';
	echo '</p>';
	echo '</div>';

	echo '<div id="exifInfo"><div class="col">';
	echo '<h3>'.$i18n['technical information'].' (Exif)</h3>';
	echo '<p><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].', '.$data['make']."</p>\n";
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
	echo '<h3>'.$i18n['database information'].'</h3>';
	echo '<p><span class="photoTxtLabel">'.$i18n['added'].':</span> '.(!empty($data['dateAdded']) ? date("d.m.Y H:i:s", $data['dateAdded']) : '').'</p>';
	echo '<p><span class="photoTxtLabel">'.$i18n['changed'].':</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['lastChange']) : '').'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="photoTxtLabel">'.$i18n['published'].':</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['datePublished']) : '').'</p>';
	echo '</div></div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
}

/**
 * Return the cropped image size from Adobe Lightroom Xmp.
 * @param $data
 */
function getSizeCropped($data) {
    $ax = $data['CropLeft'];
    $ay = $data['CropTop'];
    $bx = $data['CropRight'];
    $by = $data['CropBottom'];
    $phi = $data['CropAngle']; // in degrees


}