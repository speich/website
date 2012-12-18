<?php
/**
 * This file contains two classes to create a navigation menu.
 * @author Simon Speich
 * @package Main
 */

/**
 * Simple recursive php menu with unlimited levels which creates an unordered list
 * based on an array.
 * 
 * Each item can have its own js event handler. 
 * To increase performance only open menus are used in recursion unless you set
 * the whole menu to be open by setting the property AutoOpen = true;
 * 
 * @package Main
 */
class MenuItem {
	public $id = null;
	public $parentId = null;
	public $linkTxt = '';
	public $linkUrl = '';
	public $eventHandler = null;	// hold the event listener for an item
	public $linkTitle = '';

	private $cssClass = null;

	/**
	 * Render this items children.
	 * @var bool $RenderChild
	 */
	private $childrenToBeRendered = false;
	private $active = false;	 

	
	/**
	 * Constructs the menu item.
	 * If $title argument is true (default), then the $linkTxt will be used as the attribute value. If it is a string
	 * then that string will be used. If it is null, title attribute will not be set on link element.
	 * @param array arr
	 */
	public function __construct($arr) {
		$this->id = $arr[0];
		$this->parentId = $arr[1];
		$this->linkTxt = $arr[2];
		$this->linkUrl = isset($arr[3]) ? $arr[3] : '';
		$this->eventHandler = isset($arr[4]) ? $arr[4] : null;
		if (isset($arr[5]) && is_string($arr[5])) {
			$this->linkTitle = $arr[5];
		}
		else if (!isset($arr[5])) {
			$this->linkTitle = $this->linkTxt;
		}
	}
	
	/** Get item property if children will be rendered */
	public function getchildrenToBeRendered() { return $this->childrenToBeRendered; }
	
	/**
	 * Set item property if children will be rendered.
	 * @param bool [$ChildToBeRendered]
	 */
	public function setchildrenToBeRendered($childrenToBeRendered = true) {
		$this->childrenToBeRendered = $childrenToBeRendered;
	}
	
	/**
	 * Set item to be active.
	 * @param bool [$active]
	 */
	public function setActive($active = true) {
		$this->active = $active;
	}
	
	/**
	 * Get item active status.
	 * @return bool
	 */
	public function getActive() {
		return $this->active;
	}
	
	/**
	 * Adds a css class to the item.
	 * Allows to have mutliple CSS classes per item.
	 * @param string $name CSS class name
	 */
	public function addCssClass($name) {
		if (!is_null($this->cssClass)) {
			$this->cssClass.= ' ';	// multiple classes have to be separated by a space 
		}
		$this->cssClass.= $name;
	}
	
	/**
	 * Returns the css class string
	 * @return string 
	 */
	public function getCssClass() {
		return $this->cssClass;
	}
	
}

/**
 * Creates menu items.
 * A menu is made of menu items.
 * @package NAFIDAS
 */
class Menu extends MenuItem {
	/**
	 * Holds array of menu items.
	 * @var array menu items
	 */	 
	public $arrItem = array();
	
	/** 
	 * Holds html string of created menu.
	 * @var string menu string
	 */
	private $strMenu = '';
	
	/** All child menus are rendered by default
	 * @var bool render children
	 */
	public $allChildrenToBeRendered = false;
	
	/** Automatically mark item and all its parents as active if its url is same as url of current page.
	 * @var bool
	 */ 
	public $autoActive = true;
	
	/**
	 * Sets the url matching pattern of $autoActive property.
	 * 1 = item url matches path only, 2 = item url patches path + query, 3 item url matches any part of path + query 
	 * @var integer
	 */
	private $autoActiveMatching = 1;
	
	/** Flag to mark first ul tag in recursive method */
	private $firstUl = true;
	
	/** Menu CSS class name */
	public $cssClass = null;
	
	/** Menu CSS id name */
	public $cssId = null;
	
	/** Active item CSS class name */
	public $cssItemHasChildren = 'menuHasChildren';
	
	/** Active item CSS class name */
	public $cssItemActive = 'menuIsActive';
	
	/** Open item CSS class name */
	public $cssItemOpen = 'menuIsOpen';
	
	
	/**
	 * Constructs the menu.
	 * You can provide a 2-dim array with all menu items 
	 * or use the add method for each item singedly.
	 * @param string [$cssId] HTMLIdAttribute
	 * @param string [$cssClass] HTMLClassAttibute
	 * @param array [$arrItem] array with menu items
	 */
	public function __construct($cssId = null, $cssClass = null, $arrItem = null) {
		if (!is_null($arrItem)) {
			foreach ($arrItem as $item) {
				$this->arrItem[$item[0]] = new MenuItem($item);
			}
		}
		if (!is_null($cssClass)) {
			$this->cssClass = $cssClass;
		}
		if (!is_null($cssId)) {
			$this->cssId = $cssId;
		}
	}
	
