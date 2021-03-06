import {GoogleMapLoader} from '../../scripts/js/GoogleMapLoader.min.js';
import {key} from '../../library/GoogleMapKey.js';

let loader = new GoogleMapLoader(key);

loader.load().then(() => {
  let gmaps = google.maps,
    map, mapOptions,
    marker,
    config = JSON.parse(document.body.dataset.config);

  if (config.lat && config.lng) {
    mapOptions = {
      center: new gmaps.LatLng(config.lat, config.lng),
      zoom: 5,
      mapTypeId: gmaps.MapTypeId.HYBRID
    };
    map = new gmaps.Map(document.getElementById('map'), mapOptions);
    marker = new gmaps.Marker({
      map: map,
      position: new gmaps.LatLng(config.lat, config.lng)
    });
  }
});