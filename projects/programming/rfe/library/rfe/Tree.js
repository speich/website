/**
 * @author Simon
 */
dojo.provide('rfe.Tree');
dojo.require('dijit.Tree');

dojo.declare('rfe.Tree', dijit.Tree, {
	id: 'rfeTree',
	isEdited: false,
	openOnClick: false,
	openOnDblClick: true,
	persist: false,
	showRoot: true,

	/**
	 * Creates tree nodes that only have an expando icon, when they contain items that are folders.
	 */
	_createTreeNode: function(/*Object*/ args){
		var node = new dijit._TreeNode(args);
		if (!this.hasExpandableChildren(node.item)) {
			node.isExpandable = false;
			node._setExpando();
		}
		return node;
	},
	
	/**
	 * Checks if an item has children with children.
	 * @param {object} item dojo.data.item
	 * @return boolean
	 */
	hasExpandableChildren: function(item) {
		var store = this.model.store;
		var attr = this.model.childrenAttrs[0];
		var children = store.getValues(item, attr);
		var childIsExpandable = dojo.some(children, function(child) {
			if (store.hasAttribute(child, attr)) {
				return true;
			}
		});
		return childIsExpandable;
	}
});