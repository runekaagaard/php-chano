--TEST--
A generated testfile for the "force_escape" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<some html & special characters > here')));
foreach ($items as $i) echo $i->input->force_escape();
--EXPECT--
&lt;some html &amp; special characters &gt; here