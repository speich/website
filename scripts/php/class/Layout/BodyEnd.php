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
        $isPhoto = str_contains($_SERVER['REQUEST_URI'], '/photo');
        if ($this->language->get() === 'de') {
            $htmlFooter = ($isPhoto ? '<div>' : '').'<p>© 2003-2022 speich.net, Konzept und Programmierung Simon Speich</p>';
            $htmlFooter .= '<p class="last-update">letzte Aktualisierung '.$this->web->getLastUpdate('d.m.Y').'</p>'.($isPhoto ? '</div>' : '');
            if ($isPhoto) {
                $htmlFooter .= '<p><a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/3.0/deed.de"><img alt="Creative Commons Lizenzvertrag" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png" width="80" height="15"></a>
                    Alle Fotos stehen unter der <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de">Creative Commons Lizenz</a> zur Verfügung,
                    sofern der Bildautor folgendermassen angeben wird:<br>
                    <strong>Foto Simon Speich, www.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.</p>';
            }
        } else {
            $htmlFooter = ($isPhoto ? '<div>' : '').''.'<p>© 2003-2022 speich.net, concept und programming Simon Speich</p>';
            $htmlFooter .= '<p class="last-update">last update '.$this->web->getLastUpdate('d.m.Y').'</p>'.($isPhoto ? '</div>' : '');
            if ($isPhoto) {
                $htmlFooter .= '<p><a rel="license" href="https://creativecommons.org/licenses/by-nc/3.0/"><img alt="Creative Commons licence" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png" width="80" height="15"></a>
                All photos on this website are licenced under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution 3.0 Unported License</a>:<br>
                <strong>Photo Simon Speich, www.speich.net</strong>. For a commercial licence or higher resolution please <a href="/contact/contact.php">contact</a> the author.</p>';
            }
        }

        return '<footer class="row5">'.$htmlFooter.'</footer>';
    }
}