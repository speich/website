<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="<?php echo $lang->get(); ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>speich.net +++ Dojo BorderContainer Beispiel</title>
<script type="text/javascript">
var djConfig = {
	isDebug: false, 
	parseOnLoad: false
};
</script>
<script type="text/javascript" src="http://o.aolcdn.com/dojo/1.2.3/dojo/dojo.xd.js"></script>
<link rel="stylesheet" type="text/css" href="http://o.aolcdn.com/dojo/1.2.3/dojo/resources/dojo.css"/>
<link rel="stylesheet" type="text/css" href="http://o.aolcdn.com/dojo/1.2.3/dijit/themes/tundra/tundra.css"/>
<script type="text/javascript">
dojo.require("dijit.layout.ContentPane");
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.TabContainer");

function Init() {
	// create layout
	var El = new dijit.layout.BorderContainer({style: 'width: 100%; height: 100%;' }, 'LayoutSplit1');
	new dijit.layout.ContentPane({region: 'top'}, 'Top1');
	new dijit.layout.ContentPane({region: 'left', splitter: true, minSize: 200, style: 'width: 350px' }, 'Left1');
	new dijit.layout.ContentPane({region: 'center' }, 'Right1');
	new dijit.layout.BorderContainer({style: 'width: 100%; height: 100%;' }, 'LayoutSplit2');
	new dijit.layout.ContentPane({splitter: true, region: 'left', minSize: 300, style: 'width: 50%' }, 'Left2');
	new dijit.layout.ContentPane({region: 'center', minSize: 600 }, 'Right2');
	new dijit.layout.TabContainer({style: 'width: 100%'}, 'TabCont');
  new dijit.layout.ContentPane({title: 'Tab1'}, 'Tab1');
	new dijit.layout.ContentPane({title: 'Tab2'}, 'Tab2');
	new dijit.layout.BorderContainer({style: 'width: 100%; height: 100%;' }, 'LayoutSplit3');
	new dijit.layout.ContentPane({splitter: true, region: 'top', style: 'height: 70%'}, 'Top3');
	new dijit.layout.ContentPane({region: 'center' }, 'Bottom3');
	El.startup();	// has to be called on top element in widget hierarchy
}
dojo.addOnLoad(Init);

</script>
<style type="text/css">
#LayoutCont {
	position: absolute;
	width: 100%;
	height: 100%;
}
#Top1 {
	background-image:url(../../images/ban_bg.gif); 
	background-position: center top;
	background-repeat: repeat-x;
	height:75px;
	background-color: #1C78A6;
}
#Top1 img {
	position: absolute;
	top: 0px;
	right: 0px;
}
</style>
</head>

<body class="tundra">
<div id="LayoutCont">
	<div id="LayoutSplit1">
		<div id="Top1"><a href="../../default.php"><img alt="Layout" src="../../images/logo.gif"/></a></div>
		<div id="Left1">
			<div id="LayoutSplit3">
				<div id="Top3">Region 3 Top</div>
				<div id="Bottom3">Region 3 Center</div>
			</div>
		</div>
		<div id="Right1">
			<div id="LayoutSplit2">
				<div id="Left2">
					<div id="TabCont">
						<div id="Tab1">Tab1 Cont</div>
						<div id="Tab2">Tab2 Cont</div>
					</div>
				</div>
				<div id="Right2">Region 2 Center</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
