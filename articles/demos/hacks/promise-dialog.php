<?php
$version = isset($_GET['demo']) ? (int)$_GET['demo'] : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Demo of a blocking modal dialog</title>
<link href="dialog-polyfill.css" rel="stylesheet" type="text/css">
<style>
    dialog {
        border: 1px solid #aaa;
        box-shadow: 0 5px 5px #000;
    }

    dialog menu {
        text-align: right;
    }

    dialog menu button {
        margin-left: 0.5em;
    }
</style>
<script type="text/javascript">
let byId = document.getElementById.bind(document),
	app = {};

	window.addEventListener('load', () => {});
</script>
</head>

<body>
<h1>Example 1: Blocking modal dialog</h1>
<p>Read the full article <a href="https://hacks.mozilla.org/" target="_blank">Promises: Two useful examples</a> on
	hacks.mozilla.org</p>
<p>The demo on this page simulates the blocking behavior of JavaScript's native
<a href="https://developer.mozilla.org/en/DOM/window.confirm">window.confirm()</a> method by using a Promise.</p>
<p><button type="button">show dialog</button></p>
<script src="dialog-polyfill.js"></script>
<script>
    class DialogConfirm {

        /**
         * Constructs the dialog
         * @param {HTMLElement} node node to append the dialog to
         * @param {Object} params
         * @param {String} params.title title
         * @param {String} params.content content
         * @param {String} params.labelOk label of OK button
         * @param {String} params.labelCancel label of Cancel button
         */
        constructor(node, params) {
            Object.assign(this, params);

            this._promise = {
                reject: null,
                resolve: null
            };
            this.domNode = null;
            this.containerNode = node;
            this.buttonOk = null;
            this.buttonCancel = null;
            this.labelOk = this.labelOk || 'OK';
            this.labelCancel = this.labelCancel || 'Cancel';
            this.content = this.content || null;
            this.title = this.title || 'Confirm dialog';
            this._create();
        }

        /**
         * Create the DOM of the dialog
         * @private
         */
        _create() {
            this.domNode = document.createElement('dialog');
            this.createTitle(this.title);
            if (this.content) {
                this.createContent(this.content);
            }
            this.createButtons();
            this.initEvents();
            this.containerNode.appendChild(this.domNode);
            dialogPolyfill.registerDialog(this.domNode);
        }

        /**
         * Create the title of the dialog
         * Creates a HTMLHeadingElement and appends it to the HTMLDialogElement
         * @param {String} title
         */
        createTitle(title) {
            let h2 = document.createElement('h2');

            h2.appendChild(document.createTextNode(title));
            this.domNode.appendChild(h2);
        }

        /**
         * Create the content of the dialog
         * Creates a HTMLDivElement and appends it to the HTMLDialogElement
         * @param {String} content
         */
        createContent(content) {
            let div = document.createElement('div');

            div.classList.add('dialog-content');
            div.innerHTML = content;
            this.domNode.appendChild(div);
        }

        /**
         * Create the button menu
         * Creates a HTMLMenuElement with an OK and Cancel button and appends it to the HTMLDialogElement
         */
        createButtons() {
            let menu = document.createElement('menu');

            this.buttonCancel = this.createButton(this.labelCancel);
            this.buttonOk = this.createButton(this.labelOk);
            menu.appendChild(this.buttonCancel);
            menu.appendChild(this.buttonOk);
            this.domNode.appendChild(menu);
        }

        /**
         * Create a button
         * Creates a HTMLButtonElement and returns it.
         * @param {String} label
         */
        createButton(label) {
            let button = document.createElement('button');

            button.setAttribute('type', 'button');
            button.appendChild(document.createTextNode(label));

            return button;
        }

        /**
         * Show the dialog
         * Shows the dialog and returns a promise, which is either resolved, when the OK button is pressed or
         * is rejected when the Cancel button is pressed.
         * @returns {Promise}
         */
        show() {
            this.domNode.showModal();

            return new Promise((resolve, reject) => {
                this._promise.resolve = resolve;    // store a reference we can call when clicking on the OK button
                this._promise.reject = reject;  // store a reference we can call when clicking on the Cancel button
            });
        }

        /**
         * Close the dialog
         */
        close() {
            this.domNode.close();
        }

        /**
         * Init button events
         */
        initEvents() {
            this.buttonCancel.addEventListener('click', () => {
                this.close();
                this._promise.reject();
            });
            this.buttonOk.addEventListener('click', () => {
                this.close();
                this._promise.resolve();
            });
        }
    }

    let dialog = new DialogConfirm(document.body, {
        title: 'Dialog demo',
        content: '<a href="https://hacks.mozilla.org/" target="_blank">hacks.mozilla.org</a> is a great source of information',
        labelOk: 'Visit website'
    });

    document.getElementsByTagName('button')[0].addEventListener('click', () => {
        dialog.show().then(
            // do something after the ok button was pressed
            () => {
                console.log(dialog.labelOk);
            },
            // do something after the cancel button was pressed
            () => {
                console.log(dialog.labelCancel);
            }
        );
    });
</script>
</body>
</html>