function SetAction(ThisIndex) {
  var Bl = arrBlock[ThisIndex];
  switch(arrMap[ThisIndex]) {
    case "A": Bl.A2 = 1; Bl.Img.src = "ba.gif"; break;
    case 5: Bl.Hit = 2; break;
    case 6: Bl.Sc = 0; Bl.Hit = 0; break;
		case "B": Bl.A2 = 3; Bl.Img.src = "bb.gif"; break;
    case "C": Bl.A2 = 2; Bl.Hit.src = 2; Bl.Img.src = "bc.gif"; break;
   	case "D": Bl.A2 = 4; Bl.Img.src = "bd.gif"; break;
  }
}

function Action(ThisB) {
  var CurBlock = arrBlock[ThisB];
  function TestAct(ThisAct) {
    var Act = false;
    for (var i = 0; i < arrAction.length; i++) if (arrAction[i] == ThisAct) Act = true;
    return Act;    
  }
  switch(CurBlock.A2) {
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
			arrBall[arrBall.LastId-1].Hide(0);
			TimerAttach("MoveBall(" + (arrBall.LastId-1) + ")");
      Delay += 700;
			CreateBall();
			arrBall[arrBall.LastId-1].Hide(0);
      window.setTimeout("TimerAttach('MoveBall(" + (arrBall.LastId-1) + ")')", Delay);
			ActionAttach(3);
   		break;
  	case 4:
	    var El = document.getElementById("Racket");
      if (TestAct(1)) {
				PlaySound("S02");
			  ActionDetach(1);
			}
      else if (!TestAct(4)) {
 				PlaySound("S02");
 	    	ActionAttach(4);
   	    Racket.W = 37;
	   		El.innerHTML = '<img src="blockedup/racket_narrow.gif">';
     	  El.style.width = Racket.W + "px";
      }
    	break;
  }
}

function BlockMissed(ThisB) { var NoAction; }

arrMap = [
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,1,1,0,0,0,0,
0,0,0,1,3,3,1,0,0,0,
0,0,1,2,"A","A",2,1,0,0,
1,1,3,3,3,3,3,3,1,1,
2,"B",2,2,2,2,2,2,"B",2,
4,4,3,3,3,3,3,3,4,4,
0,0,4,2,"D","D",2,4,0,0,
0,0,0,4,3,3,4,0,0,0,
0,0,0,0,4,4,0,0,0,0
];

// arrMap = [0,0,0,0,1,1,0,0,0,0];

Level.MapLoaded = true;