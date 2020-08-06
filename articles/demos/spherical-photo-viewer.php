<?php require_once '../../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="de-ch">
<head>
    <title><?php echo $web->pageTitle; ?></title>
    <meta name="description" content="Website von Simon Speich über Fotografie und Webprogrammierung">
    <meta name="keywords" content="Fotografie, Webprogrammierung, JavaScript">
    <?php require_once '../../layout/inc_head.php' ?>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4.0.6/dist/photo-sphere-viewer.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4.0.6/dist/plugins/markers.min.css">
    <style>
        .psv-marker--normal {
            opacity: 0.8;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/uevent@2.0.0/browser.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.118.3/build/three.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/plugins/markers.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/plugins/gyroscope.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/plugins/stereo.min.js" defer></script>
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
        exampleData: [
          {
            photo: '../images/img1-41790.jpg',
            // correct that camera is facing W instead of N + individual correction
            sphereCorrection: { pan: -Math.PI / 2 + 0.11, tilt: 0, roll: 0 },
            data: [
              /* exported from LFI database with BANR, BART, BHD, AZI, DIST */
              '263718', 'Fagus sylvatica', '15.8', '19', '6.00',
              '471287', 'Acer pseudoplatanus', '37.1', '24', '12.15',
              '27773', 'Abies alba', '', '41', '6.30',
              '27775', 'Abies alba', '49.8', '81', '7.80',
              '27774', 'Pinus sylvestris', '', '109', '6.70',
              '27776', 'Abies alba', '56.4', '120', '9.30',
              '27777', 'Abies alba', '', '128', '12.50',
              '27771', 'Abies alba', '18.1', '182', '3.70',
              '27769', 'Picea abies', '', '190', '2.80',
              '391363', 'Acer pseudoplatanus', '21.6', '219', '6.10',
              '27768', 'Abies alba', '37.9', '222', '1.60',
              '27770', 'Abies alba', '', '241', '3.30',
              '420219', 'Acer pseudoplatanus', '14.2', '299', '7.94',
              '27772', 'Fagus sylvatica', '53.5', '319', '4.80'
            ]
          },
          {
            photo: '../images/439002_6.jpg',
            sphereCorrection: { pan: Math.PI / 2, tilt: 0, roll: 0 },
            data: [
              // AZI is manually corrected to fit photo
              476457, 'Pinus sylvestris', 38.1, 391, 2.25,
              476459, 'Pinus sylvestris', 18.2, 273, 7.35,
              476460, 'Pinus sylvestris', 26.7, 295, 7.45,
              476461, 'Pinus sylvestris', 37.7, 120, 7.63,
              476462, 'Pinus sylvestris', 25.3, 374, 4.27,
              476463, 'Pinus sylvestris', 45.9, 244, 8.50,
              476464, 'Pinus sylvestris', 26.0, 328.5, 7.53,
              476467, 'Pinus sylvestris', 40.6, 124, 8.10,
              476468, 'Pinus sylvestris', 25.8, 282, 5.60,
              476469, 'Pinus sylvestris', 15.8, 291, 7.04
            ]
          }
        ],

        /**
         * Limit a positive number from a range to fall into a new range.
         * The range includes min and max.
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
         * Creates the plot circle overlay.
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
         * Creates the polylines and labels for the cardinal directions overlay.
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
         * Creates the tree overlays.
         * Data used is real world data exported from the NFI database.
         * @param {array} data
         * @param corr correction factor of deviation from north
         * @return {[]}
         */
        createTrees(data, corr = null) {
          let markers = [], lat;

          data.forEach((val, i, arr) => {
            let long, size, marker, dist;

            if (i % 5 === 0) {
              dist = arr[i + 4];
              lat = -0.15 + this.calcRange(dist, 12, 0, 0.1);
              // correct that camera is facing W instead of N + individual correction
              long = arr[i + 3] / 400 * Math.PI * 2; // - Math.PI / 2 + (corr || 0);
              // sizes should be larger the closer the trees are
              size = this.calcRange(dist, 12, 0, 2);
              size = (2 - size) + 'em';
              // labels
              marker = {
                id: 'tree' + val,
                longitude: long,
                latitude: lat,
                html: arr[i + 1] + (arr[i + 2] === '' ? '' : '<br>BHD: ' + arr[i + 2]),
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
                anchor: 'center center'
                /*, tooltip: 'BHD: ' + arr[i + 2]*/
              };
              markers.push(marker);
            }
          });

          return markers;
        },

        /**
         * Creates all photo overlays.
         * Creates all overlay info for the spherical photo viewer to draw them with SVG.
         * @param {number} exampleNr number of the example to use
         * @return {{polyline_rad: (number[]|*[])[], id: *, svgStyle: (app.svgStyle1|{strokeWidth, stroke})}[]}
         */
        createOverlays(exampleNr) {
          let markers,
            example = this.exampleData[exampleNr];

          markers = this.createCardinals();
          markers.push(this.createPlotCircle(24));
          markers.push(...this.createTrees(example.data));

          return markers;
        },

        /**
         * Initializes the spherical photo viewer
         * @param {number} exampleNr number of the example to use
         * @param {array} markers
         * @return {PhotoSphereViewer.Viewer}
         */
        initViewer(exampleNr, markers) {
          let example = this.exampleData[exampleNr];

          return new PhotoSphereViewer.Viewer({
            container: 'viewer',
            panorama: example.photo,
            sphereCorrection: example.sphereCorrection,
            autorotateDelay: 500,
            autorotateSpeed: '1.2rpm',
            touchmoveTwoFingers: true,
            defaultZoomLvl: 30, // initial zoom
            defaultLong: 0,
            defaultLat: 0,
            plugins: [
              [PhotoSphereViewer.MarkersPlugin, {
                markers: markers
              }],
              PhotoSphereViewer.GyroscopePlugin,
              PhotoSphereViewer.StereoPlugin
            ]
          });
        }
      };

      window.onload = function() {
        let exampleNr = 1,
          markers = app.createOverlays(exampleNr);

        app.initViewer(exampleNr, markers);
      };
    </script>
</head>

<body>
<?php require_once '../../layout/inc_body_begin.php'; ?>
<h1>Demo of Photo Sphere Viewer</h1>
<p>This demo shows the photo sphere viewer in action with markers. If you are on a mobile device, the gryroscope is
    enabled.</p>
<p>The photo was taken for the Nation Forest Inventory of Switzerland with a Ricoh Theta.</p>
<div id="viewer" style="width: 1200px; height: 700px"></div>
<?php require_once '../../layout/inc_body_end.php'; ?>
</body>
</html>