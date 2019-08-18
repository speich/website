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
$lang->autoSet();
$lang->arrLang = ['de' => 'Deutsch', 'en' => 'English'];

$web = new WebsiteSpeich();
$web->lastUpdate = '29.06.2019';
$web->setWebroot('/');
ini_set('default_charset', $web->charset);

// TODO: just a quick fix to set wordpress language correctly
/*if (preg_match('/\/en(\/|$)/', $web->path) === 1) {
    $lang->set('en');
}
else if (preg_match('/\/de(\/|$)/', $web->path) === 1) {
    $lang->set('de');
}
else {
    $lang->set();   // auto from query string or browser
}*/

if ($lang->get() === 'de') {
	$windowTitle = 'Fotografie und Webprogrammierung';
}
else {
	$windowTitle = 'Photography and web programming';
}
$web->pageTitle = 'Simon Speich - '.$windowTitle;

require_once 'inc_nav.php';