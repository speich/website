<?php

class PagedNav extends Website {
	private $numRecPerPage;			// number of records per page, used to calculate the number of pages in navigation
	private $numRec;						// total number of records with current query, used to calculate number of pages
	private $curPageNum;				// Current page to display.
	private $range = 6;					// range of pages in paged navigation, must be an even number
	private $stepSmall = 10;		// [-10] [+10] backward - forward jump, must be an even number
	private $stepBig = 50;			// [-50] [+50] backward - forward jump, must be an even number
	private $lan = "de";				// language of text navigation
	private $varNamePgNav = 'pgNav';	// name of variable in querystring to set page number
	private $varNameNumRecPP = 'numRecPp';	// name of variable in querystringnumber to set # records per page
	private $formMethod = 'GET';			// default method is GET, e.g use querystring
	
	/**
	 * Constructs the paged navigation.
	 * 
	 * Total number of records with given query, e.g. with WHERE clause included.
	 * 
	 * @param integer $curPageNum Current page to display.
	 * @param integer $numRec Total number of records
	 * @param integer $numRecPerPage Number of records per page
	 */
	public function __construct($curPageNum, $numRec, $numRecPerPage) {
		parent::__construct();
		$this->curPageNum = $curPageNum;
		$this->numRec = $numRec;
		$this->numRecPerPage = $numRecPerPage;
		$this->addQuery(array('numRec' => $numRec));
	}
	
	/**
	 * Set post method for paged navigation.
	 * Links can either be GET or POST
	 * @param string $method
	 */
	public function setMethod($method) {
		$this->formMethod = $method;
	}
	
	/**
	 * Set the number of links to directly accessible pages.
	 * This number has to be even.
	 * @param integer $range number of links
	 */
	public function setRange($range) { 
		// TODO: check if even number
		$this->range = $range;
	}
	
	/**
	 * Set how many pages can be skipped.
	 *
	 * @param integer $stepSmall
	 * @param integer $stepBig
	 */
	public function setStep($stepSmall, $stepBig) {
		// TODO: check if even number
		$this->stepSmall = $stepSmall;
		$this->stepBig = $stepBig;
	}
	
	public function setLan($lan) { $this->lan = $lan; }
	
