<?php

namespace PhotoDb;

/**
 * This class is used to define the bind parameters for the SQL statement used to load marker data.
 * This class is only for convenience. It will let you know which bind variables are used in the SQL query returned
 * by the getSql method. The properties will show in autocomplete of an IDE. It allows the programmer
 * to know which variable names are required to use with the SQL statement for binding without even knowing the exact
 * bind name.
 */
class PhotoBindings
{
    /** @var String latitude Northeast */
    var $lat1 = ':lat1';

    /** @var String longitude Northeast */
    var $lng1 = ':lng1';

    /** @var String latitude Southwest */
    var $lat2 = ':lat2';

    /** @var String longitude Southwest */
    var $lng2 = ':lng2';

    /** @var String quality of the photo */
    var $qual = ':qual';

    /** @var String theme of the photo */
    var $theme = ':theme';

    /** @var String country photo was taken */
    var $country = ':country';

    /** @var string query limit */
    var $limit = ':limit';

    /** @var string query offset */
    var $offset = ':offset';
}