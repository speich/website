DisplStartUp("loading game engine...");

function KillBall() {
	for (var i = 0; i < arrBall.length; i++)
		if (document.getElementById(arrBall[i].Id)) BallMissed(i);
}

function BallMissed(BallN) {
  arrBall[BallN].Hide(1);
	TimerDetach("MoveBall(" + BallN + ")");
	RemoveEl(arrBall[BallN].Id, "Body");
	arrBall.NumBall--;
	for (var i = 0; i < arrTimer.length; i++) {
 		if (/MoveBlock/.test(String(arrTimer[i]))) {
	    var CurBlock = String(arrTimer[i]);
			CurBlock = CurBlock.slice(CurBlock.indexOf("(")+1,CurBlock.indexOf(")"));
			BlockMissed(CurBlock);
 		}
	}
  if (arrBall.NumBall == 0) {
		TimerDetach("All");
		ActionDetach("All");
		Level.Lives--;
	  document.getElementById("ImgBall" + Level.Lives).style.visibility = "hidden";
   	for (var i = 0; i < arrBlock.length; i++) {
     	var Bl = arrBlock[i];
     	if (Bl.V == 1 && Bl.Hit == 0 && Bl.Sc != 0) {
				Bl.Hide(1);
				Bl.Hbl = 0;
			}
   	}
	}
 	PlaySound("S03");
	if (Level.Lives == 0) GameOver();
}

function CreateBall() {
	var LastBlock = FindLastBlock();
	var i = arrBall.LastId;
  var Phi = Math.random();
	arrBall[i] = {
		Id: "Ball" + i,
    X: 195 + Board.X, Y: LastBlock.Y + LastBlock.H,
    LX: 0, LY: 0,  // last position 
    W: 12, H: 12,
    V: 0, Col: 0, Speed: 9,
		Hide: HideEl,
		Style: ""
  }
	arrBall.NumBall++;	// number of balls
	arrBall.LastId++;		// id of last created ball
	arrBall[i].Style = "top: " + arrBall[i].Y + "px; left: " + arrBall[i].X + "px; visibility: hidden";
  CreateEl("div",  arrBall[i].Id, arrBall[i].Style, '<img src="blockedup/ball.gif">', document.getElementsByTagName("BODY")[0]);
  if (Phi < 0.5) Phi = 2*Pi-Phi;
  else Phi = (1-Phi);
  arrBall[i].Phi = Phi;
}

function MoveBall(BallN) {
  var SoundPlayed = false;
  var Ba = arrBall[BallN];
  Ba.X += Ba.Speed * Math.sin(Ba.Phi);
  Ba.Y += Ba.Speed * Math.cos(Ba.Phi);
  document.getElementById(Ba.Id).style.top = Ba.Y + "px";
  document.getElementById(Ba.Id).style.left = Ba.X + "px";
  if (Ba.Y + Ba.H >= Racket.Y) {
    if (Ba.X+Ba.W > Racket.X && Ba.X < Racket.X + Racket.W) {
			if (Game.Sound) { PlaySound("S05"); SoundPlayed = true; }
      var x = (Ba.X + Ba.W/2) - (Racket.X + Racket.W/2);
      Ba.Phi = Pi - Pi*x/(2*Racket.W);
      Level.Strokes++;
      document.getElementById("Ratio").firstChild.nodeValue = CalcRatio();
    }
    else if (Ba.Y + Ba.H >= Board.Y + Board.H) BallMissed(BallN);
  }
  else if (Ba.Y <= Board.Y) {
	  if (Game.Sound && !SoundPlayed) PlaySound("S09");
		Pi-Ba.Phi >= 0 ? Ba.Phi=Pi-Ba.Phi : Ba.Phi=3*Pi-Ba.Phi;
	}
  else if (Ba.X+Ba.W >= Board.X+Board.W) {
		if (Game.Sound && !SoundPlayed) PlaySound("S09");
		Ba.X = Board.X+Board.W-Ba.W-1; Ba.Phi = 2*Pi-Ba.Phi;
	}
  else if (Ba.X <= Board.X) {
	  if (Game.Sound && !SoundPlayed) PlaySound("S09");
	  Ba.X = Board.X+1; Ba.Phi = 2*Pi-Ba.Phi;
	}
  else {
		var i = arrBlock.length-1;
    for (; i >= 0; i--) {
      var CurBl = arrBlock[i];
      if (CurBl.Hbl == 1) {
        if (DetectCollision(Ba, CurBl)) {
          if (CurBl.Col == 0) ActionStart(i, BallN);
          CurBl.Col = 1;
        }
        else CurBl.Col = 0;
      }
    }
  }
  LastSide = 0;
  Ba.LX = Ba.X;
  Ba.LY = Ba.Y;
}

