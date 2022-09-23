<?php include __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Biography | <?php echo $web->pageTitle; ?></title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/about/simon-speich-en.php">
    <link rel="alternate" hreflang="de" href="https://www.speich.net/about/simon-speich.php">
    <meta name="description" content="Simon Speich is a web developer and wildlife photographer.">
<?php echo $head->render(); ?>
<link rel="stylesheet" href="simon-speich.css">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Biography</h1>
<figure class="photoContainer"><img class="imgFrame" src="images/waldkauz.jpg" alt="Waldkauz umrahmt von Buchenblättern" width="328" height="328">
    <figcaption>A Tawny Owl (<span class="spec">Strix aluco</span>) framed by Purple Beech leaves.</figcaption></figure>
<p>I work as a web developer for a living, studied biology including scientific photography as an elective subject and strive for capturing
    <a href="../photo/photodb/photo-en.php?theme=20">forests</a>, <a href="../photo/photodb/photo-en.php?theme=8">wildlife</a> and especially
    <a href="../photo/photodb/photo-en.php?theme=1">birds</a> in the best light and perspective in my free time.</p>
<h2>Curriculum Vitae</h2>
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
<li>Concept, design and programming (PHP, JavaScript, SQL) of multilingual web applications.</li>
<li>Publication as well as documentation and archiving of results.</li>
<li>Redaction and publishing of media.</li>
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
<p>For a more complete CV head over to the <a href="simon-speich.php">German version</a>.</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>