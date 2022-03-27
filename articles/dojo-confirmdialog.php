<?php require_once __DIR__.'/../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title><?php echo $web->pageTitle; ?>: dojo confirm dialog</title>
<?php echo $head->render(); ?>
<link href="../library/dojo/1.17.2/dijit/themes/claro/claro.css" rel="stylesheet" type="text/css">
<style type="text/css">
.dijitDialog {
	width: 300px;
}
.dialogConfirmButtons {
	border-top: 1px solid #ccc;
	padding-top: 3px;
}
.button {
	padding: 3px 10px;
}
</style>
</head>

<body class="claro">
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Confirm dialog with dojo</h1>
<p>The demo on this page simulates the blocking behavior of JavaScript's native
<a href="https://developer.mozilla.org/en/DOM/window.confirm" target="_blank">window.confirm()</a> method by using a
<a href="https://dojotoolkit.org/reference-guide/1.10/dojo/Deferred.html" target="_blank">dojo/Deferred</a>.</p>
<p>The DialogConfirm.show() method is called 6 times in a loop. You can either cancel the loop, press 'OK' on each
dialog or tick the checkbox and the remaining dialogs will be skipped.</p>
<p id="startLink">Wait for dojo to load...</p>
<p>For more information read my short article about the <a href="../articles/?p=291">DialogConfirm widget</a>
or get the <a href="https://github.com/speich/dialogconfirm">code on github</a>.</p>
<script src="../library/dojo/1.17.2/dojo/dojo.js" type="text/javascript" data-dojo-config="async: true,
	packages: [
		{ 'name': 'speich', 'location': '/library/speich.net' }
	]"></script>
<script type="text/javascript">
require([
	'dojo/_base/array',
	'dojo/_base/Deferred',
	'dojo/dom',
	'dojo/dom-construct',
	'speich/DialogConfirm/DialogConfirm'
], function(array, Deferred, dom, domConstruct, DialogConfirm) {

	function startDemo() {
		var dfds = [],
			arr = [0,1,2,3,4,5];

		dfds[0] = new Deferred();
		dfds[0].resolve(false); // dummy to attach initial dfd to

		array.forEach(arr, function(value, i) {
			dfds[i + 1] = dfds[i].then(function(remember) {
				var dfd,  dialog;

				if (!remember) {
					dialog = new DialogConfirm({
						title: 'Confirm dialog #' + i,
						duration: 500,
						content: '<p>I love dojo. I love dojo. I love dojo.</p>'
					});
					return dialog.show();
				}
				else {
					dfd = new Deferred();
					dfd.resolve(true);
					console.log('skipped');

					return dfd;
				}
			});
		});
	}

	dom.byId('startLink').innerHTML = '';
	domConstruct.create('button', {
		'class':'button',
		innerHTML: '<span>start dialog demo</span>',
		'onclick': function() {
			startDemo();
		}
	}, dom.byId('startLink'));
});
</script>
<?php echo $bodyEnd->render(); ?>
</body>
</html>