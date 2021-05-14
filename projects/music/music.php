<?php require_once __DIR__.'/../../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Songs | <?php echo $web->pageTitle; ?></title>
<?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Songs and Sounds</h1>
<p>Alle Songs wurden mit <a href="https://en.wikipedia.org/wiki/Impulse_Tracker">Impulse Tracker</a> komponiert (kennt noch jemand die guten alten Mod
Zeiten ?) und mit <a href="https://www.winmap.com">Winamp</a> zu mp3 konvertiert.</p>
<ul class="main">
<li><a href="junglebase.mp3">Funky</a> (3.8 mb)<br>
Samples: Red Hot Chilli Peppers, Korg M1, Jazz</li>
<li><a href="junglebase.mp3">Jungle Base</a> (2.9 mb)<br>
Samples: Zaunk&ouml;nig, Eistaucher, Rabbit in the Moon, Korg M1, Heads Up, The Cure</li>
<li><a href="psycho.mp3">Psycho</a> (1.5 mb)<br>
Samples: Urban Assault (PC Game), Korg M1, Rabbit in the Moon</li>
<li><a href="homage_yg.mp3">Homage an Young Gods</a> (970 kb)<br>
Ich liebe Euch und v.a. Eure Konzerte. Samples: zu lange her.</li>
<li><a href="metall.mp3">Metall</a> (540 kb)<br>
unvollendet. Samples: Panthera, Red Hot Chilli Peppers</li>
<li><a href="noname.mp3">Noname</a> (1.4 mb)<br>
unvollendet, Samples: Red Hot Chilli Peppers, Korg M1, u.a.</li>
<li><a href="phaser.mp3">Phaser</a> (335 kb)<br>
unvollendet, Samples Korg M1, u.a.</li>
<li><a href="brutaldeluxe.mp3">Brutal Deluxe</a> (519 kb)<br>
Mein erstes Impuls Tracker Projekt, unvollendet. An die beklauten Opfer (Samples) 
kann ich mich nicht mehr erinnern.</li>
</ul>
<?php echo $bodyEnd->render(); ?>
</body>
</html>