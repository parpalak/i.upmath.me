<?php

/** @var string $formula */

?>
{$\displaystyle
\special{dvisvgm:bbox new formula}
\special{dvisvgm:raw<!--start {?x} {?y} -->}
<?php echo $formula; ?>
$
\special{dvisvgm:raw<!--bbox {?bbox formula} -->}}
