<?php
use WebsiteTemplate\Language;

set_time_limit(300);
date_default_timezone_set('Europe/Zurich');

// make include paths available to pages independent on subdir they reside in
$path = __DIR__.'/../';
$incPath = $path.'class'.PATH_SEPARATOR;
$incPath.= $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);

include_once 'Language.php';
include_once 'Menu.php';
include_once 'PagedNav.php';

$web = new Language();
$web->lastUpdate = '21.06.2015';
$lang = $web->getLang();
$web->setLang($lang);

ini_set('default_charset', $web->charset);

if ($lang === 'de') {
	$windowTitle = 'Fotografie und Webprogrammierung';
	$metaDescription = 'Website von Simon Speich über Fotografie und Webprogrammierung';
	$metaKeywords = 'Simon Speich, Schweiz, Fotografie, Webprogrammierung, Bilddatenbank, dojo, dojotoolkit, JavaScript, PHP, Foto, Photographie';
}
else {
	$windowTitle = 'Photography and web programming';
	$metaDescription = 'Simon Speich\'s website about photography and web programming';
	$metaKeywords = 'Simon Speich, Switzerland, photography, web programming, photo archive, dojo, dojotoolkit, JavaScript, PHP';
}
$web->pageTitle = 'speich.net - '.$windowTitle;

include_once 'inc_nav.php';

