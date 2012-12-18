/**
 * @author Simon
 */
dojo.provide('rfe.TreeStoreModel');
dojo.require('dijit.tree.TreeStoreModel');

dojo.declare('rfe.TreeStoreModel', dijit.tree.TreeStoreModel, {

	/**
	 * Overrides the getChildren method to filter folder content. Only display items in tree
	 * that are folders.
	 */
	getChildren: function(/*dojo.data.Item*/ parentItem, /*function(items)*/ onComplete, /*function*/ onError){
		// summary:
		// 		Calls onComplete() with array of child items of given parent item, all loaded.

		var store = this.store;
		if(!store.isItemLoaded(parentItem)){
			// The parent is not loaded yet, we must be in deferItemLoadingUntilExpand
			// mode, so we will load it and just return the children (without loading each
			// child item)
			var getChildren = dojo.hitch(this, arguments.callee);
			store.loadItem({
				item: parentItem,
				onItem: function(parentItem){
					getChildren(parentItem, onComplete, onError);
				},
				onError: onError
			});
			return;
		}
		// get children of specified item
		var childItems = [];
		var i = 0;
		for(; i<this.childrenAttrs.length; i++){
			var vals = store.getValues(parentItem, this.childrenAttrs[i]);
			// added: filter items for folders (dirs)
			vals = dojo.filter(vals, function(val){
				return store.hasAttribute(val, this.childrenAttrs[0]);
			}, this);
			childItems = childItems.concat(vals);
		}

		// count how many items need to be loaded
		var _waitCount = 0;
		if(!this.deferItemLoadingUntilExpand){
			dojo.forEach(childItems, function(item){ if(!store.isItemLoaded(item)){ _waitCount++; } });
		}

		if(_waitCount == 0){
			// all items are already loaded (or we aren't loading them).  proceed...
			onComplete(childItems);
		}else{
			// still waiting for some or all of the items to load
			dojo.forEach(childItems, function(item, idx){
				if(!store.isItemLoaded(item)){
					store.loadItem({
						item: item,
						onItem: function(item){
							childItems[idx] = item;
							if(--_waitCount == 0){
								// all nodes have been loaded, send them to the tree
								onComplete(childItems);
							}
						},
						onError: onError
					});
				}
			});
		}
	}
});