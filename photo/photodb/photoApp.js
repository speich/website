define(['dojo/query', 'dojo/io-query', '../../photo/photodb/scripts/js/slide.js'], function(query, ioQuery, slide) {
	return {
		init: function() {
			query('.slideCanvas a:first-child, .slideText a:first-child', document.getElementById('layoutMain')).on('click', function(evt) {
				var src = evt.target.href,
				q = ioQuery.queryToObject(src.slice(src.lastIndexOf('?') + 1));

				evt.preventDefault();

				slide.init('slideFullScreenCont');
				slide.showFull(src, q.w, q.h);
			});
		}
	};
});
