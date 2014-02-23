<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\Website;

require_once __DIR__.'/../../library/inc_script.php';
require_once 'photoinc.php';

$imgId = isset($_GET['imgId']) ? $_GET['imgId'] : $imgId = 1;
$db = new PhotoDb($web->getWebRoot());
$db->connect();

// this page is accessed from different pages in the menu tree. Set correct menu item to active.
if (strpos($web->getLastPage(), 'photo-mapsearch.php') !== false) {
	$sideNav->arrItem[1]->setActive(null);
	$sideNav->arrItem[3]->setActive();
}
//echo $web->getLastPage();
//$sideNav->setActive($web->getLastPage());
/*
if (strpos($web->getLastPage(), 'photo-search.php') !== false) {
	$sideNav->arrItem[4]->setActive();
}
else if (strpos($web->getLastPage(), 'photo-mapsearch.php') !== false) {
	$sideNav->arrItem[5]->setActive();
}
else if (isset($_GET['theme'])) {
	$themeId = $_GET['theme'];
	$sideNav->arrItem['t'.$themeId]->setActive();
}
else if (strpos($web->getLastPage(), 'photo.php') !== false) {
	$mainNav->arrItem[1]->setActive();
}
*/
//$sideNav->setActive();

$lang = ucfirst($web->getLang());
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
	GROUP_CONCAT(DISTINCT T.name".$lang.") themes,
	GROUP_CONCAT(DISTINCT K.name) categories,
	N.nameDe wissNameDe, N.nameEn wissNameEn, N.nameLa wissNameLa,
	S.name sex,
	GROUP_CONCAT(DISTINCT L.name) locations,
	GROUP_CONCAT(DISTINCT C.name".$lang.") countries
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
 * @param Array $i18n internationalization
 */
function renderPhoto($data, $db, $web, $i18n) {
	$backPage = 'photo.php'.$web->getQuery(array('imgId'), 2);
	if (strpos($backPage, 'photo-mapsearch.php') !== false) {
		$backPage = 'photo-mapsearch.php?'.$_SERVER['QUERY_STRING'];	// when coming from map via js lastPage was not set with latest query vars, use current
	}
	$imgFile = $db->webroot.$db->getPath('img').$data['imgFolder'].'/'.$data['imgName'];

	$star = '';
	$str = '<img id="RatingStar" src="../../layout/images/ratingstar.gif" alt="star icon for rating image"/>';
	for ($i = 0; $i < strlen($data['rating']); $i++) {
		$star.= $str;
	}	
	if ($data['dateTimeOriginal']) {
		$datum = date("d.m.Y H:i:s", $data['dateTimeOriginal']);
	}
	else {
		$datum = $data['imgDate'];
	}
	echo '<h1>'.$data['imgTitle']."</h1>\n";
	echo '<p>'.$data['imgDesc'].'</p>';
	echo '<div class="col colLeft">';
	
	echo '<p><span class="PhotoTxtLabel">Stichwörter:</span> '.($data['categories'] != '' ? $data['categories'].'<br/>' : '').'</p>';
	echo '<p><span class="PhotoTxtLabel">wissenschaftlicher Name:</span> <i>'.$data['wissNameLa'].'</i><br/>';
	echo '<span class="PhotoTxtLabel">Name:</span> '.$data['wissNameDe'].' - '.$data['wissNameEn'].'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">Bewertung:</span> '.$star.'</p>';
	echo '<p><span class="PhotoTxtLabel">Datum:</span> '.$datum.'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">Original-Grösse:</span> '.$data['imageWidth'].' x '.$data['imageHeight'].' Pixel';
	echo '<p><span class="PhotoTxtLabel">Bestellnummer:</span> '.$data['imgId'].'</p>';
	echo '<p><span class="PhotoTxtLabel">Dateiname:</span> '.$data['imgName'].'<p>';
	echo '</div>';
	
	echo '<div class="col colRight">';
	echo '<div id="map">';
	if ($data['showLoc'] === '0') {
		echo '<div id="mapNote">Aus Gründen des Naturschutzes/Geheimhaltung werden keine Koordinaten angezeigt.</div>';
	}
	echo '</div>';
	echo '<p><span class="PhotoTxtLabel">Ort:</span> '.$data['locations'].'</p>';
	echo '<p><span class="PhotoTxtLabel">Land:</span> '.$data['countries'].'</p>';
	echo '</div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
	
	echo '<div id="contPhoto">';
	echo '<p><a title="Foto \''.$data['imgTitle'].'\' anzeigen" href="'.$imgFile.'">';
	echo '<img src="'.$imgFile.'" id="photo" alt="'.$data['imgTitle'].'"/></a><br>';
	echo 'Foto Simon Speich, www.speich.net';
	echo '</p>';
	echo '</div>';

	echo '<div id="exifInfo"><div class="col">';
	echo '<p><strong>Technische Informationen (Exif)</strong></p>';
	echo '<p><span class="PhotoTxtLabel">Model: </span>'.$data['model'].', '.$data['make']."</p>\n";
	echo '<p><span class="PhotoTxtLabel">Original-Grösse:</span> '.$data['imageWidth'].' x '.$data['imageHeight'].'px @ ';
	if ($data['bitsPerSample'] != '') {
		echo $data['bitsPerSample'];
	}
	else {
		echo '8bit';
	}
	echo "</p>\n";
	echo '<p><span class="PhotoTxtLabel">Original-Datei:</span> '.$data['fileType']." (".$data['fileSize'].")</p>\n";
	if ($data['model'] == 'Nikon SUPER COOLSCAN 5000 ED') {
		echo '<p><span class="PhotoTxtLabel">Scanndatum: </span>'.date("d.m.Y H:i:s", $data['createDate'])."</p>\n";
		echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">Filmtyp:</span> '.$data['film'].'</p>';
	}
	else {
		echo '<p><span class="PhotoTxtLabel">Belichtung:</span> '.$data['exposureTime']." @ f/".$data['fNumber'];
		echo ' mit '.$data['meteringMode']."</p>\n";
		echo '<p><span class="PhotoTxtLabel">Program:</span> '.$data['exposureProgram']."</p>\n";
		echo '<p><span class="PhotoTxtLabel">ISO:</span> '.$data['iso']."</p>\n";
		echo '<p><span class="PhotoTxtLabel">Objektiv:</span> '.($data['lensSpec'] != '' ? $data['lensSpec'] : ($data['lens'] != '' ? $data['lens'] : '')).', VR: '.$data['vibrationReduction'].'</p>';
		echo '<p><span class="PhotoTxtLabel">Brennweite:</span> '.$data['focalLength'].", Distanz: ".$data['focusDistance'].'</p>';
		echo '<p><span class="PhotoTxtLabel">Blitz:</span> '.$data['flash']."</p>\n";
	}
	echo '</div>';
	
	echo '<div class="col">';
	echo '<p><span class="PhotoTxtLabel">Position (GPS):</span> ';
	if ($data['showLoc'] === '1') {
		echo $data['gpsLatitude'].' / '.$data['gpsLongitude'];
	}
	echo '<p><span class="PhotoTxtLabel">Höhe (GPS):</span> '.$data['gpsAltitude'].' m '.($data['gpsAltitudeRef'] == '1' ? 'b.s.l.' : 'a.s.l.');
	echo '<p><strong>Datenbankinformationen</strong></p>';
	echo '<p><span class="PhotoTxtLabel">hinzugefügt:</span> '.(!empty($data['dateAdded']) ? date("d.m.Y H:i:s", $data['dateAdded']) : '').'</p>';
	echo '<p><span class="PhotoTxtLabel">geändert:</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['lastChange']) : '').'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">publiziert:</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['datePublished']) : '').'</p>';
	echo '</div></div>';
	echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
}

