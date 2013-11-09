var ColumnCounter = 0;
var arrActionE = [0,0,0,0];
arrActionE.Seq = true;
arrAction.SndPlayed = false;

function SetAction(ThisIndex) {
  var Bl = arrBlock[ThisIndex];
  switch(arrMap[ThisIndex]) {
		case 2:
			Bl.Hbl = 0;
			Bl.Speed = 1;
			Bl.Style = Bl.Style + "-moz-opacity: 0.2; filter: alpha(opacity=20);";	
			break;
		case 5:
			Bl.Hit = 2;
			Bl.Hbl = 0;
			Bl.Style = Bl.Style + "-moz-opacity: 0.2; filter: alpha(opacity=20);";
			Bl.A1 = 5;
			break;
    case 6: Bl.A2 = 6; Bl.Sc = 0; Bl.Hit = 0; break;
		case "C":
			Bl.A2 = 5;
			Bl.Style = Bl.Style + "-moz-opacity: 0.2; filter: alpha(opacity=20);";	
			break;
    case "E1": Bl.A1 = 1; Bl.Img.src = "be1.gif"; break;
  	case "E2": Bl.A1 = 2; Bl.Img.src = "be2.gif"; break;
	  case "E3": Bl.A1 = 3; Bl.Img.src = "be3.gif"; break;
  	case "E4": Bl.A1 = 4; Bl.Img.src = "be4.gif"; break;
  }
}

function Action(ThisB) {
  var Bl = arrBlock[ThisB];
	
	if (Bl.A1 < 5) {
	// check if correct sequence
		if (arrActionE.Seq) {
			for (var i = 0; i < arrActionE.length; i++) {
				if ((arrActionE[i] == 1 && i > (Bl.A1-1)) || (arrActionE[i] == 0 && i < (Bl.A1-1))) {
					for (var j = 0; j < arrBlock.length; j++) {
						if (arrBlock[j].A2 == 5 || arrBlock[j].A2 == 6) {
							arrBlock[j].Hide(1);
							arrBlock[j].Sc = 0;
							arrBlock[j].Hit = 0;
							arrBlock[j].Hbl = 0;
							if (Game.Sound && !arrAction.SndPlayed) {
								PlaySound("S10");
								arrAction.SndPlayed = true;
							}	
							arrActionE.Seq = false;
						}
					}
				}
			}
			if (arrActionE.toString() == "1,1,1,1") {
				for (var j = 0; j < arrBlock.length; j++) {
					if (arrBlock[j].A2 == 5) {
						arrBlock[j].A = 2;
						arrBlock[j].Hit = 0;
						arrActionE = (0,0,0,0);
						TimerAttach("MoveBlock(" + j + ")");
					}
				}
			}
		}
		if (Bl.A1 < 5) {
			Bl.V = 0;
			Bl.Hbl = 0;
			Bl.Hide(1);
			if (Bl.A1 == 1) {
				var i = arrBlock.length-1;
				for (; i >= 0; i--) {
					var CurBl = arrBlock[i];
					if (arrMap[i] == 2 || arrMap[i] == 5) {
						if (Ie) document.getElementById(CurBl.d).style.filter = "filter: alpha(opacity=100)";
						else document.getElementById(CurBl.Id).style.MozOpacity = 1;
						CurBl.Hbl = 1;
					}
				}
			}
			if (arrActionE.Seq) {
				arrActionE[Bl.A1-1] = 1;
				for (var j = 0; j < arrBlock.length; j++) {
					if (arrBlock[j].A2 == 5) {
						if (Ie) document.getElementById(arrBlock[j].Id).style.filter = "filter: alpha(opacity=" + document.getElementById(arrBlock[j].Id).style.filter + 20 + ")";
						else document.getElementById(arrBlock[j].Id).style.MozOpacity = Number(document.getElementById(arrBlock[j].Id).style.MozOpacity) + 0.2;
					}
				}
			}
		}
	}
	
	if (Bl.A1 == 5 && Bl.Hit == 0) {
		Bl.Hit = 0;
		Bl.Sc = 0;
		Bl.A1 = 0;
		Bl.Img.src = "blockedup/b6.gif";
		document.getElementById(Bl.Id).firstChild.src = Bl.Img.src;
		var Column = Bl.Id.slice(2) % 10;
		var i = arrBlock.length-1;
		for (; i >= 0; i--) {
			var CurBl = arrBlock[i];
			if (arrMap[i] == 2 && CurBl.Id.slice(2) % 10 == Column) TimerAttach("DropBlock(" + i + ")");
		}
		if (ColumnCounter < 10) ColumnCounter++;
	}
	
	if (Bl.A2 == 5) {
 	  var Score = Number(document.getElementById("Score").firstChild.nodeValue);
   	Level.Score += 50;
    SetScore(Level.Score);
	}
	
	/*
	switch(Bl.A2) {
    case 1:
      var El = document.getElementById("Racket");
      if (TestAct(4)) {
			  PlaySound("S02");
				ActionDetach(4);
			}     
      else if (!TestAct(1)) {
  		  PlaySound("S02");
       	ActionAttach(1);
        Racket.W = 100;
        El.innerHTML = '<img src="blockedup/racket_wide.gif">';
        El.style.width = Racket.W + "px";
      }
    	break;
    case 2:
      var Score = Number(document.getElementById("Score").firstChild.nodeValue);
      Level.Score += 50;
      SetScore(Level.Score);
     	ActionAttach(2);
   		break;
	  case 3:	
      var Delay = 0;
			CreateBall();
			Bl[Bl.LastId-1].Hide(0);
			TimerAttach("MoveBall(" + (Bl.LastId-1) + ")");
      Delay += 700;
			CreateBall();
			Bl[Bl.LastId-1].Hide(0);
      window.setTimeout("TimerAttach('MoveBall(" + (Bl.LastId-1) + ")')", Delay);
			ActionAttach(3);
   		break;
	}*/
}

function DropBlock(ThisB) {
  var Bl = arrBlock[ThisB];
  if (Bl.Y+Bl.H < Number(Board.Y) + Number(Board.H)) {
    Bl.Y += Bl.Speed;
		document.getElementById(Bl.Id).style.top = Bl.Y + "px";  
		Bl.Speed += 0.15;
  }
  else {
    TimerDetach("DropBlock("+ThisB+")");
    if (DetectCollision(Bl, Racket)) {
			Bl.V = 0;
			Bl.Hbl = 0;
			Bl.Hide(1);
			KillBall();
		}
		else {
			var Column = Bl.Id.slice(2) % 10;
			var i = arrBlock.length-1;
			for (; i >= 0; i--) {
				var CurBl = arrBlock[i];
				if (arrMap[i] == 2 && CurBl.Id.slice(2) % 10 == Column) {
					Bl.V = 0;
					Bl.Hbl = 0;
					Bl.Hide(1);
				}
			}
		}			
  }
}


function BlockMissed(ThisB) { var NoAction; }

arrMap = [
0,0,0,6,"C","C",6,0,0,0,
0,"E2",0,6,"C","C",6,0,0,"E3",
0,0,0,"E4","C","C",6,0,0,0,
0,0,0,6,6,6,6,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,2,2,0,0,0,0,
0,0,0,2,2,2,2,0,0,0,
0,0,2,2,2,2,2,2,0,0,
0,2,2,2,2,2,2,2,2,0,
2,2,2,2,2,2,2,2,2,2,
5,5,5,5,5,5,5,5,5,5,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,"E1"
];

Level.MapLoaded = true;