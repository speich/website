<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="<?php echo $web->getLang(); ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Remote File Explorer (rfe)</title>
<style type="text/css">
@import "library/dojo/dijit/themes/claro/claro.css";
@import "library/dojo/dojox/grid/resources/claroGrid.css";

.claro .dojoxGridRowOdd .dojoxGridRowTable tr {
	background-color: #ffffff;
}
.claro .dojoxGridHeader .dojoxGridCell  {
	border-top: 0;
}
.claro .dijitSplitter {
	border-width: 0 1px;
	border-style: solid;
	border-color: #B5BCC7;
	background-color: #f1f1f1;
}
#remoteFileExplorer {
	position: absolute;
   top: 20px; left: 8px; bottom: 8px; right: 8px;
	margin: auto;
	border: 1px solid #B5BCC7;
	background-color: #ffffff;
}
#rfeContentPaneMenu {
	padding: 0;
	overflow: hidden;
}
#rfeContentPaneMenu .dijitMenuBar {
	border-width: 0 0 1px 0;
}
#rfeContentPaneTree, #rfeContentPaneGrid {
	padding: 0;
}
#rfeContentPaneGrid {
	overflow: auto;	
}
#rfeContentPaneLogging {
	border-top: 1px solid #B5BCC7;
	max-height: 300px;
}

#rfeTreeMenuBar {
	position: relative;
	width: 100%;
	font-weight: bold;
	font-size: 100%;
	line-height: 14px;
}
#rfeTreeMenuBar .dojoxGridCell {
	margin-right: 1px;
	border-right: 0;
}

#rfeGrid .dojoxGridRow {
	padding: 2px 4px 0 4px;
}

.dijitDisabled .rfeToolbarIcon {
	background-image:url("library/rfe/images/rfeToolbarIconsDisabled.png");
}
.rfeToolbarIconDirUp {
	background-position: 0 50%;
}
.rfeToolbarIcon {
	background-image:url("library/rfe/images/rfeToolbarIconsEnabled.png");
	background-repeat:no-repeat;
	height:18px;
	text-align:center;
	width:18px;
}

</style>
</head>

<body class="claro">
<div id="remoteFileExplorer"></div>
<script type="text/javascript">
var djConfig = {
	isDebug: false, 
	parseOnLoad: false,
	locale: 'de',
	modulePaths: {'rfe': '/library/rfe'},
	useCommentedJson: true
};
</script>
<script src="../../library/dojo/dojo/dojo.js" type="text/javascript"></script>
<script type="text/javascript">
dojo.require("dojox.data.ClientFilter");
dojo.require('dojox.data.JsonRestStore');
dojo.require('rfe.FileExplorer');

dojo.addOnLoad(function() {
	var args = {
		id: 'remoteFileExplorer',
		store: new dojox.data.JsonRestStore({
			target: 'library/rfe/rfefnc.php/',
			labelAttribute: 'name',
			idAttribute: 'id',
			allowNoTrailingSlash: false,
			//		query: {'name': '*'},
			cacheByDefault: true,
			sort: [{attribute: 'size', descending: true}]
			//
			// @see JsonRestStore.js
			// use this in own store
			// dojo.provide(FileExplorerStore);
			// dojo.declare("rfe.FileExplorerStore", dojox.data.JsonRestStore,{
			// constructor: function(){},
			// fetch: function(args){
			//	args.clientFilter = {start:args.start, count: args.count, sort: args.sort};
			//	// args.query will be passed to the service object for the server side handling
			//	return this.inherited(arguments);
			//	}
			//});
		})
	};
	/**
	 * Loads item from store and calls the callback handler.
	 * Fixes bug 11410 @see http://trac.dojotoolkit.org/ticket/11410
	 * @param args
	 */
	args.store.loadItem = function(args){
		var item;
		if(args.item && args.item._loadObject){
			args.item._loadObject(function(result){
				item = result; // in synchronous mode this can allow loadItem to return the value
				delete item._loadObject;
				var func = result instanceof Error ? args.onError : args.onItem;
				if(func){
					func.call(args.scope, result);
				}
			});
		}else if(args.onItem){
			// even if it is already loaded, we will use call the callback, this makes it easier to
			// use when it is not known if the item is loaded (you can always safely call loadItem).
			args.onItem.call(args.scope, args.item);
		}
		return item;
	};
	/**
	 * Fix bug parentInfo.__parent --> parentInfo.parent
	 */
	args.store.newItem = function(data, parentInfo){
		// summary:
		//		adds a new item to the store at the specified point.
		//		Takes two parameters, data, and options.
		//
		//	data: /* object */
		//		The data to be added in as an item.
		data = new this._constructor(data);
		if(parentInfo){
			// get the previous value or any empty array
			var values = this.getValue(parentInfo.parent,parentInfo.attribute,[]);
			// set the new value
			values = values.concat([data]);
			data.__parent = values;
			this.setValue(parentInfo.parent, parentInfo.attribute, values);
		}
		return data;
	}


	new rfe.FileExplorer(args);
});
</script>
</body>
</html>