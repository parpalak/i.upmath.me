<?php

/** @var string $formula */
/** @var \S2\Tex\Tpl\PackageInterface[] $extraPackages */

?>
\documentclass[11pt]{article}
\usepackage[paperwidth=180in,paperheight=180in]{geometry}
\batchmode
\usepackage{amsmath}
\usepackage{amssymb}
\usepackage{stmaryrd}
\newcommand{\R}{\mathbb{R}}
<?php
if (!empty($extraPackages)) {
	foreach ($extraPackages as $package) {
		echo $package->getCode(), "\n";
	}
}
?>
\pagestyle{empty}

\setlength{\topskip}{0pt}
\setlength{\parindent}{0pt}
\setlength{\abovedisplayskip}{0pt}
\setlength{\belowdisplayskip}{0pt}

\begin{document}
{$\displaystyle
\special{dvisvgm:bbox new formula}
\special{dvisvgm:raw<!--start {?x} {?y} -->}
<?php echo $formula; ?>
$
\special{dvisvgm:raw<!--bbox {?bbox formula} -->}}
\end{document}
