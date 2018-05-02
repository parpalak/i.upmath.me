<?php

/** @var string $formula */

?>
\begin{minipage}{0.1in}
\strut\special{dvisvgm:bbox new formula}\special{dvisvgm:raw<!--start {?x} {?y} -->}<?php echo $formula; ?>

\special{dvisvgm:raw<!--bbox {?bbox formula} -->}
\end{minipage}
