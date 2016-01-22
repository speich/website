<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en-us">
<head>
<title>speich.net +++ BlockedUp, another Arkanoid </title>
<script type="text/javascript">
<!--

/*************************************
*    copyright by Simon Speich       *
*  www.speich.net / info@speich.net  *
*************************************/
var Dom = document.getElementById ? 1 : 0;
var Op7 = (window.opera && document.createComment) ? 1 : 0;
var Ie = Dom && document.all && !Op7 ? 1 : 0;
var arrBlock = [];
var arrBall = [];
var Racket;
var Pi = Math.PI;
var arrTimer = [];
var arrAction = [];
var arrMap = [];
var LastSide = 0;
var Level = { Level: 1, Lives: 3, Hits: 0, Strokes: 0, Score: 0, Num: 4, Init: false, Loading: false, MapLoaded: false };
var Game = { Loop: true, Init: false, Speed: 30, Sound: false, SoundLoaded: false };
var arrInit = [];
var RunOnce = true;
var SplashImg = new Image();
var Board = { X:20, Y:70, W:400, H:460, Img: new Image() };
var LoadCounter = 0;
var Int;

SplashImg.src = "blockedup/startup.jpg";

if (!Ie) window.captureEvents(Event.KEYPRESS);

<?php
  $Str = file_get_contents("blockedup/hiscore.txt");
  $Str = str_replace("array(", "[", $Str);
  $Str = str_replace("\")", "\"]", $Str);
  $Str = 'var arrHighscore =  ['.$Str.'];';
  echo $Str;
?>

function DisplStartUp(Msg) {
	document.getElementById("Loading2").innerHTML = Msg;
//	document.getElementById("Loading2").appendChild(document.createElement("br"));
}

function Loading1() {
	var Str;
	switch (LoadCounter) {
		case 0: Str = "/"; break;
		case 1: Str = "--"; break;
		case 2: Str = "\\"; break;
		case 3: Str = "|"; LoadCounter = 0; break;
	}
	document.getElementById("Loading1").innerHTML = Str;
	LoadCounter++;
}

function DetachArrInit(Item){
  var Found = false;
  for (var i = 0; i < arrInit.length; i++)
	  if (arrInit[i] == Item) {
  	  Found = true;
      break;
    }
 	if (Found) {
 		for (var j = i; j < arrInit.length-1; j++)	arrInit[j] = arrInit[j+1];
 		arrInit.length--;
 	}
}

function CheckArrInit(Item) {
	for (var i = 0; i < arrInit.length; i++) {
		if (arrInit[i] == Item) { 
		 	return true;
			break;
		}
	}
	return false;
}

function Initialize() {
	if (RunOnce) {
  	Loop();
		document.getElementById("LevelInfo").appendChild(document.createElement("p"));
		TimerAttach("Initialize()");
		DisplStartUp("loading level...");
   	TimerAttach("LoadLevel()");
		arrInit.push("Level");
		DisplStartUp("checking for sound...");
		arrInit.push("Sound");
		if (window.document.Sound) {
			DisplStartUp("loading sound...");
			Game.Sound = true;
  		TimerAttach("LoadSound()");
		}
		DisplStartUp("loading board...");
		Board.Img.src = "blockedup/board.jpg";
		arrInit.push("Board");
	  SetupRacket();
		RunOnce = false;
	}
	if (Board.Img) {
		if (CheckArrInit("Board")) {
			DisplStartUp("board loaded");
			DetachArrInit("Board");
		}
	}
	if (Game.Sound) {
		if (Game.SoundLoaded) {
			if (CheckArrInit("Sound")) {
				DisplStartUp("sound loaded");
				DetachArrInit("Sound");
			}
		}
	}
	else {
		if (CheckArrInit("Sound")) {
			DisplStartUp("no sound");
			DetachArrInit("Sound");
		}
	}
	if (Level.Init) {
		if (CheckArrInit("Level")) { 
			DisplStartUp("Level loaded");
			DetachArrInit("Level");
		}
	}
	if (arrInit.length == 0) {
		TimerDetach("Initialize()");
		window.clearInterval(Int);
    CreateTblHighscore();
		arrBall.NumBall = 0;
		arrBall.LastId = 0;
	 	document.getElementById("Racket").style.visibility = "visible";
		document.getElementById("Board").style.left = (Board.X-7) + "px";
 		document.getElementById("Board").style.top = Board.Y-60 + "px";
		document.getElementById("Board").style.visibility = "visible";
		document.getElementById("Board").style.backgroundImage = "url(" + Board.Img.src + ")";
// 		document.getElementById("Info").style.visibility = "visible";
 		document.getElementById("DivTblHighscore").style.visibility = "visible";
	 	document.getElementById("DivStartUp").style.visibility = "hidden";
		for (var i = 0; i < arrBlock.length; i++) {
			if (arrBlock[i] != 0) {
				CreateEl("div", arrBlock[i].Id, arrBlock[i].Style, arrBlock[i].Html, document.getElementsByTagName("BODY")[0]);		
				arrBlock[i].Hide(0);
			}
		}
    if (Ie) {
			document.attachEvent("onkeypress", CatchKeys);
			Racket.Speed = 5;
		}
    else window.addEventListener("keypress", CatchKeys, false);
		Game.Speed = 30;
		Game.Init = true;
	}
}

