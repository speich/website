<?php


namespace speich\Layout;


use speich\LanguageMenu;
use WebsiteTemplate\Language;
use WebsiteTemplate\Menu;

class BodyStart
{
    private string $webroot;

    /** @var Language */
    private Language $language;


    /**
     * Head constructor.
     * @param string $webroot
     */
    public function __construct(string $webroot, Language $language)
    {
        $this->webroot = $webroot;
        $this->language = $language;
    }

    public function render(Menu $mainNav, Menu $sideNav, LanguageMenu $langNav): string
    {
        $bodyStart = $this->renderHeader();
        $bodyStart .= '<div class="row1 header-after layout-medium"></div>
            <div class="row2 nav-before layout-wide"></div>
            <div class="nav row2">
        	    <nav class="main">'.$mainNav->render().'</nav>
        	    <nav class="lang">'.$langNav->render().'</nav>
            </div>
            <div class="row2 nav-after layout-wide"></div>
            <nav class="sub">'.$sideNav->render().'</nav>
            <main>';
        return $bodyStart;
    }

    /**
     * @return string HtmlHeaderElement
     */
    protected function renderHeader(): string
    {
        return '<header class="row1">
            <a href="'.$this->webroot.$this->language->createPage('index.php').'">
	        <div class="speich-logo">
	        <div class="text">
	          <div>speich</div>
	          <div>.net</div>
	        </div>
	        <svg><use xlink:href="'.$this->webroot.'layout/images/symbols.svg#speich-logo"></use></svg>
	        </div>
        </a>
        </header>';
    }
}