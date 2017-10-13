/** @module NAFIDAS-vis/configChart */
define(function() {
	"use strict";

	return /** @alias module:NAFIDAS-vis/configChart */{

		/**
		 * @property {Object} axes chart axes configuration
		 * @property {Object} axes.xaxis x-axis
		 * @property {jqplot.CanvasAxisLabelRenderer} axes.xaxis.labelRenderer label renderer
		 * @property {Object} axes.xaxis.labelOptions label configuration
		 * @property {Boolean} axes.xaxis.labelOptions.enableFontSupport enable font support?
		 * @property {Object} seriesDefaults contains default values for all charts
		 */
		axes: {
			xaxis: {
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				labelOptions: {
					enableFontSupport: true
				},
				tickOptions: {
					formatString: '%s',
					showGridline: false
				}
			},
			yaxis: {
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				labelOptions: {
					enableFontSupport: true
				},
				tickOptions: {
					formatString: '%d',
					showGridline: false
				}
			}
		},
		seriesDefaults: {
			shadow: false,
			color: '#CC66CC',
			pointLabels: {
				show: false,
				darkColor: '#006666',
				brightColor: '#ffffff'
			}
		},
		cursor: {
			show: false,
			showTooltip: false,
			zoom: false
		},
		grid: {
			background: '#f8f8f3'
		},
		highlighter: {
			show: true,
			tooltipLocation: 'n',
			tooltipAxes: 'both'
		}
	};
});
