<?php

namespace PhotoDb;

use PDO;
use WebsiteTemplate\Language;
use function array_slice;
use function count;


/**
 * Class Search
 */
class SearchQuery
{

    public PDO $db;

    /**
     * SearchKeywords constructor.
     * @param PDO $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Extract words and phrases from a string.
     * Treats several words as a phrase if they are wrapped in double parentheses. Parentheses are included in the array items indicating
     * that the search needs to match exactly.
     * The length of the returned array (number of words) can be limited with the argument $maxWords. Default is 4.
     * The number of character a string has to contain to be counted as a word can be set with the parameter $minWordLength.
     * @param string $text
     * @param int $maxWords
     * @param int $minWordLength
     * @return array
     */
    public static function extractWords(string $text, $maxWords = 6, $minWordLength = 3): array
    {
        $pattern = '/".{'.$minWordLength.',}?"|\S{'.$minWordLength.',}/iu'; // matches whole words or several words encompassed with double quotations
        preg_match_all($pattern, $text, $words);
        $words = array_slice($words[0], 0, $maxWords); // throw away words exceeding limit

        return $words;
    }

    /**
     * Creates the FTS4 search query.
     * Postfixes each word with an asterix if an exact match is not wanted (e.g. not wrapped in parentheses).
     * @param array $words
     * @param string $lang
     * @return string
     */
    public static function createQuery($words, $lang): string
    {
        $search = '';
        $len = count($words);
        $language = new Language();
        $lang = $language->isValid($lang) === true ? $lang : $language->getDefault();
        foreach ($words as $i => $val) {
            if (str_contains($val, '"')) {
                $search .= $val;
            }
            else {
                $search .= $val.'*';
            }
            $search .= $i < $len - 1 ? ' OR ' : '';
        }

        //return 'Language:'.$lang.' OR Language:-'.$lang.' '.$search;   // on server only standard query syntax is enabled
        return $search;   // on server only standard query syntax is enabled
        //return '('.$search.')';
    }
}