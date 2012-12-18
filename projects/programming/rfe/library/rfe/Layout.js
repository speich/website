dojo.provide('rfe.Layout');

//dojo.require('dijit.tree.dndSource');
//dojo.require('rfe.ForestStoreModel');
dojo.require('rfe.TreeStoreModel');
dojo.require('rfe.Tree');
dojo.require('rfe.Grid');

dojo.require('dijit.InlineEditBox');

// layout
dojo.require('dijit.layout.BorderContainer');
dojo.require('dijit.layout.ContentPane');
dojo.require("dijit.MenuBar");
dojo.require("dijit.MenuBarItem");
dojo.require("dijit.PopupMenuBarItem");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.PopupMenuItem");
dojo.require("dijit.Toolbar");
dojo.require("dijit.form.Button");

dojo.declare('rfe.Layout', null, {
	store: null,
	layout: {            // contains references to all layout elements
		view: 'horizontal',
		panes: null
	},

	constructor: function(args) {
		this.treeModel = new rfe.TreeStoreModel({
			store: args.store,
			deferItemLoadingUntilExpand: true,
			childrenAttrs: ['dir']
		});
		this.tree = new rfe.Tree({
			model: this.treeModel
//			dndController: "dijit.tree.dndSource"
		});
		this.grid = new rfe.Grid({
			store: args.store
		});
	},

	/**
	 * Create the layout containers for the tree and grid.
    * @param {Number} id
    * @return {Object} layout
	 */
	createLayout: function(id)	{	
		var panes = this.createPanes(id);
		var menuBar = this.createMenus();
		var toolbar = this.createToolbar();
		var treeHead = dojo.create('div', {
			'id': 'rfeTreeMenuBar',
			'class': 'dojoxGridHeader',// 'dijitMenuBar',
			innerHTML: '<div class="dojoxGridCell"><div class="dojoxGridSortNode">folders</div></div>'  // imitating dojox grid header to use same style and size as grid headers
		}, panes.treePane.domNode, 'first');

		menuBar.placeAt(panes.menuPane.domNode);
		toolbar.placeAt(panes.menuPane.domNode);
		this.tree.placeAt(panes.treePane.domNode);
		this.grid.placeAt(panes.gridPane.domNode);
		panes.menuPane.placeAt(panes.borderContainer);
		panes.treePane.placeAt(panes.borderContainer);
		panes.gridPane.placeAt(panes.borderContainer);
		panes.loggingPane.placeAt(panes.borderContainer);

		this.setView(panes, this.layout.view);
		this.layout.menu = menuBar;
		this.layout.panes = panes;
		panes.borderContainer.startup();
		this.grid.startup(); // call startup here, otherwise store data is loaded twice (no idea why though)
	},

	/**
	 * Toggle display of a tree pane.
	 */
	toggleTreePane: function() {
		// to keep it simple for the moment we switch to vertical view where the remaining pane is the center pane
		// -> automatically expands to fill the remaining space
		var panes = this.layout.panes;
		var treePane = panes.treePane;
		var gridPane = panes.gridPane;
		if (treePane.domNode.parentNode) {  // hide pane
			if (this.layout.view == 'vertical') {
				this.setView(panes, 'horizontal');
			}
			panes.borderContainer.removeChild(treePane);
		}
		else {   // show pane
      	panes.borderContainer.addChild(treePane);
		}
	},

	/**
	 * Sets the layout view of the explorer.
	 * @param {object} panes dijit.BorderContainer
	 * @param {string} view
	 */
	setView: function(panes, view) {
		var treePane = panes.treePane;
		var gridPane = panes.gridPane;

		panes.borderContainer.removeChild(treePane);
		panes.borderContainer.removeChild(gridPane);
		if (view == 'vertical')  {
			panes.treePane.set({
				region: 'center',
				style: 'width: 100%;',
				minSize: null,
				splitter: false
			});
			panes.gridPane.set({
				region: 'bottom',
				style: 'top: auto; width: 100%; height: 50%',   // top is not removed when changing from center to bottom
				splitter: true
			});
		}
		else if (view == 'horizontal') {
			panes.treePane.set({
				region: 'left',
				style: 'top: 0; bottom: auto; width: 20%; height: 100%;',
				minSize: 180,
				splitter: true
			});
			panes.gridPane.set({
				region: 'center',
				style: 'top: 0; height: 100%'
			});
		}
		panes.borderContainer.addChild(treePane);
		panes.borderContainer.addChild(gridPane);

		this.layout.view = view;
	},

	/**
	 * Creates the main menu.
	 * @return dijit.MenuBar
	 */
	createMenus: function() {
      var menuBar = new dijit.MenuBar({
	      id: 'rfeMenuBar'
      });
		var menuFile = new dijit.Menu({
			id: 'rfeMenuFile'
		});
		var menuView = new dijit.Menu({
			id: 'rfeMenuView'
		});

		menuBar.addChild(new dijit.PopupMenuBarItem({
			label: 'File',
			popup: menuFile,
			onClick: function() {
				// TODO: set menuItems disabled if noting is selected to edit
			}
		}));
		menuBar.addChild(new dijit.PopupMenuBarItem({
			label: 'View',
         popup: menuView
		}));

		menuFile.addChild(new dijit.MenuItem({
			label: 'New'
		}));
		menuFile.addChild(new dijit.MenuItem({
			label: 'Rename',
			onClick: dojo.hitch(this, function() {
				this.edit.renameItem(this.edit.lastFocused);
			})
		}));
		menuFile.addChild(new dijit.MenuItem({
			label: 'Delete'
		}));
		menuFile.addChild(new dijit.MenuItem({
			label: 'Copy'
		}));

		menuView.addChild(new dijit.CheckedMenuItem({
			id: 'rfeMenuItemHorizontal',
			label: 'Layout horizontal',
			checked: true,
         onClick: dojo.hitch(this, function() {
            this.setView(this.layout.panes, 'horizontal');
	         dojo.publish('rfe/menuView/setView');   // notify menuView/folders to set checked = true
	         dijit.byId('rfeMenuItemVertical').set('checked', false);
         })
		}));
		menuView.addChild(new dijit.CheckedMenuItem({
			id: 'rfeMenuItemVertical',
			label: 'Layout vertical',
	      checked: false,
         onClick: dojo.hitch(this, function() {
            this.setView(this.layout.panes, 'vertical');
	         dojo.publish('rfe/menuView/setView');   // notify menuView/folders to set checked = true
	         dijit.byId('rfeMenuItemHorizontal').set('checked', false);
         })
		}));
		menuView.addChild(new dijit.CheckedMenuItem({
			id: 'rfeMenuItemFolders',
			label: 'Show Folders',
			checked: true,
         onClick: dojo.hitch(this, this.toggleTreePane)
		}));
		dojo.subscribe('rfe/menuView/setView', dijit.byId('rfeMenuItemFolders'), function() {
			this.set('checked', true);
		});
		/*
		menuView.addChild(new dijit.CheckedMenuItem({
			id: 'rfeMenuItemContents',
			label: 'Show Contents',
			checked: true,
			onClick: function() {
				// TODO: implement hiding of grid. -> Showing folders in tree instead
				this.set('checked', true);
				alert('not implemented yet');
            //onClick: dojo.hitch(this, this.toggleGridPane)
			}
		}));
		*/
		return menuBar;
	},

	/**
	 * Creates the layout panes.
	 * @param {String} id id of HTMLDivElement layout should be appended to
	 * @return {Object} dijit.BorderContainer and dijit.ContentPane
	 */
	createPanes: function(id) {
		var panes = {};
		panes.borderContainer = new dijit.layout.BorderContainer({
			liveSplitters: true,
			gutters: false

		}, id);
		dojo.connect(panes.borderContainer.domNode, 'oncontextmenu', function(evt) {
			if (evt.target.id != 'rfeTree' && evt.target.id != 'rfeGrid') {
			   dojo.stopEvent(evt);
			}
		});
		panes.menuPane = new dijit.layout.ContentPane({
			id: 'rfeContentPaneMenu',
			region: 'top'
		}, document.createElement('div'));
		panes.treePane = new dijit.layout.ContentPane({
			id: 'rfeContentPaneTree'
		}, document.createElement('div'));
		panes.gridPane = new dijit.layout.ContentPane({
			id: 'rfeContentPaneGrid'
		}, document.createElement('div'));
		panes.loggingPane = new dijit.layout.ContentPane({
			id: 'rfeContentPaneLogging',
			region: 'bottom'
		}, document.createElement('div'));
		return panes;
   },

	/**
	 * Creates the toolbar.
	 * @return {Object} dijit.Toolbar
	 */
	createToolbar: function() {
		var toolbar = new dijit.Toolbar({id: 'rfeToolbar'});
		toolbar.addChild(new dijit.form.Button({
			id: 'rfeButtonDirectoryUp',
			label: 'up',
			showLabel: true,
			iconClass: 'rfeToolbarIcon rfeToolbarIconDirUp',
			disabled: true,
			onClick: dojo.hitch(this, function() {
				var def = this.goDirUp();
				def.addCallback(dojo.hitch(this, function() {
					this.setHistory(this._curTreeItem.id);
				}));
			})
		}));
		dojo.connect(this, 'showFolderContentInGrid', dijit.byId('rfeButtonDirectoryUp'), function(item) {
			this.set('disabled', item.root ? true : false);
		});
		dojo.connect(this.grid, 'onRowDblClick', dijit.byId('rfeButtonDirectoryUp'), function(item) {
			this.set('disabled', item.root ? true : false);
		});

		toolbar.addChild(new dijit.form.Button({
			id: 'rfeButtonHistoryBack',
			label: 'history back',
			showLabel: false,
			iconClass: 'dijitEditorIcon dijitEditorIconUndo',
			disabled: true,
			onClick: dojo.hitch(this, function() {
				this.goHistory('back');
			})
		}));
		dojo.connect(this, 'setHistory', this, function(resourceId) {
			dijit.byId('rfeButtonHistoryBack').set('disabled', this.history.steps.length < 2);
		});
		dojo.connect(this, 'goHistory', this, function(resourceId) {
			dijit.byId('rfeButtonHistoryBack').set('disabled', this.history.curIdx < 1);
		});

		toolbar.addChild(new dijit.form.Button({
			id: 'rfeButtonHistoryForward',
			label: 'history forward',
			showLabel: false,
			iconClass: 'dijitEditorIcon dijitEditorIconRedo',
			disabled: true,
			onClick: dojo.hitch(this, function() {
				this.goHistory('forward');
			})
		}));
		dojo.connect(this, 'goHistory', this, function(resourceId) {
			dijit.byId('rfeButtonHistoryForward').set('disabled', this.history.curIdx > this.history.steps.length - 2);
		});

		return toolbar;
	}

});