require([
	'../../library/PhotoSwipe/dist/photoswipe',
	'../../library/PhotoSwipe/dist/photoswipe-ui-default'
], function(PhotoSwipe, PhotoSwipeUI_Default) {
	var gallery, options, slides, items = [],
		domNodeGallery = document.getElementById('gallery'),
		domNodeSlides = document.getElementById('slides'),

		/**
		 * Return the index of a child element.
		 * @param {HTMLElement} element
		 */
		getElementIndex = function(element) {
			var els = element.parentNode.children;	// note: children contains only elements :-)

			for (var i = 0, len = els.length; i < len; i++) {
				if (els[i] === element) {
					return i;
				}
			}
			return i;
		};

	// create gallery items
	slides = domNodeSlides.getElementsByClassName('slideCanvas');
	for (var i = 0, len = slides.length; i < len; i++) {
		items[i] = JSON.parse(slides[i].dataset.slide);
		items[i].src = slides[i].getElementsByTagName('a')[0].href;
	}

	// start slideshow at clicked slide using event delegation
	domNodeSlides.addEventListener('click', function(evt) {
		var parent = evt.target.parentNode;
		if (evt.target.tagName.toLowerCase() === 'a' && parent.classList.contains('slideCanvas')) {
			evt.preventDefault();
			options = {
				index: getElementIndex(parent.parentNode),
				shareButtons: null
			};
			gallery = new PhotoSwipe(domNodeGallery, PhotoSwipeUI_Default, items, options);
			gallery.init();
		}
	});
});