--TEST--
Testing chaining capabilities.
--FILE--
<?php
date_default_timezone_set('UTC');
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = array(
    array('d' => 946684800),
    array('d' => 2234832748),
);
foreach (new Chano($items) as $i) {
    echo $i->d->date('c') . "\n";
    echo $i->d->date('c')->cut("2040") . "\n";
}
--EXPECT--
2000-01-01T00:00:00+00:00
2000-01-01T00:00:00+00:00
2040-10-26T02:52:28+00:00
-10-26T02:52:28+00:00
