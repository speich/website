<?php
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
	$stmt->bindValue(':offset', ($pgNav - 1) * $numRecPerPage);
	$stmt->execute();
	$arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sqlWrapper = "SELECT * FROM (".$sql.$sqlFilter.")"; // needed for getNumRec to work
	$numRec = $db->getNumRec($sqlWrapper, 'ImgId', $arrBind, $lastPage);
}
else {
	$query = null;
	$arrData = null;
	$numRec = 0;
}

$sideNav->arrItem[5]->setActive();
$sideNav->setActive();

$pagedNav = new PagedNav($pgNav, $numRec, $numRecPerPage);
$pagedNav->setStep(5, 10);

function renderData($db, $arrData) {
	if (count($arrData) == 0) {
		echo '<p>Mit diesen Einstellungen wurden keine Datensätze gefunden.</p>';
		return false;
	}
	$c = 0;
	$num = count($arrData) - 1;
	foreach ($arrData as $i) {
		// image dimensions
		$imgFile = $db->getWebRoot().$db->getPath('img').'thumbs/'.$i['ImgFolder'].'/'.$i['ImgName'];
		$imgSize = getimagesize($db->getDocRoot().$db->getPath('img').$i['ImgFolder'].'/'.$i['ImgName']);
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

// Set the right key for Google$gMapKeys API
switch ($web->getHost()) {
	case 'speich':
		$gMapKey = 'ABQIAAAA6MsurN7eJBRBQSZMfJtPDRRxMHeuOuyeMMjj_aTZeqIoqzy0_hRIjJTsHI-W0kFc320Tnzs-TmsQYw';
		break;
	case 'www.speich.net':
		$gMapKey = "ABQIAAAA6MsurN7eJBRBQSZMfJtPDRSLb_wSuHY1Noj7kltgsY8WwZ7CtxQycVmx1PtS5TJKIuhuXgxPt9a-3g";
		break;
	case 'speich.net':
		$gMapKey = "ABQIAAAA6MsurN7eJBRBQSZMfJtPDRSLb_wSuHY1Noj7kltgsY8WwZ7CtxQycVmx1PtS5TJKIuhuXgxPt9a-3g";
		break;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->getWindowTitle(); ?>: Bildarchiv Kartensuche</title>
<?php require_once '../../layout/inc_head.php' ?>
<link href="../../layout/photodb.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.6.1/dijit/themes/tundra/tundra.css"/>
<style type="text/css">
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
<?php if (!$showMap) { ?>
<div class="toolbar">
<div class="pagingBar">
<div class="barTxt"><?php echo $numRec.' Foto'.($numRec > 1 ? 's' : ''); ?></div>
<div class="barVertSeparator"></div>
<?php echo $mRecPp->render(); ?>
<div class="barTxt">pro Seite</div>
<div class="barVertSeparator"></div>
<?php echo $pagedNav->printNav(); ?>
</div>
<div class="optionBar">
<div class="barTxt">Sortierung</div>
<?php echo $mSort->render(); ?>
<div class="barVertSeparator"></div>
<div class="barTxt">Filter</div>
<?php echo $mQuality->render(); ?>
<div class="barVertSeparator"></div>
<div id="showMap"></div>
</div>
</div>
<?php } else { ?>
<div id="map"></div>
<?php } if (!$showMap) { ?>
<div class="clearFix"><?php !is_null($arrData) ? renderData($db, $arrData) : 0; ?></div>
<div class="toolbar">
<div class="pagingBar">
<div class="barTxt"><?php echo $numRec.' Foto'.($numRec > 1 ? 's' : ''); ?></div>
<div class="barVertSeparator"></div>
<?php echo $mRecPp->render(); ?>
<div class="barTxt">pro Seite</div>
<div class="barVertSeparator"></div>
<?php echo $pagedNav->printNav(); ?>
</div>
</div>
<?php } ?>
<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var djConfig = {
	parseOnLoad: true,
	isDebug: false,
	locale: '<?php echo $locale = $web->getLang(); ?>',
	useCommentedJson: true
};
</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dojo/dojo.js"></script>
<script type="text/javascript">
dojo.require('dojo.io.script');
dojo.require("dijit.form.Button");
dojo.require("dijit.Menu");

var slide = {
	/**
	 * Show slide in full size centered on screen.
	 * The centering and filling the screen with black is done through css
	 * by imitating a table row with one cell in it.
	 * @param {object} thumbnail HTMLImgElement
	 * @param {integer} width width of image
	 * @param {integer} height height of image
	 */
	showFull: function(thumbnail, width, height) {
		var el = dojo.create('div', null, dojo.body());
		var Tbl = dojo.create('div', {
			innerHTML: '<div><img src="' + thumbnail.src + '" alt="Foto"><br/><span class="SlideFullScreenAuthor">Foto Simon Speich, www.speich.net</span></div>'
		}, el, 'first');
		dojo.style(el, 'opacity', 0);
		dojo.addClass(el, 'slideFullScreen');
		dojo.fadeIn({
			node: el
		}).play();
		dojo.connect(el, 'onclick', el, function() {
			var args = {
				node: el,
				duration: 400,
				onEnd: function() {
					dojo.destroy(el);
				}
			};
			dojo.fadeOut(args).play();
		}, false);
	}
};

var app = {
	map: null,
	mapKey: "<?php echo $gMapKey; ?>",
	mapLat: 46.8189,	// initial map coordinates
	mapLng: 8.2246,
	mapZoomLevel: 2,	// initial zoom level of map

	manager: null,
	managerMaxZoom: 13,
	managerGridSize: 40,

	qual: 3,

	/**
	 * Load the google maps api dynamically
	 */
	loadMapJs: function(mapKey) {
		google.load("maps", "2.0", {
			"callback": dojo.hitch(this, this.initMap)
		});
	},

	loadClustererJs: function() {
			// load and init markercluster
		// note: google maps has to be loaded before loading MarkerManager
		return dojo.io.script.get({
			url: '../../library/gmaps/markerclusterer/src/markerclusterer.js',
			checkString: 'MarkerClusterer',
			load: dojo.hitch(this, this.initClusterer),
			error: function(err) {
				alert(err);
			}
		});
	},

	// Define rating list to add to map
	initRatingList: function(align, posX, posY) {
		var RatingList = function() {};

		RatingList.prototype = new GControl();

		RatingList.prototype.initialize = function(map) {
			var menu = new dijit.Menu({
	       style: "display: none;"
	    });
	    var mItem1 = new dijit.MenuItem({
	    	iconClass: "mItemIconStar1",
	      onClick: function() {
					app.qual = 1;
					button.attr('iconClass', this.iconClass);
					dojo.style(dojo.byId('ratingButton_label'), 'width', '32px');
					dojo.publish('ratingSelected');
				}
	    });
	    var mItem2 = new dijit.MenuItem({
				iconClass: "mItemIconStar2",
	      onClick: function() {
					app.qual = 2;
	      	button.attr('iconClass', this.iconClass);
					dojo.style(dojo.byId('ratingButton_label'), 'width', '16px');
					dojo.publish('ratingSelected');
	    	}
	    });
			var mItem3 = new dijit.MenuItem({
				iconClass: "mItemIconStar3",
	     	onClick: function() {
					app.qual = 3;
	     		button.attr('iconClass', this.iconClass);
					dojo.style(dojo.byId('ratingButton_label'), 'width', '0px');
					dojo.publish('ratingSelected');
	    	}
	    });
		  var button = new dijit.form.DropDownButton({
	      name: 'ratingButton',
				iconClass: 'mItemIconStar' + app.qual,
	      dropDown: menu,
	      id: 'ratingButton'
	    });
			var w = (app.qual == 3 ? 0 : (app.qual == 2 ? 16 : 32));
			menu.addChild(mItem1);
			menu.addChild(mItem2);
			menu.addChild(mItem3);
		  map.getContainer().appendChild(button.domNode);
			dojo.style(dojo.byId('ratingButton_label'), 'width', w + 'px');
		  return button.domNode;
		};

		RatingList.prototype.getDefaultPosition = function() {
	  	return new GControlPosition(align, new GSize(posX, posY));
		};
		return new RatingList();
	},

	initClusterer: function() {
		var self = this;
		var clusterOpt = {
			maxZoom: this.managerMaxZoom,
			gridSize: this.managerGridSize
		};
		this.manager = new MarkerClusterer(this.map, null, clusterOpt);
		this.manager.markerHash = {};				// remember which markers were already added to the map
		this.manager.loadedBounds = {};			// remember which markers were already loaded from sqlite
		this.manager.zoomLevel = this.map.getZoom();	// remember zoom level when loading marker data
		this.loadMarkerData();
		// load marker data on every drag or on zoom
		// e.g. fired when the change of the map view ends.
		GEvent.addListener(this.map, 'dragend', function() {
			if (self.checkCaching() === false) {
				self.loadMarkerData();
			}
		});
		// set manager zoomlevel for caching check
		GEvent.addListener(this.map, 'zoomend', function(oldLevel, newLevel) {
			if (this.getZoom() < self.manager.zoomLevel) {	// always reload on zoom out
				self.loadMarkerData();
			}
			self.manager.zoomLevel = newLevel;
		});
		// load map data when rating is changed
		dojo.subscribe('ratingSelected', this, function() {
			this.manager.clearMarkers();	// this is not enougth, manager has to complete be reset. Is this a bug?
			this.manager = new MarkerClusterer(this.map, null, clusterOpt);
			this.manager.markerHash = {};
			this.manager.loadedBounds = [];
			this.loadMarkerData();
		})
	},

	initResultButton: function(align, posX, posY) {
			// Define button show photos to add to map
		var ResultButton = function ResultButton() {};

		ResultButton.prototype = new GControl();

		ResultButton.prototype.initialize = function(map) {
			var button = new dijit.form.Button({
	      name: 'showResult',
				id: 'showResult',
				iconClass: 'buttShowResult',
				label: 'Fotos anzeigen',
				onClick: dojo.hitch(map, function() {
					var bounds = map.getBounds();
					var point = bounds.getSouthWest();
					var lat1 = point.lat().toFixed(6);
					var lng1 = point.lng().toFixed(6);
					point = bounds.getNorthEast();
					var lat2 = point.lat().toFixed(6);
					var lng2 = point.lng().toFixed(6);
					var q = {};
					q.lat1 = lat1;
					q.lat2 = lat2;
					q.lng1 = lng1;
					q.lng2 = lng2;
					q.showMap = 0;
					q.qual = app.qual;
					q = dojo.objectToQuery(q);
					window.location.href = 'photo-mapsearch.php' + '?' + q;
				})
			});
			map.getContainer().appendChild(button.domNode);
			return button.domNode;
		};

		ResultButton.prototype.getDefaultPosition = function() {
			return new GControlPosition(align, new GSize(posX, posY));
		};

		return new ResultButton();
	},

	/**
	 * Inits the map as a callback from the loader.
	 */
	initMap: function() {
		var el = dojo.byId('map');
		this.map = new GMap2(el, {
			draggableCursor: 'pointer',
			draggingCursor: 'move'
		});
		var ratingList = this.initRatingList(G_ANCHOR_TOP_LEFT, 120, 7);
		var resultButton = this.initResultButton(G_ANCHOR_TOP_LEFT, 208, 7);
		this.map.addControl(new GMapTypeControl());
		this.map.addControl(new GSmallMapControl());
		this.map.addControl(ratingList);
		this.map.addControl(resultButton);
		this.map.enableScrollWheelZoom();
		this.map.enableContinuousZoom();
		this.map.setMapType(G_SATELLITE_MAP);

		this.loadClustererJs();

		// center map with posted coords
		var q = dojo.queryToObject(window.location.search.replace('?', ''));
		if (q.lat1 && q.lat2 && q.lng1 && q.lng2) {
			var bounds = new GLatLngBounds(new GLatLng(q.lat1, q.lng1), new GLatLng(q.lat2, q.lng2));
			var zLevel = this.map.getBoundsZoomLevel(bounds);
			this.map.setCenter(bounds.getCenter(), zLevel);
		}
		// default: center to switzerland
		else {
			var coord = new GLatLng(this.mapLat, this.mapLng);
			this.map.setCenter(coord, this.mapZoomLevel);
		}
		dojo.style(el, 'visibility', 'visible');
	},

	/**
	 * load data either when current center of viewport is outside last viewport
		 or zoomlevel is smaller than last zoomlevel
	 * @param {Object} map
	 * @param {Object} manager
	 */
	checkCaching: function() {
		// check if data already previously loaded
			var center = this.map.getCenter();
			var b = this.manager.loadedBounds;
			return dojo.some(b, function(bounds) {
				if (bounds.containsLatLng(center)) {
					return true;
				}
			});

	},

	/**
	 * Returns the number of items in the hash.
	 * @param {object} hash
	 * @return integer
	 */
	getNumItem: function(hash) {
		var i = 0;
		for (var key in hash) {
			i++;
		}
		return i;
	},

	/**
	 * Load points (images) for marker manager
	 * @return true|deferred
	 */
	loadMarkerData: function() {
		var bounds = this.map.getBounds();
		this.manager.loadedBounds[this.manager.loadedBounds.length] = bounds;	// cache load
		var point = bounds.getSouthWest();
		var lat1 = point.lat().toFixed(6);
		var lng1 = point.lng().toFixed(6);
		point = bounds.getNorthEast();
		var lat2 = point.lat().toFixed(6);
		var lng2 = point.lng().toFixed(6);
		var q = {};
		q.lat1 = lat1;
		q.lat2 = lat2;
		q.lng1 = lng1;
		q.lng2 = lng2;
		q.qual = this.qual;
		q = dojo.objectToQuery(q);
		var args = {
			url: 'photofnc.php?fnc=loadGMapsData&' + q,
			handleAs: 'json',
			load: dojo.hitch(this, this.createMarkers),
			error: function(response) {
				console.debug(response);
			}
		};
		dojo.xhrGet(args);
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
				console.debug('adding markers', markers)
				this.manager.addMarkers(markers);
			}
		}
		return response;
	},

	/**
	 * Show thumbnail on map.
	 * @param {GMap2} map
	 * @param {GMarker} marker
	 * @param {Object} item
	 */
	showThumb: function(marker, item) {
		var bounds = this.map.getBounds();
		var point = bounds.getSouthWest();
		var lat1 = point.lat().toFixed(6);
		var lng1 = point.lng().toFixed(6);
		point = bounds.getNorthEast();
		var lat2 = point.lat().toFixed(6);
		var lng2 = point.lng().toFixed(6);
		var q = {};
		q.lat1 = lat1;
		q.lat2 = lat2;
		q.lng1 = lng1;
		q.lng2 = lng2;
		q.imgId = item.id;
		q.qual = this.qual;
		q.showMap = 1;	// required for back page on detail page
		q = dojo.objectToQuery(q);
		var div = dojo.create('div', {
			innerHTML: '<img src="' + marker.getIcon().image + '"/><br/><a href="photo-detail.php?' + q + '">Details</a>'
		});
		marker.openInfoWindow(div);
	}
};

dojo.addOnLoad(function()	{
	var q = dojo.queryToObject(window.location.search.replace('?', ''));
	if (q.qual) {
		app.qual = q.qual;
	}
	if (!q.showMap) {
		q.showMap = '1';
	}
	if (q.showMap === '1') {
		dojo.style('map', 'display', 'block');
		var script = document.createElement("script");
		script.src = 'http://www.google.com/jsapi?key=' + app.mapKey + '&callback=app.loadMapJs';
		script.type = "text/javascript";
		document.getElementsByTagName("head")[0].appendChild(script);
	}
	else {
		var button = new dijit.form.Button({
	 		id: 'buttShowMap',
			iconClass: 'buttShowMap',
			label: 'Karte anzeigen',
			onClick: function() {
				q.showMap = '1';
				q = dojo.objectToQuery(q);
				window.location.href = 'photo-mapsearch.php' + '?' + q;
			}
		});
		dojo.byId('showMap').appendChild(button.domNode);
	}
});

</script>
</body>
</html>