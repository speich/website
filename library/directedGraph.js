/**
 * Library to generate a directed graph layout using sugiyama algorithm
 * This file contains four objects.
 * developed by Simon Speich
 * copyright: Simon Speich
 * You can use and change this freely as long as you keep this note.
 */

/**
 * Creates the svg object.
 * args: {
 *    canvas: {String},             // name of HTMLElment to append svg to
 *    gridMeshWidth: {Number},
 *    gridMeshHeight: {Number},
 *    onNodeClick: {Function},      // optional event handler called when clicking node
 *    onNodeOver: {Function},        // optional event handler called when mouse is over node
 *    onNodeOut: {Function},        // optional event handler called when mousing out from node
 *    onNodeDblClick: {Function},   // optional event handler when double clicking node
 *    invert: {Boolean}             // optional: renders the graph inverted
 * }
 * @param {Object} args initializer arguments
 */
var svgRenderer = function(args) {

	var svg = {
		canvas: d.getElementById(args.canvas),
		svg: null,
		svgNs: 'http://www.w3.org/2000/svg',
		gridSize: {
			meshWidth: args.gridMeshWidth,
			meshHeight: args.gridMeshHeight
		},
		invert: false,  // invert graph

		/**
		 * Creates the SVG canvas.
		 * @param {Number} width width of svg canvas
		 * @param {Number} height height of svg canvas
		 * @return {Object}
		 */
		initSVG: function(width, height) {
			this.svg = d.createElementNS(this.svgNs, 'svg');
			this.svg.setAttribute('version', '1.1');
			this.svg.setAttribute('width', width);
			this.svg.setAttribute('height', height);
			this.svg.setAttribute('viewBox', '0 0 ' +	width + ' ' + height);
			//this.svg.setAttribute('transform', 'scale(1, -1)');
			this.defs = d.createElementNS(this.svgNs, 'defs');	// holds markers, such as arrow heads
			this.svg.appendChild(this.defs);
			this.svgRoot = d.createElementNS(this.svgNs, 'g');	// holds all nodes and edges for simple transformation
			//this.svgRoot.setAttribute('transform', 'scale(-1, -1) translate(-300, -300)');
			this.svg.appendChild(this.svgRoot);
			this.canvas.appendChild(this.svg);
			return this.svg;
		},

		/**
		 * Creates a graph node.
		 * @param {Object} node
		 * @return {Object} svgGraphNode
		 */
		createNode: function(node) {
			var self = this;
			var group = d.createElementNS(this.svgNs, 'g');
			group.setAttribute('class', 'node');
			if (node.isRaw === true) {
				cssClassUtil.setClass(group, 'rawNode');
			}
			group.setAttribute('cx', node.lx);
			group.setAttribute('cy', node.ly);

			var circle = null;
			if (node.selected === true) {	// create first because of layer index
				circle = d.createElementNS(this.svgNs, 'circle');
				circle.setAttribute('r', '25');
				circle.setAttribute('class', 'selectedNode');
				group.appendChild(circle);
			}
			circle = d.createElementNS(this.svgNs, 'circle');
			circle.setAttribute('r', '5');
			group.appendChild(circle);

			// create multiline node label
			var text = d.createElementNS(this.svgNs, 'text');
			text.setAttribute('text-anchor', 'middle');
			text.setAttribute('x', '0px');
			var dist = this.invert ? -1 : 1;
			var label = node.label.split(' ');
			var tspan = d.createElementNS(this.svgNs, 'tspan');
			tspan.setAttribute('x', '0px');
			tspan.setAttribute('y', 1.5 * dist + 'em');
			tspan.appendChild(d.createTextNode(label[0]));
			text.appendChild(tspan);
			tspan = d.createElementNS(this.svgNs, 'tspan');
			tspan.setAttribute('class', 'font-size: 0.9em');			
			tspan.setAttribute('x', '0');
			tspan.setAttribute('y', 3 * dist + 'em');
			tspan.appendChild(d.createTextNode(label[1]));
			text.appendChild(tspan);
			// set node events
			if (this.onNodeOver) {
				group.addEventListener('mouseover', function(evt) {
					self.onNodeOver.call(this, evt, node);
				}, false);
			}
			if (this.onNodeOut) {
				group.addEventListener('mouseout', function(evt) {
					self.onNodeOut.call(this, evt, node);
				}, false);
			}
			if (this.onNodeClick) {
				group.addEventListener('click', function(evt) {
					self.onNodeClick.call(this, evt, node);
				}, false);
			}
			if (this.onNodeDblClick) {
				group.addEventListener('dblclick', function(evt) {
					self.onNodeDblClick.call(this, evt, node);
				}, false);
			}
			group.appendChild(text);
			return group;
		},

		/**
		 * Draws the node on the canvas.
		 * @param {Object} node GraphNode
		 * @param {Array} nodes graph node list
		 * @return {Object} GraphNode
		 */
		drawNode: function(node, nodes) {
			var y;
			var shift = 1;
			var x = (shift + Number(node.getAttribute('cx'))) * this.gridSize.meshWidth;
			if (this.invert) {
				y = (Number(node.getAttribute('cy')) * -1 + nodes.length) * this.gridSize.meshHeight;
			}
			else {
				y = (shift + Number(node.getAttribute('cy'))) * this.gridSize.meshHeight;
			}
			node.setAttribute('transform', 'translate(' + x + ',' + y + ')');
			this.svgRoot.appendChild(node);
			return node;
		},

		/**
		 * Draws the grid on the canvas.
		 * Note: After using compact we can not simply calculated
		 * the width from the number of nodes per layer.
		 * The height is calculated from the number of layers.
		 */
		drawGrid: function(width, height) {
			var numVertical = width / this.gridSize.meshWidth + 1;
			var numHorizontal = height / this.gridSize.meshHeight;
			var i = 1;
			for (; i < numHorizontal; i++) {
				this.drawLine(0, i, width, i);
				if (i > 0) {
					var text = d.createElementNS(this.svgNs, 'text');
					var level = this.invert ? (i + 1) * -1 + numHorizontal : i - 1;
					text.setAttribute('text-anchor', 'left');
					text.appendChild(d.createTextNode('level ' + level));
					text.setAttribute('x', 0);
					text.setAttribute('y', i * this.gridSize.meshHeight + 3);
					this.svgRoot.appendChild(text);
				}
			}
			i = 1;
			for (; i < numVertical + 1; i++) {
				this.drawLine(i, 0, i, height);
			}
		},

		/**
		 * Draws a line on the canvas.
		 * @param {Number} x1 x-coord of line start
		 * @param {Number} y1 y-coord of line start
		 * @param {Number} x2 x-coord of line end
		 * @param {Number} y2 y-coord of line end
		 * @return {Object} SVGLine
		 */
		drawLine: function(x1, y1, x2, y2) {
			var width = this.gridSize.meshWidth;
			var height = this.gridSize.meshHeight;
			var line = d.createElementNS(this.svgNs, 'line');
			line.setAttribute('x1', x1 * width);
			line.setAttribute('y1', y1 * height);
			line.setAttribute('x2', x2 * width);
			line.setAttribute('y2', y2 * height);
			this.svgRoot.appendChild(line);
			return line;
		},

		/**
		 * Creates a reusable arrow head.
		 * The arrow head is appended to the SVG defs element as a marker element
		 * and can be reused.
		 * @param {string} id id of marker
		 * @return {Object} SVGMarker
		 */
		createArrowHead: function(id) {
			var marker = document.createElementNS(this.svgNs, 'marker');
			//marker.setAttribute('markerUnits', 'userSpaceOnUse');
			marker.setAttribute('id', id);
			marker.setAttribute('markerWidth', 11);
			marker.setAttribute('markerHeight', 11);
			marker.setAttribute('orient', 'auto');
			// start arrow head where circle ends
			// TODO: find better method to get x
			var p = document.createElementNS(this.svgNs, 'polyline');
			p.setAttribute('points', '0,0 10,5 0,10 1,5');
			var nodeRadius = 10; // TODO: find generic solution
			marker.setAttribute('refX', nodeRadius + Number(marker.getAttribute('markerWidth')));
			marker.setAttribute('refY', marker.getAttribute('markerHeight')/2);
			marker.appendChild(p);
			this.defs.appendChild(marker);
			return marker;
		},

		/**
		 * Draws the edge on the canvas.
		 * The edge is drawn from node1 to node2 with arrow head.
		 * @param {Object} node1 GraphNode
		 * @param {Object} node2 GraphNode
		 * @param {Object} arrow SVGMarker
		 * @param {Array} nodes graph node list
		 * @return {Object} edge
		 */
		drawEdge: function(node1, node2, arrow, nodes) {
			var width = this.gridSize.meshWidth;
			var height = this.gridSize.meshHeight;
			var edge = document.createElementNS(this.svgNs, 'polyline');
			var x1, x2, y1, y2;
			var shift = 1;
			x1 = (shift + node1.lx) * width;
			x2 = (shift + node2.lx) * width;
			if (this.invert) {
         	y1 = (node1.ly * -1 + nodes.length) * height;
				y2 = (node2.ly * -1 + nodes.length) * height;
			}
			else {
				y1 = (shift + node1.ly) * height;
				y2 = (shift + node2.ly) * height;
			}
			edge.setAttribute('class', 'edge');
			edge.setAttribute('points', x1 + ',' + y1 + ' ' + x2 + ',' + y2);
			if (!node2.virt) {
				edge.setAttribute('marker-end', 'url(#' + arrow.id + ')');
			}
			this.svgRoot.appendChild(edge);
			return edge;
		},

		/**
		 * Places the nodes on the canvas.
		 * @param {Array} nodes graph nodes list
		 */
		placeNodes: function(nodes) {
			var i = 0;
			var numRow = nodes.length;
			for (; i < numRow; i++) {
				var j = 0;
				var numCol = nodes[i].length;
				for (; j < numCol; j++) {
					var node = nodes[i][j];
					if (node.virt) {
						continue;
					}
					var svgNode = this.createNode(nodes[i][j]);
					nodes[i][j].svgNode = this.drawNode(svgNode, nodes);	// save back for later reference
				}
			}
		},

		/**
		 * Places the edges on the canvas.
		 * @param {Array} nodes graph node list
		 */
		placeEdges: function(nodes) {
			var i = 0;
			var numRow = nodes.length;
			var arrow = this.createArrowHead('arrow');
			for (; i < numRow; i++) {
				var j = 0;
				var numCol = nodes[i].length;
				for (; j < numCol; j++) {
					var node = nodes[i][j];
					if (!node.trgNodes) {
						continue;
					}
					var z = 0;
					var l = node.trgNodes.length;
					node.svgEdges = [];
					for (; z < l; z++) {
						var x = node.trgNodes[z][0];
						var y = node.trgNodes[z][1];
						var adjNode = nodes[y][x];
						node.svgEdges[z] = this.drawEdge(node, adjNode, arrow, nodes);
					}
				}
			}
		},

		/**
		 * Clear the canvas.
		 */
		clearCanvas: function() {
			svg.canvas.innerHTML = '';
		},

		/**
		 * Render graph as SVG.
		 * @param {Object} graph
		 */
		render: function(graph) {
			var width = (graph.getGraphWidth() + 1) * this.gridSize.meshWidth;
			var height = this.gridSize.meshHeight * (graph.numLayer + 1);
			this.initSVG(width, height);
			this.drawGrid(width, height);
			this.placeEdges(graph.nodes);
			this.placeNodes(graph.nodes);
		}
	};

	// add optional args to svg, e.g. mixin
	for (var name in args) {
		if (!svg[name]) {
			svg[name] = args[name];
		}
	}
	return svg;
};

