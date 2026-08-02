<?php

namespace PhotoDb;

use speich\SqlExtended;


/**
 * Class SqlPhotoList
 * @package PhotoDb
 */
class SqlPhotoList extends SqlExtended
{
    /** @var int sort list of photos by date created */
    public const SORT_BY_DATEADDED = 1;
    public const SORT_BY_DATECREATED = 2;
    public const SORT_BY_DATECHANGED = 3;
    public const SORT_BY_IMGTITLE = 4;
    public const SORT_BY_RANDOM = 5;

    /** @var string Active UI language used to calculate search relevance weights */
    private string $lang = 'de';

    /** @var array Available languages for dynamic regex generation */
    private array $langs = ['de', 'en'];

    /** @var float Penalty multiplier applied to hits in the non-active language */
    private float $langPenalty = 0.2;

    /** @var String quality of the photo */
    public string $qual;
    /** @var string|null theme of the photo */
    public ?string $theme;
    /** @var string|null country photo was taken in */
    public ?string $country;
    /** @var string|null */
    public ?string $search;
    /**
     * limit the query to a species
     * @var string|null
     */
    public ?string $species;
    /**
     * Neutral base weights for each column in the virtual table Images_fts.
     * The order of these keys MUST exactly match the order of columns in the database for the SCORE function to work!
     *
     * @var array<string, int|float>
     */
    private array $ftsColumns = [
        'ImgId' => 2,
        'ImgFolder' => 1,
        'ImgName' => 1,
        'ImgTitle' => 4,
        'ImgDesc' => 2,
        'ThemeDe' => 4,
        'ThemeEn' => 4,
        'SubjectDe' => 1,
        'SubjectEn' => 1,
        'CountryDe' => 1,
        'CountryEn' => 1,
        'Locations' => 1,
        'KeywordsDe' => 1,
        'KeywordsEn' => 1,
        'CommonNamesDe' => 3,
        'CommonNamesEn' => 3,
        'ScientificNames' => 4,
        'Rating' => 0,  /* skip scoring since it handled separately*/
        'ImgTitlePrefixes' => 1,
        'ImgDescPrefixes' => 1,
        'KeywordsDePrefixes' => 2,
        'KeywordsEnPrefixes' => 2,
        'CommonNamesDePrefixes' => 1,
        'CommonNamesEnPrefixes' => 1,
    ];
    /** @var int sort order */
    private int $sort;  // note: only binding vars should be public

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
            $sql = match ($this->sort) {
                self::SORT_BY_DATEADDED => 'i.DateAdded DESC',
                self::SORT_BY_DATECREATED => 'CASE WHEN ImgDateOriginal IS NULL THEN 0 ELSE ImgDateOriginal END DESC, CASE WHEN ImgDateManual IS NULL THEN 0 ELSE ImgDateManual END DESC',
                self::SORT_BY_IMGTITLE => 'ImgTitle',
                self::SORT_BY_RANDOM => 'RANDOM()',
                default => 'LastChange DESC',
            };
        }

        return $sql;
    }

    /**
     * Count the number of photos with or without filters set.
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
     * @inheritDoc
     */
    public function getFrom(): string
    {
        {
            if (isset($this->search)) {
                // @see https://sqlite.org/fts3.html#appendix_a as to why we should use a subquery
                $weightString = $this->getWeightString();
                $search = ' INNER JOIN (
                    SELECT ImgId, SCORE(MATCHINFO(Images_fts, \'xncp\'), \'' . $weightString . '\', Rating - 1) Rank 
                    FROM Images_fts
                    WHERE Images_fts MATCH :search
                    ORDER BY Rank DESC
                    LIMIT -1 OFFSET 0 
                ) fts ON i.Id = fts.ImgId';
            } else {
                $search = '';
            }

            return 'Images i 
                INNER JOIN Images_Themes it ON i.Id = it.ImgId' . $search.'
                LEFT JOIN Images_ScientificNames sc ON i.Id = sc.ImgId';
        }
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): string
    {
        // filtering
        // note: to avoid confusion, only one restriction at a time, e.g. either theme or country should be allowed.
        //       This is handled in the PhotoQueryString class. Flter by rating or coordinates (bounds) is always possible.
        $sql = 'it.ThemeId != 10 AND RatingId > :qual';
        if ($this->theme !== null) {
            $sql .= ' AND ThemeId = :theme';
        }
        if ($this->country !== null) {
            $sql .= ' AND CountryId = :country';
        }
        if ($this->species !== null) {
            $sql .= ' AND sc.ScientificNameId = :species';
        }

        return $sql;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * Dynamically calculates the weight string for the SCORE function based on the active language.
     *
     * @return string
     */
    private function getWeightString(): string
    {
        $weights = [];

        // Dynamically build the regex (e.g., "De|En") from the available langs
        $langPattern = implode('|', array_map('ucfirst', $this->langs));
        $regex = '/(' . $langPattern . ')(Prefixes)?$/';

        foreach ($this->ftsColumns as $colName => $baseWeight) {
            $weight = $baseWeight;

            if (preg_match($regex, $colName, $matches)) {
                $colLang = strtolower($matches[1]);

                if ($colLang !== $this->lang) {
                    $weight *= $this->langPenalty;
                }
            }

            $weights[] = $weight;
        }

        return implode(',', $weights);
    }
}