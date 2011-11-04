--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '')));
try {
    foreach ($items as $i) echo $i->input->first();
} catch (Chano_TypeNotTraversableError $e) {
    die('caught!');
}
--EXPECT--
caught!