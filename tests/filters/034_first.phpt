--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => '')));
try {
    foreach ($items as $i) echo $i->input->first();
} catch (Chano_TypeNotArrayError $e) {
    die('caught!');
}
--EXPECT--
caught!