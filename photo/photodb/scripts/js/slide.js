define([
	'dojo/_base/fx',
	'dojo/_base/window',
	'dojo/query',
	'dojo/on',
	'dojo/dom-style',
	'dojo/dom-geometry'],
function(fx, win, query, on, domStyle, domGeometry) {

	var d = document;

	return {
		fullScreenCont: null,
		fullScreen: null,
		navClose: null,
		navPrev: null,
		navNext: null,
		inserted: false,
		imgMap: {},

		init: function(containerName) {
			var cont = d.getElementById(containerName);

			this.fullScreenCont = cont;
			this.fullScreen = query('.slideFullScreen', cont)[0];
			this.navClose = query('.slideNavClose', cont)[0];
			this.navPrev = query('.slideNavPrev', cont)[0];
			this.navNext = query('.slideNavNext', cont)[0];

			on(this.fullScreenCont, 'click', this.hide);
		},

		hide: function() {
			fx.fadeOut({
				node: this,
				duration: 500,
				onEnd: function() {
					// TODO use class instead of style
					win.body().style.overflow = 'auto';
					this.node.style.display = 'none';
				}
			}).play();
		},

		/**
		 * Show image in full size centered on screen.
		 * The centering and filling the screen with background is done through css
		 * by imitating a table row with one cell in it.
		 * @param {string} src image source
		 * @param {number} w image width
		 * @param {number} h image height
		 */
		showFull: function(src, w, h) {
			var self = this,
				scrollY = domGeometry.position('layoutTop01', false).y,
				img = new Image();

			// remove previous image
			if (this.inserted) {
				this.fullScreen.removeChild(this.fullScreen.firstChild);
			}

			domStyle.set(win.body(), 'overflow', 'hidden');
			domStyle.set(this.fullScreen, {
				width: w + 'px',
				height: h + 'px'
			});
			domStyle.set(this.fullScreenCont, {
				display: 'block',
				top: scrollY * -1 + 'px'
			});

			// set before setting src
			on(img, 'load', function() {
				fx.fadeIn({
					node: self.fullScreenCont
				}).play();
			});

			img.src = src;
			img.alt = 'photo';

			this.fullScreen.insertBefore(img, this.fullScreen.firstChild);
			this.inserted = true;
		},

		/**
		 * Show next slide
		 */
		showNext: function() {
			fx.fadeOut({
				node: this.fullScreen,
				onEnd: function() {}
			}).play();
		},

		/**
		 * Show previous slide
		 */
		showPrevious: function() {
		}
	};
});
