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
$web::setLastUpdate('2025-09-28');
$web->setWebroot('/');
ini_set('default_charset', $web->charset);

$windowTitle = $language->get() === 'de' ? 'Naturfotografie und Webprogrammierung' : 'Nature photography and web programming';
$web->pageTitle = 'Simon Speich - '.$windowTitle;

require_once __DIR__.'/../../layout/inc_nav.php';


// TODO: add preload directive to enable hsts header, @see https://hstspreload.org/?domain=lfi.ch
header('Strict-Transport-Security: max-age=63072000; includeSubdomains;');
// relax CSP for specific sites
$cspHeader = new CspHeader();
if ($web->page === 'remoteFileExplorer.php') {
    $cspHeader->set('script-src', "'self' 'unsafe-eval'");
    $cspHeader->set('style-src', "'self' 'unsafe-inline'");
} else if (str_contains($web->path, '/articles/') || str_contains($web->path, '/wp/wp-admin/')) {
    $cspHeader->set('script-src', "'self' 'unsafe-inline' 'unsafe-eval'");
    $cspHeader->set('img-src', "'self' data: img.chmedia.ch secure.gravatar.com s.w.org");
    $cspHeader->set('style-src', "'self' 'unsafe-inline'");
    $cspHeader->set('worker-src', "'self' blob:");
    $cspHeader->set('font-src', "'self' data:");
    $cspHeader->set('frame-src', "'self' blob:");
}
header($cspHeader->toString());
$head = new Head($web->getWebRoot(), $cspHeader);
$bodyStart = new BodyStart($web->getWebRoot(), $language);
$bodyEnd = new BodyEnd($web, $language);