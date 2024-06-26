import PhotoSwipeLightbox from '../../library/photoswipe/photoswipe-lightbox.esm.js';

const lightbox = new PhotoSwipeLightbox({
  gallery: '#slides',
	children: '.slideCanvas a',
  thumbSelector: 'a',
  pswpModule: () => import('../../library/photoswipe/photoswipe.esm.js')
});
lightbox.init();