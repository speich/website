<?php

namespace PhotoDb;

use PDO;
use WebsiteTemplate\Language;
use WebsiteTemplate\QueryString;
use function count;
use function strlen;

/**
 * Render information about a single photo as html.
 */
class PhotoDetail
{
    /** @var PhotoDb */
    private PhotoDb $db;

    /** @var PDO|null */
    private ?PDO $cnn;
    
    /** @var array|bool image database data */
    public readonly array|bool $data;
    
    /** @var array|bool database data with ids of more images of same species*/
    private array|bool $dataMore;
    
    private array $i18n;
    private PhotoList $dataList;

    /**
     * @param PhotoDb $db
     * @param int $imgId
     * @param Language $language
     */
    public function __construct(PhotoDb $db, int $imgId, public readonly Language $language)
    {
        $this->db = $db;
        $this->cnn = $db->db;
        $sql = new SqlPhotoDetail();
        $sql->imgId = $imgId;
        $sql->setLangPostfix($language);
        $this->data = $this->query($sql);
        $this->dataMore = $this->querySameSpecies();
        $this->i18n = require __DIR__.'/../../nls/'.$language->get().'/photo.php';
        $this->dataList = new PhotoList($db);
    }

    /**
     * @param SqlPhotoDetail $sql
     * @return array|false
     */
    private function query(SqlPhotoDetail $sql): bool|array
    {
        $strSql = $sql->get();
        $stmt = $this->cnn->prepare($strSql);
        $sql->bind([$stmt, 'bindValue']);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Query for images of the same species.
     * @return bool|array
     */
    private function querySameSpecies(): bool|array
    {
        $sql = new SqlPhotoSameSpecies();
        $sql->limit = 4;
        $sql->offset = 0;
        $sql->imgId = $this->data['imgId'];
        $sql->setScientificNameId(explode(',', $this->data['scientificNameId']));
        $strSql = $sql->getPaged();
        $stmt = $this->cnn->prepare($strSql);
        $sql->bind([$stmt, 'bindValue']);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Print HTML to display photo detail.
     */
    public function render(): void
    {
        $db = $this->db;
        $query = new QueryString();
        $backPage = $this->language->createPage('photo.php').$query->withString(null, ['imgId']);
        $imgFile = $db->webroot.$db->getPath('img').$this->data['imgFolder'].'/'.$this->data['imgName'];

        echo '<h1>'.$this->renderTitle().'</h1>';
        echo $this->data['imgDesc'] ? '<p>'.$this->dataList->renderDescLinks($this->data['imgDesc']).'</p>' : '';
        echo '<figure>
            <a title="'.$this->i18n['photo'].': '.$this->data['imgTitle'].'" href="'.$imgFile.'">
            <img src="'.$imgFile.'" id="photo" alt="'.$this->data['imgTitle'].'"/></a>
            <figcaption>'.$this->data['imgTitle'].'<br>
             © '.ucfirst($this->i18n['photo']).' Simon Speich, www.speich.net</figcaption></figure>';
        echo '<div class="flexCont">
                <div>'.$this->renderDetail().'</div>';
        if ($this->data['scientificNameId'] !== null) {
            echo '<div class="sameSpecies">
                    <div>'.$this->renderSpecies().'</div>'.
                $this->renderSpeciesLink().
                '</div>';
        }
        echo '</div>';
        echo '<p><a href="'.$backPage.'">'.$this->i18n['back'].'</a></p>';
        echo '<div id="exifInfo" class="flexCont">
                <div>'.$this->renderExif().'</div>
                <div>'.$this->renderDbInfo().'</div>
            </div>';
        echo '<p class="license">'.$this->renderLicense().'</p>
            <p><a href="'.$backPage.'">'.$this->i18n['back'].'</a></p>';
    }

    /**
     * Render the title of the photo
     * For English if the title is only available in German, use scientific name instead if available
     * @return mixed
     */
    public function renderTitle():string {
        if ($this->language->get() === 'de' || $this->data['scientificNameId'] === null) {
            $title = $this->data['imgTitle'];
        } else {
            $title = $this->data['scientificNameEn'] ?? $this->data['scientificNameLa'];
        }

        return $title;
    }

    /**
     * Render the license of the photo
     * @return string html
     */
    private function renderLicense(): string
    {
        $htmlDe = '<a rel="license" href="'.$this->data['licenseLink'].'" target="_blank"><img alt="Creative Commons Lizenzvertrag"
            src="'.$this->data['licenseLogo'].'" width="80" height="15"></a>Dieses Foto ist lizenziert unter einer <a rel="license" href="'.$this->data['licenseLink'].'" target="_blank">Creative Commons '.$this->data['licenseLabel'].'</a>.<br>
            <strong>© Foto Simon Speich, wwww.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.';

        $htmlEn = '<a rel="license" href="'.$this->data['licenseLink'].'" target="_blank"><img alt="Creative Commons Lizenzvertrag"
            src="'.$this->data['licenseLogo'].'" width="80" height="15"></a>This photo is licensed under a <a rel="license" href="'.$this->data['licenseLink'].'" target="_blank">Creative Commons '.$this->data['licenseLabel'].'</a>.<br>
            <strong>© Photo Simon Speich, www.speich.net</strong>. For a commercial licence or higher resolution please <a href="/contact/contact.php">contact</a> the author.';


        return $this->language->get() === 'de' ? $htmlDe : $htmlEn;        
    }

    private function renderDetail(): string
    {
        $dim = $this->dataList->getImageSize($this->data);

        $str = '<svg class="icon"><use xlink:href="/../../layout/images/symbols.svg#star"></use></svg>';
        $len = strlen($this->data['rating']);
        $star = str_repeat($str, $len);
        if ($this->data['dateTimeOriginal']) {
            $datum = date('d.m.Y H:i:s', $this->data['dateTimeOriginal']);
        } else {
            $datum = $this->data['ImgDateManual'];
        }

        return '<h3>'.ucfirst($this->i18n['photo']).'</h3>
            <ul>
    	        <li><span class="photoTxtLabel">'.$this->i18n['name'].':</span> '.$this->data['scientificNameDe'].' - '.$this->data['scientificNameEn'].'</li>
                <em><span class="photoTxtLabel">'.$this->i18n['scientific name'].':</span> <em>'.$this->data['scientificNameLa'].' <span title="'.$this->data['sex'].'">'.$this->data['symbol'].'</span></em></em>
                </ul><ul>
                <li><span class="photoTxtLabel">'.$this->i18n['dimensions'].($dim['isCropped'] ? ' ('.$this->i18n['cropped'].') ' : '').':</span> '.$dim['w'].' x '.$dim['h'].' px</li>
                <li><span class="photoTxtLabel">'.$this->i18n['date'].':</span> '.$datum.'</li>
                <li><span class="photoTxtLabel">'.$this->i18n['order number'].':</span> '.$this->data['imgId'].'</li>
                <li><span class="photoTxtLabel">'.$this->i18n['file name'].':</span> '.$this->data['imgName'].'</li>
            </ul>
            <ul>
                <li><span class="photoTxtLabel">'.$this->i18n['place'].':</span> '.$this->data['locations'].'</li>
    	        <li><span class="photoTxtLabel">'.$this->i18n['country'].':</span> '.($this->data['countries'] ?? $this->data['country']).'</li>
            </ul>
            <ul>
                <li><span class="photoTxtLabel">'.$this->i18n['keywords'].':</span> '.($this->data['categories'] !== '' ? $this->data['categories'].'<br/>' : '').'</li>
            </ul>
            <p class="mRating"><span class="photoTxtLabel">'.$this->i18n['rating'].':</span> '.$star.'</p>';

    }

    private function renderExif(): string
    {
        $str = '<h3>'.$this->i18n['technical information'].' (Exif)</h3>';
        if ($this->data['model'] === 'Nikon SUPER COOLSCAN 5000 ED') {
            $str .= '<ul><li><span class="photoTxtLabel">'.$this->i18n['type of film'].':</span> '.$this->data['film'].'</li>
    		    <li><span class="photoTxtLabel">'.$this->i18n['model'].': </span>'.$this->data['model'].', '.$this->data['make'].'</li></ul>';
        } else {
            $str .= '<ul>
                <li><span class="photoTxtLabel">'.$this->i18n['exposure'].':</span> '.$this->data['exposureTime'].' at ƒ'.number_format($this->data['fNumber'], 1).'
    <li><span class="photoTxtLabel">ISO:</span> '.$this->data['iso'].'</li>
    		    <li><span class="photoTxtLabel">'.$this->i18n['focal length'].':</span> '.$this->data['focalLength'].', '.$this->i18n['distance'].' : '.$this->data['focusDistance'].'</li>
    		    </ul>
    		    <ul>
    		    <li><span class="photoTxtLabel">'.$this->i18n['program'].':</span> '.$this->data['exposureProgram'].', '.$this->data['meteringMode'].'</li>
    		    <li><span class="photoTxtLabel">VR:</span> '.$this->data['vibrationReduction'].'</li>
    		    <li><span class="photoTxtLabel">'.$this->i18n['flash'].':</span> '.$this->data['flash'].'</li>
    		    <li><span class="photoTxtLabel">'.$this->i18n['lens'].':</span> '.($this->data['lensSpec'] !== '' ? $this->data['lensSpec'] : $this->data['lens']).'</li>
    	        <li><span class="photoTxtLabel">'.$this->i18n['model'].': </span>'.$this->data['model'].'</li>
    	        </ul>';
        }

        return $str;
    }

    private function renderDbInfo(): string
    {
        $str = '<h3>'.$this->i18n['database information'].'</h3>';
        $str .= '<ul><li><span class="photoTxtLabel">'.$this->i18n['added'].':</span> '.(!empty($this->data['dateAdded']) ? date('d.m.Y H:i:s',
                $this->data['dateAdded']) : '').'</li>
    	    <li><span class="photoTxtLabel">'.$this->i18n['changed'].':</span> '.(!empty($this->data['lastChange']) ? date('d.m.Y H:i:s',
                $this->data['lastChange']) : '').'</li>
            <li><span class="photoTxtLabel">'.$this->i18n['published'].':</span> '.(!empty($this->data['lastChange']) ? date('d.m.Y H:i:s',
                $this->data['datePublished']) : '').'</li></ul>';
        $str .= '<ul><li><span class="photoTxtLabel">'.$this->i18n['file format'].':</span> '.$this->data['fileType'].' ('.$this->data['fileSize'].')</li></ul>';

        return $str;
    }

    private function renderSpecies(): string
    {
        $species = $this->language->get() === 'en' ? $this->data['scientificNameEn'] : $this->data['scientificNameDe'];
        $species = $species === '' ? $this->data['scientificNameLa'] : $species;

        $alt = $this->i18n['photo'].': '.$species;
        $str = '';
        
        foreach ($this->dataMore as $item) {
            $href = '/photo/photodb/photo-detail.php?imgId='.$item['imgId'];
            $thumbPath = $this->db->webroot.$this->db->getPath('img').'thumbs/'.$item['imgFolder'].'/'.$item['imgName'];
            $imgPath = str_replace('thumbs/', '', $thumbPath);
            $thumbSize = getimagesize(__DIR__.'/../../../..'.$thumbPath);
            $str .= '<a href="'.$href.'" alt="'.$alt.'" title="'.$species.'"><img src="'.$imgPath.'" width="'.$thumbSize[0].'" height="'.$thumbSize[1].'"></a>';
        }

        return $str;
    }

    private function renderSpeciesLink(): string
    {
        if (count($this->dataMore) < 2) {
            return '';
        }

        $arrSpecies = explode(',', $this->data['scientificNameLa']);
        $arrSpeciesId = explode(',', str_replace(' ', '', $this->data['scientificNameId']));
        $commonName = $this->language->get() === 'en' ? $this->data['scientificNameEn'] : $this->data['scientificNameDe'];
        $commonName = explode(',', $commonName);

        $params = ['qual' => 0];
        $query = new QueryString();
        $str = $this->i18n['more photos'].':';
        foreach ($arrSpecies as $key => $species) {
            $name = trim($commonName[$key]) === '' ? $species : trim($commonName[$key]);
            $params['species'] = $arrSpeciesId[$key];
            $href = $this->language->createPage('photo.php').$query->withString($params, ['imgId', 'pg']);
            $str .= ($key > 0 ? '|' : '').' <a href="'.$href.'">'.$name.'</a>';
        }

        return $str;
    }
}