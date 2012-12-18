/**
 * FileExplorer mixin that provides edit functionality.
 */

dojo.provide('rfe._Edit');

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.PopupMenuItem");
//dojo.require('rfe._Logging');

dojo.declare('rfe._Edit', null, {
		edit: {
			contextMenu: null,
			lastFocused: null 	// keep track of last selected item (grid or tree)
		},

		/**
		 * Returns which item to edit.
		 * Items can be either selected in the tree or in the grid.
		 * In deciding which item to edit, focus takes precedence over selected, grid over tree.
		 * @return object dojo.data.item
		 */
		getEditItem: function() {
			console.debug('lastFocused', this.edit.lastFocused)
			return this.edit.lastFocused;
		},

		/**
		 * Create context menu for the file explorer.
		 * @target {object} target domNode 
		 * @return {object} dijit.Menu
		 */
		createContextMenu: function(target) {
			var menu = new dijit.Menu({
				id: 'rfeContextMenu',
				targetNodeIds: ['rfeContentPaneTree','rfeContentPaneGrid', 'rfeGrid'],
				popUpDelay: 200
				/*,
				onOpen: function(args) {
					console.debug('opening contextMenu', args);
				},
				onClose: function(args) {
					console.debug('closing contextMenu');
					//menu.item = null;
				}
								*/
			});
			var subMenu = new dijit.Menu();

			dojo.mixin(menu, {
				item: null	// store which item was right clicked
			});

			//make grid.Row selected onRowRightClick (needed for menu.item to be set)
			this.grid.onCellContextMenu = function(evt) {};

			dojo.forEach(menu.targetNodeIds, function(id) {
				var domNode = dojo.byId(id);
				dojo.connect(domNode, 'oncontextmenu', this, function(evt) {
					var widget = dijit.getEnclosingWidget(evt.target);
					menu.item = null;
					// Remember which item was clicked on, by setting menu.item
					if (widget.tree) {
						menu.item = widget.item;
					}
					else if (widget.grid) {
						var idx = widget.grid.selection.selectedIndex;
						menu.item = widget.grid.getItem(idx);
					}
					// If not clicked on a item (tree.node or grid.row), but below widget,
					// set all menuItems to disabled except create/upload
					if (menu.item == null) {
						dojo.filter(menu.getChildren(), function(item) {
							if (item.get('label') != 'new' && item.get('label') != 'upload') {
								item.set('disabled', true);
							}
						});
						menu.item = this.getEditItem();
					}
					else {
						dojo.forEach(menu.getChildren(), function(item) {
							item.set('disabled', false);
						});
					}
				});
			}, this);
			menu.addChild(new dijit.PopupMenuItem({
				label: 'new',
				popup: subMenu,
				iconClass: "dijitEditorIcon dijitEditorIconNewPage"
			}));
			menu.addChild(new dijit.MenuItem({
				label: 'rename',
				onClick: dojo.hitch(this, function() {
					this.renameItem(menu.item);
				})
			}));
			menu.addChild(new dijit.MenuItem({
				label: 'delete',
				onClick: dojo.hitch(this, function() {
					this.deleteItem(menu.item);
				})
			}));
			menu.addChild(new dijit.MenuItem({
				label: 'copy',
				onClick: function() {
					alert('todo');
				}
			}));
			menu.addChild(new dijit.MenuItem({
				label: 'upload',
				onClick: function() {
					alert('todo');
				}
			}));

			// subMenu new
			subMenu.addChild(new dijit.MenuItem({
				label: 'directory',
				onClick: dojo.hitch(this, function() {
					this.createItem(menu.item, {
						dir: true
					});
				})
			}));
			subMenu.addChild(new dijit.MenuItem({
				label: 'file',
				onClick: dojo.hitch(this, function() {
					this.createItem(menu.item, {
						dir: false
					});
				})
			}));

			menu.startup();
			this.edit.contextMenu = menu;
		},

		/**
		 * Delete item.
		 * @param {object} item dojo.data.item
		 * @return {object} dojo.Deferred
		 */
		deleteItem: function(item) {
			// TODO: update history
			var def = new dojo.Deferred();
			var store = this.store;
			var grid = this.grid;
			var idx  = grid.getItemIndex(item);	// note: store reference before deleting item
			store.deleteItem(item);
			store.save({
				onComplete: function(item) {
//					var node = grid.getRowNode(idx);
//					dojo.destroy(node);
//					grid.set('rowCount', grid.get('rowCount') - 1);
//					def.callback(def);
				},
				onError: function(error){
					console.debug(error);
					def.errback(error);
				}
			});
			return def;
		},

		/**
		 * Rename item
		 * @param {object} item dojo.data.item
		 */
		renameItem: function(item) {
			var store = this.store;
			var value = store.getValue(item, 'name');

			//this.grid.editable = true;
			//this.grid.edit.setEditCell(1, idx);
			//this.grid.edit.cellFocus(1, idx);
			//this.grid.doApplyCellEdit(value, 2, 3);

			store.setValue(item, 'name', 'test new');
			store.setValue(item, 'mod', this.getDate());
			store.save({
				onComplete: dojo.hitch(this, function() {
	//				var idx = this.grid.getItemIndex(item);
	//				this.grid.updateRow(idx);
				}),
				onError: function(error) {
					// jsonreststore calls revert automatically
					console.debug(error);
				}
			});
		},

		/**
		 * Create a new item.
		 * @param {object} item dojo.data.item
		 * @param {object} itemProps
		 */
		createItem: function(item, itemProps) {
			/*
			POST - This should create a new object. The URL will correspond to the target store (like /table/)
					and the body should be the properties of the new object. The server's response should include a
					Location header that indicates the id of the newly created object. This id will be used for subsequent
					PUT and DELETE requests. JsonRestStore also includes a Content-Location header that indicates
					the temporary randomly generated id used by client, and this location is used for subsequent
					PUT/DELETEs if no Location header is provided by the server or if a modification is sent prior
					to receiving a response from the server.

			#http://www.sitepen.com/blog/2008/06/13/restful-json-dojo-data/
			I have one problem with the .newItem()/.save() sequence when used with widget(Tree in this case).
			Calling newItem() creates an item client-side which triggers a callback to onNew() on the TreeStoreModel,
			causing the Tree to attempt to refresh itself. This causes the tree to re-request its contents, but it doesn't
			find the new item since save() has not yet been called. Do I have to manually refresh the tree after calling
			save (in the onComplete handler passed to save)?
			It seems to be that either onNew should only be called after a save() call, or it should at least be
			called again after save(). Am I missing something here?

			Kris Zyp says:	@RoryD: Widgets that issue fetches while there is uncommitted data do pose a challenge for
			the JsonRestStore, because their is no mechanism for the JRS to reliably send uncommitted changes to a
			server via HTTP (using RFC 2616). There are a couple of ways to deal with this. You can use the ClientFilter
			mixin with JRS or JsonQueryRestStore (a subclass of JRS) to provide client side caching of data so that
			fetches will be handled on the client side with the uncommitted data properly handled
			(http://www.sitepen.com/blog/2008/12/18/more-query-and-caching-power-for-data-stores-clientfilter-jsonquery-and-jsonqueryreststore/).
			Or you, could add a listener to the notification events (onNew in this case) and call save() so that the
			data change is sent to the server before the fetch request is sent to the server.
			*/

			var parentId, newItemArgs, saveItem;
			if (this.store.hasAttribute(item, 'dir')) {
				parentId = this.store.getValue(item, 'id');
			}
			else {
				parentId = this.getParentId(item);
			}
			newItemArgs = {
				// id: generated by server
	      	parentId: parentId ? parentId : '/',
				name: itemProps.dir ? 'new directory' : 'new text file',
				size: 0,
				mod: this.getDate()
			};
			dojo.safeMixin(newItemArgs, itemProps);

			saveItem = dojo.hitch(this, function(item) {
				var parentItem = {
					parent: item,
					attribute: this.treeModel.childrenAttrs[0]
				};
				var newItem = this.store.newItem(newItemArgs, parentItem);
				// this is part of ClientFilter
				/*
				var fetchResults;
				var requestArgs = {
					sort: [{attribute: 'name'}],
					onComplete: function(results){
						fetchResults = results;
					}
		//		{
		//			query: query-object or query-string,
		//			queryOptions: object,
		//			onBegin: Function,
		//			onItem: Function,
		//			onComplete: Function,
		//			onError: Function,
		//			scope: object,
		//			start: int
		//			count: int
		//			sort: array
		//		}
				};
				this.store.fetch(requestArgs);
			*/
				this.store.save({
					onComplete: function(res) {
//						var idx = this.grid.getItemIndex(newItem);
					//	this.grid.set('rowCount', this.grid.get('rowCount') + 1);
			//			this.store.updateResultSet(fetchResults, requestArgs);
					// works but not ideal since grid is completely reloaded:	this.showFolderContentInGrid(item)
//						console.debug(res,newItem, idx)
//						console.debug(this.store.getIdentity(newItem));
//						this.grid._onNew(newItem);
				//		this.grid._createItem(newItem, 2);
//						this.grid._fetch(0);
//						this.store.clientSideFetch()
					},
					onError: function(err) {
						console.debug(err);
					},
					scope: this
				});
			});

			console.debug('creatn item', newItemArgs)
			if (newItemArgs.parentId != '/') {
				this.store.fetchItemByIdentity({
					identity: newItemArgs.parentId,
					onItem: saveItem,
					onError: function(err) {
						console.debug(err);
					}
				});
			}
			else {
				saveItem(this.treeModel.root);
			}
		}
	
});

