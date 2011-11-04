--TEST--
A generated testfile for the "fixampersands" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'Jack & Jill & Jeroboam')));
foreach ($items as $i) echo $i->input->fixampersands();
--EXPECT--
Jack &amp; Jill &amp; Jeroboam