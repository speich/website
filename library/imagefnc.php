<?php

use PhotoDb\PhotoDb;


require_once '../library/inc_script.php';
require_once __DIR__.'/../photo/photodb/scripts/php/PhotoDb.php';

if (!isset($_GET['fnc'])) {
    exit;
}

switch ($_GET['fnc']) {
    case 'randDbImg':
        $db = new PhotoDb($web->getWebRoot());
        displRandomDbImg($db, $web->getWebRoot());
        break;
    case 'randImg':
        displRandomImg($_SERVER['DOCUMENT_ROOT'].'/layout/images/randhome/');
        break;
}

/**
 * Displays a randomly chosen image from the database.
 * @param Photodb $db
 * @param string $webroot
 */
function displRandomDbImg($db, $webroot)
{
    $sql = "SELECT ImgFolder, ImgName FROM Images
		WHERE RatingId = 3
		ORDER BY RANDOM() LIMIT 1";
    $db->connect();
    $stmt = $db->db->prepare($sql);
    $stmt->execute();
    $photo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $photo = $webroot.$db->getPath('img').$photo[0]['ImgFolder'].'/'.$photo[0]['ImgName'];
    $photo = $_SERVER['DOCUMENT_ROOT'].$photo;
    $imageType = exif_imagetype($photo);
    $mimeType = image_type_to_mime_type($imageType);
    header("Content-Type: ".$mimeType);
    readfile($photo);
}

/**
 * Display a random image from a directory.
 * Directory should only contain images. Function
 * does not check for mimetype
 * @param string $dir directory to sample
 */
function displRandomImg($dir)
{
    $images = [];
    $dh = opendir($dir);
    while (false !== ($file = readdir($dh))) {
        if ($file != '.' && $file != '..') {
            $images[] = $file;
        }
    }
    $i = mt_rand(0, count($images) - 1);
    $photo = $images[$i];
    $photo = $dir.$photo;
    $imageType = exif_imagetype($photo);
    $mimeType = image_type_to_mime_type($imageType);
    header("Content-Type: ".$mimeType);
    readfile($photo);
}

/**
 * @param string $file
 * @return array|bool
 */
function displPalette($file)
{
    $file_img = getImageSize($file);

    switch ($file_img[2]) {
        case 1: //GIF
            $srcImage = imagecreatefromgif($file);
            break;
        case 2: //JPEG
            $srcImage = imagecreatefromjpeg($file);
            break;
        case 3: //PNG
            $srcImage = imagecreatefrompng($file);
            break;

        default:

            return false;
    }

    $xloop = ceil(($file_img[0] - 20) / ($steps - 1));
    $yloop = ceil(($file_img[1] - 20) / ($steps - 1));

    for ($y = 10; $y < $file_img[1]; $y += $yloop) {
        for ($x = 10; $x < $file_img[0]; $x += $xloop) {

            $rgbNow = imagecolorat($srcImage, $x, $y);
            $colorrgb = imagecolorsforindex($srcImage, $rgbNow);

            foreach ($colorrgb as $k => $v) {
                $t[$k] = dechex($v);
                if (strlen($t[$k]) == 1) {
                    if (is_int($t[$k])) {
                        $t[$k] = $t[$k]."0";
                    } else {
                        $t[$k] = "0".$t[$k];
                    }
                }
            }

            $rgb2 = strtoupper($t[red].$t[green].$t[blue]);
            $color_set[] = $rgb2;

        }
    }

    return $color_set;
}

