DisplStartUp("initializing objects...");

function SetupRacket() {
  Racket = {
    H: 15, W: 75,
    X: 170 + Board.X, Y: 445 + Board.Y,
    V: 1, Speed: 31,
		Img1: new Image(),
		Img2: new Image(),
		Img3: new Image(),
		Img1: {src: "blockedup/racket.gif"},
		Img2: {src: "blockedup/racket_narrow.gif"},
		Img3: {src: "blockedup/racket_wide.gif"},
	  Style: ""
	}
	Racket.Style = "top:" + Racket.Y + "px; left:" + Racket.X + "px; width:" + Racket.W + "px; height:" + Racket.H + "px;" + "visibility:hidden"
  if (Ie) document.getElementById("Racket").style.cssText = Racket.Style;
  else document.getElementById("Racket").setAttribute("style", Racket.Style);
	document.getElementById("Racket").appendChild(document.createElement("img")).src = Racket.Img1.src;
}

/**
 * Sets block properties and actions based on loaded map.
 */
function SetupBlocks() {
  arrBlock = [];
 	for (var i = 0; i < arrMap.length; i++) {
    if (arrMap[i] == 0) arrBlock[i] = 0;
    else {
     	arrBlock[i] = {
       	Id: "Bl" + i,
        X: i%10 * 40 + Board.X,
        Y: Board.Y + 1+ Math.floor(i/10)*20,
        W: 40, H: 20,
        V: 1, Hit: 1, // visibility, number of needed hits
 				Hbl: 1,  		// hitable
        Sc: 1,   		// score if hit
        A1: 0,    	// immediate Action to perform
        A2: 0,   		// Action after moved block caught
				A: 0,				// which action (A1 or A2) is taking place)
				Col: 0,  		// colliding
				Speed:5,		// speed when moving down
        Hide: HideEl,
        Img: new Image(),
				Img: { src:	"b" + String(arrMap[i]).toLowerCase() + ".gif" },
				Style: "",
      	Html: ""
      };
			arrBlock[i].Html = '<img src="blockedup/' + arrBlock[i].Img.src + '">'
			arrBlock[i].Style = "left:" + arrBlock[i].X + "px; top:" + arrBlock[i].Y + "px;",
      SetAction(i);
    }
  }
}

function CreateEl(ThisEl, ThisId, ThisStyle, ThisHtml, ThisAppendTo) {
	var El = document.createElement(ThisEl);
	El.setAttribute("id", ThisId);
  if (Ie) El.style.cssText = ThisStyle; 
  else El.setAttribute("style", ThisStyle);
	El.innerHTML = ThisHtml;
  ThisAppendTo.appendChild(El);
}

function RemoveEl(ThisId, ParentEl) {
	document.getElementById(ParentEl).removeChild(document.getElementById(ThisId));
}

function HideEl(Vis) {
	this.V = (Vis == 1 ? 0 : 1);
  document.getElementById(this.Id).style.visibility = (Vis == 1 ? "hidden" : "visible");
}

DisplStartUp("objects initialized");
