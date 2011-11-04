--TEST--
A generated testfile for the "iriencode" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'fran%C3%A7ois%20%26%20jill')));
foreach ($items as $i) echo $i->input->iriencode();
--EXPECT--
fran%C3%A7ois%20%26%20jill