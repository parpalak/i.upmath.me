<?php
/**
 * English interface
 *
 * @copyright 2015-2024 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

return [
	'title'                 => 'LaTeX equations for web',
	'meta-keywords'         => 'LaTeX, equations, SVG-images, formula, convert latex equations to images',
	'meta-description'      => 'Convert LaTeX equations to SVG images ready for publishing on the web.',
	'header'                => 'LaTeX equations for&nbsp;web',
	'equation editor'       => 'Online editor',
	'formula in latex'      => 'LaTeX expression',
	'image URL'             => 'Image&nbsp;URL:&nbsp;',
	'examples'              => 'Examples',
	'examples info'         => 'Here are LaTeX source examples and rendered pictures.',
	'add to editor'         => 'Add to editor',
	'link-s2'               => '&larr; S2 CMS',
	'link-faq'              => 'FAQ',
	'link-install'          => 'Embedding',
	'page-editor'           => 'Upmath: Markdown & LaTeX',
	'samples'               => [
		'integrals'  => 'Integrals, roots and borders',
		'limits'     => 'Limits and sums',
		'chains'     => 'Continued fractions',
		'matrices'   => 'Matrices',
		'align'      => 'Multiline equations',
		'picture'    => '<code>Picture</code> environment',
		'xy-pics'    => 'Diagrams <code>xy-pic</code>',
		'tikz'       => 'TikZ graphics',
		'tikz-plots' => 'TikZ plots',
	],
	'faq section'           => '
			<h2>FAQ</h2>

			<div class="info-text">
			<div class="question">
				<h3>What is “LaTeX”?</h3>
				<p>
					LaTeX is a computer typesetting system for creating complex documents.
					It is widely used in scientific publications, especially in physics and math.
					For more details, <a href="http://en.wikipedia.org/wiki/LaTeX">refer to Wikipedia</a>.
				</p>
			</div>

			<div class="question">
				<h3>What does this website do exactly?</h3>
				<p>
					This website converts mathematical expressions into web-ready pictures.
					You don’t need to create them in a graphical editor and to upload somewhere.
					You can easily add pictures to discussions on blogs or forums, or share links via messengers.
				</p>
			</div>

			<div class="question">
				<h3>Is it free to use?</h3>
				<p>
					Yes, it is free to use, as long as there is a reasonable load on the service.
					If your requests start to obstruct other users, your access may be blocked.
				</p>
			</div>

			<div class="question">
				<h3>Is it reliable?</h3>
				<p>Yes, it is quite reliable. You can view availability data collected by an <a href="https://stats.uptimerobot.com/YVrX5ik0A5">independent external service, UptimeRobot</a>.</p>
			</div>

			<div class="question">
				<h3>Is there a guarantee that the service won’t stop working?</h3>
				<p>No, there is no guarantee. However, I personally use it on my own websites and have no plans to shut it down.</p>
			</div>

			<div class="question">
				<h3>How are the formulas converted into images?</h3>
				<p>
					The server has <a href="https://en.wikipedia.org/wiki/TeX_Live">TeX Live</a> installed,
					which works in conjunction with modern web technologies.
				</p>
			</div>

			<div class="question">
				<h3>Cyrillic and hieroglyphic characters are not displayed in the formulas!</h3>
				<p>Perhaps the \text command can help you. For example, <code>Q_\text{плавления}>0</code> is displayed correctly: $$Q_\text{плавления}>0$$.</p>
			</div>

			<div class="question">
				<h3>How to include LaTeX packages? I want images with chemical formulas and musical notations!</h3>
				<p>When creating images, a minimal set of packages is included. If you need a specific package, please write me an email. Don’t forget to explain how the package will be useful to other users.</p>
			</div>

			<div class="question">
				<h3>Do I have to type all formulas in this editor?</h3>
				<p>Not necessarily. If you have only few formulas, it’s convenient to type them directly in the equation editor. For longer texts, you can use the <a href="https://upmath.me/">Upmath editor with LaTeX and Markdown support</a>. Additionally, I have developed a script for math-rich websites that enables you to use LaTeX directly in the HTML source code.</p>
			</div>
			</div>
	',
	'embedding section 1'   => '
			<h2>Embedding into websites</h2>

			<div class="info-text">
			<p>
				Authors of mathematical text can write LaTeX expressions directly in HTML code.
				To have them automatically converted, enclose the expressions within dollar signs
				<code><span>$$</span>...$$</code>, and include the following script:
			</p>
	',
	'embedding section 2'   => '
			<p>Here is a sample of HTML code:</p>
	',
	'embedding section 2.1' => '
			<p>The rendered result:</p>
	',
	'embedding section 3'   => '
			<div class="question">
				<p>In modern browsers, the script loads pictures in vector format SVG and aligns the formula’s baseline with the surrounding text:</p>

				<p align="center"><img src="/i/baseline_en.png" alt="" width="400" height="230" class="screenshot" style="max-width: 90vw; max-height: 51.75vw;" /></p>
			</div>

			<p>
				This service powers my <a href="https://susy.page/">blog on theoretical physics</a>.
			</p>
			</div>
	',
	'copyright section'     => <<<TEXT
				&copy; 2014&ndash;2024 <a href="https://parpalak.com/">Roman Parpalak</a>.
				<script>var mailto="roman%"+"40parpalak.com";document.write('Drop&nbsp;me&nbsp;a&nbsp;line: <a href="mailto:'+unescape(mailto)+'">' + unescape(mailto) + '</a>.');</script>
TEXT
	,
];
