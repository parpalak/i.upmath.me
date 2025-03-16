<?php

/** @var bool $hasDvisvgmOption */
/** @var string $documentContent */
/** @var \S2\Tex\Tpl\PackageCollection $extraPackages */

/**
 * \documentclass[11pt,dvisvgm]{standalone}
 * %\usepackage[paperwidth=180in,paperheight=180in]{geometry}
 * \usepackage[paperwidth=180in, paperheight=180in,margin=0in]{geometry}
 * %\usepackage[a4paper, total={6in, 8in}]{geometry}
 * \standaloneconfig{crop=false}
 */

?>
\documentclass[11pt<?php if ($hasDvisvgmOption) { ?>,dvisvgm<?php } ?>]{article}
\usepackage[paperwidth=180in,paperheight=180in]{geometry}
\batchmode
\usepackage[utf8]{inputenc}
\usepackage{amsmath}
\usepackage{amssymb}
\usepackage{stmaryrd}
\newcommand{\R}{\mathbb{R}}
\newcommand{\lt}{<}
\newcommand{\gt}{>}

% Conditional definitions
\providecommand{\tg}{\operatorname{tg}}
\providecommand{\ctg}{\operatorname{ctg}}
\providecommand{\arctg}{\operatorname{arctg}}
\providecommand{\arcctg}{\operatorname{arcctg}}

\usepackage[verbose]{newunicodechar}

\newunicodechar{¬}{\ensuremath{\neg}}
\newunicodechar{Γ}{\ensuremath{\Gamma}}
\newunicodechar{γ}{\ensuremath{\gamma}}
\newunicodechar{λ}{\ensuremath{\lambda}}
\newunicodechar{φ}{\ensuremath{\varphi}}
\newunicodechar{ψ}{\ensuremath{\psi}}
\newunicodechar{ϕ}{\ensuremath{\varphi}}
\newunicodechar{ᵢ}{\ensuremath{{}_{i}}}
\newunicodechar{₀}{\ensuremath{{}_{0}}}
\newunicodechar{₁}{\ensuremath{{}_{1}}}
\newunicodechar{₂}{\ensuremath{{}_{2}}}
\newunicodechar{₃}{\ensuremath{{}_{3}}}
\newunicodechar{₄}{\ensuremath{{}_{4}}}
\newunicodechar{₅}{\ensuremath{{}_{5}}}
\newunicodechar{₆}{\ensuremath{{}_{6}}}
\newunicodechar{₇}{\ensuremath{{}_{7}}}
\newunicodechar{₈}{\ensuremath{{}_{8}}}
\newunicodechar{₉}{\ensuremath{{}_{9}}}
\newunicodechar{ₙ}{\ensuremath{{}_{n}}}
\newunicodechar{ℓ}{\ensuremath{\ell}}
\newunicodechar{→}{\ensuremath{\rightarrow}}
\newunicodechar{⇒}{\ensuremath{\supset}}
\newunicodechar{⇔}{\ensuremath{\Leftrightarrow}}
\newunicodechar{∅}{\ensuremath{\emptyset}}
\newunicodechar{∈}{\ensuremath{\in}}
\newunicodechar{∘}{\ensuremath{\circ}}
\newunicodechar{∙}{\ensuremath{\bullet}}
\newunicodechar{∧}{\ensuremath{\wedge}}
\newunicodechar{∨}{\ensuremath{\vee}}
\newunicodechar{∼}{\ensuremath{\sim}}
\newunicodechar{≠}{\ensuremath{\neq}}
\newunicodechar{≡}{\ensuremath{\equiv}}
\newunicodechar{⊃}{\ensuremath{\supset}}
\newunicodechar{⊕}{\ensuremath{\oplus}}
\newunicodechar{⊖}{\ensuremath{\ominus}}
\newunicodechar{⊢}{\ensuremath{\vdash}}
\newunicodechar{⊤}{\ensuremath{\top}}
\newunicodechar{⊥}{\ensuremath{\bot}}
\newunicodechar{⊻}{\ensuremath{\veebar}}
\newunicodechar{⟝}{\ensuremath{\vdash}}
\newunicodechar{⬓}{\ensuremath{\square}}
\newunicodechar{Σ}{\ensuremath{\sum}}
\newunicodechar{Π}{\ensuremath{\prod}}
\newunicodechar{ⱼ}{\ensuremath{{}_{j}}}

<?php

echo $extraPackages->getCode();

?>

\pagestyle{empty}

\setlength{\topskip}{0pt}
\setlength{\parindent}{0pt}
\setlength{\abovedisplayskip}{0pt}
\setlength{\belowdisplayskip}{0pt}

\begin{document}
<?php
foreach (['newwrite', 'openout'] as $disabledCommand) {
	echo '\\renewcommand{\\' . $disabledCommand . '}{\\errmessage{Command \\noexpand\\' . $disabledCommand . ' is disabled}}', "\n";
}
?>
<?php echo $documentContent; ?>
\end{document}
