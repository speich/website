import { GoogleMapLoader } from '../../scripts/js/GoogleMapLoader.min.js';
import MarkerClusterer from '../../library/node_modules/@google/markerclustererplus/dist/markerclustererplus.esm.js';
import { key } from '../../library/GoogleMapKey.js';

let gmaps, mapApp, d = document,
  /**
   * Shortcut for document.getElementById
   * @param {String} id
   */
  byId = function(id) {
    return d.getElementById(id);
  };

mapApp = {
  /** @property {URL} url of the map application */
  pageUrl: null,
  hrefPhoto: null,
  hrefPhotoDetail: null,
  target: 'controller.php',
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
    imagePath: '../../library/node_modules/@google/markerclustererplus/dist/' + MarkerClusterer.IMAGE_PATH
  },
  clusterer: null,

  /**
   * Creates and returns an image marker.
   * @param {google.maps.Map} map
   * @param {google.maps.Marker} marker
   * @param {String} data json
   * @return {google.maps.InfoWindow}
   */
  createInfoWindow: function(map, marker, data) {
    let infoWindow, img = new Image();

    img.src = 'images/' + data.img;
    //this.pageUrl.searchParams.set('imgId', data.id);
    infoWindow = new gmaps.InfoWindow();

    img.onload = function() {
      let html, dim = this.resizeImage(img, 60);

      // now that we now, we also set correct aspect of thumbnail
      marker.setIcon({
        anchor: new gmaps.Point(21, 21),
        scaledSize: new gmaps.Size(dim.w, dim.h),
        url: img.src
      });
      dim = this.resizeImage(img, 600);
      html = '<img src="' + img.src + '" alt="photo" width="' + dim.w + '" height="' + dim.h + '"><br><a href="' + this.hrefPhotoDetail + '">Details</a>';
      infoWindow.setOptions({
        content: html,
        maxWidth: dim.w + 20
      });
      infoWindow.open(map, marker);
    }.bind(this);

    return infoWindow;
  },

  /**
   *
   * @param {HTMLImageElement} img
   * @param {Number} max maximum width or height
   */
  resizeImage: function(img, max) {
    let w = img.width, h = img.height,
      r = h / w;

    if (w > h) {
      w = max;
      h = max * r;
    } else if (w < h) {
      w = max * r;
      h = max;
    } else {
      w = max;
      h = max;
    }

    return {
      w: w,
      h: h
    };
  },

  initLinks: function() {
    let pageExt = (document.documentElement.lang.indexOf('de') > -1 ? '' : '-en') + '.php';

    this.pageUrl = new URL(window.location);
    this.hrefPhoto = 'photo' + pageExt + this.pageUrl.search;
    this.hrefPhotoDetail = 'photo-detail' + pageExt + this.pageUrl.search;
  },

  initMap: function() {
    let bounds, ne, sw,
      mapOptions, toolbar,
      query = this.pageUrl.searchParams,
      lat1 = query.get('lat1'),
      lat2 = query.get('lat2'),
      lng1 = query.get('lng1'),
      lng2 = query.get('lng2');

    //window.onresize = this.setMapDimension;
    // this.setMapDimension();

    mapOptions = {
      center: new gmaps.LatLng(this.mapLat, this.mapLng),
      zoom: this.mapZoom,
      mapTypeId: gmaps.MapTypeId.HYBRID
    };
    this.map = new gmaps.Map(this.mapDiv, mapOptions);
    this.mapLastZoom = this.map.zoom;
    this.initMapEvents(this.map);

    // center map to coords from query string, has precedence over country
    if (lat1 && lng1 && lng2 && lng2) {
      ne = new gmaps.LatLng(lat1, lng1);
      sw = new gmaps.LatLng(lat2, lng2);
      bounds = new gmaps.LatLngBounds(sw, ne);
      this.map.fitBounds(bounds);
    }

    // zoom to passed country
    else if (query.get('country')) {
      fetch(this.target + '/country/' + query.get('country'))
        .then((response) => response.json)
        .then((results) => {
          let geocoder = new gmaps.Geocoder();

          geocoder.geocode({
            address: results[0].NameEn
          }, function(results, status) {
            if (status === gmaps.GeocoderStatus.OK) {
              this.map.setCenter(results[0].geometry.location);
              this.map.fitBounds(results[0].geometry.viewport);
            }
          });
        });
    }
    // use default map extent
    // TODO

    toolbar = d.getElementsByClassName('toolbar')[0];
    this.map.controls[gmaps.ControlPosition.TOP_RIGHT].push(toolbar);
  },

  /**
   * Initialize map events
   * @param {google.maps.Map} map
   */
  initMapEvents: function(map) {
    let self = this;

    gmaps.event.addDomListener(map, 'dragend', function() {
      self.mapLastEvent = 'dragend';
    });
    gmaps.event.addDomListener(map, 'zoom_changed', function() {
      self.mapLastEvent = 'zoom_changed';
    });
    gmaps.event.addDomListener(map, 'tilesloaded', function() {
      /*d.getElementsByClassName('mRating')[0].style.display = 'block';
      byId('showPhotos').style.display = 'block';*/
    });
    gmaps.event.addDomListener(map, 'idle', function() {
      let url = self.updateQueryBounds(map);
      self.updateUrl(url);
      if (self.mapLastEvent === 'dragend' || !self.mapLastEvent || map.zoom < self.mapLastZoom) {
        // update only on zoom out or drag
        self.updateMarkers();
      }
      self.mapLastZoom = map.zoom;
    });
  },

  /**
   * Update urls of map controls.
   * @param {URL} url
   */
  updateUrl: function(url) {
    let nl, a, href,
      q = url.search;

    // also update link of button to display photos
    a = document.querySelector('#showPhotos a');
    href = new URL(a.href);
    a.href = href.pathname + q;

    nl = d.getElementsByClassName('mRating')[0].getElementsByTagName('a');
    for (let i = 0, len = nl.length; i < len; i++) {
      nl.href = window.location.pathname + q;
    }
  },

  /**
   * Update query string to represent current map extent (bounds)
   * @param {google.maps.Map} map
   * @return {URL}
   */
  updateQueryBounds: function(map) {
    let q = this.pageUrl.searchParams,
      hist = window.history,
      b = map.getBounds(),
      ne = b.getNorthEast(),
      sw = b.getSouthWest();

    q.set('lat1', ne.lat());
    q.set('lng1', ne.lng());
    q.set('lat2', sw.lat());
    q.set('lng2', sw.lng());

    if (hist) {
      hist.pushState({}, 'map extent', this.pageUrl.href);
    }

    return this.pageUrl;
  },

  initMarkerClusterer: function() {
    this.clusterer = new MarkerClusterer(this.map, null, this.mcOptions);
  },

  /**
   * Update the markers on the map.
   */
  updateMarkers: function() {
    this.loadMarkerData()
      .then((data) => {
        if (data.length > 0) {
          this.addMarkers(data);
        }
      });
  },

  /**
   * Add markers to the map.
   * @param {Array} data marker data
   */
  addMarkers: function(data) {
    // TODO: respect quality settings, otherwise number of photos returned for clustering are wrong
    let mc = this.clusterer;

    data.forEach(function(item) {
      let marker;

      if (!this.isMarkerIdAlreadyAdded(item.id)) {
        marker = this.createMarker(this.map, item);
        mc.addMarker(marker);
      }
    }, this);
  },

  /**
   * Determines if a marker has already been added to the cluster.
   *
   * @param id The id of the marker to check.
   * @return True if the marker has already been added.
   */
  isMarkerIdAlreadyAdded(id) {
    let mc = this.clusterer;

    for (let i = 0; i < mc.markers_.length; i++) {
      if (id === mc.markers_[i].id) {
        return true;
      }
    }

    return false;
  },

  /**
   * Requests marker data from server according to the current boundaries defined in the query.
   * If the query string does not have the bounds (e.g. lat1, lng1, lat2, lng2), then initial map coordinates are used.
   * @return {Promise}
   */
  loadMarkerData: function() {

    return fetch(this.target + '/marker/' + this.pageUrl.search)
      .then((response) => response.json());
  },

  /**
   * Creates and returns an image marker.
   * @param {google.maps.Map} map
   * @param {String} data json
   * @return {google.maps.Marker}
   */
  createMarker: function(map, data) {
    let marker,
      infoWindow,
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
      position: latLng,
      title: 'photo'
    });

    gmaps.event.addListener(marker, 'click', function() {
      if (infoWindow) {
        infoWindow.open(map, marker);
      } else {
        infoWindow = this.createInfoWindow(map, marker, data);
      }
    }.bind(this));

    return marker;
  },

  displayElement: function(selector) {
    let el;

    el = d.querySelector(selector);
    el.classList.remove('hidden');
  },

  hideElement: function(selector) {
    let el;

    el = d.querySelector(selector);
    el.classList.add('hidden');
  },

  init: function() {
    let loader = new GoogleMapLoader(key);

    loader.load().then(() => {
      gmaps = google.maps;
      this.displayElement('.toolbar');
      this.initLinks();
      this.initMap();
      this.initMarkerClusterer();
      this.updateMarkers();
    });
  }
};

mapApp.init();