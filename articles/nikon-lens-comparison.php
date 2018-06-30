<?php require_once '../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title><?php echo $web->pageTitle; ?>: Canon vs. Nikon Teleobjektive</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="//ajax.googleapis.com/ajax/libs/dojo/1.7.0/dijit/themes/claro/claro.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="/library/dgrid/v0.3.16/css/skins/claro.css">
<link href="../layout/layout.css" rel="stylesheet" type="text/css">
<link href="nikon-lens-comparison.css" rel="stylesheet" type="text/css">
</head>

<body class="claro">
<?php require_once 'inc_body_begin.php'; ?>
<h1>Canon vs. Nikon Teleobjektive: Wenn das Gewicht eine Hauptrolle spielt</h1>
<p>Um Tiere von Nahe fotografieren zu können, braucht es meistens Teleobjektive mit Brennweiten zwischen 300mm und 800mm.
	Diese sind entsprechend schwer und müssen oft erst noch lange herumgetragen werden. Die Grafiken und Tabelle auf dieser
	Seite können als Entscheidungshilfe beim Kauf eines Teleobjektivs	dienen, wenn das Gewicht eine Hauptrolle spielt.</p>
<p>Mein Beitrag <a href="https://photographylife.com/canon-vs-nikon-telephoto-lenses" target="_blank">Canon vs Nikon Telephoto Lenses</a> auf photographylife.com liefert weitere Informationen.</p>
<h2>Vergleich 2011/2013</h2>
<ul class="legend"><li class="nikon"></li><li>Nikon</li><li class="canon"></li><li>Canon</li></ul>
<div id="chartRatio" class="chart"></div>
<div id="chartWeight" class="chart"></div>
<div id="chartDiameter" class="chart"></div>
<div id="chartLength" class="chart"></div>
<p>Schon erstaunlich, dass die Canon Objektive (wegen den Fluoritlinsen) bei gleicher Lichtstärke rund ein halbes bis
	ganzes Kilo leichter als die entsprechenden Nikon Objektive sind. Für das Gewicht eines Nikon 500mm bekommt man ein Canon 600mm...</p>
