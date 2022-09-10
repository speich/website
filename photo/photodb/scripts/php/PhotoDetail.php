<?php

namespace PhotoDb;

use PDO;
use WebsiteTemplate\Language;
use WebsiteTemplate\QueryString;

class PhotoDetail
{
    /** @var PhotoDb */
    private PhotoDb $db;

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
     * @param SqlPhotoDetail $sql
     * @return array|false
     */
    public function get(SqlPhotoDetail $sql): bool|array
    {
        $strSql = $sql->get();
        $stmt = $this->cnn->prepare($strSql);
        $sql->bind([$stmt, 'bindValue']);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Print HTML to display photo detail.
     * @param array $data
     * @param Language $lang
     * @param array $i18n internationalization
     */
    function render(array $data, Language $lang, array $i18n): void
    {
        $db = $this->db;
        $photo = new PhotoList($db);
        $query = new QueryString();
        $backPage = $lang->createPage('photo.php').$query->withString(null, ['imgId']);
        if (strpos($backPage, $lang->createPage('photo-mapsearch.php')) !== false) {
            $backPage = $lang->createPage('photo-mapsearch.php').'?'.$_SERVER['QUERY_STRING'];    // when coming from map via js lastPage was not set with latest query vars, use current
        }
        $imgFile = $db->webroot.$db->getPath('img').$data['imgFolder'].'/'.$data['imgName'];
        $dim = $photo->getImageSize($data);
        $star = '';

        $str = '<svg class="icon"><use xlink:href="/../../layout/images/symbols.svg#star"></use></svg>';
        $len = strlen($data['rating']);
        for ($i = 0; $i < $len; $i++) {
            $star .= $str;
        }
        if ($data['dateTimeOriginal']) {
            $datum = date('d.m.Y H:i:s', $data['dateTimeOriginal']);
        } else {
            $datum = $data['ImgDateManual'];
        }
        echo '<h1>'.$data['imgTitle'].'</h1>';
        echo $data['imgDesc'] ? '<p>'.$photo->renderDescLinks($data['imgDesc']).'</p>' : '';
        echo '<figure>
            <a title="'.$i18n['photo'].': '.$data['imgTitle'].'" href="'.$imgFile.'">
            <img src="'.$imgFile.'" id="photo" alt="'.$data['imgTitle'].'"/></a>
            <figcaption>'.$data['imgTitle'].'<br>
             © '.ucfirst($i18n['photo']).' Simon Speich, www.speich.net</figcaption></figure>';
        echo '<div class="col colLeft">
    	    <ul>
    	        <li><span class="photoTxtLabel">'.$i18n['keywords'].':</span> '.($data['categories'] !== '' ? $data['categories'].'<br/>' : '').'</li>
    	        <li><span class="photoTxtLabel">'.$i18n['name'].':</span> '.$data['wissNameDe'].' - '.$data['wissNameEn'].'</li>
                <em><span class="photoTxtLabel">'.$i18n['scientific name'].':</span> <em>'.$data['wissNameLa'].' <span title="'.$data['sex'].'">'.$data['symbol'].'</span></em></em>
                </ul><ul>
                <li><span class="photoTxtLabel">'.$i18n['dimensions'].($dim['isCropped'] ? ' ('.$i18n['cropped'].') ' : '').':</span> '.$dim['w'].' x '.$dim['h'].' px</li>
                <li><span class="photoTxtLabel">'.$i18n['date'].':</span> '.$datum.'</li>
                <li><span class="photoTxtLabel">'.$i18n['order number'].':</span> '.$data['imgId'].'</li>
                <li><span class="photoTxtLabel">'.$i18n['file name'].':</span> '.$data['imgName'].'</li>
            </ul>
            <ul>
                <li><span class="photoTxtLabel">'.$i18n['place'].':</span> '.$data['locations'].'</li>
    	        <li><span class="photoTxtLabel">'.$i18n['country'].':</span> '.($data['countries'] ?? $data['country']).'</li>
            </ul>
            <p class="mRating"><span class="photoTxtLabel">'.$i18n['rating'].':</span> '.$star.'</p>
            </div>';

        echo '<div class="col colRight">';
        echo '<div id="map">';
        if ($data['showLoc'] === '0') {
            echo '<div id="mapNote">'.$i18n['Coordinates are not shown'].'</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';

        echo '<div id="exifInfo">
            <div class="col">
    	    <h3>'.$i18n['technical information'].' (Exif)</h3>';
        if ($data['model'] === 'Nikon SUPER COOLSCAN 5000 ED') {
            echo '<ul><li><span class="photoTxtLabel">'.$i18n['type of film'].':</span> '.$data['film'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].', '.$data['make'].'</li></ul>';
        } else {
            echo '<ul>
                <li><span class="photoTxtLabel">'.$i18n['exposure'].':</span> '.$data['exposureTime']." at ƒ".number_format($data['fNumber'],
                    1).'
    		    <li><span class="photoTxtLabel">ISO:</span> '.$data['iso'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['focal length'].':</span> '.$data['focalLength'].', '.$i18n['distance'].' : '.$data['focusDistance'].'</li>
    		    </ul>
    		    <ul>
    		    <li><span class="photoTxtLabel">'.$i18n['program'].':</span> '.$data['exposureProgram'].', '.$data['meteringMode'].'</li>
    		    <li><span class="photoTxtLabel">VR:</span> '.$data['vibrationReduction'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['flash'].':</span> '.$data['flash'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['lens'].':</span> '.($data['lensSpec'] !== '' ? $data['lensSpec'] : ($data['lens'] !== '' ? $data['lens'] : '')).'</li>
    	        <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$data['model'].'</li>
    	        </ul>';
        }
        echo '<ul>
            <li><span class="photoTxtLabel">'.$i18n['position'].' (GPS):</span> '.($data['showLoc'] === '1' ? $data['gpsLatitude'].' / '.$data['gpsLongitude'] : '').'</li>
        	<li><span class="photoTxtLabel">'.$i18n['hight'].' (GPS):</span> '.$data['gpsAltitude'].' m '.($data['gpsAltitudeRef'] === '1' ? 'b.s.l.' : 'a.s.l.').'</li>
        	</ul>';
        echo '</div>';

        echo '<div class="col">';
        echo '<h3>'.$i18n['database information'].'</h3>';
        echo '<ul><li><span class="photoTxtLabel">'.$i18n['added'].':</span> '.(!empty($data['dateAdded']) ? date('d.m.Y H:i:s',
                $data['dateAdded']) : '').'</li>
    	    <li><span class="photoTxtLabel">'.$i18n['changed'].':</span> '.(!empty($data['lastChange']) ? date('d.m.Y H:i:s',
                $data['lastChange']) : '').'</li>
            <li><span class="photoTxtLabel">'.$i18n['published'].':</span> '.(!empty($data['lastChange']) ? date('d.m.Y H:i:s',
                $data['datePublished']) : '').'</li></ul>';
        echo '<ul><li><span class="photoTxtLabel">'.$i18n['file format'].':</span> '.$data['fileType'].' ('.$data['fileSize'].')</li></ul>';
        echo '</div></div>';
        echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
    }

    private function renderLicense()
    {


        $htmlFooter = '<p><a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/3.0/deed.de"><img alt="Creative Commons Lizenzvertrag" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png" width="80" height="15"></a>
                    Alle Fotos stehen unter der <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de">Creative Commons Lizenz</a> zur Verfügung,
                    sofern der Bildautor folgendermassen angeben wird:<br>
                    <strong>Foto Simon Speich, www.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.</p>';
    }
}