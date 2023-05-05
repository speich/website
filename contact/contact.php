<?php include __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title>Kontakt | <?php echo $web->pageTitle; ?></title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/contact/contact-en.php"/>
    <link rel="alternate" hreflang="de" href="https://www.speich.net/contact/contact.php"/>
    <?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Kontakt</h1>
<figure class="photoContainer"><img class="imgFrame" src="guyana-simon2.jpg" alt="Ein Foto von Simon mit der Nikon D800 und dem AF-S 300mm/2.8 in Aktion">
    <figcaption>Simon auf einer Fotoexpedition in den <a href="../articles/guyanas-wildlife-along-the-rewa-river/">Regenwald von Guyana</a>.</figcaption>
</figure>
<p>Simon Speich<br>
    Grellingerstrasse 79<br>
    4052 Basel</p>
<p><script type="text/javascript">
      document.write('Ich bin über die E-Mail Adresse <a href="mailto:info' + '@' + 'speich.net">info' + '@' + 'speich.net</a> kontaktierbar<br>');
    </script>
    oder auf <a href="https://github.com/speich" target="_blank">GitHub</a> / <a href="https://stackoverflow.com/users/208746/simon"
        target="_blank">Stackoverflow</a> zu finden.
</p>
<h2>Mitgliedschaften</h2>
<ul class="main">
    <li><a href="https://naturfotografen.ch/bilder/mitgliedergalerien/mitglied/simon-speich.html" target="_blank">Naturfotografen Schweiz</a></li>
    <li><a href="https://www.gdtfoto.de/mitglied/1001285/Simon-Speich" target="_blank">GDT Gesellschaft Deutscher Tierfotografen</a></li>
</ul>
<?php echo $bodyEnd->render(); ?>
</body>
</html>