<?php include '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->getWindowTitle(); ?>: Contact</title>
<?php require_once '../layout/inc_head.php' ?>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Contact</h1>
<script type="text/javascript">
document.write('<p>You can reach me by my e-Mail address <a href="mailto:info' + '@' + 'speich.net">info' + '@' + 'speich.net</a>.</p>');
</script>
<p>GitHub: <a href="https://github.com/speich" target="_blank">https://github.com/speich</a></p>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>