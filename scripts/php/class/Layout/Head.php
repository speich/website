<?php


namespace speich\Layout;

use speich\CspHeader;

/**
 * Class Head
 * Render the content of the HtmlHeadElement.
 * @package speich\Layout
 */
class Head
{
    private string $webroot;
    private CspHeader $csp;

    /**
     * Head constructor.
     * @param string $webroot
     */
    public function __construct(string $webroot, CspHeader $csp)
    {
        $this->webroot = $webroot;
        $this->csp = $csp;
    }

    /**
     * Render the content of the html head element.
     * @return string html
     */
    public function render(): string
    {
        $webroot = $this->webroot;

        return '<meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
            <link rel="preload" href="'.$webroot.'layout/fonts/open-sans-v17-latin-600.woff2" as="font" crossorigin>
            <link rel="preload" href="'.$webroot.'layout/fonts/open-sans-v17-latin-regular.woff2" as="font" crossorigin>
            <link href="'.$webroot.'layout/fonts/open-sans-v17-latin-italic.woff2" as="font" crossorigin>
            <link href="'.$webroot.'layout/fonts/open-sans-v17-latin-600italic.woff2" as="font" crossorigin>
            <link href="'.$webroot.'layout/normalize.min.css" rel="stylesheet" type="text/css" nonce="'.$this->csp->nonceStyle.'">
            <link href="'.$webroot.'layout/format.min.css" rel="stylesheet" type="text/css" nonce="'.$this->csp->nonceStyle.'">
            <link href="'.$webroot.'layout/layout.min.css" rel="stylesheet" type="text/css" nonce="'.$this->csp->nonceStyle.'">
            <link href="'.$webroot.'layout/images/favicon.png" type="image/png" rel="shortcut icon">';
    }
}