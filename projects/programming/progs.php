<?php require_once __DIR__.'/../../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Programmierung | <?php echo $web->pageTitle; ?></title>
<?php echo $head->render(); ?>
<script src="progs.js"></script>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Programmierung</h1>
<h2>06. Juli 2013, <a href="remoteFileExplorer.php" title="eine Windows Explorer ähnliche Webapplikation">remoteFileExplorer</a></h2>
<p>Eine Windows Explorer ähnliche Webapplikation zur Verwaltung von Dateien und Verzeichnissen direkt im Browser.</p>
<h2>04. Nov. 2003, <a href="games/blockedup.php" onclick="return openWin('games/blockedup.php', 'BlockedUp', 680, 600); return false;">BlockedUp, noch ein Arkanoid Klon</a></h2>
<p>Das Resultat meines ersten Ausflugs in die Gilde der Gameprogrammierer. Ein Game, das online mit dem Browser spielbar und in reinem JavaScript geschrieben ist.</p>
<ul class="main">
<li>Version: 1.0</li>
<li>Level: zur Zeit 4</li>
<li>Highscore speicherbar (XmlHttp benötigt)</li>
<li>Soundunterstützung (Flash benötigt)</li>
<li>Browser: Mozilla 1.5 (empfohlen), Internet Explorer 6</li>
</ul>
<h2>25. Okt. 2003, <a href="3d.php">3D(HTML) Cube</a></h2>
<p>Interaktiver 3D Würfel in reinem DHTML, kein Flash oder Java !!!</p>
<p>Da es in Html nicht möglich ist zu zeichnen, musste sämtliche Funktionalität wie z.Bsp. eine Linie zeichnen selbst implementiert werden. Als Pixel dienten 1x1 Div Elemente mit einer gesetzten Hintergrundfarbe.</p>
<ul>
<li>Browser: Opera 8.5 (empfohlen)</li>
</ul>
<?php echo $bodyEnd->render(); ?>
</body>
</html>