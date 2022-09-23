<?php include __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Biografie | <?php echo $web->pageTitle; ?></title>
<link rel="alternate" hreflang="en" href="https://www.speich.net/about/simon-speich-en.php">
<link rel="alternate" hreflang="de" href="https://www.speich.net/about/simon-speich.php">
<meta name="description" content="Simon Speich ist ein Webentwickler und passionierter Naturfotograf.">
<?php echo $head->render(); ?>
<link rel="stylesheet" href="simon-speich.css">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Biografie</h1>
<figure class="photoContainer"><img class="imgFrame" src="images/waldkauz.jpg" alt="Waldkauz umrahmt von Buchenblättern" width="328" height="328">
    <figcaption>Ein Waldkauz (<span class="spec">Strix aluco</span>) umrahmt von Buchenblättern.</figcaption></figure>
<p>Beruflich arbeite ich als Webprogrammierer, studiert habe ich Biologie mit wissenschaftlicher Fotografie im Wahlfach und in meiner Freizeit bin ich mit der
    Kamera unterwegs um <a href="../photo/photodb/photo.php?theme=20">Wälder</a>, <a href="../photo/photodb/photo.php?theme=1">Tiere</a> und
    insbesondere <a href="../photo/photodb/photo.php?theme=1">Vögel</a> im besten Licht und toller Perspektive einzufangen.</p>
<h2>Curriculum Vitae</h2>
<table class="tblCv">
<tr> 
<td colspan="3" class="tdCvHead">Berufserfahrung</td>
</tr>
<tr>
<td>05.2004—</td>
<td></td>
<td><p><strong>Webentwickler Landesforstinventar LFI</strong><br>
	System-Spezialist, <a href="https://www.wsl.ch" target="_blank">Eidg. Forschungsanstalt für Wald, Schnee und Landschaft</a> WSL, Birmensdorf</p>
<h3>Aufgaben:</h3>
<ul class="main">
<li>Konzeption, Gestaltung und Programmierung (PHP, JavaScript, SQL) von mehrsprachigen, datenbankgestützten Webprodukten.</li>
<li>Publikation sowie Dokumentation und Archivierung von Ergebnissen.</li>
<li>Redaktion und Unterhalt von Medien.</li>
</ul>
<h3>Projekte:</h3>
<ul class="main">
<li>Webapplikation: <a href="https://doi.org/10.1016/j.compag.2016.11.016" target="_blank">The data storage and analysis system of the Swiss National Forest Inventory</a><br>
Computers and Electronics in Agriculture Volume 132</li>
<li>Webapplikation <a href="https://www.lfi.ch/resultate/anleitung.php" target="_blank">interaktive Abfrage zu Ergebnissen</a> (Tabellen und Karten) des Schweizerischen Landesforstinventars LFI</li>
<li>Website <a href="https://www.lfi.ch" target="_blank">Schweizerisches Landesforstinventar</a></li>
<li>Website mit Bilddatenbank des <a href="https://www.forstmuseum.ch" target="_blank">Förderverein Forstmuseum Ballenberg</a></li>
<li>Fotografie und Layout <a href="https://www.lfi.ch/publikationen/publ/kalender/LFI_Kalender_2018_de.pdf" target="_blank">LFI-Kalender</a> und <a href="https://www.lfi.ch/publikationen/publ_lfiinfo.php" target="_blank">LFI info</a> (eingestellt)</li>
</ul></td>
</tr>
<tr>
<td>08.2003—11.2003</td>
<td></td>
<td><p><strong>Assistent Geoinformatik</strong><br>
	GeoIT ETH Zürich, Hönggerberg</p>
<h3>Aufgabe:</h3>
<ul class="main">
<li>Redaktion und Programmierung (XML) für e-Learning Projekt im GIS Bereich</li>
</ul>
<h3>Projekt:</h3>
<ul class="main">
<li>E-learning Platform <a href="https://www.gitta.info" target="_blank">GITTA</a> (Geographic Information Technology Training Alliance)</li>
</ul></td>
</tr>
<tr> 
<td>08.1998—05.2003</td>
<td></td>
<td><p><strong>IT Consultant</strong><br>
	Webentwickler <a href="https://www.vectoris.ch/" target="_blank">Vectoris AG</a>, Basel</p>
