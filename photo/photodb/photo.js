import PhotoSwipeLightbox from '../../library/photoswipe/photoswipe-lightbox.esm.js';
import typeahead from '../../library/typeahead-standalone/package/dist/typeahead-standalone.es.mjs';

const lightbox = new PhotoSwipeLightbox({
    gallery: '#slides',
    children: '.slideCanvas a',
    thumbSelector: 'a',
    pswpModule: () => import('../../library/photoswipe/photoswipe.esm.js')
});
lightbox.init();

const currentLang = document.documentElement.lang || 'de';
const searchInput = document.getElementById('q');
const typeaheadInstance = typeahead({
    input: searchInput,
    limit: 12,
    highlight: true,
    source: {
        remote: {
            url: `/scripts/php/controller/keywords.php?lang=${currentLang}&q=%QUERY`,
            wildcard: '%QUERY'
        },
        keys: ['keyword']
    }
});

// Submit the form immediately when a suggestion is clicked
searchInput.addEventListener('typeaheadSelect', (ev) => {
    // The input value is automatically updated by the library before this fires
    ev.target.closest('form').submit();
});