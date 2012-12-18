dojo.provide('rfe.Grid');
dojo.require('dojox.grid.DataGrid');

dojo.declare('rfe.Grid', dojox.grid.DataGrid, {
	id: 'rfeGrid',
	rowSelector: false,
	columnReordering: true,
	autoHeight: true,
	defaultHeight: '100%',
	initialWidth: '100%',
	loadingMessage: 'loading data...',
	//queryOptions: {cache: true}, // this only works, when dojox.data.ClientFilter is required in
		//	this.grid.setQuery("?sort=desc&col=id");
//	doClientSort: true,
	doClientPaging: false,
			//sortFields: [{attribute: 'size', descending: true}]
	onStyleRow: function(inRow) {
		// overrides to make all rows look the same when selected
		inRow.customClasses += (inRow.selected ? " dojoxGridRowSelected" : "") + (inRow.over ? " dojoxGridRowOver" : "");
		this.focus.styleRow(inRow);
		this.edit.styleRow(inRow);
	},
	//singleClickEdit: true,

	
	constructor: function() {
		this.structure = [{
			name: "name",
			field: '_item',	// there is a bug in 1.4 that prevents sorting on _item
			formatter: this.formatImg,
			width: '40%'
		}, {
			name: "size",
			field: "size",
			formatter: this.formatFileSize,
			width: '20%'
		}, {
			name: 'type',
			field: 'dir',
			formatter: this.formatType,
			width: '20%'
		}, {
			name: 'last modified',
			field: 'mod',
			width: '20%'
		}];

		//this.onCellContextMenu = function(){}  // prevent disabling right context menu
	},
			
	/**
	 * Format integer to display file size in kilobyte.
	 * @param {string} value
	 */
	formatFileSize: function(value) {
		return Math.round(value / 1000 * 10) / 10 + 'kb';
	},
	
	/**
	 * Return file type.
	 * @param {string} value
	 */
	formatType: function(value) {
		// TODO: return correct file type
		if (value === true || dojo.isArray(value)) {
			value = 'directory';
		}
		else {
			value = 'file';
		}
		return value;
	},
	
	/**
	 * Create HTML string to display file type icon in grid
	 * @param {string} item
	 */
	formatImg: function(item) {
		var strClass;
		strClass = item.dir ? 'dijitFolderClosed' : 'dijitLeaf';
		
		var str = '<span>';
		str += '<img class="dijitTreeIcon ' + strClass;
		str += '" alt="" src="library/dojo/dojo/resources/blank.gif"/>' + item.name;
		str += '</span>';
		return str;
	}
});