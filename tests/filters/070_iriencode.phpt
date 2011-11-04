--TEST--
A generated testfile for the "iriencode" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'Sør-Trøndelag')));
foreach ($items as $i) echo $i->input->iriencode();
--EXPECT--
S%C3%B8r-Tr%C3%B8ndelag