<?php
class Website {
	private $url, $page, $dir, $ip, $host, $docRoot;
	/** Set webroot to subbfolder */
	private $webRoot = '';

	/** Store querystring without leading question mark */
	private $query = '';

	/** Date of last website update */
	protected $lastUpdate;

	private $defaultPage = 'index.php';

	protected $windowTitle = '';

	/** current language */
	private static $lang = '';

	/** default language */
	private $langDefault = 'de';

	/** @var array Holds all available languages and the corresponding file name extensions */
	private static $arrLang = array('de', 'en');

	private static $arrLangLong = array('de' => 'Deutsch', 'en' => 'English');

	/** back page */
	private $backPage = '';

	/** namespace for session variables */
	private static $namespace = 'website';

	/**
	 * @constructor
	 */
	public function __construct() {
		// these vars are not reliable, so be careful when using them
		$arrUrl = parse_url($_SERVER["REQUEST_URI"]);
		$arrPath = pathinfo($arrUrl['path']);
		$this->page = $arrPath['basename'];
		$this->dir = $arrPath['dirname'];
		// note www.speich.net/articles/?p=12 returns / and not /articles
		if (strpos($this->page, '.') === false) {
			$this->page = '';
			$this->dir = rtrim($arrUrl['path'], '/');
		}
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->host = $_SERVER['HTTP_HOST'];
		$this->webRoot = '/';
		$this->docRoot = $_SERVER['DOCUMENT_ROOT'];
		$this->url = $_SERVER["REQUEST_URI"];
		$this->query = array_key_exists('query', $arrUrl) ? $arrUrl['query'] : '';
	}

	/**
	 * Returns physical path to webroot.
	 * Path is returned without a trailing slash and if
	 * site resides in a subfolder, this subfolder is appended to the path.
	 * @return string
	 * @see Website::setWebRoot()
	 */
	public function getDocRoot() {
		return $this->docRoot.$this->getWebRoot();
	}

	/**
	 * Sets the website root to a subfolder.
	 *
	 * It is not always possible in a webproject to use relative paths.
	 * But with absolute or physical paths you could run into problems:
	 * If you want to move your project into another subfolder or you
	 * publish your website into different folders
	 * e.g. www.mywebsite.ch and www.mywebsite.ch/developerversion/
	 * In these cases use the methods SetWebRoot() and getWebRoot().
	 *
	 * @param string $path webroot
	 */
	function setWebRoot($path) {
		$path = trim($path, '/');
		$this->webRoot = '/'.$path.'/';
	}

	/**
	 * Returns the website's root folder.
	 * Returns an absolute path with trailing slash.
	 * @return string
	 */
	function getWebRoot() {
		return $this->webRoot;
	}

	public function getIp() {
		return $this->ip;
	}

	/**
	 * Returns the host.
	 * Contains protocol, first and second level domain.
	 * @return String
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Returns complete current url.
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Returns the current web page.
	 * @return string
	 */
	public function getPage() {
		if ($this->dir == '\\' || $this->dir == '/') {
			return '';
		}
		else {
			return $this->page;
		}
	}

	/**
	 * Saves current url to a session variable.
	 * Stores Url to use to go back in a session variable. If argument
	 * is provided it is used instead.
	 * @param string $url [optional]
	 * @param string $namespace [optional]
	 */
	public function setLastPage($url = null, $namespace = null) {
		$namespace = is_null($namespace) ? self::$namespace : $namespace;
		if (isset($url)) {
			$_SESSION[$namespace]['backPage'] = $url;
		}
		else {
			$_SESSION[$namespace]['backPage'] = $this->getUrl();
		}
	}

	/**
	 * Returns the page to go back to.
	 * @param string $namespace [optional]
	 * @return null|string
	 */
	public function getLastPage($namespace = null) {
		$namespace = is_null($namespace) ? self::$namespace : $namespace;
		if (!isset($_SESSION[$namespace]['backPage'])) {
			return null;
		}
		else {
			return $_SESSION[$namespace]['backPage'];
		}
	}

