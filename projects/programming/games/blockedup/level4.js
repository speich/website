var arrActionE = [0,0,0,0];
arrActionE.Seq = true;
arrAction.SndPlayed = false;

function SetAction(ThisIndex) {
  var Bl = arrBlock[ThisIndex];
  switch(arrMap[ThisIndex]) {
    case 6: Bl.Sc = 0; Bl.Hit = 0; break;
    case "C":
			Bl.A2 = 5;
			Bl.Hbl = 0;
			Bl.Hit = 1;
			Bl.Img.src = "bc.gif";
			Bl.Style = Bl.Style + "-moz-opacity: 0.2; filter: alpha(opacity=20);";
			break;
//    case "E1": Bl.A1 = 1; Bl.A2 = 1; Bl.Img.src = "be1.gif"; break;
//  	case "E2": Bl.A1 = 2; Bl.A2 = 2; Bl.Img.src = "be2.gif"; break;
//	  case "E3": Bl.A1 = 3; Bl.A2 = 3; Bl.Img.src = "be3.gif"; break;
//  	case "E4": Bl.A1 = 4; Bl.A2 = 4; Bl.Img.src = "be4.gif"; break;
    case "E1": Bl.A1 = 1; Bl.Img.src = "be1.gif"; break;
  	case "E2": Bl.A1 = 2; Bl.Img.src = "be2.gif"; break;
	  case "E3": Bl.A1 = 3; Bl.Img.src = "be3.gif"; break;
  	case "E4": Bl.A1 = 4; Bl.Img.src = "be4.gif"; break;
	}
}

function Action(ThisB) {
  var Bl = arrBlock[ThisB];
	// check if correct sequence
	if (arrActionE.Seq) {
		for (var i = 0; i < arrActionE.length; i++) {
			if ((arrActionE[i] == 1 && i > (Bl.A1-1)) || (arrActionE[i] == 0 && i < (Bl.A1-1))) {
				for (var j = 0; j < arrBlock.length; j++) {
					if (arrBlock[j].A2 == 5) {
						arrBlock[j].Hide(1);
						arrBlock[j].Sc = 0;
						arrBlock[j].Hit = 0;
						if (Game.Sound && !arrAction.SndPlayed) {
							PlaySound("S10");
							arrAction.SndPlayed = true;
						}	
						arrActionE.Seq = false;
					}
				}
			}
		}
	}
  if (Bl.A <= 1) {
		arrActionE[Bl.A1-1] = 1;
		if (arrActionE.Seq)	{
			for (var j = 0; j < arrBlock.length; j++) {
				if (arrBlock[j].A2 == 5) {
					if (Ie) document.getElementById(arrBlock[j].Id).style.filter = "filter: alpha(opacity=" + document.getElementById(arrBlock[j].Id).style.filter + 20 + ")";
					else document.getElementById(arrBlock[j].Id).style.MozOpacity = Number(document.getElementById(arrBlock[j].Id).style.MozOpacity) + 0.2;
				}
			}
		}
	}
	else {
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
		if (Bl.A2 == 5) {
  	  var Score = Number(document.getElementById("Score").firstChild.nodeValue);
    	Level.Score += 50;
	    SetScore(Level.Score);
		}
  }
}

function BlockMissed(ThisB) {
	if (Game.Sound && !arrAction.SndPlayed) {
		PlaySound("S10");
		arrAction.SndPlayed = true;
	}		
  for (var j = 0; j < arrBlock.length; j++) {
		if (arrBlock[j].A2 == 5) {
      arrBlock[j].Hide(1);
      arrBlock[j].Sc = 0;
      arrBlock[j].Hit = 0;
    }
  }
}

arrMap = [
0,0,0,0,0,0,0,0,0,0,
0,0,0,3,6,6,3,0,0,0,
0,0,"E4",6,0,0,6,"E3",0,0,
0,3,6,0,"C","C",0,6,3,0,
0,3,6,0,"C","C",0,6,3,0,
0,0,"E1",6,0,0,6,"E2",0,0,
0,0,0,3,6,6,3,0,0,0,0
];

Level.MapLoaded = true;
