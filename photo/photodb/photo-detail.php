<?php 
require_once '../../library/inc_script.php';

$imgId = isset($_GET['imgId']) ? $_GET['imgId'] : $imgId = 1;
$db = new PhotoDb();
$db->connect();

// this page is accessed from different pages in the menu tree. Set correct menu item to active.
if (strpos($db->getLastPage(), 'photo-search.php') !== false) {
	$sideNav->arrItem[4]->setActive();
}
else if (strpos($db->getLastPage(), 'photo-mapsearch.php') !== false) {
	$sideNav->arrItem[5]->setActive();
}
else if (isset($_GET['theme'])) {
	$themeId = $_GET['theme'];
	$sideNav->arrItem['t'.$themeId]->setActive();
}
else if (strpos($db->getLastPage(), 'photo.php') !== false) {
	$sideNav->arrItem[2]->setActive();
}
$sideNav->setActive();

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

// Set the right key for Google$gMapKeys API
switch ($web->getHost()) {
	case 'speich': $gMapKey = 'ABQIAAAA6MsurN7eJBRBQSZMfJtPDRRxMHeuOuyeMMjj_aTZeqIoqzy0_hRIjJTsHI-W0kFc320Tnzs-TmsQYw'; break;
	case 'www.speich.net': $gMapKey = "ABQIAAAA6MsurN7eJBRBQSZMfJtPDRSLb_wSuHY1Noj7kltgsY8WwZ7CtxQycVmx1PtS5TJKIuhuXgxPt9a-3g"; break;
	case 'speich.net': $gMapKey = "ABQIAAAA6MsurN7eJBRBQSZMfJtPDRSLb_wSuHY1Noj7kltgsY8WwZ7CtxQycVmx1PtS5TJKIuhuXgxPt9a-3g"; break;
}

function renderPhoto($data, $db) {
	$backPage = is_null($db->getLastPage()) ? 'photo.php' : $db->getLastPage();
	if (strpos($db->getLastPage(), 'photo-mapsearch.php') !== false) {
		$backPage = 'photo-mapsearch.php?'.$_SERVER['QUERY_STRING'];	// when coming from map via js lastPage was not set with latest query vars, use current
	}
	$imgFile = $db->getWebRoot().$db->getPath('img').$data['imgFolder'].'/'.$data['imgName'];
	$imgSize = getimagesize($db->getDocRoot().$db->getPath('img').$data['imgFolder'].'/'.$data['imgName']);

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
	echo '<div class="colLeft">';
	
	echo '<p><span class="PhotoTxtLabel">Stichwörter:</span> '.($data['categories'] != '' ? $data['categories'].'<br/>' : '').'</p>';
	echo '<p><span class="PhotoTxtLabel">wissenschaftlicher Name:</span> <i>'.$data['wissNameLa'].'</i><br/>';
	echo '<span class="PhotoTxtLabel">Name:</span> '.$data['wissNameDe'].' - '.$data['wissNameEn'].'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">Bewertung:</span> '.$star.'</p>';
	echo '<p><span class="PhotoTxtLabel">Datum:</span> '.$datum.'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">Original-Grösse:</span> '.$data['imageWidth'].' x '.$data['imageHeight'].' Pixel';
	echo '<p><span class="PhotoTxtLabel">Bestellnummer:</span> '.$data['imgId'].'</p>';
	echo '<p><span class="PhotoTxtLabel">Dateiname:</span> '.$data['imgName'].'<p>';
	echo '</div>';
	
	echo '<div class="colRight">';
	echo '<div id="map">';
	if ($data['showLoc'] === '0') {
		echo '<div id="mapNote">Aus Gründen des Naturschutzes/Geheimhaltung werden keine Koordinaten angezeigt.</div>';
	}
	echo '</div>';
	echo '<p><span class="PhotoTxtLabel">Ort:</span> '.$data['locations'].'</p>';
	echo '<p><span class="PhotoTxtLabel">Land:</span> '.$data['countries'].'</p>';
	echo '</div>';
	echo '<p style="clear: right"><a href="'.$backPage.'">zurück</a></p>';
	
	echo '<div id="contPhoto">';
	echo '<p><a title="Foto \''.$data['imgTitle'].'\' anzeigen" href="'.$imgFile.'">';
	echo '<img src="'.$imgFile.'" id="photo" alt="'.$data['imgTitle'].'"/></a><br>';
	echo 'Foto Simon Speich, www.speich.net';
	echo '</p>';
	echo '</div>';	
	echo '<div class="colLeft">';
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
		echo '<p><span class="PhotoTxtLabel">Scanndatum: </span>'.date("d.m.Y H:i:s", $data['E.createDate'])."</p>\n";
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
	
	echo '<div class="colRight">';
	echo '<p><span class="PhotoTxtLabel">Position (GPS):</span> ';
	if ($data['showLoc'] === '1') {
		echo $data['gpsLatitude'].' / '.$data['gpsLongitude'];
	}
	echo '<p><span class="PhotoTxtLabel">Höhe (GPS):</span> '.$data['gpsAltitude'].' m '.($data['gpsAltitudeRef'] == '1' ? 'b.s.l.' : 'a.s.l.');
	echo '<p><strong>Datenbankinformationen</strong></p>';
	echo '<p><span class="PhotoTxtLabel">hinzugefügt:</span> '.(!empty($data['dateAdded']) ? date("d.m.Y H:i:s", $data['dateAdded']) : '').'</p>';
	echo '<p><span class="PhotoTxtLabel">geändert:</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['lastChange']) : '').'</p>';
	echo '<p class="PhotoTxtSeparator"><span class="PhotoTxtLabel">publiziert:</span> '.(!empty($data['lastChange']) ? date("d.m.Y H:i:s", $data['datePublished']) : '').'</p>';
	echo '</div>';
	echo '<p style="clear: both"><a href="'.$backPage.'">zurück</a></p>';
}

