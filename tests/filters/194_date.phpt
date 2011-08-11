--TEST--
Testing chaining capabilities.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = array(
    array('d' => 219823498374),
    array('d' => 2234832748),
);
foreach (new Chano($items) as $i) {
    echo $i->d->date('c') . "\n";
    echo $i->d->date('c')->cut("1904") . "\n";
}
--EXPECT--
1994-09-21T18:51:18+02:00
1994-09-21T18:51:18+02:00
1904-09-19T21:24:12+01:00
-09-19T21:24:12+01:00

