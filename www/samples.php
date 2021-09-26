<?php
/**
 * Equation samples for the main page.
 *
 * @copyright 2014-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

$samples = [];

$samples['integrals'] = ['height' => '78.0781px', 'text' => <<<'TEX'
\boxed{
  \int\limits_{-\infty}^{\infty}
  e^{-x^2} \, dx = \sqrt{\pi}
}
TEX
];

$samples['limits'] = ['height' => '55.3984px', 'text' => <<<'TEX'
\gamma \overset{\text{def}}{=}
\lim\limits_{n \to \infty}
  \left(
     \sum\limits_{k=1}^n {1 \over k}
     - \ln n
  \right)
\approx 0.577
TEX
];

$samples['align'] = ['height' => '99.1797px', 'text' => <<<'TEX'
\begin{align*}
 y &= x^4 + 4 =\\
   &= (x^2+2)^2 - 4x^2 \le\\
   &\le (x^2+2)^2
\end{align*}
TEX
];

$samples['matrices'] = ['height' => '99.1797px', 'text' => <<<'TEX'
A_{m,n} = \begin{pmatrix}
a_{1,1} & a_{1,2} & \cdots & a_{1,n} \\
a_{2,1} & a_{2,2} & \cdots & a_{2,n} \\
\vdots  & \vdots  & \ddots & \vdots  \\
a_{m,1} & a_{m,2} & \cdots & a_{m,n}
\end{pmatrix}
TEX
];

$samples['chains'] = ['height' => '150.359px', 'text' => <<<'TEX'
e = 2 + \cfrac{1}{
  1 + \cfrac{1}{
  2 + \cfrac{2}{
  3 + \cfrac{3}{
  4 + \cfrac{4}{\ldots}
}}}}
TEX
];

$samples['picture'] = ['height' => '36.5px', 'text' => <<<'TEX'
\begin{picture}(76,20)
\put(0,0){$A$}
\put(69,0){$B$}
\put(14,3){\line(1,0){50}}
\put(39,3){\vector(0,1){15}}
\put(14,3){\circle*{2}}
\put(64,3){\circle*{2}}
\end{picture}
TEX
];

$samples['xy-pics'] = ['height' => '98.0938px', 'text' => <<<'TEX'
\xymatrix{
  A \ar[r]^f \ar[d]_g &
  B \ar[d]^{g'} \\
  D \ar[r]_{f'} &
  C
}
TEX
];

$samples['tikz'] = ['height' => '107px', 'text' => <<<'TEX'
\begin{tikzpicture}\small
\def\r{1.8}
\coordinate[label=$A$] (A) at (0.5*\r,0.8*\r);
\coordinate[label=below:$B$] (B) at (-\r,0);
\coordinate[label=below:$C$] (C) at (\r,0);
\draw[thin] (A) -- node[above] {$c$}
   node[pos=0.03,below,inner sep=4] {$\alpha$}
   (B) -- (C) -- node[right] {$b$} (A);
\end{tikzpicture}
TEX
];

ob_start();
?>
<p>Магнитный момент $$\vec{\mathfrak{m}}$$, находящийся в начале координат, создает в точке $$\vec{R}_0$$ векторный потенциал</p>

<p>$$\vec{A} = {\vec{\mathfrak{m}}
\times \vec{R}_0 \over R_0^3}.$$(1)</p>
<?php

$samples_embedding['ru'][] = ob_get_clean();

ob_start();
?>
<p>Placed in the origin, magnetic moment $$\vec{\mathfrak{m}}$$ produces at point $$\vec{R}_0$$ magnetic vector potential</p>

<p>$$\vec{A} = {\vec{\mathfrak{m}}
\times \vec{R}_0 \over R_0^3}.$$(1)</p>
<?php

$samples_embedding['en'][] = ob_get_clean();
