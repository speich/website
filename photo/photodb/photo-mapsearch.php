<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\PagedNav;

require_once 'photoinc.php';

if (isset($_GET['showMap']) && $_GET['showMap'] === '0') {
	$showMap = false;
}
else {
	$showMap = true;
}

if ($showMap === false && isset($_GET['lat1']) && isset($_GET['lat2']) && isset($_GET['lng1']) && isset($_GET['lng2'])) {
	$lat1 = $_GET['lat1'];
	$lat2 = $_GET['lat2'];
	$lng1 = $_GET['lng1'];
	$lng2 = $_GET['lng2'];
	$sql = "SELECT Id ImgId, ImgFolder, ImgName, ImgTitle, DateAdded, LastChange, ImgTitle, RatingId,
	 	CASE WHEN imgDateOriginal IS NULL THEN
			(CASE WHEN imgDate IS NOT NULL THEN DATETIME(imgDate, 'unixepoch', 'localtime') END)
		ELSE DATETIME(imgDateOriginal, 'unixepoch', 'localtime') END date
		FROM Images";
	if ($lat1 < $lat2) {
		$sql .= " WHERE imgLat > :query0 AND imgLat < :query1
		AND imgLng > :query2 AND imgLng < :query3";
	}
	else { // bounding box contains international date line
		$sql .= " WHERE imgLat > :query0 AND imgLat < :query1
		AND (imgLng > :query2 OR imgLng < :query3)";
	}
	$sqlFilter = str_replace(' WHERE', ' AND', $sqlFilter);
	$stmt = $db->db->prepare($sql.$sqlFilter.$sqlSort." LIMIT :limit OFFSET :offset");
	$arrBind = array();
	$arrBind['query0'] = $lat1;
	$arrBind['query1'] = $lat2;
	$arrBind['query2'] = $lng1;
	$arrBind['query3'] = $lng2;
	$stmt->bindValue(':query0', $lat1);
	$stmt->bindValue(':query1', $lat2);
	$stmt->bindValue(':query2', $lng1);
	$stmt->bindValue(':query3', $lng2);
	$stmt->bindValue(':limit', $numRecPerPage);
	$stmt->bindValue(':offset', ($pg - 1) * $numRecPerPage);
	$stmt->execute();
	$arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sqlWrapper = "SELECT * FROM (".$sql.$sqlFilter.")"; // needed for getNumRec to work
	$numRec = $db->getNumRec($sqlWrapper, 'ImgId', $arrBind, $lastPage, $web);
}
else {
	$query = null;
	$arrData = null;
	$numRec = 0;
}

$pagedNav = new PagedNav($numRec, $numRecPerPage);

/**
 * @param PhotoDb $db
 * @param array $arrData
 * @return bool
 */
function renderDataMap($db, $arrData) {
	if (count($arrData) == 0) {
		echo '<p>Mit diesen Einstellungen wurden keine Datensätze gefunden.</p>';
		return false;
	}
	$c = 0;
	$num = count($arrData) - 1;
	foreach ($arrData as $i) {
		// image dimensions
		$imgFile = $db->webroot.$db->getPath('img').'thumbs/'.$i['ImgFolder'].'/'.$i['ImgName'];
		$imgSize = getimagesize($_SERVER['DOCUMENT_ROOT'].$db->getPath('img').$i['ImgFolder'].'/'.$i['ImgName']);
		$imgTitleShort = $imgTitle = $i['ImgTitle'];
		if (strlen($imgTitle) > 20) {
			$imgTitleShort = substr($imgTitle, 0, 20)."...";
		}
		if ($imgSize[0] > $imgSize[1]) {
			$css = 'slideHorizontal';
			$cssImg = 'slideImgHorizontal';
		}
		else {
			if ($imgSize[0] < $imgSize[1]) {
				$css = 'slideVertical';
				$cssImg = 'slideImgVertical';
			}
			else {
				$css = 'slideQuadratic';
				$cssImg = 'slideImgQuadratic';
			}
		}
		echo '<div class="slide">';
		echo '<div class="slideCanvas'.($c == $num ? ' slideLast' : '').' '.$css.'" style="background-image: url('.$imgFile.');">';
		echo '<a href="#" onclick="return slide.showFull(arguments[0] || window.event, this.firstChild, '.$imgSize[0].', '.$imgSize[1].');">';
		echo '<img class="'.$cssImg.'" src="'.$imgFile.'" alt="Thumbnail"/>';
		echo '</a>';
		echo '</div>'; // end slideCanvas
		echo '<div class="slideText"><a href="#" onclick="return slide.showFull(arguments[0] || window.event, this.parentNode.parentNode.getElementsByTagName(\'img\')[0], '.$imgSize[0].', '.$imgSize[1].');">Zoom</a> | ';
		echo '<a href="photo-detail.php'.$db->getQuery(array('imgId' => $i['ImgId'])).'">Details</a></div>';
		echo '</div>'; // end slide
		$c++;
	}
}

