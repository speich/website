<?php include '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title>Curriculum Vitae | <?php echo $web->pageTitle; ?></title>
<?php require_once '../layout/inc_head.php' ?>
<style type="text/css">
.tblCv { margin: 30px 0; }

.tblCv td:first-child {
	white-space: nowrap;
	margin-right: 15px;
}

.tblCv td {
	padding: 8px 0;
	border: 0 solid #DFE8D9; border-bottom-width: 1px;
}

.tdCvHead {
	font-size: 14px;
	font-weight: normal;
	color: #2E7300;
}

td:first-child {
	white-space: nowrap;
}
td:nth-child(2) {
	width: 15px;
}
</style>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
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
	<a href="http://www.wsl.ch" target="_blank">Swiss Federal Institute for Forest, Snow and Landscape Research</a> WSL, Birmensdorf</p>
<h3>Responsibilites:</h3>
<ul class="main">
<li>Concept, design and programming (PHP, JavaScript, SQL) of multilingual web products</li>
<li>Publication as well as documentation and archiving of results</li>
<li>Redaction and publishing of media</li>
</ul>
<h3>Projects:</h3>
<ul class="main">
<li>Web application to <a href="http://www.lfi.ch/resultate/anleitung-en.php" target="_blank">query and display results</a> (tables and maps) of the Swiss NFI</li>
<li>Web application: <a href="http://www.sciencedirect.com/science/article/pii/S016816991630792X" target="_blank">The data storage and analysis system of the Swiss National Forest Inventory</a><br>
Computers and Electronics in Agriculture Volume 132</li>
<li>Website of the <a href="http://www.lfi.ch/index.en.php" target="_blank">Swiss National Forest Inventory</a></li>
<li>Website with photo archive of the <a href="http://www.forstmuseum.ch" target="_blank">Förderverein Forstmuseum Ballenberg</a></li>
<li>Photography and design of <a href="http://www.lfi.ch/publikationen/publ/LFI_Kalender_2014_de.pdf" target="_blank">NFI-calendar</a></li>
<li>Redaction of <a href="http://www.lfi.ch/publikationen/publ_lfiinfo.php" target="_blank">LFI info</a> (discontinued)</li>
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
<li>Chapter for e-learning platform <a href="http://www.gitta.info" target="_blank">GITTA</a> (Geographic Information Technology Training Alliance)</li>
</ul></td>
</tr>
</table>
<p>For a more complete CV head over to the <a href="cv.php">German version</a>.</p>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>