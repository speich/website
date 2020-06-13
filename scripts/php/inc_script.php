<?php

use speich\WebsiteSpeich;
use WebsiteTemplate\Language;



date_default_timezone_set('Europe/Zurich');

// make include paths available to pages independent on subdir they reside in
$path = __DIR__.'/../../';
$incPath = $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);


require_once __DIR__.'/../../library/vendor/autoload.php';

$lang = new Language();
$lang->arrLang = ['de' => 'Deutsch', 'en' => 'English'];
$lang->autoSet();

$web = new WebsiteSpeich();
$web->lastUpdate = '13.06.2030';
$web->setWebroot('/');
ini_set('default_charset', $web->charset);
$isPhoto = strpos($_SERVER['REQUEST_URI'], '/photo') !== false;
if ($lang->get() === 'de') {
    $windowTitle = 'Fotografie und Webprogrammierung';
    $htmlFooter['de'] = ($isPhoto ? '<div>' : '').'<p>© 2003-2019 speich.net, Konzept und Programmierung Simon Speich</p>';
    $htmlFooter['de'] .= '<p class="last-update">letzte Aktualisierung '.$web->lastUpdate.'</p>'.($isPhoto ? '</div>' : '');
    if ($isPhoto) {
        $htmlFooter['de'] .= '<p><a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/3.0/deed.de"><img alt="Creative Commons Lizenzvertrag" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png"></a>
        Alle Fotos stehen unter der <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de">Creative Commons Lizenz</a> zur Verfügung,
        sofern der Bildautor folgendermassen angeben wird:<br>
        <strong>Foto Simon Speich, www.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.</p>';
    }

}
else {
	$windowTitle = 'Photography and web programming';
    $htmlFooter['en'] = ($isPhoto ? '<div>' : '').''.'<p>© 2003-2019 speich.net, concept und programming Simon Speich</p>';
    $htmlFooter['en'] .= '<p class="last-update">last update '.$web->lastUpdate.'</p>'.($isPhoto ? '</div>' : '');
    if ($isPhoto) {
        $htmlFooter['en'] .= '<p><a rel="license" href="https://creativecommons.org/licenses/by-nc/3.0/"><img alt="Creative Commons licence" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png"></a>
        All photos on this website are licenced under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution 3.0 Unported License</a>:<br>
        <strong>Photo Simon Speich, www.speich.net</strong>. For a commercial licence or higher resolution please <a href="/contact/contact.php">contact</a> the author.</p>';
    }
}
$web->pageTitle = 'Simon Speich - '.$windowTitle;

require_once 'inc_nav.php';
