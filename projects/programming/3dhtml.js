/********************************************
 *  Created by Simon Speich, www.speich.net *
 *  v1.0.2, 07.03.2008                      *
 *  v1.0.3, 09.10.2013                      *
 *  						                       *
 *  You can use/change this freely as long  *
 *  as you keep this note						  *
 ********************************************/
var Ie = document.all ? 1: 0,
Q = [],
MTrans = [],	// transformation matrix
MCube = [],		// position information of cube
I = [],			// entity matrix
Origin = {},
testing = {},
LoopTimer,
DisplArea = {
	Width: 300,
	Height: 300
};

function DrawLine(From, To) {
	var x1 = From.V[0],
	x2 = To.V[0],
	y1 = From.V[1],
	y2 = To.V[1],
	dx = Math.abs(x2 - x1),
	dy = Math.abs(y2 - y1),
	x = x1,
	y = y1,
	IncX1, IncY1,
	IncX2, IncY2,
	Den, Num,
	NumAdd, NumPix,
	El, i;

	if (x2 >= x1) {
		IncX1 = 1;
		IncX2 = 1;
	}
	else {
		IncX1 = -1;
		IncX2 = -1;
	}
	if (y2 >= y1) {
		IncY1 = 1;
		IncY2 = 1;
	}
	else {
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
	}
	else {
		IncX2 = 0;
		IncY1 = 0;
		Den = dy;
		Num = dy / 2;
		NumAdd = dx;
		NumPix = dy;
	}

	NumPix = Math.round(Q.lastPx + NumPix);

	El = document.getElementById("ViewArea").getElementsByTagName("DIV");
	i = Q.lastPx;
	for (; i < NumPix; i++) {
		El[i].style.top = y + "px";
		El[i].style.left = x + "px";
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

function CalcCross(V0, V1) {
	var Cross = [];
	Cross[0] = V0[1] * V1[2] - V0[2] * V1[1];
	Cross[1] = V0[2] * V1[0] - V0[0] * V1[2];
	Cross[2] = V0[0] * V1[1] - V0[1] * V1[0];
	return Cross;
}

function CalcNormal(V0, V1, V2) {
	var A = [] , B = [], i, Length;

	for (i = 0; i < 3; i++) {
		A[i] = V0[i] - V1[i];
		B[i] = V2[i] - V1[i];
	}
	A = CalcCross(A, B);
	Length = Math.sqrt(A[0] * A[0] + A[1] * A[1] + A[2] * A[2]);
	for (i = 0; i < 3; i++) {
		A[i] = A[i] / Length;
	}
	A[3] = 1;
	return A;
}

function CreateP(X, Y, Z) {
	var El = document.createElement("div");

	El.style.top = Y + "px";
	El.style.left = X + "px";
	El.style.visibility = "visible";
	El.style.height = "1px";
	El.style.width = "1px";
	El.style.margin = "0px";
	El.style.padding = "0px";
	El.style.position = "absolute";
	El.style.border = "0px";
	El.style.backgroundColor = "#000000";
	if (Ie) {
		// needed for ie div size 1px
		El.style.overflow = "hidden";
	}
	document.getElementById("ViewArea").appendChild(El);
	this.V = [X, Y, Z, 1];
}

// mulitplies two matrices
function MMulti(M1, M2) {
	var M = [
		[],
		[],
		[],
		[]
	], i, j;

	for (i = 0; i < 4; i++) {
		for (j = 0; j < 4; j++) {
			M[i][j] = M1[i][0] * M2[0][j] + M1[i][1] * M2[1][j] + M1[i][2] * M2[2][j] + M1[i][3] * M2[3][j];
		}
	}
	return M;
}

//multiplies matrix with vector
function VMulti(M, V) {
	var Vect = [], i;

	for (i = 0; i < 4; i++) {
		Vect[i] = M[i][0] * V[0] + M[i][1] * V[1] + M[i][2] * V[2] + M[i][3] * V[3];
	}
	return Vect;
}

function VMulti2(M, V) {
	var Vect = [], i;

	for (i = 0; i < 3; i++) {
		Vect[i] = M[i][0] * V[0] + M[i][1] * V[1] + M[i][2] * V[2];
	}
	return Vect;
}

// add to matrices
function MAdd(M1, M2) {
	var M = [
		[],
		[],
		[],
		[]
	], i, j;

	for (i = 0; i < 4; i++) {
		for (j = 0; j < 4; j++) {
			M[i][j] = M1[i][j] + M2[i][j];
		}
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
	var Cos, Sin, R, a = Phi;

	a *= Math.PI / 180;
	Cos = Math.cos(a);
	Sin = Math.sin(a);
	R = [
		[1, 0, 0, 0],
		[0, Cos, -Sin, 0],
		[0, Sin, Cos, 0],
		[0, 0, 0, 1]
	];
	return MMulti(R, M);
}

function RotateY(M, Phi) {
	var Cos, Sin, R, a = Phi;

	a *= Math.PI / 180;
	Cos = Math.cos(a);
	Sin = Math.sin(a);
	R = [
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

function DrawCube() {
	// calc current normals
	var CurN = [];
	var i = 5;
	Q.lastPx = 0;
	for (; i > -1; i--) {
		CurN[i] = VMulti2(MCube, Q.Normal[i]);
	}
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

function Loop() {
	if (testing.LoopCount > testing.LoopMax) {
		testing.Duration = new Date() - testing.StartTime;
		DisplResults();
		return;
	}
	if (!testing.StartTime) {
		alert('Drag this outside the testwindow and click ok');
		testing.StartTime = new Date();
	}
	var El, i, StartLoopTime = new Date(),
	TestingStr = String(testing.LoopCount);

	while(TestingStr.length < 3) {
		TestingStr = "0" + TestingStr;
	}
	Message("testing " + TestingStr + " : " + testing.LoopMax, false);
	MTrans = Translate(I, -Q[8].V[0], -Q[8].V[1], -Q[8].V[2]);
	MTrans = RotateX(MTrans, 1);
	MTrans = RotateY(MTrans, 3);
	MTrans = RotateZ(MTrans, 5);
	MTrans = Translate(MTrans, Q[8].V[0], Q[8].V[1], Q[8].V[2]);
	MCube = MMulti(MTrans, MCube);
	El = document.getElementById("ViewArea").getElementsByTagName("DIV");
	for (i = 8; i > -1; i--) {
		Q[i].V = VMulti(MTrans, Q[i].V);
		El[i].style.left = Math.round(Q[i].V[0]) + "px";
		El[i].style.top = Math.round(Q[i].V[1]) + "px";
	}
	DrawCube();
	testing.LoopCount++;
	testing.TimeTemp = new Date() - StartLoopTime;
	if (testing.LoopCount == 1) {
		testing.timeMin = testing.TimeTemp;
	}
	if (testing.TimeTemp > testing.timeMax) {
		testing.timeMax = testing.TimeTemp;
	}
	if (testing.TimeTemp < testing.timeMin && testing.timeMin > 0) {
		testing.timeMin = testing.TimeTemp;
	}
	testing.timeInLoop += testing.TimeTemp;
	LoopTimer = window.setTimeout("Loop()", 0);
}

function Init(CubeSize) {
	if (testing.Init) {
		window.clearTimeout(LoopTimer);
	}

	// remove previous elements
	var i, El = document.getElementById("ViewArea").getElementsByTagName("DIV");

	if (El.length > 0) {
		for (i = 0; i < El.length; i++) {
			document.getElementById("ViewArea").removeChild(El[i]);
		}
		El = document.getElementById("Msg").childNodes;
		while(El.length > 0) {
			document.getElementById("Msg").removeChild(document.getElementById("Msg").firstChild);
		}
	}

	// init/reset vars
	Origin.V = [150, 150, 20, 1];
	testing.LoopCount = 0;
	testing.LoopMax = 50;
	testing.Duration = null;
	testing.StartTime = null;
	testing.timeMax = 0;
	testing.timeAvg = 0;
	testing.timeMin = 0;
	testing.TimeTemp = 0;
	testing.timeInLoop = 0;

	testing.Init = false;

	// transformation matrix
	MTrans = [
		[1, 0, 0, 0],
		[0, 1, 0, 0],
		[0, 0, 1, 0],
		[0, 0, 0, 1]
	];

	// position information of cube
	MCube = [
		[1, 0, 0, 0],
		[0, 1, 0, 0],
		[0, 0, 1, 0],
		[0, 0, 0, 1]
	];

	// entity matrix
	I = [
		[1, 0, 0, 0],
		[0, 1, 0, 0],
		[0, 0, 1, 0],
		[0, 0, 0, 1]
	];

	// create cube
	Q[0] = new CreateP(-CubeSize, -CubeSize, CubeSize);
	Q[1] = new CreateP(-CubeSize, CubeSize, CubeSize);
	Q[2] = new CreateP(CubeSize, CubeSize, CubeSize);
	Q[3] = new CreateP(CubeSize, -CubeSize, CubeSize);
	Q[4] = new CreateP(-CubeSize, -CubeSize, -CubeSize);
	Q[5] = new CreateP(-CubeSize, CubeSize, -CubeSize);
	Q[6] = new CreateP(CubeSize, CubeSize, -CubeSize);
	Q[7] = new CreateP(CubeSize, -CubeSize, -CubeSize);

	// center of gravity
	Q[8] = new CreateP(0, 0, 0);

	// anti-clockwise edge check
	Q.Edge = [
		[0, 1, 2],
		[3, 2, 6],
		[7, 6, 5],
		[4, 5, 1],
		[4, 0, 3],
		[1, 5, 6]
	];

	// calculate squad normals
	Q.Normal = [];
	for (i = 0; i < Q.Edge.length; i++) {
		Q.Normal[i] = CalcNormal(Q[Q.Edge[i][0]].V, Q[Q.Edge[i][1]].V, Q[Q.Edge[i][2]].V);
	}

	// line drawn ?
	Q.Line = [false, false, false, false, false, false, false, false, false, false, false, false];

	// create line pixels
	Q.NumPx = 9 * 2 * CubeSize;
	for (i = 0; i < Q.NumPx; i++) {
		new CreateP(0, 0, 0);
	}

	MTrans = Translate(MTrans, Origin.V[0], Origin.V[1], Origin.V[2]);
	MCube = MMulti(MTrans, MCube);

	El = document.getElementById("ViewArea").getElementsByTagName("DIV");
	for (i = 0; i < 9; i++) {
		Q[i].V = VMulti(MTrans, Q[i].V);
		El[i].style.left = Math.round(Q[i].V[0]) + "px";
		El[i].style.top = Math.round(Q[i].V[1]) + "px";
	}
	DrawCube();
	testing.Init = true;
	Loop();
}

function DisplResults() {
	testing.timeAvg = Math.round(testing.timeInLoop / testing.LoopMax * 100) / 100;
	if (/\./.test(testing.timeAvg)) {
		(/\.[1-9]\b/.test(testing.timeAvg) ? testing.timeAvg = testing.timeAvg + "0": "");
	}
	else {
		testing.timeAvg = testing.timeAvg + ".00";
	}
	Message('Elapsed time: ' + FormatNumber(testing.Duration) + " ms", false);
	Message('Time in Loop: ' + FormatNumber(testing.timeInLoop) + " ms", true);
	Message('Average per loop: ' + FormatNumber(testing.timeAvg) + ' ms', true);
	Message('Fastest loop: ' + testing.timeMin + ' ms', true);
	Message('Slowest loop: ' + testing.timeMax + ' ms', true);
	testing.Init = false;
}

function FormatNumber(ThisNum) {
	var Separator = "'";
	var Int = String(ThisNum);
	var RegExpr = new RegExp("\\B(\\d{3})(" + Separator + "|$)");
	do {
		Int = Int.replace(RegExpr, Separator + "$1");
	}
	while(Int.search(RegExpr) >= 0);
	return Int;
}

function Message(Str, Add) {
	var Msg = document.getElementById("Msg");
	if (Msg.childNodes.length == 0 || Add) {
		if (Add) {
			Msg.appendChild(document.createElement("br"));
		}
		Msg.appendChild(document.createTextNode(Str));
	}
	else {
		Msg.firstChild.data = Str;
	}
}