function DetectSide_new(Ball, Block, Ind) {
  var Side = -1; 
	var arrI = [[],[],[],[]];
	var m1 = (Ball.Y - Ball.LY) / (Ball.X - Ball.LX);
  var b1 = (Ball.Y + Ball.H/2) - m1 * (Ball.X + Ball.W/2);
	
	// intercept with side 1
	arrI[0][0] = (Block.Y - b1) / m1;
  arrI[0][1] = Block.Y;
	// intercept with side 3
	arrI[1][0] = (Block.Y + Block.H - b1) / m1;
  arrI[1][1] = Block.Y + Block.H;
	// intercept with side 2
	arrI[2][0] = Block.X;
  arrI[2][1] = m1 * Block.X + b1;
	// intercept with side 4
	arrI[3][0] = Block.X + Block.W;
  arrI[3][1] = m1 * (Block.X + Block.W) + b1;
	
	// find intercept closer to Ball.LX/LY
	var i = 0;
	for (; i < arrI.length; i++) {
		var x = arrI[i][0]; var y = arrI[i][1];
		//check if valid intercept
		if ((x >= Block.X && x <= Block.X + Block.W) && (y >= Block.Y && y <= Block.Y + Block.H)) {
			// store distance from intercept to ball, x values are enough
			arrI[i] = Math.abs(arrI[i][0] - (Ball.X + Ball.W/2));	
		}
	}
/*
  switch (Side) {
    case 1: if (arrBlock[Ind-10] && arrBlock[Ind-10].Hbl == 1) Side = 0; break;
    case 2: if (arrBlock[Ind+1] && arrBlock[Ind+1].Hbl == 1) Side = 0; break;
    case 3: if (arrBlock[Ind+10] && arrBlock[Ind+10].Hbl == 1) Side = 0; break;
    case 4: if (arrBlock[Ind-1] && arrBlock[Ind-1].Hbl == 1) Side = 0; break;
  }
	*/
  return Side;
}

function DetectSide(Ball, Block, Ind) {
  function Calcb(y,m,x) {
    var b = y-m*x;
    return b;
  }
  var Side = -1; 
	var m1 = (Ball.Y-Ball.LY)/(Ball.X-Ball.LX);
  var b1 = Calcb(Ball.Y+Ball.H/2, m1, Ball.X+Ball.W/2);
  var Ix = (Block.Y-b1)/m1;
  var Iy = Block.Y;
  if (Ix >= Block.X && Ix <= Block.X+Block.W && Iy >= Ball.LY && Iy <= Ball.Y+Ball.H) Side = 1;
  else {
    Ix = (Block.Y+Block.H-b1)/m1;
    Iy = Block.Y+Block.H;
    if (Ix >= Block.X && Ix <= Block.X+Block.W && Iy >= Ball.Y && Iy <= Ball.LY+Ball.H) Side = 3;
    else {
      Ix = Block.X+Block.W;
      Iy = m1*(Block.X+Block.W)+b1;
      if (Iy >= Block.Y && Iy <= Block.Y+Block.H && Ix >= Ball.X && Ix <= Ball.LX+Ball.W) Side = 2;
      else {
        Ix = Block.X;
        Iy = m1*(Block.X)+b1;
        if (Iy >= Block.Y && Iy <= Block.Y+Block.H && Ix >= Ball.LX && Ix <= Ball.X+Ball.W) Side = 4;
        else if (Ball.X > Block.X+Block.W/2) { if (Ball.Phi >= Pi) Side = 2; else Side = 0; }
        else if (Ball.X < Block.X+Block.W/2) { if (Ball.Phi <= Pi) Side = 4; else Side = 0; }
        else Side = 0;
      }
    }
  }
  switch (Side) {
    case 1: if (arrBlock[Ind-10] && arrBlock[Ind-10].Hbl == 1) Side = 0; break;
    case 2: if (arrBlock[Ind+1] && arrBlock[Ind+1].Hbl == 1) Side = 0; break;
    case 3: if (arrBlock[Ind+10] && arrBlock[Ind+10].Hbl == 1) Side = 0; break;
    case 4: if (arrBlock[Ind-1] && arrBlock[Ind-1].Hbl == 1) Side = 0; break;
  }
  return Side;
}