$pageTitle = $web->getLang() == 'en' ? 'Photo Database Mapsearch' : 'Bildarchiv Kartensuche';
?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $pageTitle.' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<style type="text/css">
.relativeContainer { position: relative; }
#layoutMiddle { bottom: 100px; }

#map-canvas {
	/* dimension overwritten by js */
	height: 100%;
	width: 100%;
}

#layoutMiddle, #layoutFooterCont {
	max-width: none;
}

#map {
	margin-right: 20px;
	height: 520px;
	border: 1px solid black;
	margin-bottom: 10px;
	display: none;
	visibility: hidden;
}

img[id^=mtgt_unnamed] {
	border: 1px solid white !important;
}

.buttShowResult {
	background-image: url(../../layout/images/icon_photolist.gif);
	background-repeat: no-repeat;
	width: 16px;
	height: 16px;
}

.mItemIconStar1 {
	background-image: url(../../layout/images/ratingstar.gif);
	background-repeat: no-repeat;
	width: 16px;
	height: 16px;
}

.mItemIconStar2 {
	background-image: url(../../layout/images/ratingstar.gif);
	background-repeat: repeat-x;
	width: 32px !important;
	height: 16px;
}

.mItemIconStar3 {
	background-image: url(../../layout/images/ratingstar.gif);
	background-repeat: repeat-x;
	width: 48px !important;
	height: 16px;
}

</style>
</head>

<body class="tundra">
<?php require_once 'inc_body_begin.php'; ?>
<div class="relativeContainer">
<?php echo $mRating->render(); ?>
<div id="map-canvas"></div>

</div>

