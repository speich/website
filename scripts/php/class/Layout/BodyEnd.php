<?php


namespace speich\Layout;


use speich\WebsiteSpeich;
use WebsiteTemplate\Language;

class BodyEnd
{

    private Language $language;
    private WebsiteSpeich $web;

    public function __construct(WebsiteSpeich $website, Language $language)
    {
        $this->web = $website;
        $this->language = $language;
    }

    public function render(): string
    {
        return '</main>
            <div class="row5 footer-before layout-wide"></div>'.
            $this->renderFooter();
    }

    /**
     * @return string HTMLFooterElement
     */
    protected function renderFooter(): string
    {
        if ($this->language->get() === 'de') {
            $htmlFooter = '<p>© 2003-2022 speich.net, Konzept und Programmierung Simon Speich</p>';
            $htmlFooter .= '<p class="last-update">letzte Aktualisierung '.$this->web->getLastUpdate('d.m.Y').'</p>';
        } else {
            $htmlFooter = '<p>© 2003-2022 speich.net, concept und programming Simon Speich</p>';
            $htmlFooter .= '<p class="last-update">last update '.$this->web->getLastUpdate('d.m.Y').'</p>';
        }

        return '<footer class="row5">'.$htmlFooter.'</footer>';
    }
}