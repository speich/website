<?php

use speich\CspHeader;
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
$web::setLastUpdate('2024-06-22');
$web->setWebroot('/');
ini_set('default_charset', $web->charset);

$windowTitle = $language->get() === 'de' ? 'Naturfotografie und Webprogrammierung' : 'Nature photography and web programming';
$web->pageTitle = 'Simon Speich - '.$windowTitle;

require_once __DIR__.'/../../layout/inc_nav.php';


// TODO: add preload directive to enable hsts header, @see https://hstspreload.org/?domain=lfi.ch
header('Strict-Transport-Security: max-age=63072000; includeSubdomains');
$cspHeader = new CspHeader();
if ($web->page === 'remoteFileExplorer.php') {
    $cspHeader->set('script-src', "'self' 'unsafe-eval'");
} elseif ($language->removePostfix($web->page) === 'photo-mapsearch.php' ||
    $language->removePostfix($web->page) === 'photo-detail.php') {
    // TODO: use strict CSP instead of allowlist
    $cspHeader->set('script-src',
        "'self' 'unsafe-inline' 'unsafe-eval' https://*.googleapis.com https://*.gstatic.com *.google.com https://*.ggpht.com *.googleusercontent.com blob:");
    $cspHeader->set('img-src', "'self'  mirrors.creativecommons.org https://*.googleapis.com https://*.gstatic.com *.google.com  *.googleusercontent.com data:");
    $cspHeader->set('frame-src', '*.google.com');
    $cspHeader->set('connect-src', "'self' https://*.googleapis.com *.google.com https://*.gstatic.com  data: blob:");
    $cspHeader->set('font-src', "'self' https://fonts.gstatic.com");
    $cspHeader->set('style-src', "'self' 'unsafe-inline' https://fonts.googleapis.com");
    $cspHeader->set('worker-src', 'blob:');
}
//header($cspHeader->toString());
$head = new Head($web->getWebRoot(), $cspHeader);
$bodyStart = new BodyStart($web->getWebRoot(), $language);
$bodyEnd = new BodyEnd($web, $language);