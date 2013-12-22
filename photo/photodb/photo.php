<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\PagedNav;
use WebsiteTemplate\Website;

require_once 'photoinc.php';

// This $Sql is also used to calculate number of records for paged nav below
// join only with theme when we can filter by theme, otherwise we have multiple records per theme (group by imgid is to time expensive)
// We have to alias all fields since depending on PHP SQLite version short column names is on/off and can't be set.
$sql = "SELECT * FROM (
	SELECT DISTINCT i.id imgId, i.imgFolder imgFolder, i.imgName imgName, i.imgTitle imgTitle, dateAdded, lastChange, imgTitle,
	R.id ratingId, i.imgDateOriginal date ";
if (isset($_GET['theme'])) {
	$sql.= ", T.themeId themeId";
}
else if (isset($_GET['country'])) {
	$sql.= ", lc.countryId countryId";
}
$sql.= ", CASE WHEN i.imgDateOriginal IS NULL THEN
		(CASE WHEN i.imgDate IS NOT NULL THEN DATETIME(i.imgDate, 'unixepoch', 'localtime') END)
	ELSE DATETIME(i.imgDateOriginal, 'unixepoch', 'localtime') END date
	FROM Images i";
if (isset($_GET['theme'])) {
	$sql.= "	INNER JOIN Images_Themes T ON i.id = T.imgId";
}
else if (isset($_GET['country'])) {
	$sql.= "	INNER JOIN Images_Locations il ON i.id = il.imgId
		INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId
	";
}
$sql.= "	LEFT JOIN rating R ON i.ratingId = R.id
	)";

$stmt = $db->db->prepare($sql.$sqlFilter.$sqlSort." LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $numRecPerPage);
$stmt->bindValue(':offset', ($pgNav-1) * $numRecPerPage);
$stmt->execute();
$arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$numRec = $db->getNumRec($sql.$sqlFilter, 'imgId', $arrBind = array(), $lastPage, $web);
$pagedNav = new PagedNav($pgNav, $numRec, $numRecPerPage, $web);

/**
 * @param PhotoDb $db
 * @param array $arrData
 * @param Website $web
 * @return bool
 */
function renderData($db, $arrData, $web) {
	if (count($arrData) == 0) {
		echo '<p>Mit diesen Einstellungen wurden keine Datensätze gefunden.</p>';
		return false;
	}	
	$c = 0;
	$num = count($arrData) - 1;
	foreach ($arrData as $row) {
		// image dimensions
		$imgFile = $db->webroot.$db->getPath('img').'thumbs/'.$row['imgFolder'].'/'.$row['imgName'];
		$imgSize = getimagesize($web->getDocRoot().$db->getPath('img').$row['imgFolder'].'/'.$row['imgName']);
		$imgTitle = $row['imgTitle'];
		$link = str_replace('thumbs/', '', $imgFile);
		$detailLink = 'photo-detail.php'.$web->getQuery(array('imgId' => $row['imgId']));

		if ($imgSize[0] > $imgSize[1]) {
			$css = 'slideHorizontal';
			$cssImg = 'slideImgHorizontal';
			
		}
		else if ($imgSize[0] < $imgSize[1]) {
			$css = 'slideVertical';
			$cssImg = 'slideImgVertical';
		}
		else {
			$css = 'slideQuadratic';
			$cssImg = 'slideImgQuadratic';
		}
		echo '<div class="slide">';
		echo '<div class="slideCanvas'.($c == $num ? ' slideLast' : '').' '.$css.'" style="background-image: url('.$imgFile.')">';
		echo '<a href="'.$link.'" title="'.$imgTitle.'"><img class="'.$cssImg.'" src="'.$imgFile.'" alt="Foto" title="Thumbnail of '.$imgTitle.'"></a>';
		echo '</div>';
		echo '<div class="slideText"><a title="Foto \''.$imgTitle.'\' anzeigen" href="'.$link.'">Zoom</a> | ';
		echo '<a title="Details zu Foto \''.$imgTitle.'\' anzeigen" href="'.$detailLink.'">Details</a></div>';
		echo '</div>';	// end slide
		$c++;
	}
	return true;
}
$pageTitle = $web->getLang() == 'en' ? 'Photo Database' : 'Bilddatenbank';
?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $pageTitle.' | '.$web->pageTitle; ?></title>
<?php require_once __DIR__.'/../../layout/inc_head.php' ?>
<link href="../../layout/photodb.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.4/dijit/themes/tundra/tundra.css" media="screen"/>
<style type="text/css">
#layoutMiddle, #layoutFooterCont {
	max-width: none;
}
</style>
</head>

