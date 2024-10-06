<?php

namespace PhotoDb;

use speich\SqlExtended;

class SqlPhotoSameSpecies extends SqlExtended
{

    private array $scientificNameId;

    public int $imgId;

    /**
     * @inheritDoc
     */
    public function getList(): string
    {
        return 'i.Id imgId, i.ImgFolder imgFolder, i.ImgName imgName,
            RANK() OVER (PARTITION BY N.ScientificNameId ORDER BY i.ratingId DESC, i.Id) rankNr';
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): string
    {
        return 'Images i
            INNER JOIN Images_ScientificNames N ON i.Id = N.ImgId';
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): string
    {
        // since we now, that scientificNameId can be trusted we don't need to use bind
        $sqlIn = implode(',', $this->scientificNameId);

        return "N.ScientificNameId IN ($sqlIn)
            AND N.ImgId <> :imgId";
    }

    /**
     * @inheritDoc
     */
    public function getGroupBy(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {
        return 'i.RatingId DESC, rankNr';
    }

    public function setScientificNameId(array $scientificNameId): void
    {
        $this->scientificNameId = $scientificNameId;
    }
}