var Counter = 0;
function Debug(ThisStr) {
  if (Counter < 20) {
    document.getElementById("Msg").appendChild(document.createTextNode(ThisStr));
    document.getElementById("Msg").appendChild(document.createElement("br"));
  }
  else {
    Counter = 0;
    document.getElementById("Msg").innerHTML = "";
  }
  Counter++;
}

window.onload = Initialize;
//-->
</script>
<style type="text/css">
html {
  position: absolute;
  top: 0; left: 0;
  margin: 0; padding: 0;
  width: 100%;  height: 100%;
}

body {
  position: absolute;
  top: 0; left: 0;
  margin: 0; padding: 0;
  width: 100%;  height: 100%;
  background-color: #1C78A6;
  font-family: verdana, arial, sans-serif; font-size: 11px; color: #000000;
}

img { border: none; }

div {position: absolute;}
.Stats {margin: 0 6px 0 0; font-size: 14px; color: black;}
.TxtColor1 {color: #126994;}

#Board {
  width: 417px; height: 531px;
  color:#126994;
 	visibility: hidden;
}

#DivTblHighscore {
  position: absolute; top: 75px; left: 440px;
  width: 200px;
  padding: 5px 5px 5px 5px;
  background-color: #abcee0;
  visibility: hidden;
}

.Window {
  border: 1px solid #1C78A6;
  width: 200px;
	padding: 5px 5px 5px 5px;
  background-color: white;
}

#Racket { visibility: visible; }

#LevelInfo {
  position: absolute; top: 300px; left: 50px;
		background-color: white;
		visibility: hidden;
}

#DivStartUp {
  position: absolute; top: 20px; left: 50px;
	padding: 5px 5px 5px 5px;
  width: 340px; height: 220px;
	background-image:  url("blockedup/startup.jpg");
}

#StartUpTxt {
	position: absolute; bottom: 10px; height: 30px; width: 290px;
	left: 30px;
	border: none;
	padding: 5px;
	font-size: 10px;
}
</style>
</head>

<body id="Body">
<object id="Sound" width="1" height="1">
<param name="movie" value="blockedup/sonify.swf">
<param name="loop" value="false">
<param name="menu" value="true">
<param name="quality" value="high">
<embed src="blockedup/sonify.swf" loop="false" menu="true"
 quality="high" width="1" height="1" swliveconnect="true" name="Sound"
 type="application/x-shockwave-flash">
</embed>
</object>

<div id="Board">
<div style="margin-left: 272px; z-index: 2"><a href="https://www.speich.net"><img src="blockedup/logo.gif" alt="speich.net logo"/></a></div>
<div style="padding: 20px 0 0 7px; line-height: 1.5"><strong>BlockedUp 1.0</strong><br />
Lives: <span id="Lives" class="Stats"><img src="blockedup/ball_score.gif" id="ImgBall0" style="margin-right: 1px;"><img src="blockedup/ball_score.gif" id="ImgBall1" style="margin-right: 1px;"><img src="blockedup/ball_score.gif" id="ImgBall2" style="margin-right: 1px;"></span>
Score: <span id="Score" class="Stats">000000</span>
Level: <span id="Level" class="Stats">1</span>
Hit-Ratio: <span id="Ratio" class="Stats">0.00</span>
</div>
</div>

<div id="LevelInfo" class="Window">
<p id="PGameOver" style="top: 300px; left: 100px; display: none;">
<span style="font-size: 16px;">GAME OVER</span><br /><br />
[<a href="#" onClick="Level.Level = 1; TimerAttach('LoadLevel()'); return false;">Restart</a>] level 1<br />
[<a href="#" onClick="TimerAttach('LoadLevel()'); return false;">Retry</a>] this level<br />
[<a href="#" onClick="window.close();">Chicken out</a>] ?
</p>
<p id="PHighscore" style="display: none;">
Congratulations !<br />
You reached a new Highscore !<br />
Name: <input type="text" name="PlayerName" id="PlayerName" style="width: 100px; border: 1px solid #cccccc;" value=""/> [<a href="#"
 onClick="document.getElementById('PHighscore').style.display='none'; PostHighscore(); return false;">save</a>]
</p>
</div>

<div id="DivTblHighscore">
<table class="Window">
<caption>Highscores</caption>
<thead>
<tr>
<td class="TxtColor1">Level</td>
<td class="TxtColor1">Player</td>
<td class="TxtColor1">Score</td>
</tr>
</thead>
<tbody id="TblBodyHighscore"></tbody>
</table>
<br />
Game controls<br />
<br />
<span class="TxtColor1">[SPACE]</span> Start / continue<br />
<span class="TxtColor1">[S]</span> Move left<br />
<span class="TxtColor1">[D]</span> Move right<br />
<span class="TxtColor1">[K]</span> Kill ball<br />
<span class="TxtColor1">[P]</span> Pause / resume game<br />
<br />
<span class="TxtColor1">[Q]</span> Restart (level 1)<br />
<span class="TxtColor1">[R]</span> Restart (this level)
</div>

<div id="DivStartUp">
<div id="StartUpTxt">loading <span id="Loading1">|</span><br>
<span id="Loading2">loading js files...</span></div>
</div>

<div id="Racket"></div>
<div id="Msg"></div>
<script type="text/javascript">Int = window.setInterval("Loading1()", 200);</script>
<script type="text/javascript" src="blockedup/game.js"></script>
<script type="text/javascript" src="blockedup/setup.js"></script>
<script type="text/javascript" src="blockedup/hiscore.js"></script>
</body>
</html>
