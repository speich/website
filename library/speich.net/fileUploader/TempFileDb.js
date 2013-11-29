/**
 * Created by Simon.
 * Date: 13.03.11, Time: 10:12
 */
dojo.provide('snet.fileUploader.TempFileDb');

dojo.declare('snet.fileUploader.TempFileDb', null, {
	db: 'blub',


	/**
	 * Connect to the temporary file database
	 * @return {dojo.Deferred}
	 */
	connect: function() {
		var dfd = new dojo.Deferred();
		var req = window.mozIndexedDB.open('uploads', 'Store files before uploading to enable resume');

		req.onsuccess = dojo.hitch(this, function(evt) {
			var v = '1.0';
			var db, vReq;

			console.log('db opened');
			this.db = req.result;
			if (v != this.db.version) {
				vReq = this.db.setVersion(v);
				vReq.onsuccess = function() {
					var store = this.db.createObjectStore('files', {
						keyPath: 'name'
					});
					store.createIndex('name', 'name', {
						unique: true
					});
					console.log('object store and index created');
					dfd.resolve(vReq);
				};
			}
		});
		req.onerror = function() {
				console.log(req, req.error)
				dfd.reject(req.error);
		};
		return dfd;
	},

	/**
	 * Load file from database
	 * @param {string} fileName
	 * @return {dojo.Deferred}
	 */
	load: function(fileName) {
		var dfd = new dojo.Deferred();
		var trans = this.db.transaction(['files'], IDBTransaction.READ_ONLY);
		var store = trans.objectStore('files');
		var req = store.get(fileName);
		req.onsuccess = function(evt) {
			console.log(evt.value);
			console.log('file ', evt.result.name, 'retrieved');
			dfd.resolve(evt.result);
		};
		req.onerror = function(evt) {
			console.log(evt.value);
			dfd.reject(evt);
		};
		return dfd;
	},

	/**
	 * Save file to database.
	 * @param {File} file
	 * @return {dojo.Deferred}
	 */
	save: function(file) {
		var data;
		var dfd = new dojo.Deferred();
		var reader = new FileReader();

		dojo.connect(reader, 'load', this, function(evt) {
			data = {
				name: file.name,
				size: file.size,
				bin: evt.target.result
			};
			console.log('file read into bin', dfd);
			dfd.resolve(data);
		});
		dojo.connect(reader, 'error', function(evt) {
			console.log('could not read file', evt)
			dfd.reject(evt)
		});
		reader.readAsBinaryString(file);

		dfd = dfd.then(dojo.hitch(this, function(data) {
			var dfd = new dojo.Deferred();
			var trans = this.db.transaction(['files'], IDBTransaction.READ_WRITE, 0);
			var store = trans.objectStore('files');
			var req = store.put(data);
			console.log('saving data')
			req.onsuccess = function(evt) {
				console.log(evt.value);
				console.log('data saved');
				dfd.resolve(evt);
			};
			req.onerror = function(evt) {
				console.log(evt.value);
				dfd.reject(evt);
			};
			return dfd;
		}));

		return dfd;
	},

	/**
	 * Delete file from database.
	 * @param {string} fileName
	 */
	del: function(fileName) {
		var dfd = new dojo.Deferred();
		var trans = this.db.transaction(['files'], IDBTransaction.READ_WRITE, 0);
		var store = trans.objectStore('files');
		var req = store.delete(fileName);
		req.onsuccess = function(evt) {
			console.log(evt.value);
			console.log('file ', fileName, 'deleted');
			dfd.resolve(evt);
		};
		req.onerror = function(evt) {
			console.log(evt.value);
			dfd.reject(evt);
		};
		return dfd;
	}

});