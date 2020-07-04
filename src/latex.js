/**
 * Replaces LaTeX formulas by pictures
 * @copyright 2012-2020 Roman Parpalak
 */

(function (w, d) {
	var prtcl = location.protocol,
		ntwPath = '//i.upmath.me',
		url = (prtcl === 'http:' || prtcl === 'https:') ? ntwPath : 'http:' + ntwPath,
		im = d.implementation,
		ext = im && im.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1") ? 'svg' : 'png';

	(function (fn) {
		var done = !1,
			top = !0,
			root = d.documentElement,
			w3 = !!d.addEventListener,

			add = w3 ? 'addEventListener' : 'attachEvent',
			rem = w3 ? 'removeEventListener' : 'detachEvent',
			pre = w3 ? '' : 'on',

			init = function (e) {
				if (e.type === 'readystatechange' && d.readyState !== 'complete') {
					return;
				}
				(e.type === 'load' ? w : d)[rem](pre + e.type, init, false);
				if (!done && (done = !0)) {
					fn.call(w, e.type || e);
				}
			},

			poll = function () {
				try {
					root.doScroll('left');
				} catch (e) {
					setTimeout(poll, 50);
					return;
				}
				init('poll');
			};

		if (d.readyState === 'complete') {
			fn.call(w, 'lazy');
		} else {
			if (d.createEventObject && root.doScroll) {
				try {
					top = !w.frameElement;
				} catch (e) {
				}
				if (top) {
					poll();
				}
			}
			d[add](pre + 'DOMContentLoaded', init, !1);
			d[add](pre + 'readystatechange', init, !1);
			w[add](pre + 'load', init, !1);
		}
	})(function () {
		processTree(d.body);
	});

	var aeImg = {};

	function trackLoading(eImg, path, isCentered) {
		if (aeImg[path]) {
			aeImg[path].push(eImg);
		} else {
			aeImg[path] = [eImg];

			fetch(path)
				.then(function (resp) {
					return resp.text();
				})
				.then(function (text) {
					var m = text.match(/postMessage\((?:&quot;|")([\d\|\.\-eE]*)(?:&quot;|")/), s;

					if (m && m[1]) {
						s = m[1].split('|');
						setSizes(path, s.shift(), s.shift(), s.shift(), isCentered);
					}
				});
		}
	}

	function setSizes(path, shift, x, y, isCentered) {
		var ao = aeImg[path], i = ao.length;

		for (; i--;) {
			ao[i].setAttribute('style', 'border:0; vertical-align:' + (isCentered ? 'top; margin: 0 0 0 auto;' : (-shift) + 'pt;'));
		}
	}

	function createImgNode(formula, isCentered) {
		var i = d.createElement('img'),
			path = url + '/' + ext + '/' + encodeURIComponent(formula);

		i.setAttribute('src', path);
		i.setAttribute('class', 'latex-' + ext);
		i.setAttribute('style', 'vertical-align:middle; border:0; opacity:0;');
		i.setAttribute('alt', formula);

		isCentered && (i.style.margin = '0 0 0 auto');

		try {
			trackLoading(i, path, isCentered);
		} catch (e) {
			i.style.opacity = '1';
		}

		return i;
	}

	var processTree = function (eItem) {
		var eNext = eItem.firstChild;

		while (eNext) {
			var eCur = eNext, sNn = eCur.nodeName;
			eNext = eNext.nextSibling;

			if (eCur.nodeType === 1 && sNn !== 'SCRIPT' && sNn !== 'TEXTAREA' && sNn !== 'OBJECT') {
				processTree(eCur);
				continue;
			}

			if (eCur.nodeType !== 3) {
				continue;
			}

			var as = (' ' + eCur.nodeValue + ' ').split(/\$\$/g),
				n = as.length, i, eResult;

			if (n === 3 &&
				(/^[ \t]$/.test(as[0])) &&
				(/^(?:[ \t]*\([ \t]*\S+[ \t]*\))?[ \t]*$/.test(as[2])) &&
				eItem.tagName === 'P' && eItem.childNodes.length <= 2
			) {
				eResult = createImgNode(as[1], 1);
				eItem.insertBefore(eResult, eCur);
				eItem.setAttribute('align', 'center');
				eItem.setAttribute('style', 'display: flex; align-items: center; flex-wrap: wrap;');

				var eSpan = d.createElement('span');
				eSpan.setAttribute('style', 'float:right; order: 1; margin: 0 0 0 auto;');
				eSpan.appendChild(d.createTextNode(as[2]));

				eItem.insertBefore(eSpan, eCur);
				eItem.removeChild(eCur);
			} else if (n > 2) {
				as[0] = as[0].substring(1);
				as[n - 1] = as[n - 1].substring(0, as[n - 1].length - 1);

				for (i = 0; i < n; i++) {
					if (i % 2) {
						if (i + 1 < n) {
							eResult = createImgNode(as[i]);

							if (/^[,.;!?)\-]/.test(as[i + 1])) {
								var nobr = d.createElement('span');
								nobr.style.whiteSpace = 'pre';
								nobr.appendChild(eResult);
								eResult = nobr;
								eResult.appendChild(d.createTextNode(as[i + 1].substring(0, 1)));

								as[i + 1] = as[i + 1].substring(1);
							}
						} else {
							eResult = d.createTextNode('$$' + as[i]);
						}
					} else {
						eResult = d.createTextNode(as[i]);
					}

					eItem.insertBefore(eResult, eCur);
				}

				eItem.removeChild(eCur);
			}
		}
	};

	w.S2Latex = {processTree: processTree};
})(window, document);
