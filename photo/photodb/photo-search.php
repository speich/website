<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\PagedNav;
use WebsiteTemplate\Website;

require_once 'photoinc.php';

if (isset($_GET['q']) && strlen($_GET['q']) > 2) {
	$lang = ucfirst($web->getLang());
	/* For each column in each table create separate UNION and repeat for each search word.
	This way for each hit in a column we get one scorecount and for each search word */
	$sql = "SELECT score, t1.imgId imgId, I.imgFolder imgFolder, I.imgName imgName, I.imgTitle imgTitle, I.imgDesc imgDesc,
		CASE WHEN I.imgDateOriginal IS NULL THEN
			(CASE WHEN I.imgDate IS NOT NULL THEN DATETIME(I.imgDate, 'unixepoch', 'localtime') END)
		ELSE DATETIME(I.imgDateOriginal, 'unixepoch', 'localtime') END date,
		keywords, locations, themes
		FROM (
		SELECT recordId imgId, SUM(scoreCount) score FROM (";
		foreach ($arrQuery as $key => $query) {
			$sql.= "SELECT COUNT(so.recordId) scoreCount, so.recordId recordId FROM searchIndex si
				INNER JOIN searchOccurrences so ON si.id = so.wordId
				WHERE LOWER(si.word) LIKE :query$key
				GROUP BY so.recordId";
			if ($key < count($arrQuery) - 1) {
				$sql.= " UNION ALL ";
			}
		}
		$sql.= ")
		GROUP BY recordId
	) t1
	INNER JOIN Images I ON t1.imgId = I.id
	LEFT JOIN (SELECT	imgId, GROUP_CONCAT(DISTINCT K.Name) keywords FROM Images_Keywords IK
		INNER JOIN Keywords K ON IK.keywordId = K.id
		GROUP BY imgId) t2 ON t1.imgId = t2.imgId
	LEFT JOIN (SELECT	imgId, GROUP_CONCAT(DISTINCT L.Name) locations FROM Images_Locations IL
		INNER JOIN Locations L ON IL.locationId = L.id
		GROUP BY imgId) t3 ON t1.imgId = t3.imgId
	LEFT JOIN (SELECT	imgId, GROUP_CONCAT(DISTINCT T.Name".$lang.") themes FROM Images_Themes IT
		INNER JOIN Themes T ON IT.themeId = T.id
		GROUP BY imgId) t4 ON t1.imgId = t4.imgId
	LEFT JOIN Exif E ON t1.imgId = E.imgId
	LEFT JOIN Rating R ON I.ratingId = R.id
	LEFT JOIN Images_Themes T ON I.Id = T.ImgId";
	$sqlSort = " ORDER BY score DESC";
	$stmt = $db->db->prepare($sql.$sqlFilter.$sqlSort." LIMIT :limit OFFSET :offset");
	//print_r($db->db->ErrorInfo());
	//echo $sql.$sqlFilter.$sqlGroupBy.$sqlSort." LIMIT :limit OFFSET :offset";

	$stmt->bindValue(':limit', $numRecPerPage);
	$stmt->bindValue(':offset', ($pgNav-1) * $numRecPerPage);
	$arrBind = array();
	foreach ($arrQuery as $key => $query) {
		$val = '%'.strtolower($query).'%';
		$stmt->bindValue(':query'.$key, $val);
		$arrBind['query'.$key] = $val;
	}
	if (!is_null($query)) {
		$stmt->execute();
		$arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$sql = "SELECT * FROM (".$sql.$sqlFilter." GROUP BY t1.imgId)";
		$numRec = $db->getNumRec($sql, 'imgId', $arrBind, $lastPage, $web);
	}
	else {
		$arrData = array();
	}
}
else {
	$query = null;
	$arrData = null;
}

// set correct sidenav to active
if (isset($_GET['qt'])) {
	$queryType = $_GET['qt'];
	switch ($queryType) {
		case 'full':
			$sideNav->arrItem[4]->setActive();
			break;
		case 'geo':
			$sideNav->arrItem[5]->setActive();
			break;
		case 'sci':
			$sideNav->arrItem[6]->setActive();
			break;
	}
}
else {
	$sideNav->arrItem[4]->setActive();		
}
$sideNav->setActive();

$pagedNav = new PagedNav($numRec, $numRecPerPage);

/**
 * @param PhotoDb $search
 * @param $arrData
 * @param Website $web
 * @return bool
 */
function renderDataList($search, $arrData, $web) {
	if (count($arrData) == 0) {
		echo '<p>Mit diesen Einstellungen wurden keine Datensätze gefunden.</p>';
		return false;
	}
	foreach ($arrData as $i) {
		// image dimensions
		$imgFile = $web->getWebRoot().$search->getPath('img').'thumbs/'.$i['imgFolder'].'/'.$i['imgName'];
		$imgSize = getimagesize($web->getDocRoot().$search->getPath('img').$i['imgFolder'].'/'.$i['imgName']);
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
		echo '<div class="clearFix">';
		echo '<div class="listCanvas">';
		echo '<div class="slideCanvas '.$css.'" style="background-image: url('.$imgFile.');">';
		echo '<a href="#" onclick="return slide.showFull(arguments[0] || window.event, this.firstChild, '.$imgSize[0].', '.$imgSize[1].');">';
		echo '<img class="'.$cssImg.'" src="'.$imgFile.'" alt="Thumbnail"/>';
		echo '</a>';
		echo '</div>';
		echo '</div>';
		echo '<div class="listContent"><h2>'.$i['imgTitle'].'</h2>';
		echo '<p>'.$i['imgDesc'].'</p>';
		echo '<p><span class="PhotoTxtLabel">Themen:</span> '.$i['themes'].'</p>';
		echo '<p><span class="PhotoTxtLabel">Stichwörter:</span> '.$i['keywords'].'</p>';
		echo '<p><span class="PhotoTxtLabel">Orte:</span> '.$i['locations'].'</p>';
		echo '<p class="small"><a href="photo-detail.php'.$web->getQuery(array('imgId' => $i['imgId'])).'">Details</a></p>';
		echo '</div>';
		echo '</div>';
	}
	return true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->pageTitle; ?>: Bildarchiv Fotos Suche</title>
<?php require_once 'inc_head.php' ?>
<link href="../../layout/photodb.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/1.8.1/dijit/themes/tundra/tundra.css"/>
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
<p><input type="text" id="q" name="q" value="<?php echo isset($_GET['q']) ? $_GET['q'] : '' ?>"/><input type="submit" value="suchen"/>
<input type="hidden" value="<?php echo $qual ?>" name="qual">
<input type="hidden" value="<?php echo $sort ?>" name="sort">
<input type="hidden" value="<?php echo $numRecPerPage ?>" name="numRecPp"></p>
</form>
</div>
</div>
<p><?php !is_null($arrData) ? renderDataList($db, $arrData, $web) : 0; ?></p>
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
<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var dojoConfig = {
	locale: '<?php echo $locale = $web->getLang(); ?>'
};
</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.8.1/dojo/dojo.js"></script>
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
	showFull: function(evt, thumbnail, width, height) {
		var src, scrollY, el;
		dojo.stopEvent(evt);
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
	dojo.byId('q').focus();
});
</script>
</body>
</html>