	/**
	 * Resets the saved url.
	 * @param string $namespace [optional]
	 */
	public function resetLastPage($namespace = null) {
		$namespace = is_null($namespace) ? self::$namespace : $namespace;
		unset($_SESSION[$namespace]['BackPage']);
	}

	/**
	 * Returns the current web directory.
	 * @return string
	 */
	public function getDir() {
		return $this->dir;
	}

	/**
	 * Takes an array of key-value pairs as input and adds it to the query string.
	 *
	 * If there is already the same key in the query string its value gets overwritten
	 * with the new value. Saves the added key-value pairs to be reused (query property is changed).
	 * @return string querystring
	 * @param array $arrQuery
	 */
	public function addQuery($arrQuery) {
		if ($this->query != '') { // check if query var(s) already exists -> overwrite with new or append
			parse_str($this->query, $arrVar);
			$arrQuery = array_merge($arrVar, $arrQuery); // if arrays have same string keys, the later key will overwrite the previous
			$this->query = http_build_query($arrQuery); // update local $this->Query
		}
		else {
			$this->query = http_build_query($arrQuery);
		}
		return '?'.htmlspecialchars($this->query);
	}

	/**
	 * Returns the current query string.
	 *
	 * You can optionally add or remove key-value pairs from the returned querystring without changing it,
	 * e.g. same as AddQuery or DelQuery but Query property remains unchanged.
	 * If second argument is an array, the first array is used to add, the second to delete. Otherwise
	 * second argument is:
	 * 1 = add (default), 2 = remove
	 * @return string query string
	 * @param array [$arrQuery]
	 * @param integer|array [$modifier]
	 */
	public function getQuery($arrQuery = null, $modifier = 1) {
		if (is_null($arrQuery)) {
			if ($this->query != '') {
				return '?'.htmlspecialchars($this->query);
			}
			else {
				return '';
			}
		}
		else {
			if (is_array($modifier)) { // all of second array is to delete
				$str = $this->getQuery($modifier, 2);
				$str = str_replace('?', '', $str);
				$str = html_entity_decode($str);
				parse_str($str, $arrVar);
				$arrQuery = array_merge($arrVar, $arrQuery);
				return '?'.htmlspecialchars(http_build_query($arrQuery));
			}
			else { // first array is either add or delete, no second array
				if ($this->query != '') { // check if query var(s) already exists -> overwrite with new or append
					parse_str($this->query, $arrVar);
					if ($modifier == 1) {
						$arrQuery = array_merge($arrVar, $arrQuery); // if arrays have same string keys, the later key will overwrite the previous
						return '?'.htmlspecialchars(http_build_query($arrQuery)); // update local $this->Query
					}
					else {
						if ($modifier == 2) {
							$arr = array(); // make array keys for array_diff_key
							foreach ($arrQuery as $QueryVar) {
								$arr[$QueryVar] = null;
							}
							$arrQuery = array_diff_key($arrVar, $arr);
							if (count($arrQuery) > 0) {
								return '?'.htmlspecialchars(http_build_query($arrQuery));
							}
							else {
								return '';
							}
						}
					}
				}
				else {
					if ($modifier == 1) {
						return '?'.htmlspecialchars(http_build_query($arrQuery));
					}
					else {
						return '';
					}
				}
			}
		}
	}

	/**
	 * Removes key-value pairs from querystring before returning it.
	 * @return array
	 * @param array|string $arrQuery Object
	 */
	public function delQuery($arrQuery) {
		if (!is_array($arrQuery)) {
			$arrQuery = array($arrQuery);
		}
		if ($this->query != '') {
			foreach ($arrQuery as $queryVar) {
				$pattern = '/&?'.$queryVar.'=[^\&]*/';
				$this->query = preg_replace($pattern, '', $this->query);
			}
		}
		$this->query = preg_replace('/^\&/', '', $this->Query); // if first key-value pair was removed change ampersand to questions mark
		return htmlspecialchars($this->getQuery());
	}

	/**
	 * Returns date of last website update.
	 * @return string
	 */
	public function getLastUpdate() {
		return $this->lastUpdate;
	}