	/**
	 * Add a new menu item.	 * 
	 * Array has to be in the form of:
	 * array(id, parentId, linkTxt, optional linkUrl, optional event handler);
	 * You can add new items to menu as long as you haven't called the render method.
	 * @param array $arr menu item
	 */
	public function add($arr) {
		$this->arrItem[$arr[0]] = new MenuItem($arr);
	}
	
	/**
	 * Check if menu item has at least one child menu.
	 * @return bool
	 * @param string|integer $id item id
	 */
	private function checkChildExists($id) {
		$found = false;
		foreach ($this->arrItem as $val) {
			if ($val->parentId === $id) {
				$found = true;
				break;
			}
		}
		return $found;
	}
	
	/**
	 * Sets the url matching pattern of $autoActive property.
	 * 1 = item url matches path only (default)
	 * 2 = item url patches path + query, 
	 * 3 item url matches any part of path + query 
	 * @param object $type
	 * @return 
	 */
	public function setAutoActiveMatching($type) {
		$this->autoActiveMatching = $type;
	}
	
	/**
	 * Returns the url matching pattern of $autoActive property.
	 * @return integer
	 */
	public function getAutoActiveMatching() {
		return $this->autoActiveMatching;
	}
	
	/**
	 * Add an javascript event handler to a menu item.
	 * 
	 * Sets a js event 
	 * @param integer|string $id menu id
	 * @param string $EventHandler js event handler
	 */
	public function setEventHandler($id, $eventHandler) {
		foreach ($this->arrItem as $item) {
			if ($item->id === $id) {
				$item->eventHandler = $eventHandler;
			}
		}
	}
	
	/**
	 * Checks if an menu item should be set to active if its url matches the set pattern.
	 * Pattern can also be set globally through Menu::setAutoActiveMatching();
	 * Full path means complete current url, e.g. including page
	 * Patterns:
	 * 1 items url matches full path
	 * 2 items url matches full path + exact query string
	 * 3 items url matches full path + part of query string
	 * 4 match deepest containing folder + part of query string
	 * 
	 * Returns boolean (match no match) or the number of matched directories.
	 * This function may return Boolean TRUE OR FALSE, but may also return a non-Boolean value 
	 * which evaluates to FALSE, such as 0 or 1 to TRUE. Use the === operator for testing 
	 * the return value of this function.
	 * 
	 * If item's active property is set to null it is not considered in active check.
	 * 
	 * @param object $item MenuItem
	 * @return bool|int
	 */
	public function checkActive($item, $pattern = null) {
		if (is_null($item->getActive())) {
			return false;	// item explicitly set to null = skip
		}
		else if ($item->getActive()) {
			return true; // item excplicitply set to active
		}
		$url = $_SERVER['REQUEST_URI'];
		$arrUrlPage = parse_url($url);
		$arrUrlMenu = parse_url(html_entity_decode($item->linkUrl));		
		if (is_null($pattern)) {
			$pattern = $this->getAutoActiveMatching();
		}
		switch($pattern) {
			case 1:	
				if ($arrUrlPage['path'] == $arrUrlMenu['path']) {
					return true;
				}
				break;
			case 2:
				if ($arrUrlPage['path'].'?'.$arrUrlPage['query'] == $item->linkUrl) {
					return true;
				}
				break;
			case 3:
				if (array_key_exists('query', $arrUrlMenu)) {
					parse_str($arrUrlMenu['query'], $arr);
					// 1. check query vars
					foreach ($arr as $var => $val) {
						if (!array_key_exists($var, $_GET)) {
							return false;
						}
						else if ($_GET[$var] != $val) {
							return false;
						}
					}
				}
				// 2. check path
				if ($arrUrlPage['path'] == $arrUrlMenu['path']) {
					return true;
				}
				else {
					return false;
				}
				break;
			case 4:
				$numMatched = 0;
				$arrDirMenu = explode('/', $arrUrlMenu['path']);
				$arrDirPage = explode('/', $arrUrlPage['path']);
				for ($i = 0; $i < count($arrDirMenu); $i++) {
					if (array_key_exists($i, $arrDirPage)) {
						if ($arrDirPage[$i] == $arrDirMenu[$i]) {
							$numMatched++;
						}
					}
				}
				if ($numMatched > 0) {
					return $numMatched;
				}
				else {
					return false;
				}
				break;
			default;
				return false;
		}
	}
	
	/**
	 * Returns id of active menu items.
	 * Returns an array if there there is more than one item active.  
	 * @return mixed|false
	 */
	public function getActive() {
		$arrActive = array();
		foreach ($this->arrItem as $item) {
			if ($item->getActive()) {
				$arrActive[] = $item->id;
			}
		}
		$num = count($arrActive); 
		if ($num == 0) {
			return false;
		}
		else if ($num == 1) {
			return $arrActive[0];
		}
		else {
			return $arrActive;
		}
	}
	
