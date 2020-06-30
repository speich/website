<?php


namespace PhotoDb;



class SqlMap extends SqlPhotoList
{

    /**
     * @inheritDoc
     */
    public function getList(): string
    {
        return "i.Id id, i.ImgFolder||'/'||i.ImgName img, ROUND(i.ImgLat, 6) lat, ROUND(i.ImgLng, 6) lng";
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {
        return '';
    }
}