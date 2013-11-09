<?
$filename = 'hiscore.txt';

$FName = "hiscore.txt";
$Str = "";
$File = fopen($FName, "r+");
if ($_SERVER["SERVER_NAME"] != "127.0.0.1") flock($File, LOCK_EX);
$F = file_get_contents($FName);
eval('$arrHighscore = array('.$F.');');
if (feof($File)) rewind($File);
$Score = isset($_POST['PlayerScore']) ? $_POST['PlayerScore'] : '';
$Name =  isset($_POST['PlayerName']) ? $_POST['PlayerName'] : '';
$Level =  isset($_POST['PlayerLevel']) ? $_POST['PlayerLevel'] : '';
// prevent inserting of unwanted characters through own form
$Score = preg_replace("/\D*/", '', $Score);
$Name = preg_replace("/\W*/", '', $Name);
$Level = preg_replace("/\D*/", '', $Level);

$arrN = array($Level, $Name, $Score);
for ($i = 0; $i < count($arrHighscore); $i++) {
  if ((int) $Score >= (int) $arrHighscore[$i][2]) {
    if ($i == 0) { 
      array_unshift($arrHighscore, $arrN);
      array_pop($arrHighscore);
    }
    else {
      $arrT1 = array_slice($arrHighscore, 0, $i);
      $arrT2 = array_slice($arrHighscore, $i);
      array_push($arrT1, $arrN);
      array_pop($arrT2);
      $arrHighscore = array_merge($arrT1, $arrT2);
    }
    break;
  }
}
foreach($arrHighscore as $i) $Str .= 'array("'.implode("\",\"",$i).'"),';
$Str = substr($Str, 0, -1);
fwrite($File, $Str, strlen($Str));
ftruncate($File ,ftell($File));
flock($File, LOCK_UN);
fclose($File);
echo($Str);

?>

