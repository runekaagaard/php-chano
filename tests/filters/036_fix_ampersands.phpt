--TEST--
A generated testfile for the "fixampersands" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'Jack & Jill & Jeroboam')));
foreach ($items as $i) echo $i->input->fixampersands();
--EXPECT--
Jack &amp; Jill &amp; Jeroboam