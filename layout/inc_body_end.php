</div>
</div>
<div id="layoutFooterCont">
<div id="layoutFooter">
<?php

if ($web->getLang() === 'de') {
	echo '<p>© 2003-2013 speich.net, Konzept und Programmierung Simon Speich';
	echo '<span style="float: right;">Letzte Aktualisierung '.$web->getLastUpdate().'</span><p>';
	if (strpos($web->getDir(), '/photo') !== false) { ?>
		<p><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de"><img alt="Creative Commons Lizenzvertrag" src="http://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png"></a>
	 	Alle Fotos stehen unter der <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de">Creative Commons Lizenz</a> zur Verfügung,
		sofern der Bildautor folgendermassen angeben wird:<br>
		<strong>Foto Simon Speich, www.speich.net</strong>. Für kommerzielle Zwecke oder höhere Bildauflösungen <a href="/contact/contact.php">kontaktieren</a> Sie bitte den Bildautor.</p>
	<?php }
	if (!$mainNav) {
		echo '<p>Die Artikel auf dieser Seite laufen mit <a href="http://www.wordpress.org">WordPress</a>.</p>';
	}
}
else {
	echo '<p>© 2003-2013 speich.net, concept und programming Simon Speich';
	echo '<span style="float: right;">last update '.$web->getLastUpdate().'</span><p>';
	if (strpos($web->getDir(), '/photo') !== false) { ?>
			<p><a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/"><img alt="Creative Commons licence" src="http://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png"></a>
		 	All photos on this website are licenced under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution 3.0 Unported License</a>:<br>
			<strong>Photo Simon Speich, www.speich.net</strong>. For a commercial licence or higher resolution please <a href="/contact/contact.php">contact</a> the author.</p>
	<?php }
	if (!$mainNav) {
		echo '<p>Die Artikel auf dieser Seite laufen mit <a href="http://www.wordpress.org">WordPress</a>.</p>';
	}
} ?>
</div>
</div>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-29305865-1']);
_gaq.push (['_gat._anonymizeIp']);
_gaq.push(['_trackPageview']);
(function() {
 var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
 ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
 var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>