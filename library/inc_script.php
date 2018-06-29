<?php
use WebsiteTemplate\Language;
use WebsiteTemplate\Website;

date_default_timezone_set('Europe/Zurich');

// make include paths available to pages independent on subdir they reside in
$path = __DIR__.'/../';
$incPath = $path.'class'.PATH_SEPARATOR;
$incPath.= $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);

include_once 'Website.php';
include_once 'Language.php';
include_once 'PagedNav.php';

$lang = new Language();
$lang->arrLang = ['de', 'en'];
$lang->arrLangLong = ['de' => 'Deutsch', 'en' => 'English'];
$lang->set();

$web = new Website();
Website::$lastUpdate = '29.06.2018';
$web->setWebroot('/');

ini_set('default_charset', $web->charset);

if ($lang->get() === 'de') {
	$windowTitle = 'Fotografie und Webprogrammierung';
}
else {
	$windowTitle = 'Photography and web programming';
}
$web->pageTitle = 'Simon Speich - '.$windowTitle;


include_once 'inc_nav.php';