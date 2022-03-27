<?php

use speich\Layout\BodyEnd;
use speich\Layout\BodyStart;
use speich\Layout\Head;
use speich\WebsiteSpeich;
use WebsiteTemplate\Language;



date_default_timezone_set('Europe/Zurich');


require_once __DIR__.'/../../library/vendor/autoload.php';

$language = new Language();
$language->arrLang = ['de' => 'Deutsch', 'en' => 'English'];
$language->autoSet();

$web = new WebsiteSpeich();
$web->setLastUpdate('2022-03-27');
$web->setWebroot('/');
ini_set('default_charset', $web->charset);
$windowTitle = $language->get() === 'de' ? 'Fotografie und Webprogrammierung' : 'Photography and web programming';
$web->pageTitle = 'Simon Speich - '.$windowTitle;

require_once __DIR__.'/../../layout/inc_nav.php';

$head = new Head($web->getWebRoot());
$bodyStart = new BodyStart($web->getWebRoot(), $language);
$bodyEnd = new BodyEnd($web, $language);