<?php
require_once __DIR__.'/../../scripts/php/inc_script.php';
require_once __DIR__.'/nls/'.$language->get().'/photo.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../library/node_modules/photoswipe/dist/photoswipe.css">
<link rel="stylesheet" href="../../library/node_modules/photoswipe/dist/default-skin/default-skin.css">
</head>

<body class="tundra">
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<div class="searchBox">
<script>
  (function() {
    var cx = '000284793056488053930:vzx-zdwjz0w';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchbox></gcse:searchbox>
</div>
<div class="searchResults">
<script>
  (function() {
    var cx = '000284793056488053930:vzx-zdwjz0w';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
</div>
<gcse:searchresults-only defaultToImageSearch="true" disableWebSearch="true"></gcse:searchresults-only>
<?php echo $bodyEnd->render(); ?>
</body>
</html>