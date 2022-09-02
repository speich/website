<?php include __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Curriculum Vitae | <?php echo $web->pageTitle; ?></title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/about/cv-en.php"/>
    <link rel="alternate" hreflang="de" href="https://www.speich.net/about/cv.php"/>
<?php echo $head->render(); ?>
<link rel="stylesheet" href="cv.css">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Simon Speich's Curriculum Vitae</h1>
<p>For a more complete CV head over to the <a href="cv.php">German version</a>.</p>
<table class="tblCv">
<tr> 
<td colspan="3" class="tdCvHead">Experience</td>
</tr>
<tr>
<td>05.2004—</td>
<td></td>
<td><p><strong>Web developer for the Swiss National Forest Inventory NFI</strong><br>
	<a href="https://www.wsl.ch" target="_blank">Swiss Federal Institute for Forest, Snow and Landscape Research</a> WSL, Birmensdorf</p>
<h3>Responsibilites:</h3>
<ul class="main">
<li>Concept, design and programming (PHP, JavaScript, SQL) of multilingual web products</li>
<li>Publication as well as documentation and archiving of results</li>
<li>Redaction and publishing of media</li>
</ul>
<h3>Projects:</h3>
<ul class="main">
<li>Web application: <a href="https://doi.org/10.1016/j.compag.2016.11.016" target="_blank">The data storage and analysis system of the Swiss National Forest Inventory</a><br>
Computers and Electronics in Agriculture Volume 132</li>
<li>Web application to <a href="https://www.lfi.ch/resultate/anleitung-en.php" target="_blank">query and display results</a> (tables and maps) of the Swiss NFI</li>
<li>Website of the <a href="https://www.lfi.ch/index-en.php" target="_blank">Swiss National Forest Inventory</a></li>
<li>Website with photo archive of the <a href="https://www.forstmuseum.ch" target="_blank">Förderverein Forstmuseum Ballenberg</a></li>
<li>Photography and design of <a href="https://www.lfi.ch/publikationen/publ/kalender/LFI_Kalender_2018_de.pdf" target="_blank">NFI-calendar</a></li>
<li>Redaction of <a href="https://www.lfi.ch/publikationen/publ_lfiinfo.php" target="_blank">LFI info</a> (discontinued)</li>
</ul></td>
</tr>
<tr>
<td>08.2003—11.2003</td>
<td></td>
<td><p><strong>Assistant Geoinformatics</strong><br>
	GeoIT ETH Zürich, Hönggerberg</p>
<h3>Responsibilites:</h3>
<ul class="main">
<li>Redaction and programming (XML) for e-Learning about GIS</li>
</ul>
<h3>Projects:</h3>
<ul class="main">
<li>Chapter for e-learning platform <a href="https://www.gitta.info" target="_blank">GITTA</a> (Geographic Information Technology Training Alliance)</li>
</ul></td>
</tr>
</table>
<p>For a more complete CV head over to the <a href="cv.php">German version</a>.</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>