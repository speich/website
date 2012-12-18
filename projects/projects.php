<?php require_once '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $web->getWindowTitle(); ?></title>
<?php require_once '../layout/inc_head.php' ?>
<script type="text/javascript">
var remote = null;
function openWin(url, title, x, y) {
  if (remote && remote.open && !remote.closed) {
		remote.close();
	}
  remote = window.open(url, title, 'width=' + x +',height=' + y + ',toolbar=no,menubar=no,location=no,scrollbars=no,resizable=yes');
}
</script>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Projekte</h1>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>