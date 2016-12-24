<?php require_once 'photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title>Photo |<?php echo $web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.css" rel="stylesheet" type="text/css">
<link href="photo.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
require_once 'inc_body_begin.php';
renderPhoto($photo[0], $db, $web, $lang, $i18n);
require_once 'inc_body_end.php';
?>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	has: {
		'dojo-debug-messages': false
	},
	locale: '<?php echo $locale = $lang->get(); ?>',
	packages: [
		{name: 'gmap', location: './../../../gmap'}
	]
};
</script>
<script type="text/javascript" src="../../library/dojo/1.12.1/dojo/dojo.js"></script>
<script type="text/javascript">
require([
	'gmap/gmapLoader!https://maps.google.com/maps/api/js?v=3.&language=' + dojoConfig.locale,
	'dojo/domReady!'
], function() {

	var initMap = function() {
		var gmaps = google.maps,
			map, mapOptions,
			marker,
			lat = <?php echo (empty($photo[0]['imgLat']) ? 'null' : $photo[0]['imgLat']) ?>,
			lng = <?php echo (empty($photo[0]['imgLng']) ? 'null' : $photo[0]['imgLng']) ?>;

		if (lat && lng) {
			mapOptions = {
				center: new gmaps.LatLng(lat, lng),
				zoom: 5,
				mapTypeId: gmaps.MapTypeId.HYBRID
			};
			map = new gmaps.Map(document.getElementById('map'), mapOptions);
			marker = new gmaps.Marker({
				map: map,
				position: new gmaps.LatLng(lat, lng)
			});

		}
	};

	initMap();
});
</script>
</body>
</html>