function DetectCollision(Ball, Block) {
  if (Block.X+Block.W < Ball.X || Block.Y+Block.H < Ball.Y || Block.X > Ball.X+Ball.W || Block.Y > Ball.Y+Ball.H) return false;
  else {return true};
}

/** 
 * Check if there is no block left.
 * @return {bool}
 */
function CheckLastBlock() {
  for (var i = 0; i < arrBlock.length; i++) {
    if (arrBlock[i].Hit > 0) {
			return false;
		}
  }
	return true;
}

/**
 * Find last block set in map file.
 * 
 * This function searches the map for the last block and returns it.
 * It's used to place the starting position of the balls.
 * 
 * @return object
 */
function FindLastBlock() {
	var i = arrBlock.length-1;
	while (arrBlock[i] === 0) {
		i--;
	}
	return arrBlock[i];
}

function NextLevel() {
  var Score = String(Level.Score);
  while (Score.length < 6) Score = "0" + Score;
  var Ratio = CalcRatio();
  TimerDetach("All");
  ActionDetach("All");
  var Html = 'Level completed.<br />';
  Html += 'Score: ' + Score + '<br />';
	Html += 'Bonus: Hit-Ratio x ';
  for (var i = 0; i < Level.Lives; i++)
    Html += '<img src="blockedup/ball.gif" style="margin-right: 1px;">';
  Html += ' x Level <br>';
  Score = Number(Score) + Math.round(Ratio * Level.Lives * Level.Level);
  Level.Score = Score;
  Level.Level++;
	while (Score.length < 6) Score = "0" + Score;
  Html += 'Total Score: ' + Score;
  Html += '<br><br>[<a href="#" onclick="TimerAttach(\'LoadLevel()\'); return false;">next level</a>]';
	var El = document.createElement("p");
	El.innerHTML = Html;
	document.getElementById("LevelInfo").replaceChild(El, document.getElementById("LevelInfo").lastChild);
  document.getElementById("LevelInfo").style.visibility = "visible";
  SetScore(Level.Score);
  if (CheckHighscore()) {
		document.getElementById("PHighscore").style.display = "block";
  	if (Ie) document.detachEvent("onkeypress", CatchKeys);
  	else window.removeEventListener("keypress", CatchKeys, false);
  }
}

function InitLevel() {
  PlaySound("S08");
  Level.Hits = 0;
  Level.Strokes = 0;
  if (Level.Lives == 0) {  // GameOver
    Level.Score = 0;
    SetScore(Level.Score);
    Level.Lives = 3;
    for (var i = 0; i < Level.Lives; i++) document.getElementById("ImgBall" + i).style.visibility = "visible";
    document.getElementById("PGameOver").style.display = "none";
  }
  if (document.getElementById("LevelInfo").style.visibility == "visible")
		document.getElementById("LevelInfo").style.visibility = "hidden";
  document.getElementById("Level").firstChild.nodeValue = Level.Level;
  SetupBlocks();
	arrBall = [];
	arrBall.NumBall = 0;
	arrBall.LastId = 0;
	if (Game.Init) {
	 	document.getElementById("Racket").style.visibility = "visible";
		for (var i = 0; i < arrBlock.length; i++) {
			if (arrBlock[i] != 0) {
				CreateEl("div", arrBlock[i].Id, arrBlock[i].Style, arrBlock[i].Html, document.getElementsByTagName("BODY")[0]);
				arrBlock[i].Hide(0);
			}
		}			
	}
  if (Ie) document.attachEvent("onkeypress", CatchKeys);
  else window.addEventListener("keypress", CatchKeys, false);
	Level.Init = true;
}

