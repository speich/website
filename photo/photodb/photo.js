import PhotoSwipeLightbox from '../../library/photoswipe/5.4.3/photoswipe-lightbox.esm.js';

const lightbox = new PhotoSwipeLightbox({
  gallery: '#slides',
	children: '.slideCanvas a',
  thumbSelector: 'a',
  pswpModule: () => import('../../library/photoswipe/5.4.3/photoswipe.esm.js')
});

lightbox.init();