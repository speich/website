<?php require_once '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $web->getWindowTitle(); ?></title>
<?php require_once '../layout/inc_head.php' ?>
<meta name="keywords" content="visualization, jsviz, javascript, svg">

<!--
	JSViz Force Directed Layout: Random Circuit
	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at
	http://www.apache.org/licenses/LICENSE-2.0
	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.

	Author: Kyle Scholz      http://kylescholz.com/
	Copyright: 2006-2007
	-->
<script language="JavaScript" src="../library/jsviz/0.3.3/physics/ParticleModel.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/physics/Magnet.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/physics/Spring.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/physics/Particle.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/physics/RungeKuttaIntegrator.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/layout/graph/ForceDirectedLayout.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/layout/view/SVGGraphView.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/util/Timer.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/util/EventHandler.js"></script>
<script language="JavaScript" src="../library/jsviz/0.3.3/io/DataGraph.js"></script>
<script type="text/javascript">
var d = document;

// adjacency list before reducing it with PHP for jsviz.
var AdjList 	 = [[2,5,10],[2,3,6,7,8,9],[0,4],[1,9],[2,5],[0,4],[1],[1],[1],[1,3],[0]];
// edge direction list
// 1 = pointing towards node, 2 = away from node, 3 both ways
var AdjListDir = [[2,2,3]	,[2,2,1,1,1,1],[2,1],[2,1],[2,1],[2,2],[1],[1],[1],[1,2],[3]];

// after shorten for jsviz
// node list with attibute
var Nodes =		[['BART','250;4',1],['BART','350;4',1],['BART','250350;4',1],['HBART','350;90',1],['HBART','250350;90',1],['LBHNDH','250350;96',1],['BANR','LFI;BA',2],['BART','LFI;BA',2],['CLNR','LFI;BA',2],['INVNR','LFI;BA',2],['NEU','10;10',1]];
var AdjList 	 = [[2,5,10],[2,3,6,7,8,9],[4],[9],[5],[],[],[],[],[],[]];
var AdjListDir = [[2,2,3], [2,2,1,1,1,1],[2],[2],[2],[],[],[],[],[],[]];
var Graph = null;
var GraphLimitToDir = false;	// you can limit the graph only to nodes connected in a specific direction

// preload button images
var Butt1 = new Image();
Butt1.src = '../images/IconHideText_active.gif';
var Butt2 = new Image();
Butt2.src = '../images/IconLimit1_active.gif';
var Butt3 = new Image();
Butt3.src = '../images/IconLimit2_active.gif';

/**
 * Creates a directed graph of variable dependencies.
 * 
 * @param {Object} InvNr inventory number of root node
 * @param {Object} VarNr variable number of root node
 */
