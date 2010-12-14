--TEST--
A generated testfile for the "forceescape" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<some html & special characters > here')));
foreach ($items as $i) echo $i->input->forceescape();
--EXPECT--
&lt;some html &amp; special characters &gt; here