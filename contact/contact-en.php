<?php include __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title>Contact | <?php echo $web->pageTitle; ?></title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/contact/contact-en.php"/>
    <link rel="alternate" hreflang="de" href="https://www.speich.net/contact/contact.php"/>
    <?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Contact</h1>
<figure class="photoContainer"><img src="guyana-simon2.jpg" class="imgFrame" alt="Photo of Simon Speich"
        title="Simon with Nikon D800 and the AF-S 300mm/2.8 in Action">
    <figcaption>Simon on a photo expedition to the <a href="../articles/guyanas-wildlife-along-the-rewa-river/">rainforest of Guyana</a>.</figcaption>
</figure>
<p>Simon Speich<br>
    Grellingerstrasse 79<br>
    4052 Basel<br>
    Switzerland</p>
<p><script type="text/javascript">
      document.write('You can reach me by e-mail <a href="mailto:info' + '@' + 'speich.net">info' + '@' + 'speich.net</a><br>');
    </script>
    or find me on <a href="https://github.com/speich" target="_blank">GitHub</a> / <a href="https://stackoverflow.com/users/208746/simon" target="_blank">Stackoverflow</a>.
</p>
<h2>Memberships</h2>
<ul class="main">
    <li><a href="https://naturfotografen.ch/bilder/mitgliedergalerien/mitglied/simon-speich.html" target="_blank">Naturfotografen Schweiz</a> (in German)</li>
    <li><a href="https://www.gdtfoto.de/mitglied/1001285/Simon-Speich" target="_blank">GDT Society of German Wildlife Photographers</a> (in German)</li>
</ul>
<?php echo $bodyEnd->render(); ?>
</body>
</html>