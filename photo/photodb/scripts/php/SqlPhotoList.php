<?php

namespace PhotoDb;

use ReflectionObject;
use ReflectionProperty;
use speich\SqlFull;


/**
 * Class SqlPhotoList
 * @package PhotoDb
 */
class SqlPhotoList extends SqlFull
{
    /** @var [String] latitude Northeast */
    public $lat1;

    /** @var String longitude Northeast */
    public $lng1;

    /** @var String latitude Southwest */
    public $lat2;

    /** @var String longitude Southwest */
    public $lng2;

    /** @var String quality of the photo */
    public $qual;

    /** @var String theme of the photo */
    public $theme;

    /** @var String country photo was taken in */
    public $country;

    /** @var String sort order */
    private $sort;  // note: only binding vars should be public

    /** @var null|int first number of rows to omit from the result set returned by the SELECT statement */
    public $offset;

    /** @var null|int upper bound on the number of rows returned by the entire SELECT statement */
    public $limit;

    /** @var int sort list of photos by date created */
    public const SORT_BY_DATEADDED = 1;
    public const SORT_BY_DATECEATED = 2;
    public const SORT_BY_DATECHANGED = 3;
    public const SORT_BY_IMGTITLE = 4;

    /**
     * @inheritDoc
     */
    public function getList(): string
    {
        return 'i.Id imgId, i.ImgFolder imgFolder, i.ImgName imgName, i.ImgTitle imgTitle';
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): string
    {
        return 'Images i INNER JOIN Images_Themes it ON i.Id = it.ImgId';
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): string
    {
        // filtering
        // to avoid confusion only allow one restriction at a time, e.g. either theme or country.
        // filter by rating or coordinates (bounds) is always possible
        $sql = 'it.ThemeId != 10 AND ratingId > :qual';
        if ($this->theme !== null) {
            $sql .= ' AND themeId = :theme';
        } elseif ($this->country !== null) {
            $sql .= ' AND countryId = :country';
        }
        if ($this->lat1 !== null && $this->lng1 !== null && $this->lat2 !== null && $this->lng2 !== null) {
            if ($this->lng2 < $this->lng1) {
                $sql .= ' AND (lat >= :lat2 AND lng >= :lng2) AND (lat <= :lat1 AND lng <= :lng1)'; // parentheses are just for readability
            } // special case lng2|lng1 <-> 180|-180
            else {
                $sql .= ' AND ((lat >= :lng2 AND lng >= :lng2) OR (lat <= :lat1 AND lng <= :lng1))';
            }
            $sql .= " AND lat NOT NULL AND lng NOT NULL AND lat != '' AND lng != ''";
        }

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function getGroupBy(): string
    {
        return 'i.Id, i.ImgFolder, i.ImgName, i.ImgTitle, i.DateAdded';
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {
        switch ($this->sort) {
            case self::SORT_BY_DATECEATED:
                $sql = 'date DESC';
                break;
            case self::SORT_BY_DATECHANGED:
                $sql = 'lastChange DESC';
                break;
            case self::SORT_BY_IMGTITLE:
                $sql = 'imgTitle';
                break;
            case self::SORT_BY_DATEADDED:
            default:
                $sql = 'i.DateAdded DESC';
        }

        return $sql;
    }

    /**
     * Bind the values to the placeholders in the SQL.
     * @param callable $fnc
     */
    public function bind($fnc): void
    {
        foreach ($this->getPublicVars() as $name => $val) {
            if ($val !== null && $val !== 'sort') {
                // remember variable is passed by reference
                $fnc($name, $this->{$name});
            }
        }
    }

    /**
     * Returns an associative array of defined public non-static properties of this class no matter the scope. If a property has not been assigned a value, it will be returned with a NULL value.
     * @see https://stackoverflow.com/questions/13124072/how-to-programatically-find-public-properties-of-a-class-from-inside-one-of-its#13124184
     * @return array
     */
    public function getPublicVars(): array
    {
        $arr = [];
        $refl = new ReflectionObject($this);
        $props = $refl->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $name = $prop->getName();
            $arr[$name] = $this->{$name};
        }

        return $arr;
    }

    /**
     * Return the SQL to query the data paged.
     * Appends a LIMIT OFFSET to the SQL with the bind vars limit and offset.
     * @return string SQL
     */
    public function getPaged(): string
    {
        return $this->get().' LIMIT :limit OFFSET :offset';
    }

    /**
     * Count number of photos with or without filters set.
     * @return string SQL
     */
    public function getNumRecord(): string
    {
        return 'SELECT COUNT(Id) numRec FROM (
                SELECT i.Id FROM '.$this->getFrom().'
                WHERE '.$this->getWhere().' GROUP BY i.Id
            )';
    }

    /**
     * @param String $sort
     */
    public function setSort(String $sort): void
    {
        $this->sort = $sort;
    }
}