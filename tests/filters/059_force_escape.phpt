--TEST--
A generated testfile for the "forceescape" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => '<some html & special characters > here')));
foreach ($items as $i) echo $i->input->safe()->forceescape();
--EXPECT--
&lt;some html &amp; special characters &gt; here
