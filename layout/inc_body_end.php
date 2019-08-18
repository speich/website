</div>
</div>
<div id="layoutFooterCont">
<div id="layoutFooter">
<?php
if ($lang->get() === 'de') {
	echo '<p>© 2003-2019 speich.net, Konzept und Programmierung Simon Speich';
	echo '<span style="float: right;">letzte Aktualisierung '.$web->lastUpdate.'</span><p>';
	if (strpos($_SERVER['REQUEST_URI'], '/photo') !== false) { ?>
		<p><a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/3.0/deed.de"><img alt="Creative Commons Lizenzvertrag" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png"></a>
	 	Alle Fotos stehen unter der <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de">Creative Commons Lizenz</a> zur Verfügung,
		sofern der Bildautor folgendermassen angeben wird:<br>
		<strong>Foto Simon Speich, www.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.</p>
	<?php }
}
else {
	echo '<p>© 2003-2019 speich.net, concept und programming Simon Speich';
	echo '<span style="float: right;">last update '.$web->lastUpdate.'</span><p>';
	if (strpos($web->getDir(), '/photo') !== false) { ?>
			<p><a rel="license" href="https://creativecommons.org/licenses/by-nc/3.0/"><img alt="Creative Commons licence" src="https://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png"></a>
		 	All photos on this website are licenced under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution 3.0 Unported License</a>:<br>
			<strong>Photo Simon Speich, www.speich.net</strong>. For a commercial licence or higher resolution please <a href="/contact/contact.php">contact</a> the author.</p>
	<?php }
} ?>
</div>
</div>