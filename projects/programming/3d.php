<?php
require_once __DIR__.'/../../scripts/php/inc_script.php';
$sideNav->arrItem[1]->setActive();
?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title><?php echo $web->pageTitle; ?>: 3D(HTML) cube in pure JavaScript</title>
    <?php require_once __DIR__.'/../../layout/inc_head.php' ?>
    <script type="text/javascript">
      var remote = null;

      function openWin(url, title, x, y) {
        if (remote && remote.open && !remote.closed) {
          remote.close();
        }
        remote = window.open(url, title, 'width=' + x + ',height=' + y + ',toolbar=no,menubar=no,location=no,scrollbars=no,resizable=yes');
        return false;
      }

      /********************************************
       *  Created by Simon Speich, www.speich.net *
       *  v1.0, 02.12.2003                                                *
       *  lastChange v1.0.1 24.10.2009                        *
       *                                                            *
       /********************************************/
      var Play = false;
      var Ie = document.all ? 1 : 0;
      var Q = new Array();
      var DirX = 1;
      var DirY = 1;
      var Origin = new Object();
      Origin.V = [150, 150, 20, 1];

      var DisplArea = new Object();

      // camera vector
      var C = new Array();

      // transformation matrix
      var MTrans = [
        [1, 0, 0, 0],
        [0, 1, 0, 0],
        [0, 0, 1, 0],
        [0, 0, 0, 1]
      ];

      // position information of qube
      var MQube = [
        [1, 0, 0, 0],
        [0, 1, 0, 0],
        [0, 0, 1, 0],
        [0, 0, 0, 1]
      ];

      var I = [
        [1, 0, 0, 0],
        [0, 1, 0, 0],
        [0, 0, 1, 0],
        [0, 0, 0, 1]
      ];

      function DrawLine(From, To) {
        var x1 = From.V[0];
        var x2 = To.V[0];
        var y1 = From.V[1];
        var y2 = To.V[1];
        var dx = Math.abs(x2 - x1);
        var dy = Math.abs(y2 - y1);
        var x = x1;
        var y = y1;
        var IncX1, IncY1;
        var IncX2, IncY2;
        var Den;
        var Num;
        var NumAdd;
        var NumPix;

        if (x2 >= x1) {
          IncX1 = 1;
          IncX2 = 1;
        } else {
          IncX1 = -1;
          IncX2 = -1;
        }
        if (y2 >= y1) {
          IncY1 = 1;
          IncY2 = 1;
        } else {
          IncY1 = -1;
          IncY2 = -1;
        }
        if (dx >= dy) {
          IncX1 = 0;
          IncY2 = 0;
          Den = dx;
          Num = dx / 2;
          NumAdd = dy;
          NumPix = dx;
        } else {
          IncX2 = 0;
          IncY1 = 0;
          Den = dy;
          Num = dy / 2;
          NumAdd = dx;
          NumPix = dy;
        }

        NumPix = Math.round(Q.lastPx + NumPix);
//	CurPix = Math.round(CurPix);
        var El = document.getElementById('viewArea').getElementsByTagName('DIV');
        var i = Q.lastPx;
        for (; i < NumPix; i++) {
          El[i].style.top = y + 'px';
          El[i].style.left = x + 'px';
          Num += NumAdd;
          if (Num >= Den) {
            Num -= Den;
            x += IncX1;
            y += IncY1;
          }
          x += IncX2;
          y += IncY2;
        }
        Q.lastPx = NumPix;
      }

      function RemoveLinePx() {
// not working correcly yet
        var i = Q.lastPx;
        var El = document.getElementById('viewArea').getElementsByTagName('DIV');
        var i = El.length - 1;
        for (; i >= Q.lastPx; i--) El[i].style.visibility = 'hidden';
      }

      function CalcCross(V0, V1) {
        var Cross = new Array();
        Cross[0] = V0[1] * V1[2] - V0[2] * V1[1];
        Cross[1] = V0[2] * V1[0] - V0[0] * V1[2];
        Cross[2] = V0[0] * V1[1] - V0[1] * V1[0];
        return Cross;
      }

      function CalcNormal(V0, V1, V2) {
        var A = new Array();
        var B = new Array();
        for (var i = 0; i < 3; i++) {
          A[i] = V0[i] - V1[i];
          B[i] = V2[i] - V1[i];
        }
        A = CalcCross(A, B);
        var Length = Math.sqrt(A[0] * A[0] + A[1] * A[1] + A[2] * A[2]);
        for (var i = 0; i < 3; i++) A[i] = A[i] / Length;
        A[3] = 1;
        return A;
      }

      function CreateP(X, Y, Z) {
        El = document.createElement('div');
        El.style.top = Y + 'px';
        El.style.left = X + 'px';
        El.style.visibility = 'visible';
        El.style.height = '1px';
        El.style.width = '1px';
        El.style.margin = '0px';
        El.style.padding = '0px';
        El.style.position = 'absolute';
        El.style.border = '0px';
        El.style.backgroundColor = '#000000';
        if (Ie) El.style.overflow = 'hidden'; // needed for ie div size 1px
        document.getElementById('viewArea').appendChild(El);
        this.V = [X, Y, Z, 1];
      }

      // mulitplies two matrices
      function MMulti(M1, M2) {
        var M = [[], [], [], []];
        var i = 0;
        var j = 0;
        for (; i < 4; i++) {
          j = 0;
          for (; j < 4; j++) M[i][j] = M1[i][0] * M2[0][j] + M1[i][1] * M2[1][j] + M1[i][2] * M2[2][j] + M1[i][3] * M2[3][j];
        }
        return M;
      }

      //multiplies matrix with vector
      function VMulti(M, V) {
        var Vect = new Array();
        var i = 0;
        for (; i < 4; i++) Vect[i] = M[i][0] * V[0] + M[i][1] * V[1] + M[i][2] * V[2] + M[i][3] * V[3];
        return Vect;
      }

      function VMulti2(M, V) {
        var Vect = new Array();
        var i = 0;
        for (; i < 3; i++) Vect[i] = M[i][0] * V[0] + M[i][1] * V[1] + M[i][2] * V[2];
        return Vect;
      }

      // add to matrices
      function MAdd(M1, M2) {
        var M = [[], [], [], []];
        var i = 0;
        var j = 0;
        for (; i < 4; i++) {
          j = 0;
          for (; j < 4; j++) M[i][j] = M1[i][j] + M2[i][j];
        }
        return M;
      }

      function Translate(M, Dx, Dy, Dz) {
        var T = [
          [1, 0, 0, Dx],
          [0, 1, 0, Dy],
          [0, 0, 1, Dz],
          [0, 0, 0, 1]
        ];
        return MMulti(T, M);
      }

      function RotateX(M, Phi) {
        var a = Phi;
        a *= Math.PI / 180;
        var Cos = Math.cos(a);
        var Sin = Math.sin(a);
        var R = [
          [1, 0, 0, 0],
          [0, Cos, -Sin, 0],
          [0, Sin, Cos, 0],
          [0, 0, 0, 1]
        ];
        return MMulti(R, M);
      }

      function RotateY(M, Phi) {
        var a = Phi;
        a *= Math.PI / 180;
        var Cos = Math.cos(a);
        var Sin = Math.sin(a);
        var R = [
          [Cos, 0, Sin, 0],
          [0, 1, 0, 0],
          [-Sin, 0, Cos, 0],
          [0, 0, 0, 1]
        ];
        return MMulti(R, M);
      }

      function RotateZ(M, Phi) {
        var a = Phi;
        a *= Math.PI / 180;
        var Cos = Math.cos(a);
        var Sin = Math.sin(a);
        var R = [
          [Cos, -Sin, 0, 0],
          [Sin, Cos, 0, 0],
          [0, 0, 1, 0],
          [0, 0, 0, 1]
        ];
        return MMulti(R, M);
      }

      function DrawQube() {
        // calc current normals
        var CurN = new Array();
        var i = 5;
        Q.lastPx = 0;
        for (; i > -1; i--) CurN[i] = VMulti2(MQube, Q.Normal[i]);

        if (CurN[0][2] < 0) {
          if (!Q.Line[0]) {
            DrawLine(Q[0], Q[1]);
            Q.Line[0] = true;
          }
          if (!Q.Line[1]) {
            DrawLine(Q[1], Q[2]);
            Q.Line[1] = true;
          }
          if (!Q.Line[2]) {
            DrawLine(Q[2], Q[3]);
            Q.Line[2] = true;
          }
          if (!Q.Line[3]) {
            DrawLine(Q[3], Q[0]);
            Q.Line[3] = true;
          }
        }
        if (CurN[1][2] < 0) {
          if (!Q.Line[2]) {
            DrawLine(Q[3], Q[2]);
            Q.Line[2] = true;
          }
          if (!Q.Line[9]) {
            DrawLine(Q[2], Q[6]);
            Q.Line[9] = true;
          }
          if (!Q.Line[6]) {
            DrawLine(Q[6], Q[7]);
            Q.Line[6] = true;
          }
          if (!Q.Line[10]) {
            DrawLine(Q[7], Q[3]);
            Q.Line[10] = true;
          }
        }
        if (CurN[2][2] < 0) {
          if (!Q.Line[4]) {
            DrawLine(Q[4], Q[5]);
            Q.Line[4] = true;
          }
          if (!Q.Line[5]) {
            DrawLine(Q[5], Q[6]);
            Q.Line[5] = true;
          }
          if (!Q.Line[6]) {
            DrawLine(Q[6], Q[7]);
            Q.Line[6] = true;
          }
          if (!Q.Line[7]) {
            DrawLine(Q[7], Q[4]);
            Q.Line[7] = true;
          }
        }
        if (CurN[3][2] < 0) {
          if (!Q.Line[4]) {
            DrawLine(Q[4], Q[5]);
            Q.Line[4] = true;
          }
          if (!Q.Line[8]) {
            DrawLine(Q[5], Q[1]);
            Q.Line[8] = true;
          }
          if (!Q.Line[0]) {
            DrawLine(Q[1], Q[0]);
            Q.Line[0] = true;
          }
          if (!Q.Line[11]) {
            DrawLine(Q[0], Q[4]);
            Q.Line[11] = true;
          }
        }
        if (CurN[4][2] < 0) {
          if (!Q.Line[11]) {
            DrawLine(Q[4], Q[0]);
            Q.Line[11] = true;
          }
          if (!Q.Line[3]) {
            DrawLine(Q[0], Q[3]);
            Q.Line[3] = true;
          }
          if (!Q.Line[10]) {
            DrawLine(Q[3], Q[7]);
            Q.Line[10] = true;
          }
          if (!Q.Line[7]) {
            DrawLine(Q[7], Q[4]);
            Q.Line[7] = true;
          }
        }
        if (CurN[5][2] < 0) {
          if (!Q.Line[8]) {
            DrawLine(Q[1], Q[5]);
            Q.Line[8] = true;
          }
          if (!Q.Line[5]) {
            DrawLine(Q[5], Q[6]);
            Q.Line[5] = true;
          }
          if (!Q.Line[9]) {
            DrawLine(Q[6], Q[2]);
            Q.Line[9] = true;
          }
          if (!Q.Line[1]) {
            DrawLine(Q[2], Q[1]);
            Q.Line[1] = true;
          }
        }
        Q.Line = [false, false, false, false, false, false, false, false, false, false, false, false];
        Q.lastPx = 0;
      }

      function GetMousePos(e) {
        var W = DisplArea.Width / 2;
        var H = DisplArea.Height / 2;
        var El = document.getElementById('viewArea');
        var L = findPos(El)[0];
        var T = findPos(El)[1];
        if (Ie) {
          var X = event.clientX - L;
          var Y = event.clientY - T;
        } else {
          var X = e.clientX - L;
          var Y = e.clientY - T;
        }
        if (X > 0 && X < DisplArea.Width) X = X - W;
        else X = 0;
        if (Y > 0 && Y < DisplArea.Height) Y = Y - H;
        else Y = 0;
        C[1] = Y / 12;
        C[0] = -X / 12;
      }

      function Loop() {
        if (Play) {
          var El = document.getElementById('viewArea').getElementsByTagName('DIV');
          MTrans = Translate(I, -Q[8].V[0], -Q[8].V[1], -Q[8].V[2]);
          MTrans = RotateX(MTrans, C[1]);
          MTrans = RotateY(MTrans, C[0]);
          C[2] = (Q[8].V[0] - DisplArea.Width / 2 - C[0] * 12 * -1) / 10 * -1;
          C[3] = (Q[8].V[1] - DisplArea.Height / 2 - C[1] * 12) / 10 * -1;
          MTrans = Translate(MTrans, C[2], C[3], 0);
          MTrans = Translate(MTrans, Q[8].V[0], Q[8].V[1], Q[8].V[2]);
          MQube = MMulti(MTrans, MQube);
          var i = 8;
          for (; i > -1; i--) {
            Q[i].V = VMulti(MTrans, Q[i].V);
            El[i].style.left = Math.round(Q[i].V[0]) + 'px';
            El[i].style.top = Math.round(Q[i].V[1]) + 'px';
          }
          DrawQube();
        }
        setTimeout('Loop()', 0);
      }

      function Start() {
        if (Ie) document.attachEvent('onmousemove', GetMousePos);
        else document.addEventListener('mousemove', GetMousePos, true);
        // create qube
        Q[0] = new CreateP(-20, -20, 20);
        Q[1] = new CreateP(-20, 20, 20);
        Q[2] = new CreateP(20, 20, 20);
        Q[3] = new CreateP(20, -20, 20);
        Q[4] = new CreateP(-20, -20, -20);
        Q[5] = new CreateP(-20, 20, -20);
        Q[6] = new CreateP(20, 20, -20);
        Q[7] = new CreateP(20, -20, -20);
        // center of gravity
        Q[8] = new CreateP(0, 0, 0);
        // anti-clockwise edge check
        Q.Edge = [[0, 1, 2], [3, 2, 6], [7, 6, 5], [4, 5, 1], [4, 0, 3], [1, 5, 6]];
        // calculate squad normals
        Q.Normal = new Array();
        for (var i = 0; i < Q.Edge.length; i++) Q.Normal[i] = CalcNormal(Q[Q.Edge[i][0]].V, Q[Q.Edge[i][1]].V, Q[Q.Edge[i][2]].V);
        // line drawn ?
        Q.Line = [false, false, false, false, false, false, false, false, false, false, false, false];
        // create line pixels
        Q.NumPx = 360;
        for (var i = 0; i < Q.NumPx; i++) CreateP(0, 0, 0);

        MTrans = Translate(MTrans, Origin.V[0], Origin.V[1], Origin.V[2]);
        MQube = MMulti(MTrans, MQube);

        var El = document.getElementById('viewArea').getElementsByTagName('DIV');
        var i = 0;
        for (; i < 9; i++) {
          Q[i].V = VMulti(MTrans, Q[i].V);
          El[i].style.left = Math.round(Q[i].V[0]) + 'px';
          El[i].style.top = Math.round(Q[i].V[1]) + 'px';
        }
        DrawQube();
        Loop();
      }

      function findPos(obj) {
        // code by http://www.quirksmode.org/js/findpos.html
        var curleft = curtop = 0;
        if (obj.offsetParent) {
          do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
          }
          while (obj = obj.offsetParent);
          return [curleft, curtop];
        }
      }

      window.onload = function() {
        DisplArea.Width = window.parseInt(document.getElementById('viewArea').style.width);
        DisplArea.Height = window.parseInt(document.getElementById('viewArea').style.height);
      };
    </script>
    <style type="text/css">
        #viewAreaCont {
            position: relative;
            width: 300px;
            height: 300px;
        }

        #viewArea {
            position: absolute;
            border: 1px solid black;
            padding: 5px 5px 5px 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
<?php require_once __DIR__.'/../../layout/inc_body_begin.php'; ?>
<h1>3D(HTML) cube in pure JavaScript</h1>
<p>Mozilla testcase can be found <a href="3d.htm"
                                    onclick="return openWin('3d.htm', 'www.speich.net - Mozilla Testcase for Bug 229391', 330, 560)">here</a>
    (opens in new window).</p>
<div id="viewAreaCont">
    <div id="viewArea" onClick="Play ? Play = false: Play = true; return false;"
         style="top: 0px; left: 0px; width: 300px; height: 300px; /* has to be set inline */">
        Click me to start/stop.
    </div>
</div>
<script language="JavaScript" type="text/JavaScript">Start();</script>
<?php echo $bodyEnd->render(); ?>
</body>
</html>