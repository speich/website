/**
 * File explorer allows you to browse files.
 * 
 * The file explorer consists of a tree and a grid. The tree loads file
 * data via php from disk.
 *
 * JsonRestStore.save() contains valuable information. *
 * TODO: grid even selected, focus and selected vs selected but not focus
 */
dojo.provide('rfe.FileExplorer');

dojo.require('rfe.Layout');
dojo.require('rfe._Edit');
dojo.require('dojo.date.locale');

dojo.declare('rfe.FileExplorer', [rfe.Layout, rfe._Edit], {

	_curTreeItem: null, 	// keep track of current data item selected in tree
	store: null,
	history: {
		steps: [],        // saves the steps
		curIdx: null,     // index of current history array we're on
		numSteps: 5       // number of steps you can go forward/back
	},

	/**
	 * Creates the file explorer.
	 * The global property object contains the urls to communicate with PHP backend.
	 * @param {object} args 
	 */
	constructor: function(args) {
		var nextId = 1;
		dojo.safeMixin(this, args);

		/*
		var logging = new rfe._Logging(this);
		dojo.connect(this, 'deleteItem', this, function(def) {
			console.debug('item deleted', def)
			def.addCallback(dojo.hitch(this, function() {
				console.debug('item deleted')
				logging.log('item deleted');
			}));
		});
		*/

		// TODO: put all events in separate js file like grid/Events.js
		// init tree events
		dojo.connect(this.tree, 'onLoad', this, function() {
			var children;
			var root = this.tree.rootNode;
			this._curTreeItem = root.item;
			this.edit.lastFocused = root.item;
			this.tree._selectNode(root);
			children = this.store.getValue(root.item,  'dir');
		//	this.grid.setItems(children);
			this.setHistory(root.item.id);
		});
		dojo.connect(this.tree, 'onClick', this, function(item, node) {
			if (item != this._curTreeItem) { // do not execute twice on tree.onclick and tree.onDblclick
				this.showFolderContentInGrid(item);	// only called, when store.openOnClick is set to false
				this.setHistory(this.store.getValue(item, 'id'));
			}
			this._curTreeItem = item;
			this.edit.lastFocused = item;
		});
      dojo.connect(this.tree, 'onKeyUp', this, function(evt) {
			var node = dijit.getEnclosingWidget(evt.target);
			this.showFolderContentInGrid(node.item);
	      this.setHistory(this.store.getValue(node.item, 'id'));
		});
      // init grid events
      dojo.connect(this.grid, 'onClick', this, function(evt) {
			this.edit.lastFocused = this.grid.getItem(evt.rowIndex);
      });
		dojo.connect(this.grid, 'onMouseDown', function(evt) {
			// also select row on right click (for contextMenu)
			if (dojo.mouseButtons.isRight(evt)) {
				this.selection.clickSelectEvent(evt);
				this.edit.lastFocused = this.getItem(evt.rowIndex);
			} 			
		});
		dojo.connect(this.grid, 'onRowDblClick', this, function(evt) {
			var item = this.grid.getItem(evt.rowIndex);
			this.grid.selection.clear();
			this.display(item);
			this.setHistory(this.store.getValue(item, 'id'));
		});

		this.createLayout(this.id);
		this.createContextMenu(dojo.byId(this.id));
	},
	
	/**
	 * Displays folder content in grid.
	 * @param {Object} item dojo data item
	 */
	showFolderContentInGrid: function(item) {
		var store = this.store;
		var grid = this.grid;
		var id = store.getValue(item, 'id');
		if (store.getValue(item, 'dir')) {
			var items = store.getValues(item, this.treeModel.childrenAttrs[0]);
			store.fetch({
			//	query: {id: id + '*'},
				//sort: [{attribute: 'name'}],
				onComplete: function(item, result) {
				//	console.debug(result);
				//	store.updateResultSet(items, result);
				//	grid.sortInfo()
					grid.setItems(items);
				}
			})

		}
	},
	
	/**
	 * Displays folder content in tree.
	 * Returns false if item is not a folder, otherwise returns a dojo.Deferred
	 * @param {Object} item dojo.data.item
	 * @return {Object|Boolean} dojo.Deferred
	 */
	showFolderContentInTree: function(item) {
		var nodes = [];
		var store = this.store;
		var tree = this.tree;
		var def = new dojo.Deferred();
		if (store.getValue(item, 'dir')) {
			var id = store.getValue(item, 'id');
			var path = this.createPath(id);
			def = tree.set('path', path);
		}
		this._curTreeItem = item;
		return def;
	},

	/**
	 * Build and returns the path array from the item id.
	 * @param {String} id resource id of JsonRestStore item
	 * @return {Array}
	 */
	createPath: function(id) {
		var arr = [id];
		while (id.indexOf('/') > 0) {
			id = id.slice(0, id.lastIndexOf('/'));
			arr.unshift(id);
		}
		arr.unshift('/');
		return arr;
	},

	/**
	 * Return id of item's parent
	 * @param {object} item dojo.data.item
	 * @return {string|boolean} id or false
	 */
	getParentId: function(item) {
		// note: ids never start with a slash (except root). If item is a directory
		// 
		var parentId = this.store.getValue(item, 'id');
		if (parentId == '/') {
			return false;
		}
		parentId = parentId.split('/');
		if (parentId.length > 1) {
			parentId.pop();
			return parentId.join('/');			
		}
		else {
			return '/';
		}
	},

   /**
    * Display parent directory.
    * @param {Object} [item] dojo.data.item
    */
	goDirUp: function(item) {
		var def = new dojo.Deferred();
		if (!item) {
			item = this._curTreeItem;
		}
		if (item.root) {
			def.resolve(true);
			return def;
		}
		var parentId = this.getParentId(item);
		if (parentId != '/') {
			var args = {
				identity: parentId,
				onItem: dojo.hitch(this, function(item) {
					this.display(item);
				}),
				onError: function(err) {
					console.debug('Error occurred when going directory up', err);
				}
			};
			def.addCallback(args.onItem);			
			this.store.fetchItemByIdentity(args);
		}
		else {
			item = this.tree.rootNode.item;
			def = this.display(item);
		}
		return def;
	},

	/**
	 * Displays the data item (folder) in the tree and it's children in the grid.
	 * The tree and the grid can either be in sync meaning that they show the same content (e.g. tree folder is expanded)
	 * or the grid is one level down (e.g. tree folder is selected but not expanded).
	 * @param {Object} [item] dojo.data.item
	 * @return {Object} dojo.Deferred
	 */
	display: function(item) {
		var def;
		if (!item) {
			item = this._curTreeItem;
		}
		if (item.root || this.store.hasAttribute(item, 'dir')) {
			//grid.showMessage(grid.loadingMessage);
			def = this.showFolderContentInTree(item);
			def.addCallback(this, function() {
				this.showFolderContentInGrid(item);
			});
		}
		return def;
	},

	/**
	 * Adds current item id to history.
	 * @param {string} resourceId id of JsonRestStore item
	 */
	setHistory: function(resourceId) {
		var hist = this.history;

		// first use: initialize history array
		if (hist.curIdx == null) {
			hist.curIdx = 0;
		}
		// move index since we have not used up all available steps yet
		else if (hist.curIdx < hist.numSteps) {
			hist.curIdx++;
		}
		// back button used: reset hist array
		if (hist.curIdx < hist.steps.length - 1) {
			hist.steps = hist.steps.slice(0, hist.curIdx);
		}
		// keep hist array at constant length of number of steps
		if (hist.steps.length == hist.numSteps + 1) {
			hist.steps.shift();
		}
		hist.steps.push(resourceId);
	},

	/**
	 * Go back or forward in history.
	 * @param {string} direction
	 */
	goHistory: function(direction) {
		var hist = this.history;
		var id = null;
		if (direction == 'back' && hist.curIdx > 0) {
			id = hist.steps[--hist.curIdx];
		}
		else if (direction == 'forward' && hist.curIdx < hist.steps.length) {
			id = hist.steps[++hist.curIdx];
		}
		if (id != null) {
			if (id == '/') {
				this.display(this.treeModel.root);
			}
			else {
				this.store.fetchItemByIdentity({
					identity: id,
					onItem: dojo.hitch(this, function(item) {
						this.display(item);
					})
				});
			}
		}
	},

	/**
	 * Returns the current date.
	 * @return {string} formated date
	 */
	getDate: function() {
		return dojo.date.locale.format(new Date(), {datePattern: 'dd.MM.yyyy', timePattern: 'HH:mm'});
	}


});