/**
* Creates the graph layer object and returns it.
*
* Note: The methods expandNodeList(), splitLongEdges() orderLayer(), and updateEdgeIndexes()
* work only with integers. Compacting the graph with compact allows for x values smaller
* than unit size one. Therefore the order of applying these methods is important.
* @return {Object} Layouter
*/
var directedGraphLayout = function(args) {

	return {
		/* NOTES:
		 * Remember that in js arrays are passed by reference and so are objects.
		 * There is a tradeoff between separation of logic and performance:
		 * More separation means looping through the same loops again which reduces performance.
		 */
		numLayer: args.numLayer,
		maxNodesAllLayers: 0,   // maximum number of nodes on all layers
		numPerLayers: [],	      // used as a fast lookup to asign new x coord for virt nodes
		nodes: [],     	      // internal array to hold graph nodes. NOTE: first dim is y, second is x!
		incVirt: args.incVirt,	// increment between nodes when setting layout position. 1/incVirt has to be an integer.
		inc: args.inc,

		/**
		 * Maps the node list to a two dim array the size of the grid.
		 * Nodes are stored as xy vectors of the grid. The grid is
		 * maxNodesAllLayers x numLayer and dynamically extended with virtual nodes
		 * when splitting long edges.
		 * Returns the node list with updated nodes.
		 * @param {Array} nodeList list of nodes
		 */
		initNodeList: function(nodeList) {
			var i = 0;
			var len = nodeList.length;
			for (; i < len; i++) {
				var layer = nodeList[i].layer;
				if (typeof this.nodes[layer] === 'undefined') {
					this.nodes[layer] = [];
				}
				var col = this.nodes[layer].length;
				var node = this.createNode({
					x: col,
					y: layer,
					virt: false,
					label: nodeList[i].label,
					isRaw: nodeList[i].isRaw,
					id: nodeList[i].id,
					selected: nodeList[i].selected					
				});				
				this.nodes[layer][col] = node;	// internal node list for fast lookup in grid
				nodeList[i].node = node;			// needed to be able to update trgNodes further below (addEdges())
				this.numPerLayers[layer] = col + 1;
				// get maximum grid width
				this.maxNodesAllLayers = this.numPerLayers[layer] > this.maxNodesAllLayers ? this.numPerLayers[layer] : this.maxNodesAllLayers;
			}
			i = 0;
			len = this.nodes.length;
			for (; i < len; i++) {
				if (typeof this.nodes[i] === 'undefined') {
					this.nodes[i] = [];
				}
			}
			i = 0;
			len = this.numPerLayers.length;
			for (; i < len; i++) {
				if (typeof this.numPerLayers[i] === 'undefined') {
					this.numPerLayers[i] = 0;
				}
			}
		},

		/**
		 * Adds a node's edges.
		 * Adds a node's neighbours as a list of source and target nodes
		 * @param {Array} nodeList
		 * @param {Array} adjList list
		 */
		addEdges: function(nodeList, adjList) {
			var i = 0;
			var len = adjList.length;
			for (; i < len; i++) {
				if (adjList[i].length > 0) {
					var j = 0;
					var trgNodes = adjList[i];
					var lenJ = trgNodes.length;
					for (; j < lenJ; j++) {
						var target = nodeList[trgNodes[j]].node;
						var tx = target.x;
						var ty = target.y;
						var source = nodeList[i].node;	// get target's source nodes
						var sx = source.x;
						var sy = source.y;
						this.nodes[sy][sx].trgNodes.push([tx, ty]);
						// also add this node to the source list of the edge
						this.nodes[ty][tx].srcNodes.push([sx, sy]);
					}
				}
			}
		},

		/**
		 * Adds a node's weights.
		 * A node's weight is either based on up-neighbours
		 * or down-neighbours median.
		 */
		setNodeWeights: function() {
			var i = 0;
			var len = this.nodes.length;
			for (; i < len; i++) {
				var w = 0;
				var l = this.nodes[i].length;
				for (; w < l; w++) {
					var node = this.nodes[i][w];
					if (node.srcNodes.length > 0) {
						node.wUpper = this.getMedian(node, 'upper');
					}
					if (node.trgNodes.length > 0) {
						node.wLower = this.getMedian(node, 'lower');
					}
				}
			}
		},

		/**
		 * Adds virtual (dummy) nodes and edges to the node list.
		 */
		expandNodeList: function() {
			var i = 0;
			var numRow = this.nodes.length;
			for (; i < numRow; i++) {
				var j = 0;
				var numCol = this.nodes[i].length;
				for (; j < numCol; j++) {
					var node = this.nodes[i][j];
					if (!node.virt && node.trgNodes.length > 0) {
						this.splitLongEdges(node);
					}
				}
			}
			this.setNodeWeights();
		},

		/**
		 * Inserts virtual nodes and edges to create a proper graph.
		 * Edges spanning more than one layer will be split into multiple edges of unit length.
		 * This is done by shortening, e.g decrementing long edges continously until it has unit length.
		 * @param {Object} srcNode source node
		 */
		splitLongEdges: function(srcNode) {
			var w = 0;
			var trgNodes = srcNode.trgNodes;
			var l = trgNodes.length;

			for (; w < l; w++) {
				var lastVirtNode = null;
				var trgNode = {
					x: trgNodes[w][0],
					y: trgNodes[w][1]
				};

				var span = trgNode.y - srcNode.y;
				// skip short edges of unit length
				if (span == 1) {
					continue;
				}
				span--;		// We do not need to crate a new node for the original targetNode,
				while (span > 0) {
					/**** APPEND virtual nodes and edges ***/
					var newNode = this.createVirtualNode(srcNode, trgNode, span);
					if (!lastVirtNode) {
						lastVirtNode = newNode;
					}
					if (typeof this.nodes[newNode.y] === 'undefined') {
						this.nodes[newNode.y] = [newNode];
					}
					else {
						this.nodes[newNode.y].push(newNode);
					}
					// update maximum number of nodes per layer to know width of canvas
					this.numPerLayers[newNode.y]++;
					if (this.numPerLayers[newNode.y] > this.maxNodesAllLayers) {
						this.maxNodesAllLayers++;
					}
					span--;
				}
				// update original targetNode's srcNode to point to first inserted new node
				var origTrgNode = trgNodes[w];
				origTrgNode = this.nodes[origTrgNode[1]][origTrgNode[0]];
				var f = 0;
				var lenF = origTrgNode.srcNodes.length;
				for (; f < lenF; f++) {	// find right srcNode to update
					var node = origTrgNode.srcNodes[f];
					if (node[0] == srcNode.x && node[1] == srcNode.y) {
						origTrgNode.srcNodes[f][0] = lastVirtNode.x;
						origTrgNode.srcNodes[f][1] = lastVirtNode.y;
						break;
					}
				}
				// update srcNode's original targetNode to point to last inserted new node
				// note: do this after updating original target's srcNode, otherwise it is overwritten by newNode
				srcNode.trgNodes[w] = [newNode.x, newNode.y];
			}
		},

		/**
		 *	Creates the node object.
		 * keywordArgs:
		 * 	The keywordArgs parameter should be a simple anonymous object
		 * 	that may contain any of the following:
		 * 	{
		 * 	x: {Number} x-coordinate of unit length
		 * 	y: {Number} y-coordinate of unit length
		 *		lx: {Number} x-coordinate of layout
		 * 	ly: {Number} y-coordinate of layout
		 * 	trgNodes: {Array} list of adjacent target nodes
		 * 	srcNodes: {Array} list of adjacent source nodes
		 * 	wUpper: {Number} weight of target nodes (upper nodes),
		 * 	wLower: {Number} weight of source nodes {lower nodes),
		 *		virt: {Boolean} is node virtual
		 *		label: {String} label to display at node
		 * 	isRaw: {Boolean} is node from raw data or derived data
		 * 	selected: {Boolean} is node selected (base node of graph)
		 *    }
		 * @param {Object} keywordArgs node parameter
		 * @return {Object} node
		 */
		createNode: function(keywordArgs) {
			var node = {
				x: 'x' in keywordArgs ? keywordArgs.x : 0,
				y: 'y' in keywordArgs ? keywordArgs.y : 0,
				lx: 'lx' in keywordArgs ? keywordArgs.lx : ('x' in keywordArgs ? keywordArgs.x : 0),
				ly: 'ly' in keywordArgs ? keywordArgs.ly : ('y' in keywordArgs ? keywordArgs.y : 0),
				trgNodes: 'trgNodes' in keywordArgs ? [keywordArgs.trgNodes] : [],
				srcNodes: 'srcNodes' in keywordArgs ? [keywordArgs.srcNodes] : [],
				wUpper: 'wUpper' in keywordArgs ? keywordArgs.wUpper : null,
				wLower: 'wLower' in keywordArgs ? keywordArgs.wLower : null,
				virt: 'virt' in keywordArgs ? keywordArgs.virt : false,
				label: 'label' in keywordArgs ? keywordArgs.label : false,
				isRaw: 'isRaw' in keywordArgs ? keywordArgs.isRaw : false,
				selected: 'selected' in keywordArgs ? keywordArgs.selected : false
			};
			if ('id' in keywordArgs) {
				node.id = keywordArgs.id;
			}
			return node;
		},

		/**
		 * Creates a new virtual node.
		 * New virtual nodes are appended to the layer, e.g their
		 * x = number of nodes on this layer
		 */
		createVirtualNode: function(srcNode, trgNode, span) {
			// we have 4 cases: target is a virtualNode
			// target is originalTarget
			// source is virtual
			// source is originalSource
			var target, source;
			var curLayer = srcNode.y + span;
			if (curLayer == trgNode.y - 1) {	// second new node has original target node as target
				target = [trgNode.x, trgNode.y];
			}
			else {
				target = [this.numPerLayers[curLayer + 1] - 1, curLayer + 1];
			}
			if (span == 1) { // second to last has original source as source
				source = [srcNode.x, srcNode.y];
			}
			else {
				source = [this.numPerLayers[curLayer - 1], curLayer - 1];
			}
			var x = this.numPerLayers[curLayer];
			var newNode = this.createNode({
				x: x,
				y: curLayer,
				trgNodes: target,
				srcNodes: source,
				virt: true,
				label: null,
				isRaw: false
			});
			return newNode;
		},

		/**
		 * Counts the number of edge crossings of a layer's nodes.
		 * Compares the x-values of a node's edges with all the x-edges
		 * of the following nodes on that layer.
		 * @param {Array} layer layer's nodes
		 * @param {Number} direction
		 * @return {Number} number of crossings
		 */
		countCrossings: function(layer, direction) {
			var propName = direction == 'up' ? 'trgNodes' : 'srcNodes';
			var nodes = this.nodes[layer];
			var numCross = 0;
			var i = 0;
			var lenI = nodes.length;
			 // loop nodes
			for (; i < lenI; i++) {
				var node1 = nodes[i];
				var w = 0;
				var edges = node1[propName];
			var lenW = edges.length;
				// loop node's targets
				for (; w < lenW; w++) {
			  var edge1 = edges[w];
					edge1 = this.nodes[edge1[1]][edge1[0]];
					// compare
					var z = i + 1;
					for (; z < lenI; z++) {
					var node2 = nodes[z];
						var p = 0;
						var lenP = node2[propName].length;
				 for (; p < lenP; p++) {
							var edge2 = node2[propName][p];
							edge2 = this.nodes[edge2[1]][edge2[0]];
							if (edge1.x > edge2.x) {
								numCross++;
							}
						}
					}
				}
			}
			return numCross;
		},

		/**
		 * Minimize number of crossings between layers.
		 * Important: We only manipulate the x and y properties,
		 * when finding right ordering of nodes, the lookup properties
		 * srcNodes and trgNodes are kept unchanged. Otherwise we
		 * would have to do a lot of updating these lists.
		 * numSweep: Up and down counts as one sweep
		 * @param {Number} numRepeat number of sweeps
		 */
		minimizeCrossings: function(numRepeat) {
			/*
			// TODO: take into account two or more nodes on layer having same median
			// TODO: also use countCrossings instead of numRepeat
			var sweepDown = true;
			var sweepUp = true;
			var z= 0;
			while (sweepDown == true || sweepUp == true) {
				var i = 1;	// first layer does not have any upperWeights
				var len = this.nodes.length;
				for (; i < len; i++) {
					sweepDown = this.orderLayer(i, 'down');
				}
				i = len - 2;	// last layer does not have any lowerWeights
				for (; i > -1; i--) {
					sweepUp = this.orderLayer(i, 'up');
				}
				z++;

			}
			console.debug(z);
	*/
			var z = 0;
			for (; z < numRepeat; z++) {
				var i = 1;	// first layer does not have any upperWeights
				var len = this.nodes.length;
				for (; i < len; i++) {
					this.orderLayer(i, 'down');
				}
				i = len - 2;	// last layer does not have any lowerWeights
				for (; i > -1; i--) {
					this.orderLayer(i, 'up');
				}
			}

		},

		/**
		 * Orders nodes per layer based on median weight of incident nodes.
		 * Ordering is only done as long as the reducing in #crossings is > than threshold
		 * @param {Number} layer
		 * @param {String} direction sweep direction up/down
		 * @return {Boolean}
		 */
		orderLayer: function(layer, direction) {
			var nodeOrderRestore = this.nodes[layer].slice(0);
			var numCross1 = this.countCrossings(layer, direction);
			var nodeOrder = this.nodes[layer];	// node ordering has to be done on this.nodes for countCrossings to work
			if (direction == 'up') {
				nodeOrder.sort(this.compareByLowerWeight);
			}
			else {
				nodeOrder.sort(this.compareByUpperWeight);
			}
			var numCross2 = this.countCrossings(layer, direction);
			if (numCross2 < numCross1) {
				// reassign new position (rank)
				var i = 0;
				var len = nodeOrder.length;
				for (; i < len; i++) {
					if (i != nodeOrder[i].x) {	// set new pos only if node position has changed
						var oldX = nodeOrder[i].x;
						nodeOrder[i].x = i;
						nodeOrder[i].lx = i;
						// update edges and weights of rearranged nodes
						this.updateEdges(nodeOrder[i], oldX);
					}
				}
				return true;
			}
			else {
				// update order in nodelist
				this.nodes[layer] = nodeOrderRestore.slice(0);
				return false;
			}
		},

		/**
		 * Updates a node's edges to point to the node's new position.
		 * Since only x positions are changed we only have to update x pos.
		 * @param {Object} node
		 * @param {Number} oldIndex of node
		 */
		updateEdges: function(node, oldIndex) {
			var i = 0, x, y , lenV, v = 0;
			// update node's source nodes
			var nodes = node.srcNodes;
			var len = nodes.length;
			for (; i < len; i++) {
				x = nodes[i][0];
				y = nodes[i][1];
				// find srcNode's target nodes to update
				v = 0;
				var trgNode = this.nodes[y][x];
				var trgNodes = trgNode.trgNodes;
				lenV = trgNodes.length;
				for (; v < lenV; v++) {
					if (trgNodes[v][0] == oldIndex) {
						trgNodes[v][0] = node.x;
						trgNode.wLower = this.getMedian(trgNode, 'lower');
						break;
					}
				}
			}
			// update node's target nodes
			i = 0;
			nodes = node.trgNodes;
			len = nodes.length;
			for (; i < len; i++) {
				x = nodes[i][0];
				y = nodes[i][1];
				// find trgNode's source nodes to update
				v = 0;
				var srcNode = this.nodes[y][x];
				var srcNodes = srcNode.srcNodes;
				lenV = srcNodes.length;
				for (; v < lenV; v++) {
					if (srcNodes[v][0] == oldIndex) {
						srcNodes[v][0] = node.x;
						srcNode.wUpper = this.getMedian(srcNode, 'upper');
						break;
					}
				}
			}
		},

		/**
		 * Calculates the median of the node's edges.
		 * The median is calculated from the x values relative to the
		 * x value of the node. This can either be the nodes on the previous (up)
		 * or next (down) layer.
		 * @param {Object} node
		 * @param {string} type median of lower or upper layer
		 * @return {number} median
		 */
		getMedian: function(node, type) {
			var weights = [];
			var i = 0;
			var nodes = (type == 'upper' ? node.srcNodes : node.trgNodes);
			var len = nodes.length;
			for (; i < len; i++) {
				// do not sort srcNodes or trgNodes directly
				weights[i] = this.nodes[nodes[i][1]][nodes[i][0]].lx;
			}
			weights.sort(function ascend(a, b) {
				return a - b;
			});
			var middle = Math.floor(weights.length / 2);
			if ((weights.length % 2) != 0) {
				return weights[middle];
			}
			else {
				return (weights[middle - 1] + weights[middle]) / 2;
			}
		},

		/**
		 * Align nodes on layer according to barrycenter and priority.
		 */
		setLayoutPosition: function() {
			var w = 0;
			var lenW = 4;
			for (; w < lenW; w++) {
				var i = 0;
				var len = this.nodes.length;
				for (; i < len; i++) {
	//				this.setDegree(i, 'down');
					this.align(i, 'down');
				}
				this.setNodeWeights();

				i = len - 1;
				for (; i > -1; i--) {
	//				this.setDegree(i, 'up');
					this.align(i, 'up');
				}
				// TODO: Improve performance by only updating lower(upper) (e.g layer +- 1) when on a layer
				this.setNodeWeights();
			}
		},

		/**
		 * Defines an order based on degree and edge type.
		 * Virtual nodes get highest degree. Degree is
		 * based on number of edges.
		 */
		setDegree: function(layer, direction) {
			var i = 0;
			var nodes = this.nodes[layer];
			// find highest degree
			var maxDegree = 0;
			var len = nodes.length;
			for (; i < len; i++) {
				var degree;
				if (direction == 'up') {
					degree = nodes[i].trgNodes ? nodes[i].trgNodes.length : 0;
				}
				else {
					degree = nodes[i].srcNodes ? nodes[i].srcNodes.length : 0;
				}
				degree = degree * 2;
				if (maxDegree < degree) {
					maxDegree = degree;
				}
				if (!nodes[i].virt) {
					nodes[i].degree = degree;
				}
			}
			// give virtual nodes higher degree than max degree
			var z = 0;
			maxDegree++;
			for (; z < len; z++) {
				if (nodes[z].virt) {
					nodes[z].degree = maxDegree;
				}
			}
		},

		compareByUpperWeight: function(a, b) {
			if (a.wUpper == null || b.wUpper == null) {
				return 0;
			}
			else {
				return (a.wUpper - b.wUpper);
			}
		},

		compareByLowerWeight: function(a, b) {
			if (a.wLower === null || b.wLower === null) {
				return 0;
			}
			else {
				return (a.wLower - b.wLower);
			}
		},

		/**
		 * Returns some statistics about the graph.
		 * @return {Object}
		 */
		getGraphStat: function() {
			// since graph might have changed in the meantime we
			// have to recalculate this every time
			var stat = {
				nodesTotal: 0,
				nodesDerived: 0,
				nodesRaw: 0,
				edges: 0
			};
			var i = 0;
			var len = this.nodes.length;
			for (; i < len ; i++) {
				var z = 0;
				var lenZ = this.nodes[i].length;
				for (; z < lenZ; z++) {
					var node = this.nodes[i][z];
					if (node.isRaw) {
						stat.nodesRaw++;
					}
					if (!node.virt) {
						var j = 0;
						var lenJ = node.trgNodes.length;
						for (; j < lenJ; j++) {							
							stat.edges++;
						}
						stat.nodesTotal++;
					}                   					
				}
			}
			stat.nodesDerived = stat.nodesTotal - stat.nodesRaw;
			return stat;
		},

		/**
		 * Nodes are aligned according to the up/down barycenter.
		 * They are moved as closely to the barycenter as possible
		 * using the priority list.
		 * @param {Number} layer list of nodes to move
		 * @param {String} direction string
		 *
		 */
		align: function(layer, direction) {
			// move node according to it's upper/lower median
			var numDec = this.getDecimalPlaces(this.incVirt, '.');
			var len = this.nodes[layer].length;
			var i = len - 1;
			for (; i > -1; i--) {
				var node = this.nodes[layer][i];
				var newX = direction == 'up' ? node.wLower : node.wUpper;
				if (node.virt) {
					newX = this.round(newX, numDec);
				}
				else {
					newX = Math.round(newX / this.inc) * this.inc;	// align to inc grid
				}
				if (node.lx < newX) { // nodes can only be moved to the right, because after compacting we are already as far left as possible
					if (i == len - 1) {	// right most node can always move right
						node.lx = newX;
						continue;
					}
					// shift left as long as pos is unoccupied
					// note: when using inc you also have to check for incVirt in between
					var	lx = this.round(node.lx + this.incVirt, numDec);
					while (lx <= newX) {
						if (lx == this.nodes[layer][i + 1].lx) {	// pos already occupied
							break;
						}
						else if (node.virt || lx % this.inc === 0) {
							node.lx = lx;
						}
						lx = this.round(lx + this.incVirt, numDec);
					}
				}
			}
		},

		/**
		 * Compact graph
		 * Compacts graph by moving nodes as far to left as possible.
		 * Graph is assumed to be integers only, changed graph can
		 * have fractions.
		 */
		compact: function() {
			var numDec = this.getDecimalPlaces(this.incVirt, '.');
			var i = 0;
			var len = this.nodes.length;
			for (; i < len; i++) {
				var g = 1;	// first node is always lx == 0
				var lenG = this.nodes[i].length;
				for (; g < lenG; g++) {
					var node = this.nodes[i][g];
					var inc = node.virt ? this.incVirt : this.inc;
					var lx = node.lx - inc;
					if (lx == this.nodes[i][g - 1].lx) {	// quickly check pos if already occupied
						continue;
					}
					// shift left as long as unoccupied
					while (lx > 0 ) {
						if (lx == this.nodes[i][g - 1].lx) {	// pos already occupied
							break;
						}
						else if (node.virt || lx % this.inc === 0) {
							node.lx = lx;
						}
						lx = this.round(lx - this.incVirt, numDec);
					}
				}
			}
			this.setNodeWeights();
		},

		/**
		 * Returns the width of the graph.
		 * @return {Number}
		 */
		getGraphWidth: function() {
			var i = 0, width = 0;
			var len = this.nodes.length;
			for (; i < len; i++) {
				var z = 0;
				var lenZ = this.nodes[i].length;
				for (; z < lenZ; z++) {
					if (this.nodes[i][z].lx > width) {
						width = this.nodes[i][z].lx;
					}
				}
			}
			return width + 1;
		},

		/**
		 * Returns the rounding number.
		 * @param {Number} num number
		 * @param {Number} decimalPlaces number of decimal places
		 */
		round: function(num, decimalPlaces) {
			return Math.round(parseFloat(num) * Math.pow(10, decimalPlaces)) / Math.pow(10, decimalPlaces);
		},

		/**
		 * Returns the number of decimal places.
		 * @param {Number} x number
		 * @param {String} decSeparator decimal separator
		 * @return {Number}
		 */
		getDecimalPlaces: function(x, decSeparator) {
			var str = x.toString();
			if (str.indexOf(decSeparator) > -1){
				return str.length - str.indexOf(decSeparator) - 1;
			}
			else {
				return 0;
			}
	   }
	};
};