function RenderGraph(InvNr, VarNr) {
	var SVGNS = "http://www.w3.org/2000/svg";
	Graph = new ForceDirectedLayout(d.getElementById('DivTree'), true);
	Graph.UseToolTips = true;	// if # of nodes > 50 use tooltips instead of text to display variable info @ each node.
	Graph.model.ENTROPY_THROTTLE = true;
	Graph.config._default = {
		model: function(dataNode) {
			return { mass: 0.6 };
		},
		view: function(DataNode, ModelNode) {
			var Group = document.createElementNS(SVGNS, "g");
			var Node = document.createElementNS(SVGNS, "circle");
			Node.setAttribute('r', '5');
			if (DataNode.InvNr && DataNode.InvNr == InvNr && DataNode.VarNr == VarNr) {
				Node.setAttribute('r', '10');
				Group.setAttribute('id', 'RootNode');	// color via css
			}
			if (DataNode.Owner) {
				Group.setAttribute('class', 'RawNode');
			}
			else {
				Group.setAttribute('class', 'DepNode');
			}
			Node.addEventListener('mousedown', function() {
				Graph.handleMouseDownEvent(ModelNode.id);
			}, false);
			if (Graph.UseToolTips) {
				Node.addEventListener('mouseover', function(e){
					var OffsetLeft = 0; // offset div main
					var OffsetTop = 0; // offset div main
					var ToolTip = d.getElementById('NodeToolTip');
					ToolTip.style.left = e.clientX  - OffsetLeft + 'px';
					ToolTip.style.top = 10 + e.clientY - OffsetTop + 'px';
					ToolTip.innerHTML = DataNode.Text;
					ToolTip.style.visibility = 'visible';
				}, false);
				Node.addEventListener('mouseout', function() {
					d.getElementById('NodeToolTip').style.visibility = 'hidden';
				}, false);
				Node.addEventListener('dblclick', function() {
					var Text = this.parentNode.getElementsByTagName('text')[0];
					if (Text.style.visibility == 'hidden') {
						Text.style.visibility = 'visible';
					}
					else {
						Text.style.visibility = 'hidden';
					}
				}, false);
			}
			Group.appendChild(Node);
			// node info
			var Node = document.createElementNS(SVGNS, "text");
			var Text = document.createTextNode(DataNode.Text);
			Node.setAttribute('text-anchor', 'middle');
			Node.setAttribute('x', '0px');
			Node.setAttribute('y', '18px');
			if (DataNode.InvNr && DataNode.InvNr == InvNr && DataNode.VarNr == VarNr) {
				Node.setAttribute('y', '28px');
			}
			Node.appendChild(Text);
			Group.appendChild(Node);
			return Group;
		}
	}
  Graph.forces.spring._default = function( nodeA, nodeB, isParentChild ) {
		 // create random edge length for better spacing dependent on number of nodes
		var Len = 80 + Math.floor(Math.random() * 5) * 10;
		return {
			springConstant: 0.1,
			dampingConstant: 0.1,
			restLength: Len
		}
	}
  Graph.forces.magnet = function() {
		return {
			magnetConstant: -5000,
			minimumDistance: 50
		}
	}
	Graph.viewEdgeBuilder = function(DataNodeSrc, DataNodeDest) {
		var FromId = DataNodeSrc.particle.id;
		var ToId = DataNodeDest.particle.id;
		var Color = '#aaaaaa';
		var Width = '1px';
		// find edge direction
		for (var i = 0; i < AdjList[FromId].length; i++) {
			if (AdjList[FromId][i] == ToId) {
				var Dir = AdjListDir[FromId][i];
				if (Dir == 3) {
					Color = '#cc0000';
					Width = '2px';
				}
				break;
			}
		}
		return {
			'stroke': Color,
			'stroke-width': Width,
			'EdgeDirection': Dir
		}
	}
	// Create nodes and edges.
	// Create nodes first, then connect them (set the edges).
	var GraphNodes = [];		
	var Len = Nodes.length;
	var i = 0;
	for (; i < Len; i++) {
		var Node = new DataGraphNode();
		if (Nodes[i][2] == 1) {
			// dependent data is a deduction
			Node.InvNr = Nodes[i][1].split(';')[0];
			Node.VarNr = Nodes[i][1].split(';')[1];
			Node.Text = Nodes[i][0] + ' [' + Node.InvNr + ']';
		}
		else {
			// dependent data is raw data
			Node.Owner = Nodes[i][1].split(';')[0];
			Node.Table = Nodes[i][1].split(';')[1];
			Node.Text = Nodes[i][0] + ' [' + Node.Owner + '.' + Node.Table + ']';
		}
		Graph.newDataGraphNode(Node);
		GraphNodes.push(Node);
	}
	// create neighbours (edges) FromNode-ToNode
	Len = AdjList.length;
	i = 0;
	for (;i < Len; i++) {
		var v = 0;
		var Length = AdjList[i].length; 
		for (; v < Length; v++) {
			var NodeFrom = GraphNodes[i];
			var NodeTo = GraphNodes[AdjList[i][v]];
			Graph.newDataGraphEdge(NodeFrom, NodeTo);
		}
	}
	GraphNodes = null;
	GraphTimer = new Timer(10);
	GraphTimer.subscribe(Graph);
	GraphTimer.start();	
}

/**
 * Toogle the display of node info for all nodes in the graph.
 */
function ToggleNodeInfo() {
	if (!Graph) { return false; }
	var ColText = Graph.view.svg.getElementsByTagName('text');
	var Len = ColText.length;
	var i = 0;
	for (; i < Len; i++) {
		var Text = ColText[i];
		if (Text.style.visibility == 'hidden') {
			d.getElementById('ButtToggleNodeInfo').src = '../images/IconHideText.gif';
			Text.style.visibility = 'visible';
		}
		else {
			d.getElementById('ButtToggleNodeInfo').src = Butt1.src
			Text.style.visibility = 'hidden';
		}
	}
}

