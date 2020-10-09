<?php require_once '../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title><?php echo $web->pageTitle; ?>: Canon vs. Nikon Telephoto Lenses: If weight plays a major role</title>
<?php require_once 'inc_head.php' ?>
<link rel="stylesheet" href="/library/dgrid/0.3.21/css/skins/claro.css">
<link href="nikon-lens-comparison.css" rel="stylesheet" type="text/css">
</head>

<body class="claro">
<?php require_once 'inc_body_begin.php'; ?>
<h1>Canon vs. Nikon telephoto lenses: If weight plays a major role</h1>
<p>In order to photograph wildlife, you generally need telephoto lenses with a focal length between 300mm and 800mm.
	These lenses are also heavy, especially when carrying around on foot. The charts and table on this page may help you
	decide which telephoto lens to buy, if the weight plays a	major role.</p>
<p>For more details read my article <a href="https://photographylife.com/canon-vs-nikon-telephoto-lenses" target="_blank">Canon vs Nikon Telephoto Lenses</a> on photographylife.com</p>
<h2>Date of comparison: 2011/2013</h2>
<ul class="legend"><li class="canon"></li><li>Canon</li><li class="nikon"></li><li>Nikon</li></ul>
<div id="chartRatio" class="chart"></div>
<div id="chartWeight" class="chart"></div>
<div id="chartDiameter" class="chart"></div>
<div id="chartLength" class="chart"></div>
<p>It is kind of frustrating that the Canon lenses are about half to a full kilo lighter (because of the fluorite lenses)
	than the Nikon ones if you are an owner of Nikon equipment like me. For the weight of a Nikon 500mm you get a Canon 600mm...</p>
