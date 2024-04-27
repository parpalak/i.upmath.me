<?php

include 'samples.php';
@include '../fingerprint.php';
@include '../host.php';

if (!defined('FINGERPRINT')) {
	define('FINGERPRINT', '');
}

$service_url = '//i.upmath.me/';
$script_url = $service_url.'latex.js';

$lang = ($_SERVER['HTTP_HOST'] ?? '') === 'tex.s2cms.ru' ? 'ru' : 'en';
$i18n = include 'lang.' . $lang . '.php';

$langLinks = [
	'ru' => '//tex.s2cms.ru/',
	'en' => '//i.upmath.me/',
];

function __ ($key) {
	global $i18n;
	return $i18n[$key] ?? '<span style="color:red;">Missing translation: ' . $key . '</span>';
}

if (str_starts_with($_SERVER['REQUEST_URI'], '/g/')) {
	$editor_content = urldecode(substr($_SERVER['REQUEST_URI'], 3));
} else {
	$editor_content = 'f(x)';
}

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<meta charset="utf-8">
<title><?php echo __('title'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="<?php echo __('meta-keywords'); ?>" />
<meta name="description" content="<?php echo __('meta-description'); ?>" />
<link rel="stylesheet" href="/css/style.min.css?<?php echo FINGERPRINT; ?>">
<link rel="icon" type="image/png" href="/favicon.png" />
<body>
	<div class="section" id="moto">
		<div class="section-content">
			<h1><?php echo __('header'); ?></h1>
			<div class="lang-block">
<?php

foreach ($langLinks as $linkLang => $linkUrl) {
	if ($linkLang !== $lang) {
?>
				<a class="lang-link" href="<?php echo $linkUrl; ?>"><?php echo $linkLang; ?></a>
<?php
	}
}

?>
			</div>
		</div>
	</div>

	<div class="header sticky">
		<div class="section-content header-content">
			<div class="nav">
				<a class="nav-item inside" href="#editor"><span class="nav-link"><?php echo __('equation editor'); ?></span></a><!--
			 --><a class="nav-item inside" href="#samples"><span class="nav-link"><?php echo __('examples'); ?></span></a><!--
			 --><a class="nav-item inside" href="#faq"><span class="nav-link"><?php echo __('link-faq'); ?></span></a><!--
			 --><a class="nav-item inside" href="#embedding"><span class="nav-link"><?php echo __('link-install'); ?></span></a><!--
			 --><a class="nav-item" href="https://upmath.me/"><span class="nav-link"><?php echo __('page-editor'); ?></span></a>
			</div>
		</div>
	</div>

	<div class="section" id="editor">
		<div class="section-content">
			<h2><?php echo __('equation editor'); ?></h2>
			<form name="editor">
				<p>
					<textarea class="editor-text" name="source" rows="5" placeholder="<?php echo __('formula in latex'); ?>"><?php echo $editor_content; ?></textarea>
					<br />
					<label><input type="radio" name="format" id="svg_radio" value="svg" checked />SVG</label>
					<label><input type="radio" name="format" value="png" />PNG</label>
				</p>
				<p class="preview-block"><img id="editor-preview" class="editor-preview" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="Editor preview"></p>
				<table class="url-line">
					<tr>
						<td class="url-cell"><?php echo __('image URL'); ?></td>
						<td class="url-cell" width="100%"><input type="text" class="editor-result" name="result" value="" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>

	<div class="section" id="samples">
		<div class="section-content">
			<h2><?php echo __('examples'); ?></h2>
		</div>
		<div class="section-samples">
<?php
foreach ($samples as $hint => $sample) {
?>
			<div class="sample-box">
				<h3 class="sample-title"><?php echo $i18n['samples'][$hint]; ?></h3>
				<div class="sample-rendered" style="height: <?php echo htmlspecialchars($sample['height']); ?>">
					<img src="<?php echo $service_url . 'svg/' . rawurlencode($sample['text']); ?>" alt="" loading="lazy">
				</div>
				<div class="sample-source"><?php echo htmlspecialchars($sample['text']); ?></div>
				<p><button class="add-formula"><?php echo __('add to editor'); ?></button></p>
			</div>
<?php
}
?>
		</div>
	</div>

	<div class="section" id="faq">
		<div class="section-content text-content">
			<?php echo __('faq section'); ?>
		</div>
	</div>

	<div class="section" id="embedding">
		<div class="section-content text-content">
			<?php echo __('embedding section 1'); ?>

			<pre class="script-code"><code>&lt;script src=&quot;<a href="<?php echo $script_url; ?>"><?php echo $script_url; ?></a>&quot;&gt;&lt;/script&gt;</code></pre>

<?php
foreach ($samples_embedding[$lang] as $hint => $sample) {
	$escaped = str_replace('$$', '<span>$$</span>', htmlspecialchars($sample));
?>
			<div class="question">
				<?php echo __('embedding section 2'); ?>
				<div class="sample-source sample-box"><?php echo $escaped; ?></div>
			</div>
			<div class="question">
				<?php echo __('embedding section 2.1'); ?>
				<div class="sample-rendered"><?php echo $sample; ?></div>
			</div>
<?php
}
?>
			<?php echo __('embedding section 3'); ?>
		</div>
	</div>

	<div class="section" id="copyright">
		<div class="section-content">
			<div>
				<?php echo __('copyright section'); ?>
			</div>
		</div>
	</div>

	<script src="/latex.js"></script>
	<script src="/js/scripts.min.js?<?php echo FINGERPRINT; ?>"></script>
	<script>
		(function ready(fn) {
			if (document.readyState != 'loading'){
				fn();
			} else {
				document.addEventListener('DOMContentLoaded', fn);
			}
		})(function () {
			initTexEditor();
		});
	</script>
</body>
</html>