/**
 * Utility to search graph nodes.
 */
var pathFinder = function() {
	return {
		/**
		 * Find connected target nodes using a BFS search.
		 * @param {Object} node graph node
		 * @param {Array} nodelist list of graph nodes
		 * @param {Function} fnc callback
		 */
		searchByTargets: function(node, nodelist, fnc) {
			var openNodes = [];
			openNodes.push(node);
			while (openNodes.length > 0) {
				var newNode = openNodes.shift();
				var neighbors = newNode.trgNodes;
				var i = 0;
				var len = neighbors.length;
				// Add each neighbor to the beginning of openNodes
				for (; i < len; i++) {
					var nextNode = nodelist[neighbors[i][1]][neighbors[i][0]];
					fnc.apply(this, [nextNode]);
					openNodes.unshift(nextNode);
				}
			}
		},

		/**
		 * Find connected source nodes using a BFS search.
		 * @param {Object} node graph node
		 * @param {Array} nodelist list of graph nodes
		 * @param {Function} fnc callback
		 */
		searchBySources: function(node, nodelist, fnc) {
			var openNodes = [];
			openNodes.push(node);
			while (openNodes.length > 0) {
				var newNode = openNodes.shift();
				var neighbors = newNode.srcNodes;
				var i = 0;
				var len = neighbors.length;
				// Add each neighbor to the beginning of openNodes
				for (; i < len; i++) {
					var nextNode = nodelist[neighbors[i][1]][neighbors[i][0]];
					fnc.apply(this, [newNode, nextNode]);
					openNodes.unshift(nextNode);
				}
			}
		},

		/**
		 * Highlight all connected edges.
		 * @param {Object} node graph node
		 * @param {Array} nodelist list of graph nodes
		 */
		highlightPath: function(node, nodelist) {
			var highlightSources = function(node, srcNode) {
				var z = 0;
				var lenZ = srcNode.svgEdges.length;
				for (;z < lenZ; z++) {
					var target = srcNode.trgNodes[z];
					if (target[0] == node.x && target[1] == node.y) {	// order of trgNode and svgEdges is same
						var edge = srcNode.svgEdges[z];
						cssClassUtil.hasClass(edge, 'srcEdgeHighlighted') ? cssClassUtil.removeClass(edge, 'srcEdgeHighlighted') : cssClassUtil.setClass(edge, 'srcEdgeHighlighted');
						if (srcNode.svgNode) {
							var el = srcNode.svgNode;
							cssClassUtil.hasClass(el, 'srcNodeHighlighted') ? cssClassUtil.removeClass(el, 'srcNodeHighlighted') : cssClassUtil.setClass(el, 'srcNodeHighlighted');
						}
					}
				}
			};
			var highlightTargets = function(node) {
				var z = 0;
				var lenZ = node.trgNodes.length;
				for (;z < lenZ; z++) {
					var edge = node.svgEdges[z];
					cssClassUtil.hasClass(edge, 'targetEdgeHighlighted') ? cssClassUtil.removeClass(edge, 'targetEdgeHighlighted') : cssClassUtil.setClass(edge, 'targetEdgeHighlighted');
					var el = nodelist[node.trgNodes[z][1]][node.trgNodes[z][0]];
					if (el.virt === false) {
						el = el.svgNode;
						cssClassUtil.hasClass(el, 'trgNodeHighlighted') ? cssClassUtil.removeClass(el, 'trgNodeHighlighted') : cssClassUtil.setClass(el, 'trgNodeHighlighted');
					}
				}
			};
			var el = node.svgNode;  //.getElementsByTagName('circle')[0];
			cssClassUtil.hasClass(el, 'nodeHighlighted') ? cssClassUtil.removeClass(el, 'nodeHighlighted') : cssClassUtil.setClass(el, 'nodeHighlighted');
			el = el.getElementsByTagName('circle')[0];
			el.setAttribute('r', el.getAttribute('r') == 5 ? 8 : 5);
			highlightTargets(node);
			this.searchByTargets(node, nodelist, highlightTargets);
			this.searchBySources(node, nodelist, highlightSources);
		}
	};
};

