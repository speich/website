<?php
require_once '../scripts/php/inc_script.php';
$lang = $lang->get();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<title><?php echo $web->pageTitle.': Dojo BorderContainer '.($lang === 'en' ? 'Example' : 'Beispiel'); ?></title>
<?php echo $head->render(); ?>
<link rel="stylesheet" type="text/css" href="../library/dojo/1.16.3/dojo/resources/dojo.css"/>
<link href="../library/dojo/1.16.3/dijit/themes/claro/claro.css" rel="stylesheet" type="text/css">
<style type="text/css">
#layoutCont {
	position: absolute;
	width: 100%;
	height: 100%;
}

#top1.dijitContentPane {
	padding: 0;
}

#layoutSplit1, #layoutSplit2, #layoutSplit3 {
	width: 100%;
	height: 100%;
}

#left1 {
	width: 350px;
	padding-right: 0;
}

#right1 {
	padding-left: 0;
}

#right1 .dijitTabPaneWrapper {
	border: none;
}

#right1 .dijitTab:first-of-type {
	margin-left: 6px;
}

#left2 {
	width: 50%;
	padding: 12px 0 0 0;
}

#top1 {
	background: url(../layout/images/layout-top.jpg) no-repeat;
}

#top3 {
	height: 30%;
}
</style>
</head>

<body class="claro">
<div id="layoutCont">
	<div id="layoutSplit1">
		<div id="top1"><a href="../index.php"><img alt="Layout" src="../layout/images/layout-logo.gif"/></a></div>
		<div id="left1">
			<div id="layoutSplit3">
				<div id="top3">Region 3 top</div>
				<div id="bottom3">Region 3 center</div>
			</div>
		</div>
		<div id="right1">
			<div id="layoutSplit2">
				<div id="left2">
					<div id="tabCont">
						<div id="tab1">Tab1 content</div>
						<div id="tab2">Tab2 content</div>
					</div>
				</div>
				<div id="right2">Region 2 center</div>
			</div>
		</div>
	</div>
</div>
</body>

<script type="text/javascript" src="../library/dojo/1.16.3/dojo/dojo.js"
        data-dojo-config="async: true,	isDebug: false,	parseOnLoad: false"></script>
<script type="text/javascript">
require(['dijit/layout/ContentPane', 'dijit/layout/BorderContainer', 'dijit/layout/TabContainer'], function(ContentPane, BorderContainer, TabContainer) {
	var el = new BorderContainer({
		gutters: false
	}, 'layoutSplit1');
	new ContentPane({
		region: 'top',
		splitter: false
	}, 'top1');
	new ContentPane({
		region: 'left',
		splitter: true
	}, 'left1');
	new ContentPane({
		region: 'center'
	}, 'right1');
	new BorderContainer({}, 'layoutSplit2');
	new ContentPane({
		splitter: true,
		region: 'left',
		minSize: 300
	}, 'left2');
	new ContentPane({
		region: 'center',
		minSize: 600
	}, 'right2');
	new TabContainer({
		style: 'width: 100%'
	}, 'tabCont');
	new ContentPane({
		title: 'Tab1'
	}, 'tab1');
	new ContentPane({
		title: 'Tab2'
	}, 'tab2');
	new BorderContainer({}, 'layoutSplit3');
	new ContentPane({
		splitter: true,
		region: 'top'
	}, 'top3');
	new ContentPane({
		region: 'center'
	}, 'bottom3');
	el.startup();	// has to be called on top element in widget hierarchy
});
</script>
</html>