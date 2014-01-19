<?php
use WebsiteTemplate\PagedNav;

require_once __DIR__.'/../../library/inc_script.php';
require_once 'photoinc.php';

// This $dql is also used to calculate number of records for paged nav below
// join only with theme when we can filter by theme, otherwise we have multiple records per theme (group by imgid is to time expensive)
// We have to alias all fields since depending on PHP SQLite version short column names is on/off and can't be set.
$sql = "SELECT * FROM (
	SELECT DISTINCT i.id imgId, i.imgFolder imgFolder, i.imgName imgName, i.imgTitle imgTitle, dateAdded, lastChange, imgTitle,
	r.id ratingId, i.imgDateOriginal date ";
if (isset($_GET['theme'])) {
	$sql.= ", it.themeId themeId";
}
else if (isset($_GET['country'])) {
	$sql.= ", lc.countryId countryId";
}
$sql.= ", CASE WHEN i.imgDateOriginal IS NULL THEN
		(CASE WHEN i.imgDate IS NOT NULL THEN DATETIME(i.imgDate, 'unixepoch', 'localtime') END)
	ELSE DATETIME(i.imgDateOriginal, 'unixepoch', 'localtime') END date
	FROM Images i";
if (isset($_GET['theme'])) {
	$sql.= "	INNER JOIN Images_Themes it ON i.id = it.imgId";
}
else if (isset($_GET['country'])) {
	$sql.= "	INNER JOIN Images_Locations il ON i.id = il.imgId
		INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId
	";
}
$sql.= "	LEFT JOIN rating r ON i.ratingId = r.id)";

$stmt = $db->db->prepare($sql.$sqlFilter.$sqlSort." LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $numRecPerPage);
$stmt->bindValue(':offset', ($pg-1) * $numRecPerPage);
$stmt->execute();
$arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$numRec = $db->getNumRec($sql.$sqlFilter, 'imgId', $arrBind = array(), $lastPage, $web);
$pagedNav = new PagedNav($numRec, $numRecPerPage);
$pagedNav->renderText = false;

$pageTitle = $web->getLang() == 'en' ? 'Photo Database' : 'Bilddatenbank';


$word = 'photo'.($numRec > 1 ? 's' : '');
$pagingBar = '<div class="pagingBar">'.
	'<div class="barTxt">'.$numRec.' '.$i180n[$web->getLang()][$word].'</div>'.
	'<div class="barVertSeparator"></div>'.
	$mRecPp->render().
	'<div class="barTxt">'.$i180n[$web->getLang()]['per page'].'</div>'.
	'<div class="barVertSeparator"></div>'.
	$pagedNav->render($pg, $web).
	'</div>';
?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $pageTitle.' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.css" rel="stylesheet" type="text/css">
</head>

<body class="tundra">
<?php require_once 'inc_body_begin.php'; ?>
<div class="toolbar">
<?php echo $pagingBar ?>
<div class="search"><script>
  (function() {
    var cx = '000284793056488053930:zkcmsdcpu2k',
    	gcse = document.createElement('script'),
	 	s = document.getElementsByTagName('script')[0];

    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//www.google.com/cse/cse.js?cx=' + cx;
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:search></gcse:search></div>
<div class="optionBar">
<div class="barTxt"><?php
	echo $i180n[$web->getLang()]['sorting'];
	echo $mSort->render(); ?>
</div>
<div class="barVertSeparator"></div>
<div class="barTxt"><?php
	echo $i180n[$web->getLang()]['rating'];
	echo $mQuality->render();
?></div>
<div class="barVertSeparator"></div>
<div id="showMap"></div>
</div>
</div>

<div><ul><?php renderData($db, $arrData, $web, $i180n); ?></ul></div>

<?php echo $pagingBar ?>

<div id="slideFullScreenCont">
<div class="slideFullScreen">
<span class="slideFullScreenAuthor"><?php echo $i180n[$web->getLang()]['photo']; ?> Simon Speich, www.speich.net</span>
</div>
<!-- not implemented yet
<div class="slideNavClose">close</div>
<div class="slideNavPrev">previous</div>
<div class="slideNavNext">next</div>
-->
</div>

<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	locale: '<?php echo $locale = $web->getLang(); ?>'
};
</script>
<script type="text/javascript" src="../../library/dojo/1.9.2/dojo/dojo.js"></script>
<script type="text/javascript">
require([
	'dojo/_base/fx',
	'dojo/_base/window',
	'dojo/query',
	'dojo/io-query',
	'dojo/on',
	'dojo/dom-style',
	'dojo/dom-geometry',
	'dojo/domReady!'],
function(fx, win, query, ioQuery, on, domStyle, domGeometry) {

	var d = document,
		cont = d.getElementById('slideFullScreenCont'),

	slide = {
		fullScreenCont: cont,
		fullScreen: query('.slideFullScreen', cont)[0],
		navClose: query('.slideNavClose', cont)[0],
		navPrev: query('.slideNavPrev', cont)[0],
		navNext: query('.slideNavNext', cont)[0],
		inserted: false,
		imgMap: {},

		/**
		 * Show image in full size centered on screen.
		 * The centering and filling the screen with background is done through css
		 * by imitating a table row with one cell in it.
		 * @param {string} src image source
		 * @param {number} w image width
		 * @param {number} h image height
		 */
		showFull: function(src, w, h) {
			var scrollY = domGeometry.position('layoutTop01', false).y,
			img = new Image();

			// create image map
			if (!slide.inserted) {
				query('.slideCanvas img').forEach(function() {

				})
			}

			// remove previous image
			if (slide.inserted) {
				slide.fullScreen.removeChild(slide.fullScreen.firstChild);
			}

			domStyle.set(win.body(), 'overflow', 'hidden');
			domStyle.set(slide.fullScreen, {
				width: w + 'px',
				height: h + 'px'
			});
			domStyle.set(slide.fullScreenCont, {
				display: 'block',
				top: scrollY * -1 + 'px'
			});

			// set before setting src
			on(img, 'load', function() {
				fx.fadeIn({
					node: slide.fullScreenCont
				}).play();
			});

			img.src = src;
			img.alt = 'photo';

			slide.fullScreen.insertBefore(img, slide.fullScreen.firstChild);
			slide.inserted = true;
		},

		showNext: function() {

			fx.fadeOut({
				node: slide.fullScreen,
				onEnd: function() {

				}
			}).play();
		},

		showPrevious: function() {
		}
	};

	//query('.slideFullScreen, .slideNavClose', cont).on('click', function() {
	on(cont, 'click', function() {
		fx.fadeOut({
			node: slide.fullScreenCont,
			duration: 500,
			onEnd: function() {
				domStyle.set(win.body(), 'overflow', 'auto');
				domStyle.set(slide.fullScreenCont, 'display', 'none');
			}
		}).play();
	});

	//query('.slideFullScreenNext', cont).on('click', slide.showNext);

	query('.slideCanvas a:first-child, .slideText a:first-child', d.getElementById('layoutMain')).on('click', function(evt) {
		var src = evt.target.href,
		q = ioQuery.queryToObject(src.slice(src.lastIndexOf('?') + 1));

		evt.preventDefault();
		slide.showFull(src, q.w, q.h);
	});
});
</script>
</body>
</html>