	/**
	 * Creates the menu Html string.
	 * @param string|integer $parentId seed
	 */
	private function createHtml($parentId) {
		$this->strMenu.= '<ul';
		if ($this->firstUl) {
			if (!is_null($this->cssClass)) {
				$this->strMenu.= ' class="'.$this->cssClass.'"';
			}
			if (!is_null($this->cssId)) {
				$this->strMenu.= ' id="'.$this->cssId.'"';
			}
			$this->firstUl = false;
		}
		$this->strMenu.= ">\n";
		foreach ($this->arrItem as $item) {
			if ($item->parentId === $parentId) {
				if ($this->checkChildExists($item->id)) {
					$item->addCssClass($this->cssItemHasChildren);
					if (($this->allChildrenToBeRendered || $item->getChildrenToBeRendered())) {
						$item->addCssClass($this->cssItemOpen);
					}
				}				
				else if ($item->getActive()) {
					$item->addCssClass($this->cssItemActive);
				}				
				$this->strMenu.= '<li'.(is_null($item->getCssClass()) ? '' : ' class="'.$item->getCssClass().'"').'>';
				if ($item->linkUrl != '') {
					$this->strMenu.= '<a href="'.$item->linkUrl.'"'.(is_null($item->eventHandler) ? '' : ' '.$item->eventHandler);

				}
				else {
					$this->strMenu.= '<a'.(is_null($item->eventHandler) ? '' : ' '.$item->eventHandler);	// for css we have the same structure, with or without a link
				}
				if ($item->linkTitle !== false) {
					// title attribute on link element is important for search engines
					$this->strMenu.= ' title="'.$item->linkTitle.'"';
				}
				$this->strMenu.= '>';
				$this->strMenu.= $item->linkTxt;
				$this->strMenu.= '</a>';
				if ($this->checkChildExists($item->id)) {
					if (($this->allChildrenToBeRendered || $item->getChildrenToBeRendered())) {
						$this->createHtml($item->id);
						$this->strMenu.= "</ul>\n";
					}
				}
				$this->strMenu.= "</li>\n";
			}
		}
	}
	
	/**
	 * Sets child/parent items to render and/or active according to URL matching scheme
	 * or by explicitly setting item to active.
	 * Should be called before rendering if AutoInit is set to false;
	 * When argument $url is provided then the item with matching url is set to active.
	 * @param string $url [optional]
	 */
	public function setActive($url = null) {
		if (is_null($url)) {
			$itemId = null;	// used for matching pattern 4 = match containing folder with deepest matching
			$lastNumMatched = 0; 
			foreach ($this->arrItem as $item) {
				$numMatched = $this->checkActive($item);
				if ($numMatched === true) {
					$item->setActive();
				}
				if (is_int($numMatched)) {
					if ($numMatched >= $lastNumMatched) {
						$itemId = $item->id;	// find item with most folders matched (deepest folder match)
						$lastNumMatched = $numMatched;
					}
					
				}
				if ($this->allChildrenToBeRendered || $item->getActive()) {
					// set also item's parents to active
					$parentId = $item->parentId;
					while (array_key_exists($parentId, $this->arrItem)) {
						$this->arrItem[$parentId]->setChildrenToBeRendered();
						$this->arrItem[$parentId]->setActive();
						$parentId = $this->arrItem[$parentId]->parentId;
					}
				}				
			}
			// only used for matching pattern 4
			if (!is_null($itemId)) {
				$this->arrItem[$itemId]->setActive();
				foreach ($this->arrItem as $item) {
					if ($this->allChildrenToBeRendered || $item->getActive()) {
						// set also item's parents to active
						$parentId = $item->parentId;
						while (array_key_exists($parentId, $this->arrItem)) {
							$this->arrItem[$parentId]->setChildrenToBeRendered();
							$this->arrItem[$parentId]->setActive();
							$parentId = $this->arrItem[$parentId]->parentId;
						}
					}
				}
			}	
		}
		// match provided url
		else {
			foreach ($this->arrItem as $item) {
				if ($item->linkUrl == $url) {
					$item->setActive();
					if ($this->allChildrenToBeRendered || $item->getActive()) {
						// set also item's parents to active
						$parentId = $item->parentId;
						while (array_key_exists($parentId, $this->arrItem)) {
							$this->arrItem[$parentId]->setChildrenToBeRendered();
							$this->arrItem[$parentId]->setActive();
							$parentId = $this->arrItem[$parentId]->parentId;
						}
					}
				}
			}
		}
	}
	
	/**
	 * Returns a HTML string of the menu.
	 * Call init method first.
	 * @return string
	 */
	public function render() {
		if ($this->autoActive) {
			$this->setActive();
		}
		if (count($this->arrItem) > 0) {
			if ($this->strMenu == '') {
				$this->createHtml(reset($this->arrItem)->parentId);	// render only once when render() method is called more than once
			}
			return $this->strMenu."</ul>\n";
		}
		else {
			return '';
		}
	}
}
?>