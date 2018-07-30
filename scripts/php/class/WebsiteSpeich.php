<?php

namespace speich;

use WebsiteTemplate\Website;


class WebsiteSpeich extends Website
{
    /** @var array keys that are allowed in the query string */
    protected $whitelistQueryString = ['pg', 'theme', 'country', 'qual', 'lang', 'sort', 'imgId', 'numRecPp', 'lat1', 'lat2', 'lng1', 'lng2'];

    /**
     * @return array
     */
    public function getWhitelistQueryString(): array
    {
        return $this->whitelistQueryString;
    }

}