<?php

namespace PhotoDb;

use speich\SqlExtended;

class SqlPhotoSameSpecies extends SqlExtended
{

    public int $scientificNameId;

    public int $imgId;

    /**
     * @inheritDoc
     */
    public function getList(): string
    {
        return 'i.Id imgId, i.ImgFolder imgFolder, i.ImgName imgName';
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
        return 'N.ScientificNameId = :scientificNameId
            AND N.ImgId <> :imgId';
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
        return 'i.RatingId DESC';
    }
}