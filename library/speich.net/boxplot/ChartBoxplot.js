/**
 * @module NAFIDAS-vis/ChartBoxplot
 */
define([
	'dojo/_base/declare',
	'boxplot/BoxplotRenderer',
	'boxplot/Chart',
	'jqplot/plugins/jqplot.categoryAxisRenderer'
], function(declare, BoxplotRenderer, Chart) {

	/**
	 * Draws a boxplot chart.
	 * @class module:NAFIDAS-vis/ChartBoxplot
	 * @mixes module:NAFIDAS-vis/BoxplotRenderer
	 */
	return declare(Chart, /** @lends module:NAFIDAS-vis/ChartBoxplot */ {

		constructor: function() {
			this.domNode.classList.add('boxplot'); // do not overwrite inherited css class 'vizChart'

			$.extend(true, this.options, {
				seriesDefaults: {
					renderer: BoxplotRenderer,
					rendererOptions: {
						candleStick: true,
						bodyWidth: 40
					}
				},
				axesDefaults: {
					pad: 0
					/*tickOptions: {
						showMark: false,
						showLabel: false
					}*/
				},
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						tickOptions: {
							showMark: false,	// bug: only gets applied after redraw
							showLabel: false	// bug: only gets applied after redraw in _create() below
						}
					},
					yaxis: {
						tickOptions: {
							showMark: true,
							showLabel: true,
							showGridline: true
						}
					}
				},
				highlighter: {
					show: false
				}
			});
		},

		/**
		 * Sets the options property.
		 * @param {Object} data
		 */
		_updateOptions: function(data) {
			this.options.axes.xaxis.label = data.xLabel;
		},

		/**
		 * Creates the chart
		 * @param {Object} data
		 */
		_create: function(data) {
			data.series = [
				[['D7', data.q25, data.max, data.min, data.q75, data.median]]	// cat, 25%, max, min, 75%
			];
			this.plot = $.jqplot(this.id, data.series, this.options);
		}
	});
});