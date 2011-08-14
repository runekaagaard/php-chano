--TEST--
A generated testfile for the "date" filter.
--FILE--
<?php
date_default_timezone_set('UTC');
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => '1135810800')));
foreach ($items as $i) echo $i->input->date('%eth of %B');
--EXPECT--
28th of December
