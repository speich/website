<?php require_once '../../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="de-ch">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta name="description" content="Website von Simon Speich über Fotografie und Webprogrammierung">
<meta name="keywords" content="Fotografie, Webprogrammierung, JavaScript">
<?php require_once '../../layout/inc_head.php' ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@3.5.1/dist/photo-sphere-viewer.min.css">
<style>
.psv-marker--normal {
	opacity: 0.8;
}
</style>
<script src="https://cdn.jsdelivr.net/gh/olado/doT@1.1.2/doT.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/combine/npm/uevent@2.0.0,npm/uevent@1.0.0" defer></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.108.0/build/three.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.108.0/examples/js/controls/DeviceOrientationControls.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.108.0/examples/js/effects/StereoEffect.js" defer></script>
<!--<script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@3.5.1/dist/photo-sphere-viewer.min.js" defer></script>-->
<script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4.0.0-alpha.2/dist/photo-sphere-viewer.min.js" defer></script>
<script>
let app = {

	svgStyle1: {
		fill: 'none',
		stroke: 'rgba(0, 162, 200, 0.6)',
		strokeWidth: '2px'
	},
	svgStyle3: {
		fill: 'rgba(255, 255, 255, 0.3)',
		stroke: 'rgba(0, 162, 200, 0.8)',
		strokeWidth: '2px'
	},
	htmlStyle1: {
		maxWidth: '20px',
		color: 'rgba(0, 162, 200, 1)',
		fontSize: '3em',
		textAlign: 'center'
	},
	htmlStyle2: {
		color: 'white',
		maxWidth: '200px',
		fontSize: '2em',
		textAlign: 'center'
	},

	/**
	 * Limit a positive number from a range to fall into a new range.
	 * The rang includes min and max.
	 * @example
	 * @param num number
	 * @param maxOrig maximum of original range
	 * @param minNew minimum of new range
	 * @param maxNew maximum of new range
	 */
	calcRange(num, maxOrig, minNew, maxNew) {
		let n = num;

		if (num > maxOrig) {
			n = maxOrig;
		}

		return minNew + n / maxOrig * (maxNew - minNew);
	},

	/**
	 * Created coordinates for edges to draw the sample plot polygon.
	 * @param numEdges number of edges to draw
	 */
	createPlotCircle(numEdges) {
		let lat = -0.13, long, edges = [], len, i;

		// from -π to +π
		i = -numEdges / 2 - 1;
		len = numEdges / 2 + 1;
		for (i; i < len; i++) {
			long = i * 2 * Math.PI / numEdges;
			edges.push([long, lat]);
		}

		return {
			id: 'circle',
			polygonRad: edges,
			svgStyle: this.svgStyle1
		};
	},

	/**
	 * Creates the polylines and labels for the cardinal directions.
	 * @return {{polyline_rad: [number[], [*, *]], id: *, svgStyle: (app.svgStyle1|{strokeWidth, stroke})}[]}
	 */
	createCardinals() {
		let markers = [],
			lat = -0.33, long,
			data = ['west', 'nord', 'east', 'south'];

		data.forEach((val, i) => {
			let marker, label;

			// polyline
			long = i * Math.PI / 2 - Math.PI / 2;
			marker = {
				id: val,
				polylineRad: [[0, -1.571], [long, lat]],
				svgStyle: this.svgStyle1
			};
			markers.push(marker);

			// label
			label = val.charAt(0).toUpperCase();
			marker = {
				id: 'label' + label,
				longitude: long,
				latitude: lat,
				html: label,
				anchor: 'center bottom',
				scale: [0.5, 2.5],
				style: this.htmlStyle1
			};
			markers.push(marker);
		});

		return markers;
	},

	/**
	 *
	 * @param corr correction factor of deviation from north
	 * @return {[]}
	 */
	createTrees(corr) {
		/* export from database with BANR, BART, BHD, AZI, DIST */
		let markers = [], lat, data = [
			"263718", "Fagus sylvatica", "15.8", "19", "6.00",
			"471287", "Acer pseudoplatanus", "37.1", "24", "12.15",
			"27773", "Abies alba", '', "41", "6.30",
			"27775", "Abies alba", "49.8", "81", "7.80",
			"27774", "Pinus sylvestris", '', "109", "6.70",
			"27776", "Abies alba", "56.4", "120", "9.30",
			"27777", "Abies alba", '', "128", "12.50",
			"27771", "Abies alba", "18.1", "182", "3.70",
			"27769", "Picea abies", '', "190", "2.80",
			"391363", "Acer pseudoplatanus", "21.6", "219", "6.10",
			"27768", "Abies alba", "37.9", "222", "1.60",
			"27770", "Abies alba", '', "241", "3.30",
			"420219", "Acer pseudoplatanus", "14.2", "299", "7.94",
			"27772", "Fagus sylvatica", "53.5", "319", "4.80"
		];
		data.forEach((val, i, arr) => {
			let long, size, marker, dist;

			if (i % 5 === 0) {
				dist = arr[i + 4];
				lat = -0.15 + this.calcRange(dist, 12, 0, 0.1);
				// correct that camera is facing W instead of N + individual correction
				long = arr[i + 3] / 400 * Math.PI * 2 - Math.PI / 2 + corr;
				// sizes should be larger the closer the trees are
				size = this.calcRange(dist, 12, 0, 2);
				size = (2 - size) + 'em';
				// labels
				marker = {
					id: 'tree' + val,
					longitude: long,
					latitude: lat,
					html: arr[i + 1] + (arr[i + 2] === '' ? '': '<br>BHD: ' + arr[i + 2]),
					anchor: 'center center',
					scale: [0.5, 2],
					style: this.htmlStyle2
				};
				marker.style.fontSize = size;
				markers.push(marker);

				// circles
				size = 16 - this.calcRange(dist, 12, 2, 12);
				marker = {
					id: 'ba' + val,
					longitude: long,
					latitude: lat,
					circle: size,
					scale: [1, 2],
					svgStyle: this.svgStyle3,
					anchor: 'center center'/*,
					tooltip: 'BHD: ' + arr[i + 2]*/
				};
				markers.push(marker);
			}
		});

		return markers;
	},

	createMarkers() {
		let markers;

		markers = this.createCardinals();
		markers.push(this.createPlotCircle(24));
		markers.push(...this.createTrees(0.11));

		return markers;
	},

	initViewer(markers) {
		return new PhotoSphereViewer({
			container: 'viewer',
			panorama: '../images/img1-41790.jpg',
			timeAnim: false,
			defaultZoomLvl: 1, // initial zoom
			defaultLong: 0,
			defaultLat: 0,
			markers: markers
		});
	}
};

window.onload = function() {
	let markers = app.createMarkers();

	app.initViewer(markers);
}
</script>
</head>

<body>
<?php require_once '../../layout/inc_body_begin.php'; ?>
<h1>Demo of Photo Sphere Viewer</h1>
<p>This demo shows the photo sphere viewer in action with markers. If you are on a mobile device, the gryroscop is
	enabled.</p>
<p>The photo was taken for the Nation Forest Inventor of Switzerland with a Ricoh Theta </p>
<div id="viewer" style="width: 1200px; height: 700px"></div>
<?php require_once '../../layout/inc_body_end.php'; ?>
</body>
</html>