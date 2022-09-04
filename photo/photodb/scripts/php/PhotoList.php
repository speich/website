<?php

namespace PhotoDb;

use PDO;
use WebsiteTemplate\Language;
use WebsiteTemplate\QueryString;
use WebsiteTemplate\Website;


/**
 * Class Photo
 */
class PhotoList
{
    /** @var int include this photo quality in the list */
    public const QUALITY_LOW = 0;
    public const QUALITY_MEDIUM = 1;
    public const QUALITY_HIGH = 2;

    /** @var int number of photos shown in the list */
    public const NUMPERPAGE_LOW = 14;
    public const NUMPERPAGE_MEDIUM = 28;
    public const NUMPERPAGE_HIGH = 56;
    public const NUMPERPAGE_VERYHIGH = 112;

    /** @var PDO|PhotoDb */
    private PDO|PhotoDb $db;

    /** @var PDO|null */
    private ?PDO $cnn;

    /**
     * @param PhotoDb $db
     */
    public function __construct(PhotoDb $db)
    {
        $this->db = $db;
        $this->cnn = $db->db;
    }

    /**
     * Load photos from database.
     * @param SqlPhotoList $sql
     * @return array database records of photos or false
     */
    public function loadPhotos(SqlPhotoList $sql): array
    {
        $strSql = $sql->getPaged();
        $stmt = $this->cnn->prepare($strSql);
        $sql->bind([$stmt, 'bindValue']);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Return number of records in query.
     * @param SqlPhotoList $sql
     * @return int|bool number of records or false
     */
    public function getNumRec(SqlPhotoList $sql): bool|int
    {
        $strSql = $sql->getNumRecord();
        $stmt = $this->cnn->prepare($strSql);
        $sql->bind([$stmt, 'bindValue']);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row ? $row['numRec'] : false;
    }


    /**
     * Display photos.
     * @param array $photos database records
     * @param Website $web
     * @param Language $lang
     * @param array $i18n
     * @return string HTML
     */
    public function renderData(array $photos, Website $web, Language $lang, array $i18n): string
    {
        $str = '';
        $css = '';
        $cssImg = '';
        if (\count($photos) === 0) {
            $str .= '<p>'.$i18n['not found'].'</p>';

            return $str;
        }

        $query = new QueryString();
        foreach ($photos as $row) {
            // get image dimensions
            $imgFile = $web->getWebRoot().$this->db->getPath('img').'thumbs/'.$row['imgFolder'].'/'.$row['imgName'];
            $thumbSize = getimagesize(__DIR__.'/../../../..'.$imgFile);
            $imgSize = getimagesize(__DIR__.'/../../../..'.$web->getWebRoot().$this->db->getPath('img').$row['imgFolder'].'/'.$row['imgName']);
            $imgTitle = $row['imgTitle'];
            $link = str_replace('thumbs/', '', $imgFile);
            $detailLink = $lang->createPage('photo-detail.php').$query->withString(['imgId' => $row['imgId']]);
            if ($imgSize[0] > $imgSize[1]) {
                $css = 'slideHorizontal';
                $cssImg = 'slideImgHorizontal';
            } elseif ($imgSize[0] < $imgSize[1]) {
                $css = 'slideVertical';
                $cssImg = 'slideImgVertical';
            } elseif ($imgSize[0] === $imgSize[1]) {
                $css = 'slideQuadratic';
                $cssImg = 'slideImgQuadratic';
            }

            $str .= '<li class="slide">';
            $str .= '<div class="slideCanvas '.$css.'">';
            $str .= '<a href="'.$link.'" title="'.$imgTitle.'" data-pswp-width="'.$imgSize[0].'" data-pswp-height="'.$imgSize[1].'">';
            $str .= '<img class="'.$cssImg.'" src="'.$imgFile.'" alt="'.$i18n['photo'].'" title="'.$i18n['thumbnail of'].' '.$imgTitle.'" width="'.$thumbSize[0].'" height="'.$thumbSize[1].'">';
            $str .= '</a></div>';
            $title = $i18n['zoom photo'].': '.$imgTitle;
            $str .= '<div class="slideText"><a title="'.$title.'" href="'.$link.'">'.$i18n['zoom'].'</a> | ';
            $str .= '<a title="'.$i18n['show details'].'" href="'.$detailLink.'">'.$i18n['details'].'</a></div>';
            $str .= '</li>';
        }

        return $str;
    }

    /**
     * Displays the file, but only if xmp info is available.
     * Note: Idea is not to show original size if image is cropped, but crop information is not available yet (e.g. older photodb versions)
     * Returns an array with keys w, h and isCropped
     * @param $imgData
     * @return array
     */
    public function getImageSize($imgData): array
    {
        if ($imgData['CropTop'] === null || $imgData['CropBottom'] === null) {
            $arr = [];
            $arr['w'] = $imgData['imageWidth'];
            $arr['h'] = $imgData['imageHeight'];
            $arr['isCropped'] = false;
            //} else if ((float) $imgData['CropAngle'] > 0) {
        } else {
            $arr = $this->calcCropDimensions($imgData);
            $arr['isCropped'] = true;
        }

        return $arr;
    }

    /**
     * Renders the links in the image descriptions.
     * Converts markup links [text](link) into real links and adds the target blank property.
     * @param $text
     * @return string|string[]|null
     */
    public function renderDescLinks($text): array|string|null
    {
        return preg_replace_callback(
            '/\[(.*?)\]\((.*?)\)/',
            function ($matches) {
                return '<a href="'.$matches[2].'" target="_b">'.$matches[1].'</a>';
            },
            htmlspecialchars($text, ENT_QUOTES, 'utf-8')
        );
    }

    /**
     * Adobe Lightroom Xmp cropping data
     * @param array $imgData
     * @return array array with width and height
     */
    public function calcCropDimensions($imgData): array
    {
        // TODO:  @see www.speich.net/articles/...
        // test method with images (ImgId):
        // 7216 2017-10-Fenalet-009.NEF     5232 x 3492  1.71
        // 7099 2017-03-Florida-016.NEF     4498 x 2999 -2.04
        // 7162 2017-03-Florida-054.NEF     7205 x 4809  0.83
        // 7207 2017-03-Florida-103.NEF     4276 x 2851  3.15

        $ax = $imgData['CropLeft'];     // x of vector a [CropLeft, CropTop]
        $ay = $imgData['CropTop'];      // y of vector a [CropLeft, CropTop]
        $bx = $imgData['CropRight'];    // x of vector b [CropRight, CropBottom]
        $by = $imgData['CropBottom'];   // y of vector b [CropRight, CropBottom]
        $theta = $imgData['CropAngle'];   // in degrees clockwise

        // scale
        // remember: y is scaled differently than x, e.g. values are width = 100% and height = 100% in Adobe XMP
        $ax *= $imgData['imageWidth'];
        $ay *= $imgData['imageHeight'];
        $bx *= $imgData['imageWidth'];
        $by *= $imgData['imageHeight'];

        $a2 = abs(sqrt((((($ax - $bx) / 2) ** 2) + ((($ay - $by) / 2) ** 2))));    // magnitude of vector a' (after translating image to origin)
        $alpha = rad2deg(atan2(($ay - $by), ($ax - $bx))); //* -1;
        $arr['w'] = abs(round(cos(deg2rad($alpha - $theta)) * $a2 * 2));
        $arr['h'] = abs(round(sin(deg2rad($alpha - $theta)) * $a2 * 2));

        return $arr;
    }
}