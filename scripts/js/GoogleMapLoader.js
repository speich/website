/**
 * Loader for the Google Maps API
 * @exports {GoogleMapsLoader}
 * @see https://github.com/stephenscaff/google-maps-es6
 */
export class GoogleMapLoader {

  /**
   * Constructor
   * @property {string} key Google maps key
   */
  constructor(key) {
    // api key for google maps
    this.apiKey = key;

    // set a globally scoped callback if it doesn't already exist
    if (!window.GoogleMapLoader) {
      this.callbackName = 'GoogleMapLoader.mapLoaded';
      window.GoogleMapLoader = this;
      window.GoogleMapLoader.mapLoaded = this.isMapLoaded.bind(this);
    }
  }

  /**
   * Load the Google Maps API
   * Creates the script element with google maps api url, containing the api key and callback for map init.
   * @return {promise}
   */
  load() {
    if (!this.promise) {
      this.promise = new Promise(resolve => {
        this.resolve = resolve;
        if (typeof window.google === 'undefined') {
          let script = document.createElement('script');

          script.src = `//maps.googleapis.com/maps/api/js?key=${this.apiKey}&callback=${this.callbackName}`;
          script.async = true;
          document.body.append(script);
        } else {
          this.resolve();
        }
      });
    }

    return this.promise;
  }

  /**
   * Globally scoped callback for the map loaded
   * Global callback for loaded/resolved map instance.
   */
  isMapLoaded() {
    if (this.resolve) {
      this.resolve();
    }
  }
}