function LoadLevel() {
  if (Level.Level > Level.Num) {
    alert('Sorry, zur Zeit gibt es leider nur '+ Level.Num + ' Level');
    TimerDetach("LoadLevel()");
    return;
  }
  if (!Level.Loading) {
    for (var i = 0; i < arrMap.length; i++)  // remove blocks from last level
      if (arrMap[i] != 0) document.getElementById("Bl"+ i).parentNode.removeChild(document.getElementById("Bl"+ i));
    LevelLoader('blockedup/level' + Level.Level + '.js');
    Level.Loading = true;
  }
  if (Level.MapLoaded) {
    TimerDetach("LoadLevel()");
    Level.Loading = false;
    Level.MapLoaded = false;
    InitLevel();
  }
}

function LevelLoader(File){
  var Scr = document.getElementById('LoadMap');
  var Head = document.getElementsByTagName('head').item(0)
  if (Scr) Head.removeChild(Scr);
  Scr = document.createElement('script');
  Scr.setAttribute("id", 'LoadMap');
  Scr.setAttribute("src", File);
  Scr.setAttribute("type", 'text/javascript');
	Head.appendChild(Scr);
}

function GameOver() {
  TimerDetach("All");
  ActionDetach("All");
  document.getElementById("PGameOver").style.display = "block";
	document.getElementById("LevelInfo").lastChild.style.display = "none";
	document.getElementById("LevelInfo").style.visibility = "visible";
	if (CheckHighscore()) {
		document.getElementById("PHighscore").style.display = "block";
    if (Ie) document.detachEvent("onkeypress", CatchKeys);
    else window.removeEventListener("keypress", CatchKeys, false);
  }
	PlaySound("S04");
}

function SetScore(ThisS) {
  var Score = String(ThisS);
  while (Score.length < 6) Score = "0" + Score;
  document.getElementById("Score").innerHTML = Score;
}

function MoveBlock(ThisB) {
  var CurB = arrBlock[ThisB];
  if (CurB.Y+CurB.H < Number(Board.Y) + Number(Board.H)) {
    CurB.Y += CurB.Speed;
    document.getElementById(CurB.Id).style.top = CurB.Y + "px";  
  }
  else {
    TimerDetach("MoveBlock("+ThisB+")");
    CurB.Hide(1);
		CurB.Hbl = 0;
    if (DetectCollision(CurB, Racket)) Action(ThisB);
    else { BlockMissed(ThisB); };
    if (CheckLastBlock()) NextLevel();
  }
}

function MoveRacket(Direction) { 
  Racket.X += Racket.Speed * Direction;
  Racket.X = Math.min(Math.max(Board.X, Racket.X), Board.W + Board.X - Racket.W);
  document.getElementById("Racket").style.left = Racket.X + "px";  
}

function CalcRatio() {
  if (Level.Strokes == 0) return "0.00";
  var Ratio = Math.round(Level.Hits/Level.Strokes*100)/100;
  if (/\./.test(Ratio)) (/\.[1-9]\b/.test(Ratio) ? Ratio = Ratio + "0" : "");  
  else Ratio = Ratio + ".00";
  return Ratio;
}

function LoadSound() {
	try {
		if (window.document.Sound.PercentLoaded() == 100) { 
			TimerDetach("LoadSound()");
			Game.SoundLoaded = true;
		}
	}
	catch(e) {
		DisplStartUp("error loading sound");
		DetachArrInit("Sound");
		Game.Sound = false;
	}
}

function PlaySound(ThisSound) {
	if (Game.Sound) window.document.Sound.TPlay(ThisSound);
}

