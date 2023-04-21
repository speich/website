<?php

use speich\Layout\BodyEnd;
use speich\Layout\BodyStart;
use speich\Layout\Head;
use speich\WebsiteSpeich;
use WebsiteTemplate\Language;

if (!str_contains($_SERVER['REQUEST_URI'], '/wp/')) {  // php needs to set this by itself
    date_default_timezone_set('Europe/Zurich');
}


require_once __DIR__.'/../../library/vendor/autoload.php';

$language = new Language();
$language->arrLang = ['de' => 'Deutsch', 'en' => 'English'];
$language->autoSet();

$web = new WebsiteSpeich();
$web->setLastUpdate('2023-04-20');
$web->setWebroot('/');
ini_set('default_charset', $web->charset);

$windowTitle = $language->get() === 'de' ? 'Naturfotografie und Webprogrammierung' : 'Nature photography and web programming';
$web->pageTitle = 'Simon Speich - '.$windowTitle;

require_once __DIR__.'/../../layout/inc_nav.php';

$head = new Head($web->getWebRoot());
$bodyStart = new BodyStart($web->getWebRoot(), $language);
$bodyEnd = new BodyEnd($web, $language);