--TEST--
Testing chaining capabilities.
--FILE--
<?php
date_default_timezone_set('UTC');
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = array(
    array('d' => "2000-01-01"),
    array('d' => new DateTime("2000-01-01")),
    array('d' => 946684800),
);
foreach (new Chano($items) as $i) echo $i->d->date('D d M Y') . "\n";
--EXPECT--
Sat 01 Jan 2000
Sat 01 Jan 2000
Sat 01 Jan 2000
