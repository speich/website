<?php require_once __DIR__.'/../../../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title><?php echo $web->pageTitle; ?>: REST with dojo and PHP: Demo</title>
<?php echo $head->render(); ?>
<link href="../../../library/dojo/1.17.3/dijit/themes/claro/claro.css" rel="stylesheet"	type="text/css">
<link rel="stylesheet" href="../../../library/dgrid/0.3.21/css/skins/claro.css">
<link rel="stylesheet" href="dojo-demo-dgrid.css">
</head>

<body class="claro">
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>REST with dojo and PHP: Demo of a dgrid observing changes to the JsonRest store</h1>
<p>This demo shows <a href="http://www.sitepen.com">sitepen</a>'s new <a href="https://github.com/SitePen/dgrid/">dgrid</a> hooked up to a caching store and listening to changes to the store.
	The caching store combines a <a href="http://dojotoolkit.org/reference-guide/dojo/store/JsonRest.html">JsonRest store</a> as the master with a <a href="http://dojotoolkit.org/reference-guide/dojo/store/Memory.html">Memory store</a> as the slave. The master store communicates with a PHP controller script on the server.
	The Memory store is used to cache the data locally. Changes to the data such as
	create, update and delete are observed and the grid updates correspondingly.</p>
<p>1. Open Firebug's Net XHR panel to follow the different requests (GET, POST, PUT, DELETE).</p>
<p>2. Clicking the button will demonstrate the use of the cache by calling store.get(3) twice. The NET XHR panel will only show one request GET /3
	<button id="buttonStart1" class="button"><span>start sequence 1</span></button></p>
<p>3. Clicking the button will demonstrate how the dgrid updates automatically, when a store object is created (POST), renamed (PUT) and then deleted (DELETE).
	<button id="buttonStart3" class="button"><span>start sequence 2</span></button></p>
<p><strong>Tip</strong>: Start sequence 2 again, click cancel in the second or third dialog. Then reload Browser...</p>
<div id="palette"></div>
<div id="grid"></div>
<ul class="main">
<li><a href="../../downloads/demo-dgrid-jsonrest.zip">Download all necessary PHP files</a> for the dgrid demo.</li>
<li>Notes about <a href="/articles/2011/12/29/rest-with-dojo-and-php-notes-on-using-dgrid-with-a-caching-store/">possible pitfalls when using a caching store and an observable</a>.</li>
<li>You can find a much more advanced demo showing a <a href="../../../projects/programming/remoteFileExplorer.php/demo/photos" title="demo of removeFileExplorer">dgrid combined with a tree</a> hooked up to a dojo/store/JsonRest.</li>
</ul>
<script type="text/javascript">
 var dojoConfig = {
	  async: true,
	  packages:[
			{ name:'dgrid', location:'/library/dgrid/0.3.21'},
			{ name:'xstyle', location:'/library/xstyle/0.3.3'},
			{ name:'put-selector', location:'/library/put-selector/0.3.6'},
			{ name:'speich.net', location:'/library/speich.net'}
	  ]
 };
