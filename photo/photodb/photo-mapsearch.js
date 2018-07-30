require([
	'dojo/_base/lang',
	'dojo/_base/array',
	'dojo/window',
	'dojo/request/xhr',
	'dojo/dom-style',
	'dojo/dom-geometry',
	'dojo/io-query',
	'gmap/gmapLoader!https://maps.google.com/maps/api/js',
	'/library/gmap/markerclustererplus/src/markerclusterer_packed.js'
], function(lang, array, win, xhr, domStyle, domGeometry, ioQuery) {

	var mapApp, d = document,
		/**
		 * Shortcut for document.getElementById
		 * @param {String} id
		 */
		byId = function(id) {
			return d.getElementById(id);
		},
		gmaps = google.maps;

	mapApp = {
		queryObj: ioQuery.queryToObject(window.location.search.replace('?', '')),
		map: null,
		mapOptions: {},
		mapLat: 45,	// initial map coordinates
		mapLng: 12,
		mapZoom: 5,	// initial map zoom
		mapLastZoom: null,
		mapLastEvent: null,
		mapDiv: byId('map-canvas'),
		mcOptions: {
			maxZoom: 11,
			imagePath: '/library/gmap/markerclustererplus/images/m'
		},
		clusterer: null,
		target: 'controller.php',

		/**
		 * Fit map to window size.
		 */
		setMapDimension: function() {
			var winDim = win.getBox(),
			cont = domGeometry.position(byId('layoutMain')),
			footer = domGeometry.position(byId('layoutFooterCont'));

			// set map dimensions
			domStyle.set(byId('map-canvas'), {
				width: winDim.w - cont.x + 'px',
				height: winDim.h - cont.y - footer.h + 'px'
			});
		},

		/**
		 * Requests marker data from server.
		 * @return {dojo/promise}
		 */
		loadMarkerData: function() {
			var q = ioQuery.objectToQuery(this.queryObj);

			q = (q === '' ? '' : '?') + q;
			return xhr.get(this.target + '/marker/' + q, {
				handleAs: 'json'
			});
		},

		/**
		 * Creates and returns an image marker.
		 * @param {google.maps.Map} map
		 * @param {String} data json
		 * @return {google.maps.Marker}
		 */
		createMarker: function(map, data) {
			var marker,
				infoWindow = null,
				latLng = new gmaps.LatLng(data.lat, data.lng),
				imgUrl = 'images/' + data.img,
				image = {
					anchor: new gmaps.Point(21, 21),
					scaledSize: new gmaps.Size(40, 40),
					url: imgUrl
				};
			marker = new gmaps.Marker({
				id: data.id,
				icon: image,
				position: latLng
			});

			gmaps.event.addListener(marker, 'click', lang.hitch(this, function() {
				if (!infoWindow) {
					infoWindow = this.createInfoWindow(map, marker, data);
				}
				else {
					infoWindow.open(map, marker);
				}
			}));

			return marker;
		},

		/**
		 * Creates and returns an image marker.
		 * @param {google.maps.Map} map
		 * @param {google.maps.Marker} marker
		 * @param {String} data json
		 * @return {google.maps.InfoWindow}
		 */
		createInfoWindow: function(map, marker, data) {
			var langExt, infoWindow, img = new Image();

			img.src = 'images/' + data.img;
			this.queryObj.imgId = data.id;
			infoWindow = new gmaps.InfoWindow();
			langExt = dojo.locale !== 'de' ? dojo.locale : '';

			img.onload = lang.hitch(this, function() {
				var html, dim = this.resizeImage(img, 60);

				// now that we now, we also set correct aspect of thumbnail
				marker.setIcon({
					anchor: new gmaps.Point(21, 21),
					scaledSize: new gmaps.Size(dim.w, dim.h),
					url: img.src
				});
				dim = this.resizeImage(img, 600);
				html = '<img src="' + img.src + '" alt="photo" width="' + dim.w + '" height="' + dim.h + '"><br><a href="photo-detail' + langExt + '.php?' + ioQuery.objectToQuery(this.queryObj) + '">Details</a>'
				infoWindow.setOptions({
					content: html,
					maxWidth: dim.w + 20
				});
				infoWindow.open(map, marker);
			});

			return infoWindow;
		},

		/**
		 *
		 * @param {HTMLImageElement} img
		 * @param {Number} max maximum width or height
		 */
		resizeImage: function(img, max) {
			var w = img.width, h = img.height,
				r = h / w;

			if (w > h) {
				w = max;
				h = max * r
			}
			else if (w < h) {
				w = max * r;
				h = max;
			}
			else {
				w = h = max;
			}

			return {
				w: w,
				h: h
			}
		},

		initMap: function () {
			var map = this.map,
				bounds, ne, sw,
				mapOptions, menu,
				queryObj = this.queryObj;

			window.onresize = this.setMapDimension;
			this.setMapDimension();

			mapOptions = {
				center: new gmaps.LatLng(this.mapLat, this.mapLng),
				zoom: this.mapZoom,
				mapTypeId: gmaps.MapTypeId.HYBRID
			};
			map = this.map = new gmaps.Map(this.mapDiv, mapOptions);
			this.mapLastZoom = map.zoom;

			this.initMapEvents(map);

			// center map to coords from query string, has precedence over country
			if (queryObj.lat1 && queryObj.lng1 && queryObj.lng2 && queryObj.lng2) {
				ne = new gmaps.LatLng(queryObj.lat1, queryObj.lng1);
				sw = new gmaps.LatLng(queryObj.lat2, queryObj.lng2);
				bounds = new gmaps.LatLngBounds(sw, ne);
				map.fitBounds(bounds);
			}

			// zoom to passed country
			else if (queryObj.country) {
				xhr.get(this.target + '/country/' + queryObj.country, { handleAs: 'json' }).then(function(results) {
					var geocoder = new gmaps.Geocoder();
					geocoder.geocode({
						address: results[0].NameEn
					}, function(results, status) {
						if (status == gmaps.GeocoderStatus.OK) {
			      		map.setCenter(results[0].geometry.location);
							map.fitBounds(results[0].geometry.viewport);
						}
					});
				});
			}

			// add rating control
			menu = d.getElementsByClassName('mRating')[0];
			map.controls[gmaps.ControlPosition.TOP_RIGHT].push(menu);
			// add control to list photos
			map.controls[gmaps.ControlPosition.TOP_RIGHT].push(byId('showPhotos'));
		},

		/**
		 * Initialize map events
		 * @param {google.maps.Map} map
		 */
		initMapEvents: function(map) {
			var self = this;

			gmaps.event.addDomListener(map, 'dragend', function() {
				self.mapLastEvent = 'dragend';
			});
			gmaps.event.addDomListener(map, 'zoom_changed', function() {
				self.mapLastEvent = 'zoom_changed';
			});
			gmaps.event.addDomListener(map, 'tilesloaded', function() {
				d.getElementsByClassName('mRating')[0].style.display = 'block';
				byId('showPhotos').style.display = 'block';
			});
			gmaps.event.addDomListener(map, 'idle', function() {
				var q = self.updateQueryBounds(map);

				self.updateUrl(q);

				if (self.mapLastEvent == 'dragend' || !self.mapLastEvent || map.zoom < self.mapLastZoom) {
					// update only on zoom out or drag
					self.updateMarkers();
				}
				self.mapLastZoom = map.zoom;
			});
		},

		/**
		 * Update urls of map controls.
		 * @param {Object} query
		 */
		updateUrl: function(query) {
			var nl, q = ioQuery.objectToQuery(query);

			// also update link of button to display photos
			byId('linkShowPhotos').href = 'photo.php?' + q;

			nl = d.getElementsByClassName('mRating')[0].getElementsByTagName('a');
			for (var i = 0, len = nl.length; i < len; i++) {
				nl.href = window.location.pathname + '?' + q;
			}
		},

		/**
		 * Update query string to represent current map extent (bounds)
		 * @param {google.maps.Map} map
		 * @return {Object} query
		 */
		updateQueryBounds: function(map) {
			var q = this.queryObj,
				hist = window.history,
				b = map.getBounds(),
				ne = b.getNorthEast(),
				sw = b.getSouthWest();

			q.lat1 = ne.lat();
			q.lng1 = ne.lng();
			q.lat2 = sw.lat();
			q.lng2 = sw.lng();

			this.queryObj = q;

			if (hist) {
				hist.pushState({}, 'map extent', window.location.pathname + '?' + ioQuery.objectToQuery(q));
			}

			return q;
		},

		updateMarkers: function() {
			this.loadMarkerData().then(lang.hitch(this, this.addMarkers));
		},

		addMarkers: function(data) {
			var mc = this.clusterer;

			array.forEach(data, function(item) {
				var marker = this.createMarker(this.map, item);

				mc.addMarker(marker);
			}, this);
		},

		initMarkerClusterer: function() {
			this.clusterer = new MarkerClusterer(this.map, null, this.mcOptions);
		},

		init: function() {
			byId('loading').style.display = 'none';	// otherwise map is not placed and dimensioned correctly
			byId('mapContainer').style.display = 'block';	// otherwise map is not placed and dimensioned correctly
			this.initMap();
			this.initMarkerClusterer();
			this.updateMarkers();
		}
	};

	mapApp.init();
});