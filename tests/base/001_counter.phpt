--TEST--
Testing that counting works.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = array(
    array('d' => 1),
    array('d' => 1),
    array('d' => 1),
    array('d' => 1),
);
foreach (new Chano($items) as $i) echo $i->counter();
echo ":";
foreach (new Chano($items) as $i) echo $i->counter0();
echo ":";
foreach (new Chano($items) as $i) echo $i->revcounter();
echo ":";
foreach (new Chano($items) as $i) echo $i->revcounter0();
--EXPECT--
1234:0123:4321:3210