	/**
	 * Sets the date of the last website update.
	 * @param string $lastUpdate
	 */
	public function setLastUpdate($lastUpdate) {
		$this->lastUpdate = $lastUpdate;
	}

	/**
	 * Returns the language code.
	 * @return string
	 */
	public function getLang() {
		// explicitly changing lang?
		if (isset($_GET['lang'])) {
			$regExpr = "/[^".implode('', $this->getArrLang())."]/";
			self::$lang = $lang = preg_replace($regExpr, '', $_GET['lang']);
		} // session?
		else {
			if (isset($_SESSION[self::$namespace]['lang'])) {
				self::$lang = $_SESSION[self::$namespace]['lang'];
			} // check for lang preference?
			else {
				if (isset($_COOKIE['lang'])) {
					self::$lang = $_COOKIE['lang'];
				} // check language header
				else {
					$lang = $this->getLangFromHeader();
					self::$lang = $lang ? $lang : $this->langDefault;
				}
			}
		}

		return self::$lang;
	}

	/**
	 * Returns an array containing the content from the accept-language header.
	 * e.g. Array (
	 *    [en-ca] => 1
	 *    [en] => 0.8
	 *    [en-us] => 0.6
	 *    [de-de] => 0.4
	 *    [de] => 0.2
	 * )
	 * @see http://www.thefutureoftheweb.com/blog/use-accept-language-header
	 * @return array
	 */
	public function getLangHeader() {
		$arr = array();

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $arrLang);

			if (count($arrLang[1]) > 0) {
				// create a list like "en" => 0.8
				$arr = array_combine($arrLang[1], $arrLang[4]);

				// set default to 1 for any without q factor
				foreach ($arr as $lang => $val) {
					if ($val === '') {
						$arr[$lang] = 1;
					}
				}
				// sort list based on value
				arsort($arr, SORT_NUMERIC);
			}
		}
		return $arr;
	}

	public function getLangFromHeader() {
		$arr = $this->getLangHeader();

		// look through sorted list and use first one that matches our languages
		foreach ($arr as $lang => $val) {
			$lang = explode('-', $lang);
			if (in_array($lang[0], self::$arrLang)) {
				return $lang[0];
			}
		}
		return false;
	}

	public function getArrLang() {
		return self::$arrLang;
	}

	/**
	 * Sets the language.
	 * @param string $lang
	 */
	public function setLang($lang = null) {
		if (isset($lang) && in_array($lang, self::$arrLang)) {
			setcookie('lang', $lang, time() + 3600 * 24 * 365);
			$_SESSION[self::$namespace]['lang'] = $lang;
			self::$lang = $lang;
		}
	}

	/**
	 * Returns a HTML string with links to select the language.
	 * @return string Html
	 */
	public function renderLangNav() {
		if (strpos($this->getDir(), '/articles') !== false) {
			return '';
		}
		$str = '';
		$str .= '<ul id="navLang" class="nav">';

		foreach (self::$arrLang as $lang) {
			$page = $this->getPage();
			if ($page === '' || $page === 'default.php') {
				$url = 'default.php';
			}
			else {
				$langDir = str_replace('/'.$this->getLang().'/', '/'.$lang.'/', $this->getDir().'/');
				$url = $langDir.$this->getPage();
			}
			$url = $url.$this->getQuery(array('lang' => $lang));

			$str .= '<li';
			if ($lang == $this->getLang()) {
				$str .= ' class="navActive"';
			}
			$str .= '><a href="'.$url.'" title="'.self::$arrLangLong[$lang].'">'.strtoupper($lang).'</a>';
			$str .= '</li>';
		}
		$str .= '</ul>';
		return $str;
	}

	/**
	 * Returns the title of the browser window.
	 * @return string
	 */
	public function getWindowTitle() {
		return $this->windowTitle;
	}

	/**
	 * Set the title of the browser window.
	 * @param string $title
	 * @return void
	 */
	public function setWindowTitle($title) {
		$this->windowTitle = $title;
	}

	public function getNamespace() {
		return self::$namespace;
	}
}