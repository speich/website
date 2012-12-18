<?php include '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $web->getWindowTitle(); ?>: Kontakt</title>
<?php require_once '../layout/inc_head.php' ?>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Kontakt</h1>
<script type="text/javascript">
document.write('<p>Ich bin über die E-Mail Adresse <a href="mailto:info' + '@' + 'speich.net">info' + '@' + 'speich.net</a> kontaktierbar.</p>');
</script>
<p>Diese Website kann auch über die folgenden Adressen erreicht werden:</p>
<ul class="main">
<li><a href="http://www.vogelbild.ch/">www.vogelbild.ch</a></li>
<li><a href="http://www.tierbild.ch/">www.tierbild.ch</a></li>
<li><a href="http://www.pflanzenbild.ch">www.pflanzenbild.ch</a></li>
<li><a href="http://www.photodb.ch">www.photodb.ch</a></li>
<li><a href="http://www.simonspeich.ch">www.simonspeich.ch</a></li>
</ul>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>