<?php
$wOrig = 5568;
$hOrig = 3712;

// test 1
$ax = 0.101513 * $wOrig;
$ay = 0.19265 * $hOrig;
$bx = 0.928063* $wOrig;
$by = 0.9568 * $hOrig;
$phi = 2.04302; // * -1

// result 1
// w = 4498
// h = 2999
$a2 = abs(sqrt((pow(($ax - $bx) / 2, 2) + pow(($ay - $by) / 2, 2))));    // magnitude of vector a' (after translating image to origin)
echo "||a'||: $a2<br>";

$alpha = rad2deg(atan2(($ay - $by), ($ax - $bx) )) * -1;
echo "alpha: $alpha<br>";

$arr['w'] = abs(round(cos(deg2rad($alpha - $phi)) * $a2 * 2));
$arr['h'] = abs(round(sin(deg2rad($alpha - $phi)) * $a2 * 2) );

echo "{$arr['w']} x {$arr['h']}<br>";
echo 'should be: 4498 x 2999<br><br>';

// test 2
$ax = round(0.115399 * $wOrig);
$ay = round(0 * $hOrig);
$bx = round(0.811426 * $wOrig);
$by = round(1 * $hOrig);
$phi = -10.0756;

// result 2
// w = 4465 : 4790 325
// h = 2977 : 3203

$a2 = abs(sqrt((pow(($ax - $bx) / 2, 2) + pow(($ay - $by) / 2, 2))));    // magnitude of vector a' (after translating image to origin)
echo "||a'||: $a2<br>";

$alpha = rad2deg(atan2(($ay - $by), ($ax - $bx) )) * -1;
echo "alpha: $alpha<br>";

$arr['w'] = abs(round(cos(deg2rad($alpha - $phi)) * $a2 * 2));
$arr['h'] = abs(round(sin(deg2rad($alpha - $phi)) * $a2 * 2));

echo "{$arr['w']} x {$arr['h']}<br>";
echo 'should be: 4465 x 2977<br><br>';



// square around center with 2625 x 2625 and 0°
$ay = 0.146447;
$ax = 0.264298;
$by = 0.853553;
$bx = 0.735702;
$phi = 0;

$a2 = abs(sqrt((pow(($ax - $bx) / 2, 2) + pow(($ay - $by) / 2, 2))));    // magnitude of vector a' (after translating image to origin)
echo "||a'||: $a2<br>";

$alpha = rad2deg(atan2(($ay - $by), ($ax - $bx) )) * -1;
echo "alpha: $alpha<br>";

$arr['w'] = abs(round(cos(deg2rad($alpha - $phi)) * $a2 * 2 * $wOrig));
$arr['h'] = abs(round(sin(deg2rad($alpha - $phi)) * $a2 * 2 * $hOrig));

echo "{$arr['w']} x {$arr['h']}<br>";
echo 'should be: 2625 x 2625<br><br>';

// square around center with 2625 x 2625 and 45°
echo 'rotated by 45°:<br>';
$ay = 0.5 ;
$ax = 0.166667;
$by = 0.5;
$bx = 0.833333;
$phi = 45;

$a2 = abs(sqrt((pow(($ax - $bx) / 2, 2) + pow(($ay - $by) / 2, 2))));    // magnitude of vector a' (after translating image to origin)
echo "||a'||: $a2<br>";

$alpha = rad2deg(atan2(($ay - $by), ($ax - $bx) )) * -1;
echo "alpha: $alpha<br>";

$arr['w'] = abs(round(cos(deg2rad($alpha - $phi)) * $a2 * 2 * $wOrig));
$arr['h'] = abs(round(sin(deg2rad($alpha - $phi)) * $a2 * 2 * $hOrig * $wOrig / $hOrig));

echo "{$arr['w']} x {$arr['h']}<br>";
echo 'should be: 2625 x 2625<br><br>';



// square around center with 1 x 1 and 0°
echo 'rotated by 0°:<br>';
$ay = 0.5;
$ax = 0.5;
$by = -0.5;
$bx = -0.5;
$phi = 0;

$a2 = abs(sqrt((pow(($ax - $bx) / 2, 2) + pow(($ay - $by) / 2, 2))));    // magnitude of vector a' (after translating image to origin)
echo "||a'||: $a2<br>";

$alpha = rad2deg(atan2(($ay - $by), ($ax - $bx) )) * -1;
echo "alpha: $alpha<br>";

$arr['w'] = abs(round(cos(deg2rad($alpha - $phi)) * $a2 * 2));
$arr['h'] = abs(round(sin(deg2rad($alpha - $phi)) * $a2 * 2 ));

echo "{$arr['w']} x {$arr['h']}<br>";
echo 'should be: 1 x 1<br><br>';


// square around center with 1 x 1 and 0°
echo 'rotated by 45°:<br>';
$ay = 0;
$ax = -0.7071068;
$by = 0;
$bx = 0.7071068;
$phi = 45;

$a2 = abs(sqrt((pow(($ax - $bx) / 2, 2) + pow(($ay - $by) / 2, 2))));    // magnitude of vector a' (after translating image to origin)
echo "||a'||: $a2<br>";

$alpha = rad2deg(atan2(($ay - $by), ($ax - $bx) )) * -1;
echo "alpha: $alpha<br>";

$arr['w'] = abs(round(cos(deg2rad($alpha - $phi)) * $a2 * 2));
$arr['h'] = abs(round(sin(deg2rad($alpha - $phi)) * $a2 * 2 ));

echo "{$arr['w']} x {$arr['h']}<br>";
echo 'should be: 1 x 1';