<h2>Nikon und Canon Teleobjektive</h2>
<p>Hinweis: Die Tabelle ist interaktiv und lässt sich mit Klick auf den Spaltenkopf sortieren.</p>
<div id="grid"></div>
<p><a href="/articles/2011/12/05/canon-vs-nikon-teleobjektive-wenn-das-gewicht-eine-hauptrolle-spielt/">Kommentar hinzufügen</a></p>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	baseUrl: '//ajax.googleapis.com/ajax/libs/dojo/1.9.0/',
	packages: [
		{name: 'dojo', location: 'dojo'},
		{name: 'dijit', location: 'dijit'},
		{name: 'dojox', location: 'dojox'},
		{name: 'dgrid', location: '/library/dgrid/v0.3.16'},
		{name: 'xstyle', location: '/library/xstyle'},
		{name: 'put-selector', location: '/library/put-selector'},
		{name: 'snet', location: '/library/speich.net'}
	]
};
</script>
<script src="//ajax.googleapis.com/ajax/libs/dojo/1.9.0/dojo/dojo.js" type="text/javascript"></script>
<script type="text/javascript">
require([
	'dojo/store/Memory',
	'dojox/charting/Chart2D',
	'snet/charting/themes/snet',
	'dojox/charting/action2d/Tooltip',
	'dojox/charting/StoreSeries',
	'dgrid/Grid',
	'dgrid/extensions/ColumnResizer',
	'dojo/_base/declare',
	'xstyle/has-class',
	'xstyle/css',
	'put-selector/put'
], function(Memory, Chart2D, snet, Tooltip, StoreSeries, Grid, ColumnResizer, declare) {

	var data = [
		{
			id: 1,
			f: 2.8,
			w: 2.9,
			d: 124,
			l: 268,
			r1: 103,
			lens: '300mm f/2.8G ED VR II AF-S',
			model: 'Nikon',
			fLength: 300,
			link: 'https://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_300mmf_28g_ed_vr2/index.htm',
			img: 'https://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2186-AF-S-NIKKOR-300mm-f2.6G-ED-VR-II-Super-Telephoto/Views/160_2186_AFS-300-ED-VR-II_front.png'
		},
		{
			id: 2,
			f: 2.8,
			w: 4.62,
			d: 160,
			l: 368,
			r1: 87,
			lens: '400mm f/2.8G ED VR AF-S',
			model: 'Nikon',
			fLength: 400,
			link: 'https://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_400mmf_28g_vr/index.htm',
			img: 'https://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2171_AF-S-NIKKOR-400mmf-2.8G-ED-VR/Views/160_2171_AF-S-NIKKOR-400mmf-2.8G-ED-VR_front.png'
		},
		{
			id: 3,
			f: 4,
			w: 3.88,
			d: 140,
			l: 391,
			r1: 129,
			lens: '500mm f/4G ED VR AF-S',
			model: 'Nikon',
			fLength: 500,
			link: 'https://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_500mmf_4g_vr/index.htm',
			img: 'https://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2172-AF-S-NIKKOR-500mm-f-4G-ED-VR/Views/160_2172_AF-S-NIKKOR-500mm-f-4G-ED-VR_front.png'
		},
		{
			id: 4,
			f: 4,
			w: 5.06,
			d: 166,
			l: 445,
			r1: 119,
			lens: '600mm f/4G ED VR AF-S',
			model: 'Nikon',
			fLength: 600,
			link: 'https://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_600mmf_4g_vr/index.htm',
			img: 'https://cdn-4.nikon-cdn.com/en_INC/IMG/Assets/Camera-Lenses/2010/2173_AF-S-NIKKOR-600mm-f-4G-ED-VR/Views/160_2173_AF-S-NIKKOR-600mm-f-4G-ED-VR_FRONT.png'
		},
		{
			id: 9,
			f: 5.6,
			w: 4.59,
			d: 160,
			l: 461,
			r1: 174,
			lens: '800mm f/5.6G ED VR AF-S',
			model: 'Nikon',
			fLength: 800,
			link: 'https://imaging.nikon.com/lineup/lens/singlefocal/Telephoto/af-s_800mmf_56g_fl_ed_vr/index.htm',
			img: 'https://cdn-4.nikon-cdn.com/en_INC/o/LDqJE40w3j_g9iVRjQ6KkyaYSas/Views/160_2205-AF-S-NIKKOR-800mm.png'
		},
		{
			id: 5,
			f: 2.8,
			w: 2.4,
			d: 128,
			l: 248,
			r1: 125,
			lens: '300mm f/2.8L IS II USM',
			model: 'Canon',
			fLength: 300,
			link: 'https://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_300mm_f_2_8l_is_usm',
			img: 'https://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef300_28lisu_c2_60x90.gif'
		},
		{
			id: 6,
			f: 2.8,
			w: 3.85,
			d: 163,
			l: 343,
			r1: 104,
			lens: '400mm f/2.8L IS II USM',
			model: 'Canon',
			fLength: 400,
			link: 'https://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_400mm_f_2_8l_is_ii_usm',
			img: 'https://www.usa.canon.com/CUSA/assets/app/images/cameras/lenses/EF400_LISIIU/profile/ef400lisiiu_3q_90x60.gif'
		},
		{
			id: 7,
			f: 4,
			w: 3.19,
			d: 146,
			l: 383,
			r1: 157,
			lens: '500mm f/4L IS II USM ',
			model: 'Canon',
			fLength: 500,
			link: 'https://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_500mm_f_4l_is_usm',
			img: 'https://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef500_4lisu_c2_60x90.gif'
		},
		{
			id: 8,
			f: 4,
			w: 3.92,
			d: 168,
			l: 448,
			r1: 153,
			lens: '600mm f/4L IS II USM ',
			model: 'Canon',
			fLength: 600,
			link: 'https://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_600mm_f_4l_is_usm',
			img: 'https://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef600_4lisu_c2_60x90.gif'
		},
		{
			id: 10,
			f: 5.6,
			w: 4.5,
			d: 162,
			l: 461,
			r1: 177,
			lens: '800mm f/5.6L IS USM ',
			model: 'Canon',
			fLength: 800,
			link: 'https://www.usa.canon.com/cusa/consumer/products/cameras/ef_lens_lineup/ef_800mm_f_5_6l_is_usm',
			img: 'https://www.usa.canon.com/CUSA/assets/app/images/product/Camera/ef800_56lisu_cl_hr_60x90.gif'
		}
	];

	var store = new Memory({
		data: data

	});

	var grid = declare([Grid, ColumnResizer])({
		columns: [
			{
				label: 'Marke', field: 'model', renderCell: function(object, data, td, options) {
					td.innerHTML = '<span class="' + object.model.toLowerCase() + '"></span>' + object.model;
				}
			},
			{
				label: "Objektiv", field: 'lens', renderCell: function(object, data, td, options) {
					td.innerHTML = '<a href="' + object.link + '"><img class="thumb" src="' + object.img + '">' + object.lens + '</a>';
				}
			},
			{label: "Blende", field: 'f'},
			{label: "Gewicht [kg]", field: 'w'},
			{label: "Durchmesser [mm]", field: 'd'},
			{label: "Länge [mm]", field: 'l'},
			{label: "Brennweite/Gewicht [mm/kg]", field: 'r1'}
		]
	}, 'grid');
	grid.renderArray(data);
	grid.sort('lens', false);

	var chartWeight = new Chart2D('chartWeight', {
		title: 'Objektivgewicht'
	});
	var chartDiameter = new Chart2D('chartDiameter', {
		title: 'Objektivdurchmesser'
	});
	var chartLength = new Chart2D('chartLength', {
		title: 'Objektivlänge'
	});
	var chartRatio = new Chart2D('chartRatio', {
		title: 'Brennweite pro Gewicht'
	});

	var dataSeries1 = new StoreSeries(store, {
		query: {model: 'Nikon'}
	}, {
		y: 'w',
		x: 'fLength'
	});
	var dataSeries2 = new StoreSeries(store, {
		query: {model: 'Canon'}
	}, {
		y: 'w',
		x: 'fLength'
	});
	var dataSeries3 = new StoreSeries(store, {
		query: {model: 'Nikon'}
	}, {
		y: 'd',
		x: 'fLength'
	});
	var dataSeries4 = new StoreSeries(store, {
		query: {model: 'Canon'}
	}, {
		y: 'd',
		x: 'fLength'
	});

	var dataSeries5 = new StoreSeries(store, {
		query: {model: 'Nikon'}
	}, {
		y: 'l',
		x: 'fLength'
	});
	var dataSeries6 = new StoreSeries(store, {
		query: {model: 'Canon'}
	}, {
		y: 'l',
		x: 'fLength'
	});
	var dataSeries7 = new StoreSeries(store, {
		query: {model: 'Nikon'}
	}, {
		y: 'r1',
		x: 'fLength'
	});
	var dataSeries8 = new StoreSeries(store, {
		query: {model: 'Canon'}
	}, {
		y: 'r1',
		x: 'fLength'
	});

	chartWeight.setTheme(snet);
	chartDiameter.setTheme(snet);
	chartLength.setTheme(snet);
	chartRatio.setTheme(snet);

	chartWeight.addPlot("default", {
		type: "ClusteredColumns",
		gap: 5,
		minBarSize: 16,
		markers: true
	});
	chartDiameter.addPlot("default", {
		type: "ClusteredColumns",
		gap: 5,
		minBarSize: 16,
		markers: true
	});
	chartLength.addPlot("default", {
		type: "ClusteredColumns",
		gap: 5,
		minBarSize: 16,
		markers: true
	});
	chartRatio.addPlot("default", {
		type: "ClusteredColumns",
		gap: 5,
		minBarSize: 16,
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
		title: 'Brennweite [mm]',
		titleOrientation: 'away',
		min: 250,
		max: 850,
		majorTicks: true,
		majorLabels: true,
		minorLabels: false,
		minorTicks: false
	};

	chartWeight.addAxis("x", xAxis);
	chartWeight.addAxis("y", {
		title: 'Gewicht [kg]',
		titleFont: 'normal normal normal 11px Verdana',
		min: 2,
		max: 6,
		vertical: true,
		minorTicks: true,
		minorTickStep: 0.25,
		fixed: true
	});
	chartDiameter.addAxis("x", xAxis);
	chartDiameter.addAxis("y", {
		title: 'Durchmesser [mm]',
		titleFont: 'normal normal normal 11px Verdana',
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
		title: 'Länge [mm]',
		titleFont: 'normal normal normal 11px Verdana',
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
		title: 'Verhältnis [mm/kg]',
		titleFont: 'normal normal normal 11px Verdana',
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