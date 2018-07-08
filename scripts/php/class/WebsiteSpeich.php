<?php

namespace speich;

use WebsiteTemplate\Website;


class WebsiteSpeich extends Website
{
    /** @var array keys that are allowed in the query string */
    protected $whitelistQueryString = ['pg', 'theme', 'country', 'qual', 'lang', 'sort', 'imgId', 'numRecPp'];

    /**
     * @return array
     */
    public function getWhitelistQueryString(): array
    {
        return $this->whitelistQueryString;
    }

}