/**
 * Formula format watcher.
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

function initTexEditor(serviceURL) {
	var source = document.querySelector('.editor-text'),
		preview = document.getElementById('editor-preview'),
		oldOutput;

	/**
	 * Displays after 300ms the formula rendered.
	 *
	 * @param text
	 * @param format
	 */
	function timerTick(text, format) {
		var encodedText = encodeURIComponent(text),
			output = text ? serviceURL + format + '/' + encodedText : '';

		if (output === oldOutput) {
			return;
		}
		oldOutput = output;

		document.forms['editor'].result.value = output;
		preview.src = output;

		if (encodedText === 'f(x)' && location.pathname === '/') {
			return;
		}
		history && history.replaceState && history.replaceState(null, '', '/g/' + encodedText);
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
		var cur = source.value,
			sampleText = this.parentNode.querySelector('.sample-source').innerText;

		if (cur) {
			cur += "\n";
		}
		source.value = cur + sampleText;
		autosize.update(source);
		updateSource();
		scrollPage(document.getElementById('editor'));
	};
	for (i = formulaButtons.length; i--;) {
		formulaButtons[i].onclick = formulaButtonHandler;
	}

	function scrollPage(target) {
		scrollIt(target.offsetTop - 45, 300, 'easeInOutCubic');
	}

	var innerClickHandler = function (e) {
		if (location.pathname.replace(/^\//, '') !== this.pathname.replace(/^\//, '')) {
			return;
		}
		if (location.hostname !== this.hostname) {
			return;
		}

		var id = this.hash.slice(1);
		var target = document.getElementById(id) || document.querySelector('[name=' + id + ']');

		if (target) {
			scrollPage(target);
			e.stopPropagation();
			e.preventDefault();
		}
	};

	var insideLinks = document.querySelectorAll('a.inside');
	for (i = insideLinks.length; i--;) {
		insideLinks[i].onclick = innerClickHandler;
	}

	// https://pawelgrzybek.com/page-scroll-in-vanilla-javascript/
	function scrollIt(destination, duration, easing, callback) {
		duration = duration || 200;
		easing = easing || 'linear';

		var easings = {
			linear: function (t) {
				return t;
			},
			easeInQuad: function (t) {
				return t * t;
			},
			easeOutQuad: function (t) {
				return t * (2 - t);
			},
			easeInOutQuad: function (t) {
				return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
			},
			easeInCubic: function (t) {
				return t * t * t;
			},
			easeOutCubic: function (t) {
				return (--t) * t * t + 1;
			},
			easeInOutCubic: function (t) {
				return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
			},
			easeInQuart: function (t) {
				return t * t * t * t;
			},
			easeOutQuart: function (t) {
				return 1 - (--t) * t * t * t;
			},
			easeInOutQuart: function (t) {
				return t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t;
			},
			easeInQuint: function (t) {
				return t * t * t * t * t;
			},
			easeOutQuint: function (t) {
				return 1 + (--t) * t * t * t * t;
			},
			easeInOutQuint: function (t) {
				return t < 0.5 ? 16 * t * t * t * t * t : 1 + 16 * (--t) * t * t * t * t;
			}
		};

		var start = window.pageYOffset;
		var startTime = 'now' in window.performance ? performance.now() : new Date().getTime();

		var documentHeight = Math.max(document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);
		var windowHeight = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
		var destinationOffset = typeof destination === 'number' ? destination : destination.offsetTop;
		var destinationOffsetToScroll = Math.round(documentHeight - destinationOffset < windowHeight ? documentHeight - windowHeight : destinationOffset);

		if ('requestAnimationFrame' in window === false) {
			window.scroll(0, destinationOffsetToScroll);
			if (callback) {
				callback();
			}
			return;
		}

		function scroll() {
			var now = 'now' in window.performance ? performance.now() : new Date().getTime();
			var time = Math.min(1, ((now - startTime) / duration));
			var timeFunction = easings[easing](time);
			window.scroll(0, Math.ceil((timeFunction * (destinationOffsetToScroll - start)) + start));

			if (window.pageYOffset === destinationOffsetToScroll) {
				if (callback) {
					callback();
				}
				return;
			}

			requestAnimationFrame(scroll);
		}

		scroll();
	}
}

function initTexSite() {
	Stickyfill.add(document.querySelectorAll('.sticky'));
}