function displPalette($file) {
	//$file = $_SERVER['DOCUMENT_ROOT'].$file;
	$steps = 3;
	$file_img = getImageSize($file);
	switch ($file_img[2]) {
		case 1: //GIF
			$srcImage = imagecreatefromgif($file);
			break;
		case 2: //JPEG
			$srcImage = imagecreatefromjpeg($file);
			break;
		case 3: //PNG
			$srcImage = imagecreatefrompng($file);
			break;
		default:
			return false;
	}
	$xloop = ceil( ( $file_img[0] - 60 ) / ($steps - 1) );
	$yloop = ceil( ( $file_img[1] - 60 ) / ($steps - 1) );
	for ($y=10; $y<$file_img[1]; $y+=$yloop) {
		for ($x=10; $x<$file_img[0]; $x+=$xloop) {
			$rgbNow	  = imagecolorat($srcImage, $x, $y);
			$colorrgb = imagecolorsforindex($srcImage,$rgbNow);
			foreach($colorrgb as $k => $v) {
				$t[$k] = dechex($v);
				if( strlen($t[$k]) == 1 ) {
					if( is_int($t[$k]) ) {
						$t[$k] = $t[$k] . "0";
					} else {
						$t[$k] = "0" . $t[$k];
					}
				}
			}
			$rgb2 = strtoupper($t['red'] . $t['green'] . $t['blue']);
			$color_set[] = $rgb2;
		}
	}
	return $color_set;	
}