/*
if (isset($_GET['ImgId'])) {
	$ImgId = $_GET['ImgId'];
}
 
 
 

$Sql = "SELECT ImgFolder, ImgName	FROM Images	WHERE Id = :ImgId";
$Stmt = $Db->db->prepare($Sql);
$Stmt->bindValue(':ImgId', $ImgId);
$Stmt->execute();
$photo = $Stmt->fetchAll(PDO::FETCH_ASSOC);
$ImgFile = $Db->getWebRoot().$Db->GetPath('Img').$photo[0]['ImgFolder'].'/'.$photo[0]['ImgName'];


//start the output buffer
ob_start();

//get the source file url
$source_file = $_SERVER['DOCUMENT_ROOT'].$ImgFile;
//get the mime type
$mime = exif_imagetype($source_file);

//create the image
if ($mime == 2) $image = imagecreatefromjpeg($source_file); 
if ($mime == 1) $image = imagecreatefromgif($source_file); 
if ($mime == 3) $image = imagecreatefrompng($source_file); 


//check if we need to run a generic histogram or a rgb one
$do_rgb = $_GET['rgb'];
if ($do_rgb != 'true') $do_rgb = false;
else $do_rgb = true;

//check if we need to output a image or javascript
$type = $_GET['type'];


///// histogram options

//width of the histogram
$height = 175;
if (@$HTTP_GET_VARS['height']) $height = $HTTP_GET_VARS['height'];
if ($height > 385) $height = 385;
if ($height < 0) $height = 0;

//height of the histogram
$width = 2.36*$height;
//width of 1 bar in the histogram
$bar_width = 1;

///// get the original image dimensions

//image width
$image_width = imagesx($image);
//image height
$image_height = imagesy($image);
//megapixel
$megapixel = $image_width * $image_height;


///// Initialize all the arrays that will hold the different histograms
$histogram_all = array();
$histogram_red = array();
$histogram_green = array();
$histogram_blue = array();

///// Initialize the histograms
for ($i = 0; $i <= 85; $i++) {
	$histogram_all[$i] = 0;
	$histogram_red[$i] = 0;
	$histogram_green[$i] = 0;
	$histogram_blue[$i] = 0;
}

///// Loop through all the pixels and record them in all the histograms
for ($i=0; $i<$image_width; $i++) {
	for ($j=0; $j<$image_height; $j++) {
        //get the rgb value for current pixel
        $rgb = @ImageColorAt($image, $i, $j); 

        //extract each value for r, g, b  
		$cols = imagecolorsforindex($image, $rgb);
		$r = $cols['red'];
		$g = $cols['green'];
		$b = $cols['blue'];
                
        // get the luminanse from the RGB value
        //$l = round(($r + $g + $b)/3);
        $l = round((0.3*$r + 0.59*$g + 0.11*$b));


		//calculate the indexes (rounding to the nearest (lowest) 5)
		$l_index = ($l - $l%3)/3;
		$r_index = ($r - $r%3)/3;
		$g_index = ($g - $g%3)/3;
		$b_index = ($b - $b%3)/3;
		
        
		// add the points to the histogram
        $histogram_all[$l_index] 	+= $l / $megapixel;    
        $histogram_red[$r_index] 	+= $r / $megapixel;
        $histogram_green[$g_index]  += $g / $megapixel;
        $histogram_blue[$b_index] 	+= $b / $megapixel;
    }
}

//build up the url
$url =  "http://chart.apis.google.com/chart?cht=ls";
//decide what histogram to build
if ($do_rgb == true) {
	//get the max value for any histogram
	$max_array[0] = max($histogram_red);
	$max_array[1] = max($histogram_green);
	$max_array[2] = max($histogram_blue);
	$max = max($max_array);
	//encode the histograms
	$encoded_red  = 	encodeHistogram($histogram_red,$max);
	$encoded_green  = 	encodeHistogram($histogram_green,$max);
	$encoded_blue  = 	encodeHistogram($histogram_blue,$max);

	
	//build up the url more
	$url .= "&chd=s:".$encoded_red.",".$encoded_green.",".$encoded_blue;
	$url .= "&chco=c21f1fAA,99c274AA,519bc2AA";
	$url .= "&chls=2,5,0|2,5,0|2,5,0";
} else {
	//encode the histogram
	$max = max($histogram_all);
	$encoded_all  = 	encodeHistogram($histogram_all, $max);
	//build up the url more
	$url .= "&chd=s:".$encoded_all;
	$url .= "&chco=AAAAAA";
	$url .= "&chls=2,5,0";
	$url .= "&chm=B,AAAAAA,0,0,0";
}
$url .= "&chs=".$width."x".$height;


	// send the right headers
	header("Content-Type: image/png");
	// dump the picture and stop the script
	readfile($url);

//output flush
ob_end_flush();


/**
  * Caches the putput if necessary
  */
function cache($caching, $filename)
{
    //cache the output before outputting it
    if ($caching) {
        // open the cache file for writing
        $fp = fopen($filename, 'w');

        // save the contents of output buffer to the file
        fwrite($fp, ob_get_contents());

        // close the
        fclose($fp);
    }
}

/**
 * This functions encodes the historgram data to a GChart compatible encoding
 */

function encodeHistogram($histogram, $max)
{

    // Port of JavaScript from http://code.google.com/apis/chart/
    // http://james.cridland.net/code

    // A list of encoding characters to help later, as per Google's example
    $simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $chartData = "";
    for ($i = 0; $i < count($histogram); $i++) {
        $currentValue = $histogram[$i];

        if ($currentValue > -1) {
            $chartData .= substr($simpleEncoding, 61 * ($currentValue / $max), 1);
        } else {
            $chartData .= '_';
        }
    }

    // Return the chart data
    return $chartData;
}

/**
 * Gets the mime of a remote file
 */
function getMime($link)
{
    //try and check the mime type
    try {
        //unless the link is empty
        if ($link != "") {
            //try and open the link (read only)
            @$file = fopen($link, "r");
            //if the file is not readable, return false
            if (!$file) {
                return false;
            } //if the file is readable, check the mime type
            else {
                //get the metadata of the file
                $wrapper = stream_get_meta_data($file);
                //get the file headers
                $headers = $wrapper['wrapper_data'];

                //loop through the headers and search for the content type
                foreach ($headers as $header) {
                    //if the content type matches the provided content type, return true
                    if (stripos($header, 'Content-Type') !== false) {
                        return substr($header, 14);
                    }
                }

                //if we exited the loop, clearly no correct header was found, and return false
                return false;
            }
        }
    } //return false if fetching the file fails
    catch (Exception $e) {
        return false;
    }
}

?>