function ActionStart(ThisB, BallN) {
  var Played = false;
  var Bl = arrBlock[ThisB];
  var Ba = arrBall[BallN];
	if (Bl.Hit != 0 && Bl.Sc != 0) Level.Hits++;
	Bl.Hit--;
	if (Game.Sound && !Played) PlaySound("S01");
  document.getElementById("Ratio").firstChild.nodeValue = CalcRatio();
  Level.Score += Bl.Sc;
  SetScore(Level.Score);
  if (CheckLastBlock()) {
		for (var i = 0; i < arrBall.length; i++) {
			if (document.getElementById(arrBall[i].Id)) arrBall[i].Hide(1);
		}
    Bl.Hide(1);
		Bl.Hbl = 0;
    NextLevel();
		return;
 	} 
  if (Bl.Hbl) {  // deflect ball
    var Side = (DetectSide(Ba, Bl, ThisB));
		if (LastSide != Side && Side > 0) {
      Ba.Phi = Ba.Phi+Math.random()/500;
   	  if (Ba.Phi == 0) Ba.Phi = 0.01;
      if (Ba.Phi == Pi) Ba.Phi = Pi+0.01
      if (Side == 1 || Side == 3) { Pi-Ba.Phi >= 0 ? Ba.Phi=Pi-Ba.Phi : Ba.Phi=3*Pi-Ba.Phi; }
      else Ba.Phi = 2*Pi-Ba.Phi;
      LastSide = Side;
    }
  }
  if (Bl.Hit == 0 && Bl.Sc != 0) {
    if (Bl.A1 == 0 && Bl.A2 == 0) {
			if (Game.Sound) { PlaySound("S07"); Played = true; }
      Bl.Hide(1);
			Bl.Hbl = 0;
    }
    else {
			if (Game.Sound) { PlaySound("S06"); Played = true; }
      if (Bl.A1 != 0) { Bl.A = 1; Action(ThisB); }
      if (Bl.A2 != 0) { Bl.A = 2 ; Bl.Hbl = 0; TimerAttach("MoveBlock(" + ThisB + ")"); }
    }
  }
}

function ActionAttach(ThisA) { arrAction[arrAction.length] = ThisA; }

function ActionDetach(ThisA) {
  function Reset(Act) {
    switch(Act) {
		  case 1: case 4:
        var El = document.getElementById("Racket");
        Racket.W = 75;
        El.style.width = Racket.W + "px";
        El.innerHTML = '<img src="blockedup/racket.gif">';
    		break;
      case 3:
        MBall = 3;
      	break;
		}
  }
  if (ThisA == "All") {
    for (var i = 0; i < arrAction.length; i++)
      Reset(arrAction[i]);
    arrAction = [];
  }
  else {
    Reset(ThisA);
    var Fnd = false;
    for (var i = 0; i < arrAction.length; i++)
      if (arrAction[i] == ThisA) { Fnd = true; break; }
  	if (Fnd) {
  		for(var j = i; j < arrAction.length-1; j++)	arrAction[j] = arrAction[j+1];
  		arrAction.length--;
      ActionDetach(ThisA);  // in case of multiple occurences
  	}
  }
}

function TimerAttach(Fnc) { arrTimer[arrTimer.length] = Fnc; }

function TimerDetach(Fnc){
  var Found = false;
  if (Fnc == "All") arrTimer.length = 0;
  else {
    for (var i = 0; i < arrTimer.length; i++)
      if (arrTimer[i] == Fnc) {
        Found = true;
        break;
      }
  	if (Found) {
  		for(var j = i; j < arrTimer.length-1; j++)	arrTimer[j] = arrTimer[j+1];
  		arrTimer.length--;
  	}
  }
}

function CatchKeys(e) {
  if (!Game.Init) return;
  Ie ? (K = event.keyCode) : (K = e.which);
//	alert(K)
  switch(K) {
    case 13:  return false; break;
    case 32:
			if (Level.Lives > 0 && arrBall.NumBall == 0) {
				CreateBall();
				arrBall[arrBall.LastId-1].Hide(0);
				TimerAttach("MoveBall(" + (arrBall.LastId-1) + ")");
    	}
  		break;
    case 100: if (Game.Loop) MoveRacket(1); break;
    case 107: KillBall(); break;
		case 112: { Game.Loop ? Game.Loop = false : Game.Loop = true; } break;
		case 113: Level.Level = 1; TimerAttach('LoadLevel()'); break;
		case 114: TimerAttach('LoadLevel()'); break;
    case 115: if (Game.Loop) MoveRacket(-1); break;
  }
}

function Loop() {
	//Debug(arrTimer.length + ": "+arrTimer.toString())
	if (Game.Loop) {	// pause / resume game
	  for (var i = 0; i < arrTimer.length; i++) eval(arrTimer[i]);
	}
  setTimeout("Loop()", Game.Speed);
}

DisplStartUp("game engine loaded");