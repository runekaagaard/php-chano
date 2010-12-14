--TEST--
A generated testfile for the "linebreaks" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->linebreaks();
--EXPECT--
<p>123</p>