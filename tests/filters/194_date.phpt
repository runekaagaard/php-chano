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
    echo $i->d->date('%r') . "\n";
    echo $i->d->date('%r')->cut("08:24") . "\n";
}
--EXPECT--
12:00:00 AM
12:00:00 AM
08:24:12 PM
:12 PM
