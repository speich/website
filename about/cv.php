<?php include __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Curriculum Vitae | <?php echo $web->pageTitle; ?></title>
<?php echo $head->render(); ?>
<link rel="stylesheet" href="cv.css">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Curriculum Vitae von Simon Speich</h1>
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
<li>Konzeption, Gestaltung und Programmierung (PHP, JavaScript, SQL) von mehrsprachigen, datenbankgestützten Webprodukten</li>
<li>Publikation sowie Dokumentation und Archivierung von Ergebnissen</li>
<li>Redaktion und Unterhalt von Medien</li>
</ul>
<h3>Projekte:</h3>
<ul class="main">
<li>Webapplikation: <a href="https://www.sciencedirect.com/science/article/pii/S016816991630792X" target="_blank">The data storage and analysis system of the Swiss National Forest Inventory</a><br>
Computers and Electronics in Agriculture Volume 132</li>
<li>Webapplikation <a href="https://www.lfi.ch/resultate/anleitung.php" target="_blank">interaktive Abfrage zu Ergebnissen</a> (Tabellen und Karten) des Schweizerischen Landesforstinventars LFI</li>
<li>Website <a href="https://www.lfi.ch" target="_blank">Schweizerisches Landesforstinventar</a></li>
<li>Website mit Bilddatenbank des <a href="http://www.forstmuseum.ch" target="_blank">Förderverein Forstmuseum Ballenberg</a></li>
<li>Fotografie und Layout <a href="https://www.lfi.ch/publikationen/publ/kalender/LFI_Kalender_2018_de.pdf" target="_blank">LFI-Kalender</a> und <a href="http://www.lfi.ch/publikationen/publ_lfiinfo.php" target="_blank">LFI info</a> (eingestellt)</li>
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
<li>E-learning Platform <a href="http://www.gitta.info" target="_blank">GITTA</a> (Geographic Information Technology Training Alliance)</li>
</ul></td>
</tr>
<tr> 
<td>08.1998—05.2003</td>
<td></td>
<td><p><strong>IT Consultant</strong><br>
	Webentwickler <a href="http://www.vectoris.ch/" target="_blank">Vectoris AG</a>, Basel</p>
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
	<a href="http://www.beat-ernst-basel.ch/" target="_blank">Beat Ernst</a>, Basel</p>
<h3>Aufgaben (Teilzeit):</h3>
<ul class="main">
<li>Betreuung Bildarchiv Nutzpflanzen infolge Ferienabwesenheit</li>
<li>Digitale Bildbearbeitung</li>
</ul>
<h3>Projekte:</h3>
<ul class="main">
<li>Digitale Bildbearbeitung für Multimediaprogramm &quot;<a href="http://www.christoph-merian-verlag.ch/buecher/detail.cfm?ObjectID=3A9F99BA-FEF4-F4E1-0FE0345F6E1A58B4" target="_blank">Natur und Landschaft
der Region Basel</a>&quot;</li>
</ul></td>
</tr>
<tr> 
<td>05.1998—07.1998</td>
<td></td>
<td><p><strong>Biologe</strong><br>
	Wissenschaftlicher Mitarbeiter <a href="http://www.vogelwarte.ch/" target="_blank">Schweizerische Vogelwarte</a>, Sempach.</p>
<h3>Aufgaben:</h3>
<ul class="main">
<li>Bearbeitung und Auswertung von digitalen Radardaten</li>
</ul>
<h3>Projekt:</h3>
<ul class="main">
<li><a href="http://www.vogelwarte.ch/de/projekte/vogelzug/radarornithologie.html" target="_blank">Radar-Zugforschung</a> im Mittelmeerraum</li>
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
<td>Anwendungsorientiertes Programmieren für GIS (ArcGIS) <a href="http://www.geoit.ethz.ch/" target="_blank">GeoIT ETH Zürich</a>, Hönggerberg</td>
</tr>
<tr>
<td>11.2003—04.2004</td>
<td></td>
<td>Nachdiplomkurs räumliche Informationssysteme <a href="http://www.igp.ethz.ch/" target="_blank">igp ETH Zürich</a>, Hönggerberg</td>
</tr>
<tr> 
<td>11.1993—10.1999</td>
<td></td>
<td><p>Dipl. Biologe <a href="http://www.unibas.ch/" target="_blank">Universität Basel</a>, Basel<br>
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
<td>Matura Typus B <a href="http://www.gyml.unibas.ch/" target="_blank">Gymnasium am Kohlenberg</a>, Basel</td>
</tr>
</table>
<p>Weitere Details auf Anfrage</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>