/**
 * Set direction limit.
 * 
 * You can limit the graph to nodes connected only in a certain direction.
 * 1 = pointing to node
 * 2 = pointing away from node
 * 3 = pointing both ways
 * @param {integer} Dir
 */
function ToggleGraphDirectionLimit(Dir) {
	// ResetPage();
	if (GraphLimitToDir) {
		if (GraphLimitedDir == Dir) {	// turn off limit of same
			GraphLimitToDir = false;
			d.getElementById('ButtToggleLimit' + Dir).src = 'layout/images/IconLimit' + Dir + '.gif';
		}
		else {
			GraphLimitedDir = Dir;			// limit to other
			d.getElementById('ButtToggleLimit' + Dir).src = 'layout/images/IconLimit' + Dir + '_active.gif';
			if (Dir == 1)  {
				d.getElementById('ButtToggleLimit2').src = 'layout/images/IconLimit2.gif';
			}
			else {
				d.getElementById('ButtToggleLimit1').src = 'layout/images/IconLimit1.gif';
			}					
		}
	}
	else {
		GraphLimitToDir = true;
		GraphLimitedDir = Dir;
		d.getElementById('ButtToggleLimit' + Dir).src = 'layout/images/IconLimit' + Dir + '_active.gif';
	}
	CreateGraph();
}


window.onload = function() {
	d.getElementById('ButtToggleNodeInfo').addEventListener('click', ToggleNodeInfo, false);
//	d.getElementById('ButtToggleLimit1').addEventListener('click', function() { ToggleGraphDirectionLimit(1); }, false);
//	d.getElementById('ButtToggleLimit2').addEventListener('click', function() { ToggleGraphDirectionLimit(2); }, false);
	RenderGraph(350, 4);
}
</script>
<style type="text/css">
#DivCont {
	height: 650px;
}	
	
#DivTree {
	width: 600px; height: 500px;
	border: 1px solid #aaaaaa;
	float:left;
}	

.RawNode {
	fill: #cccc00;
}

.DepNode {
	fill: #1C78A6;
}

#RootNode {
	fill: #ECD2EC;
}

#RootNode > text {
	font-size: 14px;
}

circle {
	cursor: move;
	stroke: #aaaaaa;
	stroke-width: 1px;
}

text {
	font-family: verdana, arial;
	font-size: 11px;
	font-weight: normal;
	
}

#NodeToolTip {
	position: absolute;
	visibility: hidden;
	background-color: #ECD2EC;
	color: black;
	padding: 3px;
	border: 1px solid #aaaaaa;
	font-size: 12px;
}

.ButtToggle {
	cursor: pointer;
}
</style>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Visualisierung mit JsViz</h1>
<p>Beispiel einer Visualisierung von Datenbankvariablen mit <a href="http://www.jsviz.org">JsViz</a>. Ursprünglich entwickelt 
für die Auswertesoftware NAFIDAS des <a href="http://www.lfi.ch">Schweizerischen Landesforstinventars LFI</a>.</p>
<ul class="main">
<li>Knoten des Graphen können mit der Maus durch Klicken und Ziehen verschoben werden.</li>
<li>Doppelklick auf einen Knoten blendet Info aus/ein.</li>
</ul>
<div id="DivTree"></div>
<div style="float: left; width: 22px; margin-left: 3px;">
<img src="images/IconHideText.gif" id="ButtToggleNodeInfo" class="ButtToggle" title="Ableitunginfo ein/aus" alt="Icon toggle node info"/>
<!-- <img src="images/IconLimit1.gif" id="ButtToggleLimit1" class="ButtToggle" title="Limitierung 1 ein/aus" alt="Icon toggle graph limit 1"/> -->
<!-- <img src="images/IconLimit2.gif" id="ButtToggleLimit2" class="ButtToggle" title="Limitierung 2 ein/aus" alt="Icon toggle graph limit 2"/> -->
</div>
<div id="NodeToolTip"></div>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>