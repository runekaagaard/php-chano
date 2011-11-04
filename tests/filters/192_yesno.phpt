--TEST--
A generated testfile for the "yesno" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => NULL)));
foreach ($items as $i) echo $i->input->yesno('certainly','get out of town','perhaps');
--EXPECT--
perhaps
