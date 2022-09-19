<?php

namespace PhotoDb;

use PDO;
use WebsiteTemplate\Language;
use WebsiteTemplate\QueryString;

/**
 * Render information about a single photo as html.
 */
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
     * @param array $record
     * @param Language $lang
     * @param array $i18n internationalization
     */
    function render(array $record, Language $lang, array $i18n): void
    {
        // TODO: split into smaller methods
        $db = $this->db;
        $photo = new PhotoList($db);
        $query = new QueryString();
        $backPage = $lang->createPage('photo.php').$query->withString(null, ['imgId']);
        if (strpos($backPage, $lang->createPage('photo-mapsearch.php')) !== false) {
            $backPage = $lang->createPage('photo-mapsearch.php').'?'.$_SERVER['QUERY_STRING'];    // when coming from map via js lastPage was not set with latest query vars, use current
        }
        $imgFile = $db->webroot.$db->getPath('img').$record['imgFolder'].'/'.$record['imgName'];
        $dim = $photo->getImageSize($record);
        $star = '';

        $str = '<svg class="icon"><use xlink:href="/../../layout/images/symbols.svg#star"></use></svg>';
        $len = strlen($record['rating']);
        for ($i = 0; $i < $len; $i++) {
            $star .= $str;
        }
        if ($record['dateTimeOriginal']) {
            $datum = date('d.m.Y H:i:s', $record['dateTimeOriginal']);
        } else {
            $datum = $record['ImgDateManual'];
        }
        echo '<h1>'.$record['imgTitle'].'</h1>';
        echo $record['imgDesc'] ? '<p>'.$photo->renderDescLinks($record['imgDesc']).'</p>' : '';
        echo '<figure>
            <a title="'.$i18n['photo'].': '.$record['imgTitle'].'" href="'.$imgFile.'">
            <img src="'.$imgFile.'" id="photo" alt="'.$record['imgTitle'].'"/></a>
            <figcaption>'.$record['imgTitle'].'<br>
             © '.ucfirst($i18n['photo']).' Simon Speich, www.speich.net</figcaption></figure>';
        echo '<div class="col colLeft">
    	    <ul>
    	        <li><span class="photoTxtLabel">'.$i18n['keywords'].':</span> '.($record['categories'] !== '' ? $record['categories'].'<br/>' : '').'</li>
    	        <li><span class="photoTxtLabel">'.$i18n['name'].':</span> '.$record['wissNameDe'].' - '.$record['wissNameEn'].'</li>
                <em><span class="photoTxtLabel">'.$i18n['scientific name'].':</span> <em>'.$record['wissNameLa'].' <span title="'.$record['sex'].'">'.$record['symbol'].'</span></em></em>
                </ul><ul>
                <li><span class="photoTxtLabel">'.$i18n['dimensions'].($dim['isCropped'] ? ' ('.$i18n['cropped'].') ' : '').':</span> '.$dim['w'].' x '.$dim['h'].' px</li>
                <li><span class="photoTxtLabel">'.$i18n['date'].':</span> '.$datum.'</li>
                <li><span class="photoTxtLabel">'.$i18n['order number'].':</span> '.$record['imgId'].'</li>
                <li><span class="photoTxtLabel">'.$i18n['file name'].':</span> '.$record['imgName'].'</li>
            </ul>
            <ul>
                <li><span class="photoTxtLabel">'.$i18n['place'].':</span> '.$record['locations'].'</li>
    	        <li><span class="photoTxtLabel">'.$i18n['country'].':</span> '.($record['countries'] ?? $record['country']).'</li>
            </ul>
            <p class="mRating"><span class="photoTxtLabel">'.$i18n['rating'].':</span> '.$star.'</p>
            </div>';

        echo '<div class="col colRight">';
        echo '<div id="map">';
        if ($record['showLoc'] !== '1') {
            echo '<div id="mapNote">'.$i18n['Coordinates are not shown'].'</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '<p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';

        echo '<div id="exifInfo">
            <div class="col">
    	    <h3>'.$i18n['technical information'].' (Exif)</h3>';
        if ($record['model'] === 'Nikon SUPER COOLSCAN 5000 ED') {
            echo '<ul><li><span class="photoTxtLabel">'.$i18n['type of film'].':</span> '.$record['film'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$record['model'].', '.$record['make'].'</li></ul>';
        } else {
            echo '<ul>
                <li><span class="photoTxtLabel">'.$i18n['exposure'].':</span> '.$record['exposureTime'].' at ƒ'.number_format($record['fNumber'], 1).'
    		    <li><span class="photoTxtLabel">ISO:</span> '.$record['iso'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['focal length'].':</span> '.$record['focalLength'].', '.$i18n['distance'].' : '.$record['focusDistance'].'</li>
    		    </ul>
    		    <ul>
    		    <li><span class="photoTxtLabel">'.$i18n['program'].':</span> '.$record['exposureProgram'].', '.$record['meteringMode'].'</li>
    		    <li><span class="photoTxtLabel">VR:</span> '.$record['vibrationReduction'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['flash'].':</span> '.$record['flash'].'</li>
    		    <li><span class="photoTxtLabel">'.$i18n['lens'].':</span> '.($record['lensSpec'] !== '' ? $record['lensSpec'] : $record['lens']).'</li>
    	        <li><span class="photoTxtLabel">'.$i18n['model'].': </span>'.$record['model'].'</li>
    	        </ul>';
        }
        echo '<ul>
            <li><span class="photoTxtLabel">'.$i18n['position'].' (GPS):</span> '.($record['showLoc'] === '1' ? $record['gpsLatitude'].' / '.$record['gpsLongitude'] : '').'</li>
        	<li><span class="photoTxtLabel">'.$i18n['hight'].' (GPS):</span> '.$record['gpsAltitude'].' m '.($record['gpsAltitudeRef'] === '1' ? 'b.s.l.' : 'a.s.l.').'</li>
        	</ul>';
        echo '</div>';

        echo '<div class="col">';
        echo '<h3>'.$i18n['database information'].'</h3>';
        echo '<ul><li><span class="photoTxtLabel">'.$i18n['added'].':</span> '.(!empty($record['dateAdded']) ? date('d.m.Y H:i:s',
                $record['dateAdded']) : '').'</li>
    	    <li><span class="photoTxtLabel">'.$i18n['changed'].':</span> '.(!empty($record['lastChange']) ? date('d.m.Y H:i:s',
                $record['lastChange']) : '').'</li>
            <li><span class="photoTxtLabel">'.$i18n['published'].':</span> '.(!empty($record['lastChange']) ? date('d.m.Y H:i:s',
                $record['datePublished']) : '').'</li></ul>';
        echo '<ul><li><span class="photoTxtLabel">'.$i18n['file format'].':</span> '.$record['fileType'].' ('.$record['fileSize'].')</li></ul>';
        echo '</div></div>';
        echo '<p class="license">'.$this->renderLicense($record, $lang).'</p>
            <p><a href="'.$backPage.'">'.$i18n['back'].'</a></p>';
    }

    /**
     * Render the license of the photo
     * @param array $record
     * @param Language $lang
     * @return string html
     */
    private function renderLicense(array $record, Language $lang): string
    {
        $htmlDe = '<a rel="license" href="'.$record['licenseLink'].'" target="_blank"><img alt="Creative Commons Lizenzvertrag"
            src="'.$record['licenseLogo'].'" width="80" height="15"></a>Dieses Foto ist lizenziert unter einer <a rel="license" href="'.$record['licenseLink'].'" target="_blank">Creative Commons '.$record['licenseLabel'].'</a>.<br>
            <strong>© Foto Simon Speich, wwww.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.</p>';
        $htmlEn = '<a rel="license" href="'.$record['licenseLink'].'" target="_blank"><img alt="Creative Commons Lizenzvertrag"
            src="'.$record['licenseLogo'].'" width="80" height="15"></a>This photo is licensed under a <a rel="license" href="'.$record['licenseLink'].'" target="_blank">Creative Commons '.$record['licenseLabel'].'</a>.<br>
            <strong>© Photo Simon Speich, www.speich.net</strong>. For a commercial licence or higher resolution please <a href="/contact/contact.php">contact</a> the author.</p>';


        return $lang->get() === 'de' ? $htmlDe : $htmlEn;
    }
}