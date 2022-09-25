<?php

namespace PhotoDb;


use function is_array;

/**
 * This class is used to define the bind parameters for the SQL statement used to load marker data.
 * This class is only for convenience. It will let you know which bind variables are used in the SQL query returned
 * by the getSql method. The properties will show in autocomplete of an IDE. It allows the programmer
 * to know which variable names are required to use with the SQL statement for binding without even knowing the exact
 * bind name.
 */
class PhotoQueryString
{
    /** @var float|null latitude Northeast */
    public ?float $lat1;

    /** @var float|null longitude Northeast */
    public ?float $lng1;

    /** @var float|null latitude Southwest */
    public ?float $lat2;

    /** @var float|null longitude Southwest */
    public ?float $lng2;

    /** @var int quality of the photo */
    public int $qual;

    /** @var int|null theme of the photo */
    public ?int $theme;

    /** @var int|null country photo was taken */
    public ?int $country;

    /** @var int number of photos per page */
    public int $numPerPg;

    /** @var int current page number */
    public int $pg = 0;

    /** @var int */
    public int $sort;

    /** @var string|null search query */
    public ?string $search;

    /**
     * PhotoListBindings constructor
     * @param object|array $data query string data
     */
    public function __construct(object|array $data)
    {
        if (is_array($data)) {
            $data = (object)$data;
        }
        $this->validate($data);
        $this->sanitizeInput($data);
    }

    /**
     * @param object $data
     * @return void
     */
    public function validate(object $data): void
    {
        // theme and country are not allowed at the same time, since country is handled like a theme
        if (property_exists($data, 'theme') && property_exists($data, 'country')) {
            unset($data->country);
        }
    }

    /**
     * If a value is not posted, default value will be set.
     * @param object $data
     */
    public function sanitizeInput($data): void
    {
        if (property_exists($data, 'q') && $data->q !== '') {
            $this->search = filter_var($data->q, FILTER_SANITIZE_SPECIAL_CHARS);
            $this->qual = property_exists($data, 'qual') ? filter_var($data->qual,
                FILTER_SANITIZE_NUMBER_INT) : PhotoList::QUALITY_LOW;
        } else {
            $this->qual = property_exists($data, 'qual') ? filter_var($data->qual,
                FILTER_SANITIZE_NUMBER_INT) : PhotoList::QUALITY_HIGH;
        }
        $this->theme = property_exists($data, 'theme') ? filter_var($data->theme, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->country = property_exists($data, 'country') ? filter_var($data->country,
            FILTER_SANITIZE_NUMBER_INT) : null;
        $this->lat1 = property_exists($data, 'lat1') ? filter_var($data->lat1, FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION) : null;
        $this->lng1 = property_exists($data, 'lng1') ? filter_var($data->lng1, FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION) : null;
        $this->lat2 = property_exists($data, 'lat2') ? filter_var($data->lat2, FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION) : null;
        $this->lng2 = property_exists($data, 'lng2') ? filter_var($data->lng2, FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION) : null;
        $this->sort = property_exists($data, 'sort') ? filter_var($data->sort,
            FILTER_SANITIZE_NUMBER_INT) : SqlPhotoList::SORT_BY_DATEADDED;
        $this->numPerPg = property_exists($data, 'numPerPg') ? filter_var($data->numPerPg,
            FILTER_SANITIZE_NUMBER_INT) : PhotoList::NUMPERPAGE_MEDIUM;
        $this->pg = property_exists($data, 'pg') ? filter_var($data->pg, FILTER_SANITIZE_NUMBER_INT) - 1 : $this->pg;
    }
}