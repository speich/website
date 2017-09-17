/**
 * Dojo AMD Google Maps Loader Plugin
 *	https://gist.github.com/ca0v/7450696
 * example: require(["plugins/async!http://maps.google.com/maps/api/js?v=3&sensor=false"], function () {// can use google.maps now});
 */
define(['dojo/_base/config', 'dojo/_base/kernel'], function(config, kernel) {
	'use strict';

	var w = kernel.global,
		cb = '_googleApiLoadCallback',
		lang = config.locale;

	return {
		load: function(param, req, loadCallback) {
			if (!cb) {
				return;
			}
			w[cb] = function() {
				delete w[cb];
				cb = null;
				loadCallback();
			};

			require([param + '?key=' + config.gmapsApiKey + '&callback=' + cb + '&language=' + lang + '&v=3']);
		}
	};
});