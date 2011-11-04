--TEST--
A generated testfile for the "wordwrap" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->wordwrap(2);
--EXPECT--
12
3
