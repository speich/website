<?php

namespace PhotoDb;


/**
 * Class Search
 */
class Fts4Ranking
{
    /* @var string also return the number of phrases used in the search query */
    public const MATCHINFO_NUM_PHRASES = 'p';

    /* @var string also return the number of columns in the FTS4 table excluding the docid */
    public const MATCHINFO_NUM_COLS = 'c';

    public const MATCHINFO_PHRASES_BY_COLS_INFO = 'x';

    /** @var string also return the number of rows in the FTS4 table. */
    public const MATCHINFO_NUM_ROWS = 'n';

    /** @var string also return */
    public const MATCHINFO_NUM_TOKEN_AVERAGE = 'a';

    public const MATCHINFO_NUM_TOKEN = 'l';

    /**
     * @param string $matchInfo
     * @return array|false
     */
    public function toInt(string $matchInfo)
    {
        return unpack('L*', $matchInfo);
    }

    /**
     * @param $col
     * @param string $args
     * @return string
     */
    public function matchInfoData($col, string $args = self::MATCHINFO_NUM_TOKEN_AVERAGE)
    {
        //$args = self::MATCHINFO_NUM_PHRASES.self::MATCHINFO_NUM_COLS.self::MATCHINFO_PHRASES_BY_COLS_INFO
        $arrInt = $this->toInt($col);

        return implode(' ', $arrInt);
    }


}