<?php

namespace speich;


/**
 * Class LanguageMenu
 * @package speich
 */
class LanguageMenu extends \WebsiteTemplate\LanguageMenu
{

    /**
     * Returns a HTML string with links to the current page in all available languages.
     * Overrides rendering of menu links without checking if file exists on disk in case of virtual /articles/{lang}/... directory.
     * Sets the new link directly to /articles/{newLang}/...
     * @return string html
     */
    public function render(): string
    {
        $url = $_SERVER['REQUEST_URI'];
        if (strpos($url, '/articles/') === false) {
            return parent::render();
        }
        $language = $this->lang;
        $str = '';
        $str .= '<ul id="'.$this->ulId.'" class="'.$this->ulClass.'">';
        foreach ($language->arrLang as $lang => $label) {
            if ($lang === $language->get()) {
                $str .= '<li class="'.$this->liClassActive.'">'.$label.'</li>';
            }
            else {
                $str .= '<li><a href="/articles/'.$lang.'/">'.$label.'</a></li>';
            }
        }
        $str .= '</ul>';

        return $str;
    }
}