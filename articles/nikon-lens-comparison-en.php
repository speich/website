<?php require_once '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->getWindowTitle(); ?>: Canon vs. Nikon Telephoto Lenses: If weight plays a major role</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="../layout/reset.css" rel="stylesheet" type="text/css">
<link href="http://ajax.googleapis.com/ajax/libs/dojo/1.7.0/dijit/themes/claro/claro.css" rel="stylesheet"
		type="text/css">
<link rel="stylesheet" href="/library/dgrid/css/skins/claro.css">
<link href="../layout/layout.css" rel="stylesheet" type="text/css">
<style type="text/css">
.chart {
	width: 360px;
	height: 245px;
	display: inline-block;
}

#legend {
	display: inline-block;
	width: 90px;
	margin-top: 80px;
	font-family: Verdana, Tahoma, Helvetica, Arial, sans-serif;
}
.claro .dgrid { border-width: 1px 0 0 0; }
#grid.dgrid {
	height: 800px;
	margin: 24px 0;
}
#grid .dgrid-header {
	right: 0;
	font-size: 10px;
}
#grid .dgrid-header-scroll { display: none; }
#grid .dgrid, #grid .dgrid-scroller { overflow: auto; }
#grid .thumb {
	width: 90px;
	vertical-align: middle;
	margin-right: 6px;
}
.field-model { width: 70px; }
.field-lens { width: 290px; }
.field-f { width: 70px; }
.field-w { width: 70px; }
.field-d { width: 70px; }
.field-l { width: 70px; }
.nikon, .canon {
	display: inline-block;
	margin: 0 4px 2px 0;
	width: 6px;
	height: 6px;
	background-color: #9e4897;
}
.canon { background-color: #73a84c; }}
</style>
</head>

<body class="claro">
<?php require_once 'inc_body_begin.php'; ?>
<h1>Canon vs. Nikon Telephoto Lenses: If weight plays a major role</h1>
<p>The table (sortable) and charts on this page may help you decide which telephoto lens to buy if the weight plays a major role.</p>
<p>It is kind of frustrating that the Canon <img src="images/circle-canon.gif" alt="green circle icon"> lenses are about half to a full kilo lighter than the Nikon <img src="images/square-nikon.gif" alt="pink square icon"> ones if you are an owner of Nikon equipment like me.
	For the weight of a Nikon 500mm you get a Canon 600mm...</p>
<p><a href="http://www.speich.net/articles/2011/12/05/canon-vs-nikon-telephoto-lenses-if-weight-plays-a-major-role/">leave a comment</a></p>
<div id="chartRatio" class="chart"></div>
<div id="chartWeight" class="chart"></div>
<div id="grid"></div>
<div id="chartDiameter" class="chart"></div>
<div id="chartLength" class="chart"></div>

<script type="text/javascript">
var dojoConfig = {
	async: true,
	baseUrl: 'http://ajax.googleapis.com/ajax/libs/dojo/1.7.0/',
	packages: [
		{ name: 'dojo', location: 'dojo' },
		{ name: 'dijit', location: 'dijit' },
		{ name: 'dojox', location: 'dojox' },
		{ name: 'dgrid', location: '/library/dgrid'},
		{ name: 'xstyle', location: '/library/xstyle'},
		{ name: 'put-selector', location: '/library/put-selector'},
		{ name: 'snet', location: '/library/speich.net'
		}
	]
};
</script>
<script src="http://ajax.googleapis.com/ajax/libs/dojo/1.7.0/dojo/dojo.js" type="text/javascript"></script>
<script type="text/javascript">
require([
	'dojo/store/Memory',
	'dojox/charting/Chart2D',
	'snet/charting/themes/snet',
	'dojox/charting/action2d/Tooltip',
	'dojox/charting/widget/Legend',
	'dojox/charting/StoreSeries',
	'dgrid/Grid',
	'dgrid/extensions/ColumnResizer',
	'dojo/_base/declare',
	'xstyle/has-class',
	'xstyle/css',
	'put-selector/put'
], function(Memory, Chart2D, snet, Tooltip, Legend, StoreSeries, Grid, ColumnResizer, declare) {

	var data = [
		{ id: 1, f: 2.8, w: 2.9, d: 124, l: 268, r1: 103,lens: '300mm f/2.8G ED VR II AF-S', model: 'Nikon', fLength: 300, link: 'http://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_300mmf_28g_ed_vr2/index.htm', img: 'http://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2186-AF-S-NIKKOR-300mm-f2.6G-ED-VR-II-Super-Telephoto/Views/160_2186_AFS-300-ED-VR-II_front.png' },
		{ id: 2, f: 2.8, w: 4.62, d: 160, l: 368, r1: 87, lens: '400mm f/2.8G ED VR AF-S', model: 'Nikon', fLength: 400, link: 'http://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_400mmf_28g_vr/index.htm', img: 'http://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2171_AF-S-NIKKOR-400mmf-2.8G-ED-VR/Views/160_2171_AF-S-NIKKOR-400mmf-2.8G-ED-VR_front.png' },
		{ id: 3, f: 4, w: 3.88, d: 140, l: 391, r1: 129, lens: '500mm f/4G ED VR AF-S', model: 'Nikon', fLength: 500, link: 'http://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_500mmf_4g_vr/index.htm', img: 'http://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2172-AF-S-NIKKOR-500mm-f-4G-ED-VR/Views/160_2172_AF-S-NIKKOR-500mm-f-4G-ED-VR_front.png' },
		{ id: 4, f: 4, w: 5.06, d: 166, l: 445, r1: 119, lens: '600mm f/4G ED VR AF-S', model: 'Nikon', fLength: 600, link: 'http://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_600mmf_4g_vr/index.htm', img: 'http://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2173_AF-S-NIKKOR-600mm-f-4G-ED-VR/Views/160_2173_AF-S-NIKKOR-600mm-f-4G-ED-VR_FRONT.png' },
		{ id: 9, f: 5.6, w: 4.59, d: 160, l: 461, r1: 174, lens: '800mm f/5.6G ED VR AF-S', model: 'Nikon', fLength: 800, link: 'http://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_800mmf_56g_fl_ed_vr/index.htm', img: 'http://cdn-4.nikon-cdn.com/en_INC/o/LDqJE40w3j_g9iVRjQ6KkyaYSas/Views/160_2205-AF-S-NIKKOR-800mm.png' },
		{ id: 5, f: 2.8, w: 2.4, d: 128, l: 248, r1: 125, lens: '300mm f/2.8L IS II USM', model: 'Canon', fLength: 300, link: 'http://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_300mm_f_2_8l_is_usm', img: 'http://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef300_28lisu_c2_60x90.gif' },
		{ id: 6, f: 2.8, w: 3.85, d: 163, l: 343, r1: 104, lens: '400mm f/2.8L IS II USM', model: 'Canon', fLength: 400, link: 'http://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_400mm_f_2_8l_is_ii_usm', img: 'http://www.usa.canon.com/CUSA/assets/app/images/cameras/lenses/EF400_LISIIU/profile/ef400lisiiu_3q_90x60.gif' },
		{ id: 7, f: 4, w: 3.19, d: 146, l: 383, r1: 157, lens: '500mm f/4L IS II USM ', model: 'Canon', fLength: 500, link: 'http://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_500mm_f_4l_is_usm', img: 'http://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef500_4lisu_c2_60x90.gif' },
		{ id: 8, f: 4, w: 3.92, d: 168, l: 448, r1: 153, lens: '600mm f/4L IS II USM ', model: 'Canon', fLength: 600, link: 'http://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_600mm_f_4l_is_usm', img: 'http://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef600_4lisu_c2_60x90.gif' },
		{ id: 10, f: 5.6, w: 4.5, d: 162, l: 461, r1: 177, lens: '800mm f/5.6L IS USM ', model: 'Canon', fLength: 800, link: 'http://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_800mm_f_5_6l_is_usm', img: 'http://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef800_56lisu_cl_hr_60x90.gif' }
	];

	var store = new Memory({
		data: data

	});

	var grid = declare([Grid, ColumnResizer])({
		columns: [
			{ label: 'make', field: 'model', renderCell: function(object, data, td, options) {
				td.innerHTML = '<span class="'+ object.model.toLowerCase() + '"></span>' + object.model;
			} },
			{ label: "lens", field: 'lens', renderCell: function(object, data, td, options) {
				td.innerHTML = '<a href="' + object.link + '"><img class="thumb" src="' + object.img + '">' + object.lens + '</a>';
			}},
			{ label: "aperture", field: 'f' },
			{ label: "weight [kg]", field: 'w' },
			{ label: "diameter [mm]", field: 'd' },
			{ label: "length [mm]", field: 'l' },
			{ label: "focal length/weight [mm/kg]", field: 'r1' }
		]
	}, 'grid');
	grid.renderArray(data);
	grid.sort('lens', false);

	var chartWeight = new Chart2D('chartWeight', {
		title: 'Lens Weight'
	});
	var chartDiameter = new Chart2D('chartDiameter', {
		title: 'Lens Diameter'
	});
	var chartLength = new Chart2D('chartLength', {
		title: 'Lens Length'
	});
	var chartRatio = new Chart2D('chartRatio', {
		title: 'Ratio of Focal Length to Weight'
	});

	var dataSeries1 = new StoreSeries(store, {
		query: {	model: 'Nikon' }
	}, {
		y: 'w',
		x: 'fLength'
	});
	var dataSeries2 = new StoreSeries(store, {
		query: { model: 'Canon' }
	}, {
		y: 'w',
		x: 'fLength'
	});
	var dataSeries3 = new StoreSeries(store, {
		query: {	model: 'Nikon' }
	}, {
		y: 'd',
		x: 'fLength'
	});
	var dataSeries4 = new StoreSeries(store, {
		query: {	model: 'Canon' }
	}, {
		y: 'd',
		x: 'fLength'
	});

	var dataSeries5 = new StoreSeries(store, {
		query: {	model: 'Nikon' }
	}, {
		y: 'l',
		x: 'fLength'
	});
	var dataSeries6 = new StoreSeries(store, {
		query: {	model: 'Canon' }
	}, {
		y: 'l',
		x: 'fLength'
	});
	var dataSeries7 = new StoreSeries(store, {
		query: {	model: 'Nikon' }
	}, {
		y: 'r1',
		x: 'fLength'
	});
	var dataSeries8 = new StoreSeries(store, {
		query: {	model: 'Canon' }
	}, {
		y: 'r1',
		x: 'fLength'
	});

	chartWeight.setTheme(snet);
	chartDiameter.setTheme(snet);
	chartLength.setTheme(snet);
	chartRatio.setTheme(snet);

	chartWeight.addPlot("default", {
		type: "Lines",
		markers: true
	});
	chartDiameter.addPlot("default", {
		type: "Lines",
		markers: true
	});
	chartLength.addPlot("default", {
		type: "Lines",
		markers: true
	});
	chartRatio.addPlot("default", {
		type: "Lines",
		markers: true
	});

	/*
	chartWeight.addPlot("Grid", {
		type: "Grid",
		hAxis: "x",
		vAxis: "y",
		hMajorLines: true,
		hMinorLines: false,
		vMajorLines: false,
		vMinorLines: false
	});
	chartDiameter.addPlot("Grid", {
		type: "Grid",
		hAxis: "x",
		vAxis: "y",
		hMajorLines: true,
		hMinorLines: false,
		vMajorLines: false,
		vMinorLines: false
	});
	*/
	var xAxis = {
		title: 'Focal length [mm]',
		titleOrientation: 'away',
		min: 290,
		max: 810,
		majorTicks: true,
		majorLabels: true,
		minorLabels: false,
		minorTicks: false
	};

	chartWeight.addAxis("x", xAxis);
	chartWeight.addAxis("y", {
		title: 'Weight [kg]',
		min: 2,
		max: 6,
		vertical: true,
		minorTicks: true,
		minorTickStep: 0.25,
		fixed: true
	});
  	chartDiameter.addAxis("x", xAxis);
	chartDiameter.addAxis("y", {
		title: 'Diameter [mm]',
		min: 120,
		max: 170,
		vertical: true,
		fixUpper: "major",
		fixLower: "major",
		minorTicks: true,
		fixed: false
	});
	chartLength.addAxis("x", xAxis);
	chartLength.addAxis("y", {
		title: 'Length [mm]',
		min: 240,
		max: 460,
		vertical: true,
		majorTickStep: 50,
		fixUpper: "major",
		minorTicks: true,
		minorTickStep: 10,
		fixed: false
	});
	chartRatio.addAxis("x", xAxis);
	chartRatio.addAxis("y", {
		title: 'Ratio [mm/kg]',
		min: 80,
		max: 170,
		vertical: true,
		majorTickStep: 20,
		fixUpper: "major",
		minorTicks: true,
		minorTickStep: 4,
		fixed: false
	});
	chartWeight.addSeries("Nikon", dataSeries1);
	chartWeight.addSeries("Canon", dataSeries2);
	chartDiameter.addSeries("Nikon", dataSeries3);
	chartDiameter.addSeries("Canon", dataSeries4);
	chartLength.addSeries("Nikon", dataSeries5);
	chartLength.addSeries("Canon", dataSeries6);
	chartRatio.addSeries("Nikon", dataSeries7);
	chartRatio.addSeries("Canon", dataSeries8);

	new Tooltip(chartWeight, "default");
	new Tooltip(chartDiameter, "default");
	new Tooltip(chartLength, "default");
	new Tooltip(chartRatio, "default");

	chartWeight.render();
	chartDiameter.render();
	chartLength.render();
	chartRatio.render();

});

</script>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>