</script>
<script src="../../../library/dojo/1.17.3/dojo/dojo.js" type="text/javascript"></script>
<script type="text/javascript">
require([
	'dojo/when',
	'dojo/store/Memory',
	'dojo/store/Observable',
	'dojo/store/Cache',
	'dojo/store/JsonRest',
	'dgrid/OnDemandGrid',
	'dgrid/extensions/ColumnResizer',
	'dojo/_base/declare',
	'dojo/_base/lang',
	'dojo/on',
	'dojo/dom',
	'dojo/dom-style',
	'dojo/date/locale',
	'dijit/registry',
	'speich.net/DialogConfirm/DialogConfirm',
	'xstyle/has-class',
	'xstyle/css',
	'put-selector/put'
], function(when, Memory, Observable, Cache, JsonRest, Grid, ColumnResizer, declare, lang, on, dom, domStyle, locale, registry, DialogConfirm) {

	var demo = {
		store: null,
		storeMaster: new JsonRest({
			target: 'controller.php/'
		}),
		storeMemory: new Observable(new Memory()),

		grid: new (declare([Grid, ColumnResizer]))({
			columns: [{
				label: "name",
				field: 'name',
				sortable: false,
				renderCell: function(obj, data, td, options) {
					var strClass = obj.dir ? 'dijitFolderClosed' : 'dijitLeaf';
					var str = '<span>';
					str += '<img class="dijitTreeIcon ' + strClass;
					str += '" alt="" src="' + require.toUrl("dojo/resources/blank.gif") + '"/>' + obj.name;
					str += '</span>';
					td.innerHTML = str;
				}},{
				label: 'size',
				field: 'size',
				sortable: false
			},{
				label: 'type',
				field: 'dir',
				sortable: false
			},{
				label: 'last modified',
				field: 'mod',
				sortable: false
			}]
		}, 'grid'),

		initCache: function() {
			this.store = new Cache(this.storeMaster, this.storeMemory);      	// uses the storeMemory's queryEngine
		},

	   init: function() {
			var id, parentId, query;

			on(dom.byId('buttonStart1'), 'click', lang.hitch(this, this.doSequence1));
			on(dom.byId('buttonStart3'), 'click', lang.hitch(this, this.doSequence3));

			/**
			 * Doing get() and query() call results are added to the cache (unless they don't meet provided criteria),
			 * but only get() uses the cache, whereas query() uses the master store.
			 * If you want to a query to come from the cache, directly call query() on the caching store.
			 */
			parentId = 2;
			query = this.store.query(parentId + '/');	// get 2's children which fills the cache

			when(query, lang.hitch(this, function() {

				var query = this.storeMemory.query({parId: parentId});	// Note: Memory store expects object {parId: 2} whereas JsonRest expects string

				 // note: Observe events are only triggered if the object matches the query constraint, e.g. parentId = 2
				 query.observe(function (obj, removedFrom, insertedInto) {
		//			console.log('observed:', obj, removedFrom, insertedInto);
					if (insertedInto === -1) {
						console.log('removed object', obj, 'from index', removedFrom);
					}
					else if (removedFrom === -1) {
						console.log('added object', obj, 'to index', insertedInto);
					}
					else {
						console.log('updated object', obj, 'with index', removedFrom, insertedInto);
					}
				}, true);
				this.grid.setStore(this.storeMemory);

				domStyle.set('buttonStart1', 'display', 'block');
				domStyle.set('buttonStart3', 'display', 'block');
			}));

		},

		// note: JsonRest store's methods do not return the same types as Memory store which is unfortunate,
		// but makes sense, because it safes extra roundtrips to server

		// Demonstrate use of cache with get. get() always uses cache and result is added to the cache
		doSequence1: function() {
			var dialogGet,
				self = this,
				id = 3;

			dialogGet = registry.byId('dialogGet') || new DialogConfirm({
				id: 'dialogGet',
				title: 'GET: Load object from the store',
				content: "<p>Load the folder 'texts' from your virtual disk on the server ?</p>",
				hasSkipCheckBox: false,
				hasUnderlay: false
			});

			when(dialogGet.show(), function() {
				return self.store.get(id);
			}).then(function(obj) {
				console.log(id, 'loaded', obj);
				return dialogGet.show();
			}).then(function() {
				var obj = self.store.get(id);		// since this is coming from the cache, deferred is not necessary
				console.log(id, 'loaded from cache', obj);	// check firebug net panel
			});
		},

		// not used in this demo, but keep for tutorial
		doSequence2: function() {
			var self = this,
				parentId = 1;

			when(self.store.query(parentId + '/'), function(result) {
				console.log('query', parentId, 'loaded from server', result);	// check firebug net panel
			}).then(function() {
				// loaded from cache from query 1/
				var id = 6, result = self.store.get(id);	// since coming from cache not deferred necessary
				console.log('id', id, 'loaded from cache', result);
			});
		},


		// Note: get() always uses the cache (if available) and the results are added to the cache. query(), however always uses the master store,
		// while the results are added to the cache. If you want the query to come from	the cache, directly call query() on the Memory store.

		/**
		 * Executes a POST, PUT, DELETE sequence to demonstrate the dgrid observing the store.
		 */
		doSequence3: function() {
			var self = this,
				dialogCreate, dialogRename, dialogDelete, obj;
			
			dialogCreate = registry.byId('dialogCreate') || new DialogConfirm({
				id: 'dialogCreate',
				title: 'POST: Add object to the store',
				content: "<p>Add a new folder 'testfolder' to your virtual disk on the server ?</p>",
				hasSkipCheckBox: false,
				hasUnderlay: false
			});
			dialogRename = registry.byId('dialogRename') || new DialogConfirm({
				id: 'dialogRename',
				title: 'PUT: Update object in the store',
				content: "<p>Rename the folder 'testfolder' to 'renamed folder' on the server ?</p>",
				hasSkipCheckBox: false,
				hasUnderlay: false
			});
			dialogDelete = registry.byId('dialogDelete') || new DialogConfirm({
				id: 'dialogDelete',
				title: 'DELETE: Remove object from the store',
				content: "<p>Delete the folder 'renamed folder' on the server ?</p>",
				hasSkipCheckBox: false,
				hasUnderlay: false
			});

			obj = {
				parId: 2,
				name: "testfolder",
				size: 0,
				mod: this.setDate(),
				dir: true
			};
			
			when(dialogCreate.show(), function() {
			   return self.store.add(obj);

			}).then(function(obj) {
				obj.name = 'renamed';
				obj.mod = self.setDate();
				return when(dialogRename.show(), function() {
					return self.store.put(obj);
				});

			}).then(function(obj) {
				dialogDelete.show().then(function() {
					self.store.remove(obj.id);
				});
			});
		},

		setDate: function() {
			return locale.format(new Date(), {
				datePattern: 'dd.MM.yyyy',
				timePattern: 'HH:mm'
			});
		}
	};

	demo.initCache();
	demo.init();
});

</script>
<?php echo $bodyEnd->render(); ?>
</body>
</html>