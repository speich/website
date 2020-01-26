<?php

namespace PhotoDb;

use stdClass;


/**
 * This class is used to define the bind parameters for the SQL statement used to load marker data.
 * This class is only for convenience. It will let you know which bind variables are used in the SQL query returned
 * by the getSql method. The properties will show in autocomplete of an IDE. It allows the programmer
 * to know which variable names are required to use with the SQL statement for binding without even knowing the exact
 * bind name.
 */
class PhotoListBindings
{
    /** @var float latitude Northeast */
    public $lat1;

    /** @var float longitude Northeast */
    public $lng1;

    /** @var float latitude Southwest */
    public $lat2;

    /** @var float longitude Southwest */
    public $lng2;

    /** @var int quality of the photo */
    public $qual;

    /** @var int theme of the photo */
    public $theme;

    /** @var int country photo was taken */
    public $country;

    /** @var int number of photos per page */
    public $numPerPg;

    /** @var int current page number */
    public $pg = 0;

    /** @var int */
    public $sort;

    /**
     * PhotoListBindings constructor
     * @param int $sort sort photos by
     * @param int $quality quality of photos to display
     * @param int $numPerPg number of photos per page
     */
    public function __construct($sort = SqlPhotoList::SORT_BY_DATEADDED, $quality = PhotoList::QUALITY_HIGH, $numPerPg = PhotoList::NUMPERPAGE_MEDIUM)
    {
        $this->qual = $quality;
        $this->numPerPg = $numPerPg;
        $this->sort = $sort;
    }

    /**
     * If a value is not posted, default value will be set.
     * @param array|stdClass $data
     */
    public function sanitizeInput($data): void
    {
        if (is_array($data)) {
            $data = (object)$data;
        }
        $this->qual = property_exists($data, 'qual') ? (int)filter_var($data->qual, FILTER_SANITIZE_NUMBER_INT) : $this->qual;
        $this->theme = property_exists($data, 'theme') ? (int)filter_var($data->theme, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->country = property_exists($data, 'country') ? (int)filter_var($data->country, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->lat1 = property_exists($data, 'lat1') ? (float)filter_var($data->lat1, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $this->lng1 = property_exists($data, 'lng1') ? (float)filter_var($data->lng1, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $this->lat2 = property_exists($data, 'lat2') ? (float)filter_var($data->lat2, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $this->lng2 = property_exists($data, 'lng2') ? (float)filter_var($data->lng2, FILTER_SANITIZE_NUMBER_FLOAT) : null;
        $this->sort = property_exists($data, 'sort') ? (int)filter_var($data->sort, FILTER_SANITIZE_NUMBER_INT) : $this->sort;
        $this->numPerPg = property_exists($data, 'numPerPg') ? (int)filter_var($data->numPerPg, FILTER_SANITIZE_NUMBER_INT) : $this->numPerPg;
        $this->pg = property_exists($data, 'pg') ? (int)filter_var($data->pg, FILTER_SANITIZE_NUMBER_INT) - 1 : $this->pg;
    }
}