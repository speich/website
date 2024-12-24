<?php

namespace PhotoDb;


use function is_array;

/**
 * This class is used to sanitize all query string parameters, which then can be used in a SQL query.
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
     * @var mixed|null
     */
    public ?int $species;

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
            $this->qual = property_exists($data, 'qual') ? filter_var($data->qual,FILTER_SANITIZE_NUMBER_INT) : PhotoList::QUALITY_LOW;
        } else {
            $this->qual = property_exists($data, 'qual') ? filter_var($data->qual,FILTER_SANITIZE_NUMBER_INT) : PhotoList::QUALITY_HIGH;
        }
        $this->theme = property_exists($data, 'theme') ? filter_var($data->theme, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->country = property_exists($data, 'country') ? filter_var($data->country, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->species = property_exists($data, 'species') ? filter_var($data->species, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->sort = property_exists($data, 'sort') ? filter_var($data->sort,FILTER_SANITIZE_NUMBER_INT) : SqlPhotoList::SORT_BY_DATECHANGED;
        $this->numPerPg = property_exists($data, 'numPerPg') ? filter_var($data->numPerPg, FILTER_SANITIZE_NUMBER_INT) : PhotoList::NUMPERPAGE_MEDIUM;
        $this->pg = property_exists($data, 'pg') ? filter_var($data->pg, FILTER_SANITIZE_NUMBER_INT) - 1 : $this->pg;
    }
}