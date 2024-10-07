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
        return 't.imgId, t.imgFolder, t.imgName,
           RANK() OVER (PARTITION BY t.ScientificNameId ORDER BY t.ratingId DESC, t.num) rankNr';
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): string
    {

        // since we now, that scientificNameId can be trusted we don't need to use bind
        $sqlIn = implode(',', $this->scientificNameId);

        return "(SELECT i.Id imgId, i.ImgFolder imgFolder, i.ImgName imgName, i.RatingId,
            N.ScientificNameId,
            random() num
            FROM Images i
            INNER JOIN Images_ScientificNames N ON i.Id = N.ImgId
            WHERE N.ScientificNameId IN ($sqlIn)
            AND N.ImgId <> :imgId
            ) t";
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): string
    {

        return '';
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
        return 't.ratingId DESC, rankNr';
    }

    public function setScientificNameId(array $scientificNameId): void
    {
        $this->scientificNameId = $scientificNameId;
    }
}