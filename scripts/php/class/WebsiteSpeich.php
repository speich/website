<?php

namespace speich;

use WebsiteTemplate\Website;


class WebsiteSpeich extends Website
{
    /** @var array keys that are allowed in the query string */
    protected array $whitelistQueryString = [
        'pg',
        'theme',
        'country',
        'qual',
        'lang',
        'sort',
        'imgId',
        'numPerPg',
        'q'
    ];

    /** @var array whitelisted public domains */
    protected array $domains = ['speich.net', 'www.speich.net'];

    /** @var array whitelisted developer domains */
    protected array $domainsDev = ['speich.test', 'speich.localhost'];

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
    public function getDomains(string $type = 'public'): array
    {
        return match ($type) {
            'dev' => $this->domainsDev,
            'all' => array_merge($this->domainsDev, $this->domains),
            default => $this->domains,
        };
    }

    /**
     * @return array
     */
    public function getWhitelistQueryString(): array
    {
        return $this->whitelistQueryString;
    }

}