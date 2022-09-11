<?php

namespace PhotoDb;

use speich\SqlExtended;
use WebsiteTemplate\Language;

/**
 * Query to get information about a specific photo by its id.
 */
class SqlPhotoDetail extends SqlExtended
{
    private string $langPostfix;

    public int $imgId;

    public function setLangPostfix(Language $language) {
        $this->langPostfix = ucfirst($language->get());
    }

    public function getList(): string
    {
        return "I.Id imgId, I.ImgFolder imgFolder, I.ImgName imgName, I.ImgTechInfo imgTechInfo,
        	I.DateAdded dateAdded, I.LastChange lastChange, I.DatePublished datePublished, I.ImgDesc imgDesc, I.ImgTitle imgTitle, I.ImgDateOriginal imgDateOriginal,
        	I.ImgDateManual ImgDateManual,
        	I.ImgLat imgLat, I.ImgLng imgLng, I.ShowLoc showLoc,
        	CASE WHEN F.Code NOT NULL THEN F.Name ||' ('||F.Code||')' ELSE F.Name END film,
        	R.Name rating,
        	E.Make make, E.Model model, E.FileSize fileSize, E.ExposureTime exposureTime, E.FNumber fNumber, E.Iso iso,
        	E.FocalLength focalLength, E.ExposureProgram exposureProgram,
        	E.MeteringMode meteringMode, E.Flash flash, E.FocusDistance focusDistance, E.ImageWidth imageWidth,
        	E.ImageHeight imageHeight, E.DateTimeOriginal dateTimeOriginal,
        	E.GpsLatitude gpsLatitude, E.GpsLongitude gpsLongitude, E.GpsAltitude gpsAltitude, E.GpsAltitudeRef gpsAltitudeRef,
        	E.LensSpec lensSpec, E.Lens lens, E.FileType fileType, E.VibrationReduction vibrationReduction,
        	X.CropTop, X.CropLeft, X.CropRight, X.CropBottom, X.CropAngle,
        	GROUP_CONCAT(DISTINCT T.Name".$this->langPostfix.') themes,
        	GROUP_CONCAT(DISTINCT K.Name) categories,
        	GROUP_CONCAT(DISTINCT N.NameDe) wissNameDe, GROUP_CONCAT(DISTINCT N.NameEn) wissNameEn, GROUP_CONCAT(DISTINCT N.NameLa) wissNameLa,
        	S.Name'.$this->langPostfix.' sex, S.Symbol symbol,
        	GROUP_CONCAT(DISTINCT L.Name) locations,
        	GROUP_CONCAT(DISTINCT C.Name'.$this->langPostfix.') countries,
        	C2.Name'.$this->langPostfix.' country,
        	Lc.UrlLink licenseLink, Lc.UrlLogo licenseLogo, Lc.Label'.$this->langPostfix.' licenseLabel';
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): string
    {
        return 'Images I
            LEFT JOIN FilmTypes F ON I.FilmTypeId = F.Id
            LEFT JOIN Rating R ON I.RatingId = R.Id
            LEFT JOIN Exif E ON I.Id = E.ImgId
            LEFT JOIN Xmp X ON I.Id = X.ImgId
            LEFT JOIN Images_Themes IT ON I.Id = IT.ImgId
            LEFT JOIN Themes T ON IT.ThemeId = T.Id
            LEFT JOIN Images_Keywords IK ON I.Id = IK.ImgId
            LEFT JOIN Keywords K ON IK.KeywordId = K.Id
            LEFT JOIN Images_ScientificNames ISc ON I.Id = ISc.ImgId
            LEFT JOIN ScientificNames N ON ISc.ScientificNameId = N.Id
            LEFT JOIN Sexes S ON ISc.SexId = S.Id
            LEFT JOIN Images_Locations IL ON I.Id = IL.ImgId
            LEFT JOIN Locations L ON IL.LocationId = L.Id
            LEFT JOIN Locations_Countries LC ON L.Id = LC.LocationId
            LEFT JOIN Countries C ON LC.CountryId = C.Id
            LEFT JOIN Countries C2 ON I.CountryId = C2.Id
            INNER JOIN Licenses Lc ON I.LicenseId = Lc.Id';
    }

    public function getWhere(): string
    {
        return 'I.Id = :imgId';
    }

    public function getGroupBy(): string
    {
        return '';
    }

    public function getOrderBy(): string
    {
        return '';
    }
}