<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	locale: '<?php echo $locale = $web->getLang(); ?>',
	packages: [
		{name: 'gmap', location: './../../../gmap'}
	]
};
</script>
<script type="text/javascript" src="../../library/dojo/1.9.2/dojo/dojo.js"></script>
<script type="text/javascript">
require([
	'dojo/_base/lang',
	'dojo/_base/array',
	'dojo/window',
	'dojo/request/xhr',
	'dojo/dom-style',
	'dojo/dom-geometry',
	'dojo/io-query',
	'gmap/gmapLoader!http://maps.google.com/maps/api/js?v=3&sensor=false&key=AIzaSyBSisbdkhszQj2OvSyWWjE-Vmi8sV34oeA&language=' + dojoConfig.locale,
	'/library/gmap/markerclusterer/src/markerclusterer_packed.js',
	'dojo/domReady!'
], function(lang, array, win, xhr, domStyle, domGeometry, ioQuery) {

	var mapApp,
		byId = function(el) {
		return document.getElementById(el);
	},
		gmaps = google.maps;

	mapApp = {
		queryObj: ioQuery.queryToObject(window.location.search.replace('?', '')),
		map: null,
		mapOptions: {},
		mapLat: 28,	// initial map coordinates
		mapLng: 12,
		mapZoom: 3,
		mapDiv: byId('map-canvas'),
		bounds: null,
		manager: {
			maxZoom: 13,
			gridSize: 40,
			markers: {},
			bounds: {},
			IMAGE_PATH: '/library/gmap/markerclusterer/images/m'
		},
		url: 'controller.php',

		/**
		 * Fit map to window size.
		 */
		setMapDimension: function() {
			var winDim = win.getBox(),
			cont = domGeometry.position(byId('layoutMain')),
			footer = domGeometry.position(byId('layoutFooterCont'));

			// set map dimensions
			domStyle.set(byId('map-canvas'), {
				width: winDim.w - cont.x - 15 + 'px',
				height: winDim.h - cont.y - footer.h - 15 + 'px'
			});
		},

		loadMarkerData: function() {
			var q = this.queryObj.qual ? '?qual=' + this.queryObj.qual : 3;

			return xhr.get(this.url + '/marker/' + q, {
				handleAs: 'json'
			});
		},

		/**
		 * Add markers to the marker manager.
		 * @param {Object} response
		 */
		createMarkers: function(response) {
			var self = this;
			if (response.markers.length > 0) {
				var markers = [];
				dojo.forEach(response.markers, function(item) {
					if (!self.manager.markerHash[item.id]) {
						var point = new GLatLng(item.lat, item.lng);
						var icon = new GIcon();
						icon.image = 'images/thumbs/' + item.img;
						icon.iconSize = new GSize(40, 40);
						icon.iconAnchor = new GPoint(21, 21);
						icon.infoWindowAnchor = new GPoint(21, 21);
						var marker = new GMarker(point, {
							icon: icon,
							draggable: true,
							bouncy: false
						});
						GEvent.addListener(marker, "click", function() {
							self.showThumb(marker, item);
						});
						markers.push(marker);
						self.manager.markerHash[item.id] = item;
					}
				});
				if (markers.length > 0) {
					this.manager.addMarkers(markers);
				}
			}
			return response;
		},

		createMarker: function(data) {
			var marker,
				latLng = new gmaps.LatLng(data.lat, data.lng),
				image = {
					anchor: new gmaps.Point(21, 21),
					scaledSize: new gmaps.Size(40, 40),
					url: 'images/' + data.img
				};
			marker = new gmaps.Marker({
				icon: image,
				position: latLng
			});
			return marker;
		},

		initMap: function () {
			var mapOptions, queryObj = this.queryObj;

			window.onresize = this.setMapDimension;
			this.setMapDimension();

			mapOptions = {
				center: new gmaps.LatLng(this.mapLat, this.mapLng),
				zoom: this.mapZoom
			};
			this.map = new gmaps.Map(this.mapDiv, mapOptions);

			// center map  to coords from query string
			if (queryObj.lat1 && queryObj.lat2 && queryObj.lng1 && queryObj.lng2) {
				this.bounds = new gmaps.LatLngBounds(new gmap.LatLng(queryObj.lat1, queryObj.lng1), new gmaps.LatLng(queryObj.lat2, queryObj.lng2));
				this.map.fitBounds(bounds);
			}

			// add rating control
			this.map.controls[gmaps.ControlPosition.TOP_RIGHT].push(byId('mRating'));
		},

		initMarkerClusterer: function () {
			var mc = new MarkerClusterer(this.map, null, {
				imagePath: this.manager.IMAGE_PATH
			});

			this.loadMarkerData().then(lang.hitch(this, function(data) {
				var markers = [];

				array.forEach(data, function(item) {
					var marker = this.createMarker(item);
		  			markers.push(marker);
				}, this);

				mc.addMarkers(markers);
			}));

			this.manager.clusterer = mc;
		},

		init: function() {
			this.initMap();
			this.initMarkerClusterer();
		}
	};

	mapApp.init();

});
</script>
</body>
</html>