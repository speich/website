<?php
session_start();
set_time_limit(300);
date_default_timezone_set('Europe/Zurich');
ini_set('default_charset', 'utf-8');

$webroot = '';

// make include paths available to pages independent on subdir they reside in
$path = $_SERVER['DOCUMENT_ROOT'].$webroot;
$incPath = $path.'/class'.PATH_SEPARATOR;
$incPath.= $path.'/layout'.PATH_SEPARATOR;
$incPath.= $path.'/library';
set_include_path($incPath);
include_once 'Website.php';
include_once 'PhotoDb.php';
include_once 'PhotoDbNav.php';
include_once 'Menu.php';
include_once 'PagedNav.php';

$web = new Website();
$web->setLastUpdate('23.02.2013');
$lang = $web->getLang();
$web->setLang($lang);

if ($lang === 'de') {
	$windowTitle = 'Fotografie und Webprogrammierung';
	$metaDescription = 'Website von Simon Speich über Fotografie und Webprogrammierung';
	$metaKeywords = 'Fotografie, Webprogrammierung, Bilddatenbank, dojo, dojotoolkit, JavaScript, PHP, Foto, Photographie';
}
else {
	$windowTitle = 'Photography and web programming';
	$metaDescription = 'Simon Speich\'s website about photography and web programming';
	$metaKeywords = 'photography, web programming, photo archive, dojo, dojotoolkit, JavaScript, PHP';
}
$web->setWindowTitle('speich.net ::: '.$windowTitle);

include_once 'inc_nav.php';