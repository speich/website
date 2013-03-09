<?php
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
		(CASE WHEN i.imgDate IS NOT NULL THEN DATETIME(STRTOTIME(i.imgDate), 'unixepoch', 'localtime') END)
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
$numRec = $db->getNumRec($sql.$sqlFilter, 'imgId', $arrBind = array(), $lastPage);
$pagedNav = new PagedNav($pgNav, $numRec, $numRecPerPage);
$pagedNav->setStep(10, 50);

/**
 * @param PhotoDb $db
 * @param array $arrData
 * @return bool
 */
function renderData($db, $arrData) {
	if (count($arrData) == 0) {
		echo '<p>Mit diesen Einstellungen wurden keine Datensätze gefunden.</p>';
		return false;
	}	
	$c = 0;
	$num = count($arrData) - 1;
	foreach ($arrData as $row) {
		// image dimensions
		$imgFile = $db->getWebRoot().$db->getPath('img').'thumbs/'.$row['imgFolder'].'/'.$row['imgName'];
		$imgSize = getimagesize($db->getDocRoot().$db->getPath('img').$row['imgFolder'].'/'.$row['imgName']);
		$imgTitle = $row['imgTitle'];
		$detailLink = 'photo-detail.php'.$db->getQuery(array('imgId' => $row['imgId']));

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
		echo '<a href="'.$detailLink.'" title="'.$imgTitle.'" onclick="slide.showFull(arguments[0] || window.event, this.firstChild, '.$imgSize[0].', '.$imgSize[1].');">';
		echo '<img class="'.$cssImg.'" src="'.$imgFile.'" alt="Foto" title="Thumbnail of '.$imgTitle.'"/>';
		echo '</a>';
		echo '</div>';	// end slideCanvas
		echo '<div class="slideText"><a title="Foto \''.$imgTitle.'\' anzeigen" href="#'.$imgTitle.'" onclick="slide.showFull(arguments[0] || window.event, this.parentNode.parentNode.getElementsByTagName(\'img\')[0], '.$imgSize[0].', '.$imgSize[1].');">Zoom</a> | ';
		echo '<a title="Details zu Foto \''.$imgTitle.'\' anzeigen" href="'.$detailLink.'">Details</a></div>';
		echo '</div>';	// end slide
		$c++;
	}
	return true;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title>speich.net Bildarchiv: Fotos <?php echo $pageTitle; ?></title>
<?php require_once '../../layout/inc_head.php' ?>
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
<?php $pagedNav->printNav(); ?>
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
<div class="clearFix"><?php renderData($db, $arrData); ?></div>
<div class="toolbar">
<div class="pagingBar">
<div class="barTxt"><?php echo $numRec.' Foto'.($numRec > 1 ? 's' : ''); ?></div>
<div class="barVertSeparator"></div>
<?php echo $mRecPp->render(); ?>
<div class="barTxt">pro Seite</div>
<div class="barVertSeparator"></div>
<?php $pagedNav->printNav(); ?>
</div>
</div>
<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var djConfig = {
	parseOnLoad: false,
	isDebug: false,
	locale: '<?php echo $locale = $web->getLang(); ?>',
	useCommentedJson: true
};
</script>
<!-- <script type="text/javascript" src="../../library/dojo/dojo/dojo.js"></script> -->
<script src="http://ajax.googleapis.com/ajax/libs/dojo/1.4/dojo/dojo.xd.js" type="text/javascript"></script>
<script type="text/javascript">
dojo.require("dijit.form.Button");

var slide = {
	/**
	 * Show slide in full size centered on screen.
	 * The centering and filling the screen with black is done through css
	 * by imitating a table row with one cell in it.
	 * @param {object} thumbnail HTMLImgElement
	 * @param {number} width width of image
	 * @param {number} height height of image
	 */
	showFull: function(evt, thumbnail) {
		var src, scrollY, el, img;
		dojo.stopEvent(evt);
		//img = new Image();
		src = thumbnail.src.replace('thumbs/', '');
		scrollY = dojo.position('layoutTop01').y;
		//scrollY = scrollY.y;
		el = dojo.create('div', null, dojo.body());
		dojo.create('div', {
			innerHTML: '<div><img src="' + src + '" alt="Foto"><br/><span class="SlideFullScreenAuthor">Foto Simon Speich, www.speich.net</span></div>'
		}, el, 'first');
		dojo.addClass(el, 'slideFullScreen');
		dojo.style(el, {
			opacity: 0,
			top: dojo.style(el, 'top') + (scrollY * -1) + 'px'
		});
		dojo.fadeIn({
			node: el
		}).play();
		dojo.connect(el, 'onclick', el, function() {
			var args = {
				node: el,
				duration: 400,
				onEnd: function() {
					dojo.destroy(el);
				}
			};
			dojo.fadeOut(args).play();
		});
	}
};

dojo.addOnLoad(function() {
	var button = new dijit.form.Button({
 		id: 'buttShowMap',
		iconClass: 'buttShowMap',
		label: 'Kartensuche',
		onClick: function() {
			window.location.href = 'photo-mapsearch.php'
		}
	});
	dojo.byId('showMap').appendChild(button.domNode);
});
</script>
</body>
</html>