$pageTitle = ($web->getLang() == 'en' ? 'Photo' : 'Foto').' '.$photo[0]['imgTitle'];
?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $pageTitle.' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.css" rel="stylesheet" type="text/css">
<style type="text/css">
#layoutMiddle {width: 955px;}
.col {
	display: inline-block;
	width: 365px;
	vertical-align: top;
}

#contPhoto, #exifInfo {
	margin-bottom: 20px;
	border-bottom: 6px solid #DFE8D9;
}

#photo {
	border: 1px solid black;
	max-width: 740px;
}

#exifInfo {
	font-size: 11px;
}

#ratingStar {
	margin-right: 2px;
	vertical-align: text-bottom;
}

#map, #mapNote {
	/* clear: both; */
	width: 373px; height: 200px;

}
#map {
	border: 1px solid black;
	margin-bottom: 10px;
}

#mapNote {
	margin: auto;
	width: 240px;
	padding-top: 30px;
	text-align: center;
}

</style>
</head>

<body>
<?php
require_once 'inc_body_begin.php';
renderPhoto($photo[0], $db, $web, $i18n);
require_once 'inc_body_end.php';
?>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	has: {
		'dojo-debug-messages': false
	},
	locale: '<?php echo $locale = $web->getLang(); ?>',
	packages: [
		{name: 'gmap', location: './../../../gmap'}
	]
};
</script>
<script type="text/javascript" src="../../library/dojo/1.9.2/dojo/dojo.js"></script>
<script type="text/javascript">
require([
	'gmap/gmapLoader!http://maps.google.com/maps/api/js?v=3&sensor=false&language=' + dojoConfig.locale,
	'dojo/domReady!'
], function() {

	var initMap = function() {
		var gmaps = google.maps,
			map, mapOptions,
			marker,
			lat = <?php echo (empty($photo[0]['imgLat']) ? 'null' : $photo[0]['imgLat']) ?>,
			lng = <?php echo (empty($photo[0]['imgLng']) ? 'null' : $photo[0]['imgLng']) ?>;

		if (lat && lng) {
			mapOptions = {
				center: new gmaps.LatLng(lat, lng),
				zoom: 5,
				mapTypeId: gmaps.MapTypeId.HYBRID
			};
			map = new gmaps.Map(document.getElementById('map'), mapOptions);
			marker = new gmaps.Marker({
				map: map,
				position: new gmaps.LatLng(lat, lng)
			});

		}
	};

	initMap();
});
</script>
</body>
</html>