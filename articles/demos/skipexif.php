<?php

/**
 * Extract start and end pointers of APP1 markers (exif, xmp).
 * Scans a jpeg for APP1 markers and returns them as a 2-dim array with start and end position. Scanning starts
 * from the second byte (assuming the first byte is the SOI marker) and ends on the first SOF marker found.
 * @param $data
 * @param bool $exifOnly
 * @return array|null
 */
function extractPointers($data, $exifOnly = false)
{
    // see https://cs.haifa.ac.il/~nimrod/Compression/JPEG/J6sntx2007.pdf
    $size = strlen($data);
    $pointers = [];
    $offset = 2;    // skip SOI (FFD8)
    while ($offset < $size) {
        $chunk = substr($data, $offset, 4);
        $uInt16Big = unpack('n*', $chunk);   // note: unpack starts index at 1!
        if ($uInt16Big[1] === 0xFFE1) { // there might be multiple (0xFFE1) APP1 markers (e.g. Exif and Xmp)
            $pointers[] = [$offset, $offset + $uInt16Big[2] + 2];
            if ($exifOnly === true && substr($data, $offset + 4, 4) === 'Exif') {
                break;
            }
            $offset += $uInt16Big[2] + 2;   // jump directly to next marker, note: length does not include 2byte marker
        } elseif ($uInt16Big[1] > 65471 && $uInt16Big[1] < 65488) {    // SOF0-S0F16 (0xFFC0-0xFFCF) start of frame, note that inside exif data there might also one
            break;
        } else {
            $offset += 2;
        }
    }

    return count($pointers) > 0 ? $pointers : null;
}

/**
 * Render the photo by skipping Exif or Xmp.
 * Skippes start and end positions in the byte string indicated by passed the pointers array.
 * @param $photo
 * @param $pointers
 */
function renderPhoto(&$photo, $pointers)
{
    if ($pointers === null) {
        echo $photo;
    } else {
        echo substr($photo, 0, $pointers[0][0]);
        for ($i = 0, $len = count($pointers) - 1; $i < $len; $i++) {
            echo substr($photo, $pointers[$i][1], $pointers[$i + 1][0] - $pointers[$i][1]);
        }
        echo substr($photo, $pointers[$i][1]);
    }
}

// thumbnail, /thumbnails/{id} is rewritten in .htaccess to ?thumb={id}
if ($imageId === null) {
    $id = $thumbnailId;
    $column = 'p.picCont photo';
} // full image, /{id} is rewritten to ?img={id} in .htaccess
else {
    $id = $imageId;
    $column = 'wp.waPicCont photo';
}
$sql = "SELECT {$column}, w.datum, p.zensTyp, p.picCat FROM lfi.waPics wp
   INNER JOIN lfiwebapp.mimiPics p ON wp.clNr = p.clNr AND wp.invNr = p.invNr AND wp.waPicNr = p.picNr
   INNER JOIN lfi.wa w ON wp.clNr = w.clNr AND wp.invNr = w.invNr
   WHERE p.mimiPicNr = :id AND p.picCat IN (1,2,3,4,7) AND w.datum < TO_DATE('".Censorship::$lastUpdate."', 'YYYY-MM-DD')";
if ($censored === true) {
    $sql .= ' AND p.zensTyp = -1';
}
$stmt = $db->parse($sql);
$db->bind($stmt, 'id', $id);
$db->execute($stmt);
$record = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_LOBS);

// set headers to allow for caching of photos
$etag = md5($record['PHOTO'].$record['ZENSTYP']);
$etagHeader = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false;
header("ETag: $etag");
if ($etagHeader === $etag) {    // we have a matching etag -> not modified
    header('HTTP/1.1 304 Not Modified');
} else {
    // Send back the requested resource with a 200 status, because we don't have an ETag matching the given one ()
    header('Content-Type: image/jpeg');
    if ($etagHeader !== false) {
        // photo or censoring changed do not cache (next time)
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: 0');
    }
    // find exif info and remove it
    if ($imageId !== null) { // thumbnails have exif already removed when created in database, no need to extract exif
        $pointers = extractPointers($record['PHOTO'], $record['PICCAT'] !== '7');
        renderPhoto($record['PHOTO'], $pointers);
    } else {
        echo $record['PHOTO'];
    }
}
