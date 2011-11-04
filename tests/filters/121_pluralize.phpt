--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 0)));
foreach ($items as $i) echo $i->input->pluralize('y', 'ies');
--EXPECT--
ies
