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
// TODO: move date scanned out of Images?
$db = new PhotoDb($web->getWebRoot());
$db->connect();
$sql = "SELECT I.Id imgId, I.ImgFolder imgFolder, I.ImgName imgName, I.ImgTechInfo imgTechInfo,
	I.DateAdded dateAdded, I.LastChange lastChange, I.DatePublished datePublished, I.ImgDesc imgDesc, I.ImgTitle imgTitle, I.ImgDateOriginal imgDateOriginal,
	I.ImgDateManual ImgDateManual,
	I.ImgLat imgLat, I.ImgLng imgLng, I.ShowLoc showLoc,
	CASE WHEN F.Code NOT NULL THEN F.Name ||' ('||F.Code||')' ELSE F.Name END film,
	R.Name rating,
	E.Make make, E.Model model, E.FileSize fileSize, E.ExposureTime exposureTime, E.FNumber fNumber, E.Iso iso,
	E.FocalLength focalLength, E.ExposureProgram exposureProgram,
	E.MeteringMode meteringMode, E.Flash flash, E.FocusDistance focusDistance, E.ImageWidth imageWidth,
	E.ImageHeight imageHeight, E.DateTimeOriginal dateTimeOriginal,
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

$jsConfig = [
    'lat' => empty($photo[0]['imgLat']) ? null : $photo[0]['imgLat'],
    'lng' => empty($photo[0]['imgLng']) ? null : $photo[0]['imgLng']
];
$jsConfig = json_encode($jsConfig, JSON_NUMERIC_CHECK);
$jsConfig = htmlspecialchars($jsConfig, ENT_COMPAT, $web->charset);


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
    $dim = $photo->getImageSize($data);
	$star = '';

	$str = '<img class="imgRatingStar" src="../../layout/images/ratingstar.gif" alt="star icon for rating image"/>';
	for ($i = 0; $i < strlen($data['rating']); $i++) {
		$star.= $str;
	}
	if ($data['dateTimeOriginal']) {
		$datum = date("d.m.Y H:i:s", $data['dateTimeOriginal']);
	}
	else {
		$datum = $data['ImgDateManual'];
	}
	echo '<h1>'.ucfirst($i18n['photo']).': '.$data['imgTitle']."</h1>\n";
	echo '<p>'.$data['imgDesc'].'</p>';
	echo '<div class="col colLeft">
	    <ul>
	        <li><span class="photoTxtLabel">'.$i18n['keywords'].':</span> '.($data['categories'] != '' ? $data['categories'].'<br/>' : '').'</li>
	        <li><span class="photoTxtLabel">'.$i18n['name'].':</span> '.$data['wissNameDe'].' - '.$data['wissNameEn'].'</li>
            <li><span class="photoTxtLabel">'.$i18n['scientific name'].':</span> <i>'.$data['wissNameLa'].'</i></li>
            </ul><ul>';
            //<li><span class="photoTxtLabel">'.$i18n['dimensions'].($dim['isCropped'] ? ' ('.$i18n['cropped'].') ' : '').':</span> '.$dim['w'].' x '.$dim['h'].'</li>
     echo '       <li><span class="photoTxtLabel">'.$i18n['date'].':</span> '.$datum.'</li>
            <li><span class="photoTxtLabel">'.$i18n['order number'].':</span> '.$data['imgId'].'</li>
            <li><span class="photoTxtLabel">'.$i18n['file name'].':</span> '.$data['imgName'].'</li>
        </ul>
        <ul>
            <li><span class="photoTxtLabel">'.$i18n['place'].':</span> '.$data['locations'].'</li>
	        <li><span class="photoTxtLabel">'.$i18n['country'].':</span> '.(is_null($data['countries']) ? $data['country'] : $data['countries']).'</li>
        </ul>
        <p class="photoTxtSeparator"><span class="photoTxtLabel">'.$i18n['rating'].':</span> '.$star.'</p>
        </div>';

    echo '<div class="col colRight">';
	echo '<div id="map">';
	if ($data['showLoc'] === '0') {
		echo '<div id="mapNote">'.$i18n['Coordinates are not shown'].'</div>';
	}
	echo '</div>';
	echo '</div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';

	echo '<div id="contPhoto">';
	echo '<p><a title="'.$data['imgTitle'].' '.$i18n['photo'].'" href="'.$imgFile.'">';
	echo '<img src="'.$imgFile.'" id="photo" alt="'.$data['imgTitle'].'"/></a><br>';
	echo $data['imgTitle'].' © Simon Speich, www.speich.net';
	echo '</p>';
	echo '</div>';

	echo '<div id="exifInfo">
        <div class="col">
	    <h3>'.$i18n['technical information'].' (Exif)</h3>';
	if ($data['model'] == 'Nikon SUPER COOLSCAN 5000 ED') {
	    echo '<ul><li><span class="photoTxtLabel">'.$i18n['type of film'].':</span> '.$data['film'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].', '.$data['make'].'</li></ul>';
	}
	else {
		echo '<ul>
            <li><span class="photoTxtLabel">'.$i18n['exposure'].':</span> '.$data['exposureTime']." at ƒ".number_format($data['fNumber'], 1).'
		    <li><span class="photoTxtLabel">ISO:</span> '.$data['iso'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['focal length'].':</span> '.$data['focalLength'].', '.$i18n['distance'].' : '.$data['focusDistance'].'</li>
		    </ul>
		    <ul>
		    <li><span class="photoTxtLabel">'.$i18n['program'].':</span> '.$data['exposureProgram'].', '.$data['meteringMode'].'</li>
		    <li><span class="photoTxtLabel">VR:</span> '.$data['vibrationReduction'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['flash'].':</span> '.$data['flash'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['lens'].':</span> '.($data['lensSpec'] != '' ? $data['lensSpec'] : ($data['lens'] != '' ? $data['lens'] : '')).'</li>
	        <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].'</li>
	        </ul>';
	}
    echo '<ul>
        <li><span class="photoTxtLabel">'.$i18n['position'].' (GPS):</span> '.($data['showLoc'] === '1' ? $data['gpsLatitude'].' / '.$data['gpsLongitude'] : '').'</li>
    	<li><span class="photoTxtLabel">'.$i18n['hight'].' (GPS):</span> '.$data['gpsAltitude'].' m '.($data['gpsAltitudeRef'] == '1' ? 'b.s.l.' : 'a.s.l.').'</li>
    	</ul>';
	echo '</div>';

	echo '<div class="col">';
	echo '<h3>'.$i18n['database information'].'</h3>';
	echo '<ul><li><span class="photoTxtLabel">'.$i18n['added'].':</span> '.(!empty($data['dateAdded']) ? date("d.m.Y H:i:s", $data['dateAdded']) : '').'</li>
	    <li><span class="photoTxtLabel">'.$i18n['changed'].':</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['lastChange']) : '').'</li>
        <li><span class="photoTxtLabel">'.$i18n['published'].':</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['datePublished']) : '').'</li></ul>';
	echo '<ul><li><span class="photoTxtLabel">'.$i18n['file format'].':</span> '.$data['fileType']." (".$data['fileSize'].')</li></ul>';
	echo '</div></div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
}