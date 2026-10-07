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
    diacritics: true,
    preventSubmit: true,
    source: {
        remote: {
            url: `/scripts/php/controller/keywords.php?lang=${currentLang}&q=%QUERY`,
            wildcard: '%QUERY'
        },
        keys: ['keyword']
    },
    onSubmit: (e, selectedItem) => {
        // selectedItem contains the JSON object if they chose something from the dropdown.
        // (If they just typed raw text and hit enter, selectedItem is undefined)
        if (selectedItem) {
            let keyword = selectedItem.keyword;

            // Wrap in double quotes if there's a space for exact phrase matching
            if (keyword.includes(' ')) {
                searchInput.value = `"${keyword}"`;
            } else {
                searchInput.value = keyword;
            }
        }

        // Finally, manually trigger the form submission
        searchInput.closest('form').submit();
    }
});

// Fix the initial PHP pre-fill overlap
const hintInput = searchInput.parentElement.querySelector('.tt-hint');

// If there is a pre-filled value from PHP on page load, empty the hint box
if (hintInput && searchInput.value !== '') {
    hintInput.value = '';
}
