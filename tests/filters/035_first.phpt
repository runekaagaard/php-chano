--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'test')));
try {
    foreach ($items as $i) echo $i->input->first();
} catch (Chano_TypeNotTraversableError $e) {
    die('caught!');
}
--EXPECT--
caught!