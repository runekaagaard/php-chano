--TEST--
A generated testfile for the "date" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => '1135810800')));
foreach ($items as $i) echo $i->input->date('jS \o\f F');
--EXPECT--
29th of December