	/**
	 * Outputs HTML paged data navigation.
	 */
	public function printNav() {
		// prints paged navigation
		// if form uses POST to submit request then javascript is used to resubmit the form on every page (which is not perfect for usabilty)
		//		then to additional variables are needed: name of form and name of hidden field with page number
		// else GET is used then no js is necessary
		if ($this->formMethod == 'GET') {
			$useJs = false;	
		}
		else {
			// not implemented yet
			$frmName = func_get_arg(0);			// name of form to submit with js
			$fldCurPageNum = func_get_arg(1);	// name of hidden input field with page number
			$useJs = true;
		}		
		// language  dependend strings
		switch ($this->lan) {
			case 'fr':
				$lanStr01 = ' inscriptions';
				$lanStr02 = ' inscription';
				$lanStr03 = ' pages: ';
				$lanStr04 = ' page';
				$lanStr05 = 'Résultat de la recherche: ';
				$lanStr06 = '';
				break;
			case 'it':
				$lanStr01 = ' iscrizioni';
				$lanStr02 = ' inscriptione';
				$lanStr03 = ' pagine: ';
				$lanStr04 = ' pagina';
				$lanStr05 = 'Risultato della ricerca: ';
				$lanStr06 = '';
				break;				
			case 'en':
				$lanStr01 = ' entries';
				$lanStr02 = ' entry';
				$lanStr03 = ' pages: ';
				$lanStr04 = ' page';
				$lanStr05 = 'search result: ';
				$lanStr06 = 'on';
				break;
			default:
				$lanStr01 = ' Fotos';
				$lanStr02 = ' Foto';
				$lanStr03 = ' Seiten: ';
				$lanStr04 = ' pro Seite';
				$lanStr05 = ' sortiert nach: ';				
				break;
		}
				
		// calc total number of pages
		$numPage = ceil($this->numRec / $this->numRecPerPage);
		// lower limit (start)
		$start = 1;
		if ($this->curPageNum - $this->range/2 > 0) { $start = $this->curPageNum - $this->range/2; }
		// upper limit (end)
		
	//	if ($this->curPageNum * $this->numRecPerPage > $this->numRec) {	// check that links do not exceed number of records
	//		$end = $this->curPageNum;
			//echo $this->numRec / $this->numRecPerPage;
			/*
			$end = $this->curPageNum;
			while ($end < $this->range / 2) {
				$end+= 1;
			}
			*/
//		}
//		else { 
		 $end = $this->curPageNum + $this->range / 2;
//		}
		if ($this->curPageNum + $this->range/2 > $numPage) { $End = $numPage;	}
		// special cases
		if ($numPage < $this->range) { $end = $numPage; }
		else if ($end < $this->range) { $end = $this->range; }

		echo '<div class="pagedNavBar">';
		// to do: 
		// setup method POST
		// jump back big step
		if ($this->curPageNum > $this->stepBig / 2) { // && $this->curPageNum >= $this->stepBig + $this->stepSmall) {
			$stepBig = ($this->curPageNum > $this->stepBig ? $this->stepBig : $this->curPageNum - 1);
			if ($useJs) { }
			else {
				echo '<div><a class="linkJumpBig" href="'.$this->getPage().$this->addQuery(array($this->varNamePgNav => ($this->curPageNum - $stepBig))).'">';
				echo '<img src="'.$this->getWebRoot().'layout/images/icon_backfast.gif" alt="Icon back" title="schnell Rückwärts blättern [-'.$stepBig.']"/></a></div>';
			}
		}		
		// jump back small step
		if ($this->curPageNum > $this->stepSmall / 2) {
			$stepSmall = ($this->curPageNum > $this->stepSmall ? $this->stepSmall : $this->curPageNum - 1);
			if ($useJs) { }
			else {
				echo '<div><a class="linkJumpSmall" href="'.$this->getPage().$this->addQuery(array($this->varNamePgNav => ($this->curPageNum - $stepSmall))).'">';
				echo '<img src="'.$this->getWebRoot().'layout/images/icon_back.gif" alt="Icon back" title="Rückwärts blättern [-'.$stepSmall.']"/></a></div>';
			}
		}		
		// direct accessible pages (1 2 3 4... links)
		$Count = 0;
		for ($i = $start; $i <= $end && (($i-1) * $this->numRecPerPage < $this->numRec); $i++) {
			if ($numPage > 1) {
				if ($Count > 0) { echo ' '; }
				$Count++;
				if ($i == $this->curPageNum) { echo ' <div class="linkCurPageNum">'; }
				else {
					echo '<div class="pages">';
					if ($useJs) {	}
					else {
						echo '<a class="linkJumpPage" href="'.$this->getPage().$this->addQuery(array($this->varNamePgNav => $i)).'">';
					}
				}
				echo $i;	// page number
				if ($i == $this->curPageNum) { echo '</div>'; }
				else { echo '</a></div>'; }
			}
		}		
		// jump forward small step
		if ($numPage > $this->curPageNum + $this->stepSmall / 2) {
			$stepSmall = ($numPage > ($this->curPageNum + $this->stepSmall) ? $this->stepSmall : $numPage - $this->curPageNum);
			if ($useJs) { }
			else {
				echo '<div><a class="linkJumpSmall" href="'.$this->getPage().$this->addQuery(array($this->varNamePgNav => ($this->curPageNum + $stepSmall))).'">';
				echo '<img src="'.$this->getWebRoot().'layout/images/icon_forward.gif" alt="Icon forward" title="Vorwärts blättern [+'.$stepSmall.']"/></a></div>';
			}
		}
		// jump forward big step
		if ($numPage >= $this->curPageNum + $this->stepBig / 2) {
			$stepBig = ($numPage > $this->curPageNum + $this->stepBig ? $this->stepBig : $numPage - $this->curPageNum);
			if ($useJs) { }
			else {
				echo '<div><a class="linkJumpBig" href="'.$this->getPage().$this->addQuery(array($this->varNamePgNav => ($this->curPageNum + $stepBig))).'">';
				echo '<img src="'.$this->getWebRoot().'layout/images/icon_forwardfast.gif" alt="Icon forward" title="schnell Vorwärts blättern [+'.$stepBig.']"/></a></div>';
			}
		}
		echo "</div>\n";
	}
}

?>