function getColor($img) {
	/*
	 * Code adapted from CristianoBetta.com
	 * (c) 2008 Cristiano Betta <code@cristianobetta.com>
	 *
	 * This code has been licensed under the GPL2.0 License
	 * http://creativecommons.org/licenses/GPL/2.0/
	 */
	$img = $_SERVER['DOCUMENT_ROOT'].$img;
	$mime = exif_imagetype($img);
	switch($mime) {
		case 1: $img = imagecreatefromgif($img); break;
		case 2: $img = imagecreatefromjpeg($img); break;
		case 3: $img = imagecreatefrompng($img); break;
	}
	$imgWidth = imagesx($img);
	$imgHeight = imagesy($img);
	$megaPixel = $imgWidth * $imgHeight;
	$arrHist = array();
	$arrHistR = array();
	$arrHistG = array();
	$arrHistB = array();
	
	// init arrays with zeros
	for ($i = 0; $i < 86; $i++) {
		$arrHist[$i] = 0;
		$arrHistR[$i] = 0;
		$arrHistG[$i] = 0;
		$arrHistB[$i] = 0;
	}
	
	// loop through all the pixels and get rgb and luminance
	for ($i = 0; $i < $imgWidth; $i++) {
		for ($j = 0; $j < $imgHeight; $j++) {
			$rgb = imagecolorat($img, $i, $j);
			$cols = imagecolorsforindex($img, $rgb);
			$red = $cols['red'];
			$green = $cols['green'];
			$blue = $cols['blue'];
			$luminace = round(0.3 * $red + 0.59 * $green + 0.11 * $blue);

			// calculate the indexes (rounding to the nearest (lowest) 5)
			$iLuminance = ($luminance - $luminance % 3) / 3;
			$iRed = ($red - $red % 3) / 3;
			$iGreen = ($green - $green % 3) / 3;
			$iBlue = ($blue - $blue % 3) / 3;

			$arrHist[$iLuminance] += $luminance / $megapixel;
			$arrHistR[$iRed] += $red / $megapixel;
			$arrHistG[$iGreen] += $green / $megapixel;
			$arrHistB[$iBue] += $blue / $megapixel;
			print_r($arrHistB);
		}
	}
}
$pageTitle = ($web->getLang() == 'en' ? 'Photo' : 'Foto').' '.$photo[0]['imgTitle'] ?></title>
?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><? echo $pageTitle ?> ::: speich.net</title>
<?php require_once '../../layout/inc_head.php' ?>
<link href="../../layout/photodb.css" rel="stylesheet" type="text/css">
<style type="text/css">
.colLeft {
	float: left;
	width: 365px;
}

.colRight {
	margin-left: 375px;
	width: 365px;
}
	
#contPhoto {
	margin-bottom: 10px;
	padding-bottom: 10px;
	border-bottom: 1px solid #DFE8D9;
}

#photo {
	border: 1px solid black;
	max-width: 740px;
}

#RatingStar {
	margin-right: 2px;
	vertical-align: text-bottom;
}

#map, #mapNote {
	/* clear: both; */
	width: 373x; height: 200px;

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
renderPhoto($photo[0], $db);
require_once 'inc_body_end.php';
?>

<script type="text/javascript">

<?php echo 'var key = "'.$gMapKey.'";'; ?>
/**
 * Load the google maps api dynamically by inserting script tag.
 */
function initGMapsLoader(key) {
  var script = document.createElement("script");
  script.src = 'http://www.google.com/jsapi?key='+ key + '&callback=initMap';
  script.type = "text/javascript";
  document.getElementsByTagName("head")[0].appendChild(script);
}

/**
 * Inits the map as a callback from the loader.
 */
function initMap() {
	var lat = <?php echo (empty($photo[0]['imgLat']) ? 'null' : $photo[0]['imgLat']) ?>;
	var lng = <?php echo (empty($photo[0]['imgLng']) ? 'null' : $photo[0]['imgLng']) ?>;
	if (lat && lng) {
		google.load("maps", "2.0", {
			"callback": function(){
				var El = document.getElementById('map');
				var map = new GMap2(El, {
					draggableCursor: 'pointer',
					draggingCursor: 'move'
				});
				map.addControl(new GMapTypeControl());
				map.addControl(new GSmallMapControl());
				map.enableScrollWheelZoom();
				map.enableContinuousZoom();
				map.setMapType(G_HYBRID_MAP);
				var Coord = new GLatLng(lat, lng);
				map.setCenter(Coord, 12);
				var point = new GLatLng(lat, lng);
				var icon = new GIcon();
				icon.image = "../../layout/images/crosshair.gif";
				icon.iconSize = new GSize(35, 35);
				icon.iconAnchor = new GPoint(6, 6);
				var marker = new GMarker(point, icon);
				map.addOverlay(marker);
			}
		});
	}
}

window.onload = function() {
	initGMapsLoader(key);
}
</script>
</body>
</html>