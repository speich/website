<?php

namespace speich;

use WebsiteTemplate\Website;


class WebsiteSpeich extends Website
{
    /** @var array keys that are allowed in the query string */
    protected $whitelistQueryString = ['pg', 'theme', 'country', 'qual', 'lang', 'sort', 'imgId', 'numRecPp', 'lat1', 'lat2', 'lng1', 'lng2'];

    /** @var array whitelisted public domains */
    private $domains = ['speich.ch', 'www.speich.ch'];

    /** @var array whitelisted developer domains */
    private $domainsDev = ['speich', 'localhost'];

    /**
     * Constructs the class.
     * Sets default properties for language
     */
    public function __construct()
    {
        $domains = $this->getDomains('all');
        parent::__construct($domains);
    }

    /**
     * Returns the whitelisted domains.
     * Returns the whitelisted domains by type 'public', 'dev' or 'all'
     * @param string $type
     * @return array
     */
    public function getDomains($type = 'public'): array
    {
        switch ($type) {
            case 'dev':
                $domains = $this->domainsDev;
                break;
            case 'all':
                $domains = array_merge($this->domainsDev, $this->domains);
                break;
            case 'public':
                $domains = $this->domains;
                break;
            default:
                $domains = $this->domains;
        }

        return $domains;
    }

    /**
     * @return array
     */
    public function getWhitelistQueryString(): array
    {
        return $this->whitelistQueryString;
    }

}