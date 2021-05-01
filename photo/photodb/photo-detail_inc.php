<?php

use PhotoDb\PhotoList;
use PhotoDb\PhotoDb;
use WebsiteTemplate\Language;


require_once __DIR__.'/../../scripts/php/inc_script.php';
require_once 'photo_inc.php';

if (isset($_GET['imgId'])) {
	$imgId = $_GET['imgId'];
} else {
	header('Location: http://www.speich.net/photo/photodb/photo.php');
}

$ucLang = ucfirst($language->get());
// TODO: move date scanned out of Images?
$photoDb = new PhotoDb($web->getWebRoot());
$photoDb->connect();
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
	GROUP_CONCAT(DISTINCT T.Name".$ucLang.') themes,
	GROUP_CONCAT(DISTINCT K.Name) categories,
	GROUP_CONCAT(DISTINCT N.NameDe) wissNameDe, GROUP_CONCAT(DISTINCT N.NameEn) wissNameEn, GROUP_CONCAT(DISTINCT N.NameLa) wissNameLa,
	S.Name'.$ucLang.' sex, S.Symbol symbol,
	GROUP_CONCAT(DISTINCT L.Name) locations,
	GROUP_CONCAT(DISTINCT C.Name'.$ucLang.') countries,
	C2.Name'.$ucLang.' country
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
	WHERE I.Id = :imgId';
$stmt = $photoDb->db->prepare($sql);
$stmt->bindValue(':imgId', $imgId);
$stmt->execute();
$photo = $stmt->fetchAll(PDO::FETCH_ASSOC);
// pass data to js
$jsConfig = [
	'lat' => empty($photo[0]['imgLat']) ? null : $photo[0]['imgLat'],
	'lng' => empty($photo[0]['imgLng']) ? null : $photo[0]['imgLng']
];
$jsConfig = json_encode($jsConfig, JSON_NUMERIC_CHECK);
$jsConfig = htmlspecialchars($jsConfig, ENT_COMPAT, $web->charset);


/**
 * Print HTML to display photo detail.
 * @param array $data
 * @param PhotoDb $db
 * @param Language $lang
 * @param array $i18n internationalization
 */
function renderPhoto($data, $db, $lang, $i18n)
{
	$photo = new PhotoList($db);
    $query = new \WebsiteTemplate\QueryString();
	$backPage = $lang->createPage('photo.php').$query->withString(null, ['imgId']);
	if (strpos($backPage, $lang->createPage('photo-mapsearch.php')) !== false) {
		$backPage = $lang->createPage('photo-mapsearch.php').'?'.$_SERVER['QUERY_STRING'];    // when coming from map via js lastPage was not set with latest query vars, use current
	}
	$imgFile = $db->webroot.$db->getPath('img').$data['imgFolder'].'/'.$data['imgName'];
	$dim = $photo->getImageSize($data);
	$star = '';

	$str = '<svg class="icon"><use xlink:href="/../../layout/images/symbols.svg#star"></use></svg>';
    $len = strlen($data['rating']);
	for ($i = 0; $i < $len; $i++) {
		$star .= $str;
	}
	if ($data['dateTimeOriginal']) {
		$datum = date('d.m.Y H:i:s', $data['dateTimeOriginal']);
	} else {
		$datum = $data['ImgDateManual'];
	}
	echo '<h1>'.$data['imgTitle'].'</h1>';
	echo $data['imgDesc'] ? '<p>'.$photo->renderDescLinks($data['imgDesc']).'</p>' : '';
    echo '<figure>
        <a title="'.$i18n['photo'].': '.$data['imgTitle'].'" href="'.$imgFile.'">
        <img src="'.$imgFile.'" id="photo" alt="'.$data['imgTitle'].'"/></a>
        <figcaption>'.$data['imgTitle'].'<br>
         © '.ucfirst($i18n['photo']).' Simon Speich, www.speich.net</figcaption></figure>';
	echo '<div class="col colLeft">
	    <ul>
	        <li><span class="photoTxtLabel">'.$i18n['keywords'].':</span> '.($data['categories'] !== '' ? $data['categories'].'<br/>' : '').'</li>
	        <li><span class="photoTxtLabel">'.$i18n['name'].':</span> '.$data['wissNameDe'].' - '.$data['wissNameEn'].'</li>
            <em><span class="photoTxtLabel">'.$i18n['scientific name'].':</span> <em>'.$data['wissNameLa'].' <span title="'.$data['sex'].'">'.$data['symbol'].'</span></em></em>
            </ul><ul>
            <li><span class="photoTxtLabel">'.$i18n['dimensions'].($dim['isCropped'] ? ' ('.$i18n['cropped'].') ' : '').':</span> '.$dim['w'].' x '.$dim['h'].' px</li>
            <li><span class="photoTxtLabel">'.$i18n['date'].':</span> '.$datum.'</li>
            <li><span class="photoTxtLabel">'.$i18n['order number'].':</span> '.$data['imgId'].'</li>
            <li><span class="photoTxtLabel">'.$i18n['file name'].':</span> '.$data['imgName'].'</li>
        </ul>
        <ul>
            <li><span class="photoTxtLabel">'.$i18n['place'].':</span> '.$data['locations'].'</li>
	        <li><span class="photoTxtLabel">'.$i18n['country'].':</span> '.($data['countries'] ?? $data['country']).'</li>
        </ul>
        <p class="mRating"><span class="photoTxtLabel">'.$i18n['rating'].':</span> '.$star.'</p>
        </div>';

	echo '<div class="col colRight">';
	echo '<div id="map">';
	if ($data['showLoc'] === '0') {
		echo '<div id="mapNote">'.$i18n['Coordinates are not shown'].'</div>';
	}
	echo '</div>';
	echo '</div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';

	echo '<div id="exifInfo">
        <div class="col">
	    <h3>'.$i18n['technical information'].' (Exif)</h3>';
	if ($data['model'] === 'Nikon SUPER COOLSCAN 5000 ED') {
		echo '<ul><li><span class="photoTxtLabel">'.$i18n['type of film'].':</span> '.$data['film'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].', '.$data['make'].'</li></ul>';
	} else {
		echo '<ul>
            <li><span class="photoTxtLabel">'.$i18n['exposure'].':</span> '.$data['exposureTime']." at ƒ".number_format($data['fNumber'],
				1).'
		    <li><span class="photoTxtLabel">ISO:</span> '.$data['iso'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['focal length'].':</span> '.$data['focalLength'].', '.$i18n['distance'].' : '.$data['focusDistance'].'</li>
		    </ul>
		    <ul>
		    <li><span class="photoTxtLabel">'.$i18n['program'].':</span> '.$data['exposureProgram'].', '.$data['meteringMode'].'</li>
		    <li><span class="photoTxtLabel">VR:</span> '.$data['vibrationReduction'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['flash'].':</span> '.$data['flash'].'</li>
		    <li><span class="photoTxtLabel">'.$i18n['lens'].':</span> '.($data['lensSpec'] !== '' ? $data['lensSpec'] : ($data['lens'] !== '' ? $data['lens'] : '')).'</li>
	        <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].'</li>
	        </ul>';
	}
	echo '<ul>
        <li><span class="photoTxtLabel">'.$i18n['position'].' (GPS):</span> '.($data['showLoc'] === '1' ? $data['gpsLatitude'].' / '.$data['gpsLongitude'] : '').'</li>
    	<li><span class="photoTxtLabel">'.$i18n['hight'].' (GPS):</span> '.$data['gpsAltitude'].' m '.($data['gpsAltitudeRef'] === '1' ? 'b.s.l.' : 'a.s.l.').'</li>
    	</ul>';
	echo '</div>';

	echo '<div class="col">';
	echo '<h3>'.$i18n['database information'].'</h3>';
	echo '<ul><li><span class="photoTxtLabel">'.$i18n['added'].':</span> '.(!empty($data['dateAdded']) ? date('d.m.Y H:i:s',
			$data['dateAdded']) : '').'</li>
	    <li><span class="photoTxtLabel">'.$i18n['changed'].':</span> '.(!empty($data['lastChange']) ? date('d.m.Y H:i:s',
			$data['lastChange']) : '').'</li>
        <li><span class="photoTxtLabel">'.$i18n['published'].':</span> '.(!empty($data['lastChange']) ? date('d.m.Y H:i:s',
			$data['datePublished']) : '').'</li></ul>';
	echo '<ul><li><span class="photoTxtLabel">'.$i18n['file format'].':</span> '.$data['fileType'].' ('.$data['fileSize'].')</li></ul>';
	echo '</div></div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
}