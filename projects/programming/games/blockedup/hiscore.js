//  **** yes I know its easy to cheat, but instead help to improve the game or send level ****
DisplStartUp("loading highscores...");

var objXml;
try { objXml = Ie ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest() }
catch(e) { objXml = false; }

if (objXml) DisplStartUp("XmlHttp detected");
function CheckHighscore() {
  if (objXml) {
    // update Highscore first, maybe new Highscore by other user
    objXml.open("GET","blockedup/sethiscore.php", false);
    objXml.send(null);
    arrHighscore = objXml.responseText;
    arrHighscore = arrHighscore.replace(/array\(/g,"[");
    arrHighscore = arrHighscore.replace(/"\)/g,"\"]");
    arrHighscore = eval("arrHighscore = [" + arrHighscore + "];");
    ClearTblHighscore();
    CreateTblHighscore();
    for (var i = 0; i < arrHighscore.length; i++) {
      if (Level.Score >= arrHighscore[i][2]) return true;
    }
    return false;
  }
  else alert("Sorry, saving not possible.\n You need XmlHttp installed or update your browser.");
} 

function PostHighscore() {
  var PlayerName = document.getElementById('PlayerName').value;
  if (PlayerName.length > 32) PlayerName = PlayerName.slice(0,32);
  PlayerName = PlayerName.replace(/'/g,"&#039;");
  PlayerName = PlayerName.replace(/\"/g,"&#034;");
  PlayerName = PlayerName.replace(/</g,"&#060;");
  PlayerName = PlayerName.replace(/>/g,"&#062;");    
  if (objXml) {
    var strQuery = "PlayerName=" + PlayerName + "&PlayerScore=" + Level.Score + "&PlayerLevel=" + Level.Level;
    objXml.open("POST","blockedup/sethiscore.php", false);
    objXml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    objXml.send(strQuery);
    arrHighscore = objXml.responseText;
    arrHighscore = arrHighscore.replace(/array\(/g,"[");
    arrHighscore = arrHighscore.replace(/"\)/g,"\"]");
    arrHighscore = eval("arrHighscore = [" + arrHighscore + "];");
    ClearTblHighscore();
    CreateTblHighscore();
  }
  else alert("Sorry, saving not possible.\n You need XmlHttp installed or update your browser.\n *** Mozilla (www.mozilla.org) recommended ***");
}

function ClearTblHighscore() {
  var El = document.getElementById("TblBodyHighscore");
  while (El.childNodes.length > 0) {
    El.removeChild(El.firstChild);
  }
}

function CreateTblHighscore() {
  var Tr, Td, Txt;
  TblBody = document.getElementById("TblBodyHighscore");
  for (var i = 0; i < arrHighscore.length; i++) {
  	Tr = document.createElement("tr");
    for (var j = 0; j < arrHighscore[i].length; j++) {
    	Td = document.createElement("td");
    	Txt = document.createTextNode(arrHighscore[i][j]);
     	Td.appendChild(Txt);
  	  Tr.appendChild(Td);
    }
    TblBody.appendChild(Tr);
  }  
}

DisplStartUp("Highscores loaded");