<h2>Nikon and Canon telephoto lenses</h2>
<p>Note: The column headings of the table below are sortable. Just click on them.</p>
<div id="grid"></div>
<p><a href="https://www.speich.net/articles/2011/12/05/canon-vs-nikon-telephoto-lenses-if-weight-plays-a-major-role/">leave	a comment</a></p>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	baseUrl: '../library/dojo/1.16.3/',
	packages: [
		{name: 'dojo', location: 'dojo'},
		{name: 'dijit', location: 'dijit'},
		{name: 'dojox', location: 'dojox'},
		{name: 'dgrid', location: '../../dgrid/0.3.21'},
		{name: 'xstyle', location: '../../xstyle/0.3.3'},
		{name: 'put-selector', location: '../../put-selector/0.3.6'},
		{
			name: 'snet', location: '../../speich.net'
		}
	]
};
</script>
<script src="/library/dojo/1.16.3/dojo/dojo.js" type="text/javascript"></script>
<script type="text/javascript">
require([
	'dojo/store/Memory',
	'dojox/charting/Chart2D',
	'snet/charting/themes/snet',
	'dojox/charting/action2d/Tooltip',
	'dojox/charting/StoreSeries',
	'dojox/charting/widget/Legend',
	'dgrid/Grid',
	'dgrid/extensions/ColumnResizer',
	'dojo/_base/declare',
	'xstyle/has-class',
	'xstyle/css',
	'put-selector/put'
], function(Memory, Chart2D, snet, Tooltip, StoreSeries, Legend, Grid, ColumnResizer, declare) {

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
		],
		store = new Memory({
			data: data
		}),
		grid = declare([Grid, ColumnResizer])({
			columns: [
				{
					label: 'make', field: 'model', renderCell: function(object, data, td) {
						td.innerHTML = '<span class="' + object.model.toLowerCase() + '"></span>' + object.model;
					}
				},
				{
					label: "lens", field: 'lens', renderCell: function(object, data, td) {
						td.innerHTML = '<a href="' + object.link + '"><img class="thumb" src="' + object.img + '">' + object.lens + '</a>';
					}
				},
				{label: "aperture", field: 'f'},
				{label: "weight [kg]", field: 'w'},
				{label: "diameter [mm]", field: 'd'},
				{label: "length [mm]", field: 'l'},
				{label: "focal length/weight [mm/kg]", field: 'r1'}
			]
		}, 'grid'),

		chartWeight = new Chart2D('chartWeight', {
			title: 'Lens Weight'
		}),
		chartDiameter = new Chart2D('chartDiameter', {
			title: 'Lens Diameter'
		}),
		chartLength = new Chart2D('chartLength', {
			title: 'Lens Length'
		}),
		chartRatio = new Chart2D('chartRatio', {
			title: 'Ratio of Focal Length to Weight'
		}),

		dataSeries1 = new StoreSeries(store, {
			query: {model: 'Nikon'}
		}, {
			y: 'w',
			x: 'fLength'
		}),
		dataSeries2 = new StoreSeries(store, {
			query: {model: 'Canon'}
		}, {
			y: 'w',
			x: 'fLength'
		}),
		dataSeries3 = new StoreSeries(store, {
			query: {model: 'Nikon'}
		}, {
			y: 'd',
			x: 'fLength'
		}),
		dataSeries4 = new StoreSeries(store, {
			query: {model: 'Canon'}
		}, {
			y: 'd',
			x: 'fLength'
		}),
		dataSeries5 = new StoreSeries(store, {
			query: {model: 'Nikon'}
		}, {
			y: 'l',
			x: 'fLength'
		}),
		dataSeries6 = new StoreSeries(store, {
			query: {model: 'Canon'}
		}, {
			y: 'l',
			x: 'fLength'
		}),
		dataSeries7 = new StoreSeries(store, {
			query: {model: 'Nikon'}
		}, {
			y: 'r1',
			x: 'fLength'
		}),
		dataSeries8 = new StoreSeries(store, {
			query: {model: 'Canon'}
		}, {
			y: 'r1',
			x: 'fLength'
		}),
		xAxis = {
			title: 'Focal length [mm]',
			titleOrientation: 'away',
			titleFont: 'normal normal normal 11px Verdana',
			min: 250,
			max: 850,
			majorTicks: true,
			majorLabels: true,
			minorLabels: false,
			minorTicks: false
		};

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

	chartWeight.addAxis("x", xAxis);
	chartWeight.addAxis("y", {
		title: 'Weight [kg]',
		titleFont: 'normal normal normal 11px Verdana',
		min: 2,
		max: 6,
		vertical: true,
		minorTicks: true,
		minorTickStep: 0.5
	});
	chartDiameter.addAxis("x", xAxis);
	chartDiameter.addAxis("y", {
		title: 'Diameter [mm]',
		titleFont: 'normal normal normal 11px Verdana',
		min: 120,
		max: 170,
		vertical: true,
		fixUpper: "major",
		fixLower: "major",
		minorTicks: true
	});
	chartLength.addAxis("x", xAxis);
	chartLength.addAxis("y", {
		title: 'Length [mm]',
		titleFont: 'normal normal normal 11px Verdana',
		min: 240,
		max: 460,
		vertical: true,
		majorTickStep: 50,
		fixUpper: "major",
		minorTicks: true,
		minorTickStep: 10
	});
	chartRatio.addAxis("x", xAxis);
	chartRatio.addAxis("y", {
		title: 'Ratio [mm/kg]',
		titleFont: 'normal normal normal 11px Verdana',
		min: 80,
		max: 170,
		vertical: true,
		majorTickStep: 20,
		fixUpper: "major",
		minorTicks: true,
		minorTickStep: 2
	});

	chartWeight.addSeries("Nikon", dataSeries1);
	chartWeight.addSeries("Canon", dataSeries2);
	chartDiameter.addSeries("Nikon", dataSeries3);
	chartDiameter.addSeries("Canon", dataSeries4);
	chartLength.addSeries("Nikon", dataSeries5);
	chartLength.addSeries("Canon", dataSeries6);
	chartRatio.addSeries("Nikon", dataSeries7);
	chartRatio.addSeries("Canon", dataSeries8);

	chartWeight.setTheme(snet);
	chartDiameter.setTheme(snet);
	chartLength.setTheme(snet);
	chartRatio.setTheme(snet);

	new Tooltip(chartWeight, "default");
	new Tooltip(chartDiameter, "default");
	new Tooltip(chartLength, "default");
	new Tooltip(chartRatio, "default");

	chartWeight.render();
	//new Legend({chart: chartWeight}, "Manufacturer");
	chartDiameter.render();
	chartLength.render();
	chartRatio.render();

	grid.renderArray(data);
	grid.sort('lens', false);
});

</script>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>