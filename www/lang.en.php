<?php
/**
 * English interface
 *
 * @copyright 2015-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

return [
	'title'               => 'LaTeX equations for web',
	'meta-keywords'       => 'LaTeX, equations, SVG-images, formula, convert latex equations to images',
	'meta-description'    => 'Convert LaTeX equations to SVG images ready for publishing on the web.',
	'header'              => 'LaTeX equations for&nbsp;web',
	'equation editor'     => 'Online editor',
	'formula in latex'    => 'LaTeX expression',
	'image URL'           => 'Image&nbsp;URL:&nbsp;',
	'examples'            => 'Examples',
	'examples info'       => 'Here are LaTeX source examples and rendered pictures.',
	'add to editor'       => 'Add to editor',
	'link-s2'             => '&larr; S2 CMS',
	'link-faq'            => 'FAQ',
	'link-install'        => 'Embedding',
	'page-editor'         => 'Upmath: Markdown & LaTeX',
	'samples'             => [
		'integrals' => 'Integrals, roots and borders',
		'limits'    => 'Limits and sums',
		'chains'    => 'Continued fractions',
		'matrices'  => 'Matrices',
		'align'     => 'Multiline equations',
		'picture'   => '<code>Picture</code> environment',
		'xy-pics'   => 'Diagrams <code>xy-pic</code>',
	],
	'faq section'         => '
			<h2>FAQ</h2>

			<div class="info-text">
			<div class="question">
				<h3>What is “LaTeX”?</h3>
				<p>
					LaTeX is a computer typesetting system for complex documents.
					It is widely used in scientific publications, especially in physics and math.
					<a href="http://en.wikipedia.org/wiki/LaTeX">See details in Wikipedia</a>.
				</p>
			</div>

			<div class="question">
				<h3>What does this website exactly do?</h3>
				<p>
					The website converts mathematical expressions into pictures ready for use in web.
					You don’t have to create ones in a graphical editor and to upload somewhere.
					Add pictures to discussions in blogs or forums or send links via messengers.
				</p>
			</div>

			<div class="question">
				<h3>Is it for free?</h3>
				<p>Yep. In case of reasonable load, of course. If you obstruct other users, your requests will be blocked.</p>
			</div>

			<div class="question">
				<h3>How do I know if the service doesn’t stop working one day?</h3>
				<p>You don’t. But I use it in my own projects and I’m not going to close it.</p>
			</div>

			<div class="question">
				<h3>How is the math converted into pictures?</h3>
				<p>
					There is <a href="https://en.wikipedia.org/wiki/TeX_Live">Tex Live</a> installed on the server.
					It has been set up to work with modern web technologies.
				</p>
			</div>

			<div class="question">
				<h3>How to include LaTeX packages? I’d like to write chemical formulas and musical notation!</h3>
				<p>When processing pictures, only minimal package set is included. If there is a missing package,
				please write me a letter. Don’t forget to explain how the package will help other users.</p>
			</div>

			<div class="question">
				<h3>Should I type math in this equation editor?</h3>
				<p>Not necessarily. It’s convenient if you have only few formulas. Type large texts
				in the <a href="https://upmath.me/">Upmath editor with LaTeX and Markdown support</a>.
				Also, I’ve written a script for reach-math sites which allows you to use LaTeX directly in HTML source code.</p>
			</div>
			</div>
	',
	'embedding section 1' => '
			<h2>Embedding into websites</h2>

			<div class="info-text">
			<p>
				Authors of mathematical text may write LaTeX expressions right into HTML code.
				In order to be automatically converted, these expressions are to be written in dollar signs
				<code><span>$$</span>...$$</code>, and the following script must be included:
			</p>
	',
	'embedding section 2' => '
			<p>Here is the HTML code sample:</p>
	',
	'embedding section 2.1' => '
			<p>The rendered result:</p>
	',
	'embedding section 3' => '
			<div class="question">
				<p>In modern browsers, script loads pictures in vector format SVG and aligns formula baseline with surrounding text:</p>

				<p align="center"><img src="/i/baseline_en.png" alt="" width="400" height="230" class="screenshot" style="max-width: 90vw; max-height: 51.75vw;" /></p>
			</div>

			<p>
				Our <a href="http://susy.written.ru/">blog on theoretical physics</a> is based on this service.
			</p>
			</div>
	',
	'copyright section'   => <<<TEXT
				&copy; 2014&ndash;2020 <a href="https://written.ru/">Roman Parpalak</a>.
				<script>var mailto="roman%"+"40written.ru";document.write('Drop&nbsp;me&nbsp;a&nbsp;line: <a href="mailto:'+unescape(mailto)+'">' + unescape(mailto) + '</a>.');</script>
				&nbsp; &nbsp;
				<div class="likely" data-url="https://i.upmath.me/">
					<div class="facebook" title="Share">&nbsp;</div>
					<div class="vkontakte" title="Share">&nbsp;</div>
					<div class="twitter" data-via="r_parpalak" title="Tweet">&nbsp;</div>
				</div>
TEXT
	,
];
