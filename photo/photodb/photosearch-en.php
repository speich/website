<?php
require_once __DIR__.'/../../library/inc_script.php';
require_once __DIR__.'/nls/'.$lang->get().'/photo.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../library/PhotoSwipe/dist/photoswipe.css">
<link rel="stylesheet" href="../../library/PhotoSwipe/dist/default-skin/default-skin.css">
</head>

<body class="tundra">
<?php require_once 'inc_body_begin.php'; ?>
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
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>