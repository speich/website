/**
 * Base class to draw charts using the jqplot library.
 * // TODO: adjust padding of bars in case of chart width is small
 * @module NAFIDAS-vis/Chart
 */
define([
	'require',
	'dojo/_base/declare',
	'dojo/_base/lang',
	'dojo/dom-construct',
	'dojo/topic',
	'jqplot/plugins/jqplot.canvasTextRenderer',
	'jqplot/plugins/jqplot.canvasAxisLabelRenderer',
	'jqplot/plugins/jqplot.highlighter'
], function(require, declare, lang, domConstruct, topic) {
	'use strict';

	var config, idx = 0;

	require(['boxplot/configChart'], function(configChart) {
		// canvasAxisLabelRenderer has to be loaded first
		config = configChart;
	});

	/**
	 * Generates an id for the chart if one is not provided.
	 * @returns {string}
	 */
	function generateId() {
		return "chart-" + idx++;
	}

	/**
	 * @class module:NAFIDAS-vis/Chart
	 * @param {HTMLElement|null} containerNode
	 */
	return declare(null, /** @lends module:NAFIDAS-vis/Chart# */ {

		title: '',
		plot: null,
		options: null,
		limit: 30,	// number of data points before changing chart style
		checkLimit: true,
		domNode: null,
		varInfo: null, // holds information about variable to chart
		className: 'vizChart',

		/**
		 * Initialize chart options
		 * @param params
		 * @param {string|HTMLElement} srcNodeRef node or id referencing a node
		 */
		constructor: function(params, srcNodeRef) {

			var domNode = srcNodeRef || document.createElement('div');

			if (typeof domNode === 'string') {
				domNode = document.getElementById(srcNodeRef);
			}

			params = params || {};
			lang.mixin(this, params);

			this.id = domNode.id || this.id || generateId();
			this.domNode = domNode;
			this.domNode.id = this.id;
			this.domNode.classList.add(this.className);
			this.options = $.extend(true, {}, config);
			this.options.title = params.title || null;

			$.jqplot.config.enablePlugins = true;
		},

		/**
		 * Initialize chart events.
		 */
		_initEvents: function() {
			var self = this, timer;

			$(window).on('resize', function() {
				if (timer) {
					window.clearTimeout(timer);
				}
				timer = window.setTimeout(function() {
					var chart = self.plot;

					// fix for bar widths are not reset
					$.each(chart.series, function(index, series) {
						series.barWidth = undefined;
					});
					chart.replot({
						resetAxes: true
					});
				}, 300);
			});

			// notify app when done rendering
			$.jqplot.postDrawHooks.push(lang.hitch(this, function() {
				topic.publish('chart/postCreate', this);
			}));
		},

		/**
		 * Update chart options from loaded data.
		 * @param {Object} data
		 * @private
		 */
		_updateOptions: function(data) {
			var options = {
				series: []
			};

			$.extend(true, this.options, {
				axes: {
					xaxis: {
						label: data.xLabel,
						min: (data.min > 0 ? 0 : data.min),
						max: data.max
					},
					yaxis: {
						label: data.yLabel,
						min: data.yMin,
						max: data.yMax
					}
				}
			});

			// adjust charts according to number of data points
			for (var i = 0, len = data.series.length; i < len; i++) {
				if (data.series[i].length < this.limit) {
					options.series.push({
						markerOptions: {
							shadow: true,
							size: 9
						},
						highlighter: {
							show: false
						}
					});

					$.extend(true, this.options, options);
				}
			}

			// only one series and not many datapoints -> we can always show data point labels
			if (data.series.length === 1 && data.series[0].length < this.limit) {
				this.options.series[0].pointLabels = {show: true};
			}
		},

		/**
		 * Create chart.
		 * @param {Object} [data] chart options and data points
		 */
		create: function(data) {
			data = data || this.data;
			this.domNode.innerHTML = '';  // remove loading msg
			this._initEvents();
			this._updateOptions(data);
			this._create(data);
		},

		_create: function(data) {
			this.plot = $.jqplot(this.id, data.series, this.options);
			// TODO: figure out why tickOptions.showMark and tickOptions.showLabel are not respected and we need to call replot()
			this.plot.replot();
		},

		getSizeLimit: function() {
			var w = this._width,
				numDataPoints = this.data[0].length;

			return parseInt(w / numDataPoints, 10);
		},

		/**
		 * Destroys the DOM node and it's children.
		 */
		destroy: function() {
			domConstruct.destroy(this.domNode);
		}
	});
});