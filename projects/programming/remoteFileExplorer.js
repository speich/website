require(['dojo/ready', 'rfe/FileExplorer'], function(ready, FileExplorer) {
	ready(function() {
		let rfe = new FileExplorer({
			id: 'remoteFileExplorer',
			origPageUrl: '/projects/programming/remoteFileExplorer.php'
		});
		rfe.startup();
	});
});