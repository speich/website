<?php
$version = isset($_GET['demo']) ? (int)$_GET['demo'] : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Demo of all images loaded</title>
<style type="text/css">
html, body {
	height: calc(100% - 0.5em);
	background-color: #aaaaaa;
	padding: 0;
	margin: 0;
	font-size: 100%;
}

body {
	padding: 0.5em;
	font: 12px Verdana, Arial, Myriad, Helvetica, Tahoma, clean, sans-serif;
	font-weight: normal;
}

h1 {
	font-size: 1.5em;
}

h2 {
	font-size: 1em;
}

h1, h2 {
	font-weight: bold;
}

p {
	font-size: 1em;
}

.imgContainer {
	height: 50%;
	min-width: 300px;
	min-height: 168px;
	overflow: hidden;
	overflow-y: auto;
	border-top: 1px solid black;
	border-bottom: 1px solid black;
}

ul {
	margin: 2em;
	padding: 0;
}

li {
	list-style: none;
	margin-bottom: 1em;
}

li.selected {
	border: 1px solid #0a246a;
	background-color: #fff;
}

li img {
	vertical-align: middle;
	border: 1.5em #ffffff solid;
	margin-right: 1em;
}

.copyright {
	font-size: 0.8em;
	margin-top: 0.2em;
}

</style>
<script type="text/javascript">
let byId = document.getElementById.bind(document),
	app = {

		/**
		 * Load a list of images from service.
		 * @return {Promise}
		 */
		loadImages: function() {
			return fetch('controller.php')
				.then(response => {
					return response.json();
				})
				.then(result => {
					return result;
				});
		},

		/**
		 * Render a row of the list
		 * @param {Object} item
		 * @return {HTMLUListElement}
		 */
		renderRow: function(item) {
			let img = new Image(),
				a = document.createElement('a'),
				li = document.createElement('li');

			img.src = '../../../photo/photodb/images/thumbs/' + item.ImgFolder + '/' + item.ImgName;
			img.id = item.Id;
			a.href = '../../../photo/photodb/photo-detail-en.php?imgId=' + item.Id;

			a.appendChild(img);
			a.appendChild(document.createTextNode(item.NameEn));
			li.appendChild(a);

			return li;
		},

		/**
		 * Render a list of images
		 * @param {Array} items list of objects
		 */
		renderList: function(items) {
			let ul = document.createElement('ul');

			items.forEach(function(item) {
				let li = this.renderRow(item);

				ul.appendChild(li);
			}.bind(this));

			document.getElementsByClassName('imgContainer')[0].appendChild(ul);
		},

      <?php if ($version > 1) { ?>
		/**
		 * Return the image id from the hash in the url
		 * @return {string}
		 */
		getId: function() {
			return location.hash.slice(1);
		},

		/**
		 * Scroll to image and set it selected.
		 * @param {String} id image id
		 */
		select: function(id) {
			let img = byId(id);

			if (img) {
				img.scrollIntoView({behavior: 'smooth', block: 'center'});
				img.parentNode.parentNode.classList.add('selected');
			}
		},

      <?php } if ($version === 3) { ?>

		/**
		 * Check if all images are loaded.
		 * Returns a promise which is resolved when all images are loaded or failed, e.g. settled.
		 * @param {DOMString} selectors css selectors to match
		 * @returns {Promise<any[]>}
		 */
		queryAllImagesSettled: function(selectors) {
			let images = document.querySelectorAll(selectors),
				promises = [];

			images.forEach(img => {
				let promise, handler;

				if (img.complete) {
					// in case image is already loaded before we add the onload event
					promise = Promise.resolve();
				}
				else {
					promise = new Promise((resolve) => {
						handler = () => { 	resolve(); };

						img.addEventListener('load', handler);
						// also resolve on loading errors, since we only care about the height change
						// and not if a specific image was loaded successfully or not
						img.addEventListener('error', handler);
					}).then(() => {
						// remove handlers, since we no longer need them
						img.removeEventListener('load', handler);
						img.removeEventListener('error', handler);
					});
				}
				promises.push(promise);
			});

			return Promise.all(promises);
		}
      <?php } ?>
	};

<?php if ($version === 1) { ?>
window.addEventListener('load', () => {
	app.loadImages()
		.then(items => {
			app.renderList(items);
		});
});

<?php } else if ($version === 2) { ?>
window.addEventListener('load', () => {
	app.loadImages()
		.then(items => {
			let id = app.getId();

			app.renderList(items);
			app.select(id);
		});
});

<?php } else if ($version === 3) { ?>
window.addEventListener('load', () => {
	app.loadImages()
		.then(items => {
			app.renderList(items);

			return app.queryAllImagesSettled('ul img');
		})
		.then(() => {
			let id = app.getId();

			app.select(id);
		});
});
<?php }  ?>
</script>
</head>

<body>
<h1>Example 1: Check if all images of a container element are loaded</h1>
<p>Read the full article <a href="https://hacks.mozilla.org/" target="_blank">Promises: Two useful examples</a> on
	hacks.mozilla.org</p>
<?php if ($version === 1) { ?>
	<p><strong>Demo 1</strong> | <a href="promise-images.php?demo=2#7085">demo 2</a> | <a
			href="promise-images.php?demo=3#7085">demo 3</a><br>
		Load and render a list of images using the fetch API.</p>
<?php } elseif ($version === 2) { ?>
	<p><a href="promise-images.php?demo=1">demo 1</a> | <strong>demo 2</strong> | <a
			href="promise-images.php?demo=3#7085">demo 3</a><br>
		Scroll to a specific image using scrollIntoView() after rendering a list of images using the fetch API. Demo needs
		your cache to be cleared (Ctrl + Shift + R).</p>
<?php } elseif ($version === 3) { ?>
	<p><a href="promise-images.php?demo=1">demo 1</a> | <a href="promise-images.php?demo=2#7085">demo 2</a> | demo 3<br>
		Use Promise.all() to check if all images are loaded before scrolling a specific one into view after rendering a list
		of images using the fetch API. Demo needs your cache to be cleared (Ctrl + Shift + R).</p>
<?php } ?>
<div class="imgContainer"></div>
<p class="copyright">All <a href="../../../photo/photodb/photo-en.php?lang=en" target="_blank">photos by Simon
		Speich</a>, www.speich.net</p>
</body>
</html>