<h3>Aufgaben:</h3>
<ul class="main">
<li>Webprogrammierung und Webdesign (JavaScript, CSS, HTML und Flash)</li>
<li>Datenbankanbindung und Frontendentwicklung (ASP, SQL)</li>
<li>Planen und Abwickeln von Projekten</li>
<li>Betreuung von Kunden und Mitarbeitern</li>
</ul>
<h3>Projekte (Auszug):</h3>
<ul class="main">
<li>Autorensystem mit Wysiwyg Editor und Newsletter Versand</li>
<li>Monte Carlo Simulation für Versicherungsbroker</li>
<li>Applikation zur Zeiterfassung</li>
<li>Kursleitung Einführung in Microsoft Access</li>
</ul></td>
</tr>
<tr> 
<td>1999</td>
<td></td>
<td><p><strong>Vertretung</strong> Bildarchiv<br>
	<a href="https://www.beat-ernst-basel.ch/" target="_blank">Beat Ernst</a>, Basel</p>
<h3>Aufgaben (Teilzeit):</h3>
<ul class="main">
<li>Betreuung Bildarchiv Nutzpflanzen infolge Ferienabwesenheit</li>
<li>Digitale Bildbearbeitung</li>
</ul>
<h3>Projekte:</h3>
<ul class="main">
<li>Digitale Bildbearbeitung für Multimediaprogramm &quot;<a href="https://www.regionatur.ch" target="_blank">Natur und Landschaft
der Region Basel</a>&quot;</li>
</ul></td>
</tr>
<tr> 
<td>05.1998—07.1998</td>
<td></td>
<td><p><strong>Biologe</strong><br>
	Wissenschaftlicher Mitarbeiter <a href="https://www.vogelwarte.ch/" target="_blank">Schweizerische Vogelwarte</a>, Sempach.</p>
<h3>Aufgaben:</h3>
<ul class="main">
<li>Bearbeitung und Auswertung von digitalen Radardaten</li>
</ul>
<h3>Projekt:</h3>
<ul class="main">
<li><a href="https://www.vogelwarte.ch/de/projekte/vogelzug/radarornithologie.html" target="_blank">Radar-Zugforschung</a> im Mittelmeerraum</li>
</ul></td>
</tr>
<tr> 
<td>1995—1998</td>
<td></td>
<td><p><strong>IT Mitarbeiter</strong><br>
	<a href="http://www.eye.ch/" target="_blank">EYE Communications AG</a>, Basel<br>
Teilzeit während dem Studium</p></td>
</tr>
<tr>
<td>1990—1994</td>
<td></td>
<td><p><strong>Velokurier</strong><br>
	Velokurier, Basel<br>
Teilzeit während dem Gymnasium</p></td>
</tr>
</table>
<table class="tblCv">
<tr> 
<td colspan="3" class="tdCvHead">Ausbildung</td>
</tr>
<tr>
<td>11.2005</td>
<td></td>
<td>Anwendungsorientiertes Programmieren für GIS (ArcGIS) <a href="https://baug.ethz.ch/" target="_blank">GeoIT ETH Zürich</a>, Hönggerberg</td>
</tr>
<tr>
<td>11.2003—04.2004</td>
<td></td>
<td>Nachdiplomkurs räumliche Informationssysteme <a href="https://baug.ethz.ch/" target="_blank">igp ETH Zürich</a>, Hönggerberg</td>
</tr>
<tr> 
<td>11.1993—10.1999</td>
<td></td>
<td><p>Dipl. Biologe <a href="https://www.unibas.ch/" target="_blank">Universität Basel</a>, Basel<br>
Studium der Zoologie, Evolution und Ökologie sowie wissenschaftlicher Fotografie</p>
<h3>Diplomarbeit:</h3>
<ul class="main">
<li><a href="diplomarbeit.php" target="_blank">Temporal and Spatial Pattern of Nocturnal Bird Migration across the Western
Mediterranean Sea Studied by Radar</a> bei Prof. Bruno Bruderer</li>
</ul>
</td>
</tr>
<tr> 
<td>1984—1992</td>
<td></td>
<td>Matura Typus B <a href="https://www.gymnasium-leonhard.ch/" target="_blank">Gymnasium am Kohlenberg</a>, Basel</td>
</tr>
</table>
<p>Weitere Details auf Anfrage</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>