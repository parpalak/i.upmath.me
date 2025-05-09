// noinspection ES6ConvertVarToLetConst

/**
 * Formula and format watcher.
 *
 * @type {{setFormat, setSource, getSource, setCallback, resetTimer}}
 */
var Renderer = (function () {
	var source, format,
		callback = function () {
		},
		timeout;

	function timerTick() {
		callback(source, format);
	}

	function update() {
		if (timeout) {
			clearTimeout(timeout);
		}
		timeout = setTimeout(timerTick, 300);
	}

	return {
		setFormat: function (value) {
			if (format !== value) {
				format = value;
				update();
			}
		},
		setSource: function (value) {
			if (source !== value) {
				source = value;
				update();
			}
		},
		getSource: function () {
			return source;
		},
		setCallback: function (f) {
			callback = f;
		}
	};
}());

function initTexEditor() {
	var source = document.querySelector('.editor-text'),
		preview = document.getElementById('editor-preview'),
		serviceURL = '//' + (window.location.host === 'tex.s2cms.ru' ? 'i.upmath.me' : window.location.host) + '/',
		oldOutput;

	/**
	 * Displays the rendered image after 300ms.
	 *
	 * @param text
	 * @param format
	 */
	function timerTick(text, format) {
		var encodedResult = encodeFormula(text);

		if (encodedResult && typeof encodedResult.then === 'function') {
			// Promise in modern browsers
			encodedResult.then(function(result) {
				processEncodedResult(result, text, format);
			});
		} else {
			processEncodedResult(encodedResult, text, format);
		}
	}

	function processEncodedResult(encodedResult, text, format) {
		var encodedText = encodedResult.text,
			output = text ? serviceURL + format + encodedResult.postfix + '/' + encodedText : '';

		if (output === oldOutput) {
			return;
		}
		oldOutput = output;

		document.forms['editor'].result.value = output;
		preview.src = output !== '' ? output : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		if (encodedText === 'f(x)' && location.pathname === '/') {
			return;
		}
		history && history.replaceState && history.replaceState(null, '', '/g' + encodedResult.postfix + '/' + encodedText);
	}

	/**
	 * Encodes formula to the deflate-compressed URL-safe Base64 if possible or to the URL-encoded.
	 * @param {string} text
	 * @returns {Promise<{postfix: string, text: string}> | {postfix: string, text: string}} - Object with the following properties:
	 *   - postfix: 'b' if compressed or '' otherwise
	 *   - text: compressed or URL-encoded formula
	 */
	function encodeFormula(text) {
		var urlEncoded = encodeURIComponent(text),
			defaultResult = { postfix: '', text: urlEncoded };

		if (typeof CompressionStream !== 'function' ||
			typeof Promise === 'undefined' ||
			!Promise.prototype.then) {
			return defaultResult;
		}

		try {
			var promise = (async function() {
				var stream = new Blob([text]).stream();
				var compressedStream = stream.pipeThrough(new CompressionStream('deflate-raw'));
				var compressedBlob = await new Response(compressedStream).blob();
				var compressedArray = new Uint8Array(await compressedBlob.arrayBuffer());
				return btoa(String.fromCharCode.apply(null, compressedArray))
					.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
			})();

			return promise.then(function (compressedBase64) {
				return (compressedBase64.length + 1 < urlEncoded.length) ?
					{postfix: 'b', text: compressedBase64} :
					defaultResult;
			}).catch(function () {
				return defaultResult;
			});
		} catch (e) {
			return defaultResult;
		}
	}

	// Connect to the renderer
	Renderer.setCallback(timerTick);

	function updateFormat() {
		Renderer.setFormat(document.getElementById('svg_radio').checked ? 'svg' : 'png');
	}

	function updateSource() {
		Renderer.setSource(source.value.trim());
	}

	var radios = document.forms['editor'].elements['format'];
	for (var i = radios.length; i--;) {
		radios[i].onchange = updateFormat;
	}
	source.addEventListener('propertychange', updateSource);
	source.addEventListener('keyup', updateSource);
	source.addEventListener('input', updateSource);
	source.addEventListener('paste', updateSource);

	// Renderer initialization
	updateFormat();
	updateSource();

	// Select the content of URL field on focus.
	document.querySelector('.editor-result').onfocus = function () {
		var that = this;
		setTimeout(function () {
			that.select();
		}, 10);
	};

	autosize(source);

	// Highlight textarea when the formula is invalid.
	preview.onerror = function () {
		if (Renderer.getSource() !== '') {
			setTimeout(function () {
				source.classList.remove('load-error');
				source.classList.add('load-error');
			}, 0);
		}
	};
	preview.onload = function () {
		source.classList.remove('load-error');
	};

	// Add sample formula to the textarea.
	var formulaButtons = document.querySelectorAll('.add-formula');
	var formulaButtonHandler = function () {
		var curFormula = source.value,
			sampleText = this.parentNode.parentNode.querySelector('.sample-source').innerText;

		if (curFormula !== '') {
			curFormula += "\n";
		}
		source.value = curFormula + sampleText;
		autosize.update(source);
		updateSource();
		document.getElementById('editor').scrollIntoView();
	};
	for (i = formulaButtons.length; i--;) {
		formulaButtons[i].onclick = formulaButtonHandler;
	}
}
