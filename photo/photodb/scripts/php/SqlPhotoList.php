<?php

namespace PhotoDb;

use speich\SqlExtended;


/**
 * Class SqlPhotoList
 * @package PhotoDb
 */
class SqlPhotoList extends SqlExtended
{
    /** @var string|null latitude Northeast */
    public ?string $lat1;

    /** @var string|null longitude Northeast */
    public ?string $lng1;

    /** @var string|null latitude Southwest */
    public ?string $lat2;

    /** @var string|null longitude Southwest */
    public ?string $lng2;

    /** @var String quality of the photo */
    public string $qual;

    /** @var string|null theme of the photo */
    public ?string $theme;

    /** @var string|null country photo was taken in */
    public ?string $country;

    /** @var string|null */
    public ?string $search;

    /** @var int sort list of photos by date created */
    public const SORT_BY_DATEADDED = 1;
    public const SORT_BY_DATECREATED = 2;
    public const SORT_BY_DATECHANGED = 3;
    public const SORT_BY_IMGTITLE = 4;

    /** @var String sort order */
    private string $sort;  // note: only binding vars should be public

    /**
     * Weights to be used for each column for the SCORE function.
     * Order corresponds with the order in the virtual table Images_fts:
     * ImgId, ImgFolder, ImgName, ImgTitle, ImgDesc, Theme, Country, Keywords, Locations, CommonName, ScientificName,
     * Subject, ImgTitlePrefixes, ImgDescPrefixes, KeywordsPrefixes, CommonNamePrefixes.
     *
     * Note: Folder and file names are often locations. Weighing them too high, can lead to undesired results, such
     * as searching for Winter (winter) and ending up with Winterthur!
     * @var array
     */
    private array $colWeights = [2, 1, 1, 4, 2, 4, 1, 1, 1, 1, 1, 4, 0, 1, 1, 1, 1];

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
        if (isset($this->search)) {
            // @see https://sqlite.org/fts3.html#appendix_a as to why we should use a subquery
            $search = ' INNER JOIN (
                SELECT ImgId, SCORE(MATCHINFO(Images_fts, \'xncp\'), \''.implode(',', $this->colWeights).'\', Rating - 1) Rank 
                FROM Images_fts
                WHERE Images_fts MATCH :search
                ORDER BY Rank DESC
                LIMIT -1 OFFSET 0 
            ) fts ON i.Id = fts.ImgId';
        } else {
            $search = '';
        }

        return 'Images i 
            INNER JOIN Images_Themes it ON i.Id = it.ImgId'.$search;
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): string
    {
        // filtering
        // to avoid confusion only allow one restriction at a time, e.g. either theme or country.
        // filter by rating or coordinates (bounds) is always possible
        $sql = 'it.ThemeId != 10 AND RatingId > :qual';
        if ($this->theme !== null) {
            $sql .= ' AND ThemeId = :theme';
        } elseif ($this->country !== null) {
            $sql .= ' AND CountryId = :country';
        }
        if ($this->lat1 !== null && $this->lng1 !== null && $this->lat2 !== null && $this->lng2 !== null) {
            if ($this->lng2 < $this->lng1) {
                $sql .= ' AND (Imglat >= :lat2 AND Imglng >= :lng2) AND (Imglat <= :lat1 AND Imglng <= :lng1)'; // parentheses are just for readability
            } // special case lng2|lng1 <-> 180|-180
            else {
                $sql .= ' AND ((Imglat >= :lng2 AND Imglng >= :lng2) OR (Imglat <= :lat1 AND Imglng <= :lng1))';
            }
            $sql .= " AND Imglat NOT NULL AND Imglng NOT NULL AND Imglat != '' AND Imglng != ''";
        }

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function getGroupBy(): string
    {
        return 'i.Id, i.ImgFolder, i.ImgName, i.ImgTitle, i.DateAdded, ImgDateOriginal, ImgDateManual';
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {

        if (isset($this->search)) {
            $sql = 'Rank DESC, LastChange DESC';
        } else {
            switch ($this->sort) {
                case self::SORT_BY_DATECREATED:
                    $sql = 'CASE WHEN ImgDateOriginal IS NULL THEN 0 ELSE ImgDateOriginal END DESC, CASE WHEN ImgDateManual IS NULL THEN 0 ELSE ImgDateManual END DESC';
                    break;
                case self::SORT_BY_DATECHANGED:
                    $sql = 'LastChange DESC';
                    break;
                case self::SORT_BY_IMGTITLE:
                    $sql = 'ImgTitle';
                    break;
                case self::SORT_BY_DATEADDED:
                default:
                    $sql = 'i.DateAdded DESC';
            }
        }

        return $sql;
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
    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }
}