<body class="tundra">
<?php require_once 'inc_body_begin.php'; ?>
<div class="toolbar">
<div class="pagingBar">
<div class="barTxt"><?php echo $numRec.' Foto'.($numRec > 1 ? 's' : ''); ?></div>
<div class="barVertSeparator"></div>
<?php echo $mRecPp->render(); ?>
<div class="barTxt">pro Seite</div>
<div class="barVertSeparator"></div>
<?php $pagedNav->printNav($pgNav, $web); ?>
</div>
<div class="optionBar">
<div class="barTxt">Sortierung</div>
<?php echo $mSort->render(); ?>
<div class="barVertSeparator"></div>
<div class="barTxt">Filter</div>
<?php echo $mQuality->render(); ?>
<div class="barVertSeparator"></div>
<div id="showMap"></div>
<div class="barVertSeparator"></div>
<form action="photo-search.php" method="GET">
<p><input type="text" id="q" name="q"/><input type="submit" value="suchen"/>
<input type="hidden" value="1" name="qual">
<input type="hidden" value="<?php echo $sort ?>" name="sort">
<input type="hidden" value="<?php echo $numRecPerPage ?>" name="numRecPp"></p>
</form>
</div>
</div>
<div class="clearFix"><?php renderData($db, $arrData, $web); ?></div>
<div class="toolbar">
<div class="pagingBar">
<div class="barTxt"><?php echo $numRec.' Foto'.($numRec > 1 ? 's' : ''); ?></div>
<div class="barVertSeparator"></div>
<?php echo $mRecPp->render(); ?>
<div class="barTxt">pro Seite</div>
<div class="barVertSeparator"></div>
<?php $pagedNav->printNav($pgNav, $web); ?>
</div>
</div>
<div id="slideFullScreenCont"><div><div><span>left</span><span id="slideFullScreen">
<br>
<span class="slideFullScreenAuthor">Foto Simon Speich, www.speich.net</span>
</span><span>right</span></div></div></div>
<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	locale: '<?php echo $locale = $web->getLang(); ?>'
};
</script>
<script type="text/javascript" src="../../library/dojo/1.9.1/dojo/dojo.js"></script>
<script type="text/javascript">
require(['dojo/query', 'dojo/on', 'dojo/dom-style', 'dojo/dom-geometry', 'dojo/_base/fx', 'dojo/domReady!'],
function(query, on, domStyle, domGeometry, fx) {

	var d = document,

	slide = {
		fullScreenCont: d.getElementById('slideFullScreenCont'),
		fullScreen: d.getElementById('slideFullScreen'),
		inserted: false,

		/**
		 * Show image in full size centered on screen.
		 * The centering and filling the screen with background is done through css
		 * by imitating a table row with one cell in it.
		 * @param {Event} evt
		 */
		showFull: function(evt) {
			var scrollY = domGeometry.position('layoutTop01', false).y,
				img = new Image(),
				src = evt.target.href;

			// remove previous image
			if (slide.inserted) {
				slide.fullScreen.removeChild(slide.fullScreen.firstChild);
			}

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

		showNext: function() {},

		showPrevious: function() {	}
	};

	on(slide.fullScreenCont, 'click', function() {
		fx.fadeOut({
			node: slide.fullScreenCont,
			duration: 500,
			onEnd: function() {
				domStyle.set(slide.fullScreenCont, 'display', 'none');
			}
		}).play();
	});

	query('.slideCanvas a:first-child, .slideText a:first-child', d.getElementById('layoutMain')).on('click', function(evt) {
		evt.preventDefault();
		slide.showFull(evt);
	});
});
</script>
</body>
</html>