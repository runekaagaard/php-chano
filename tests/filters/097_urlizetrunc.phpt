--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->urlizetrunc(1);
--EXPECT--
123