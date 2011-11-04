--TEST--
A generated testfile for the "forceescape" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '<some html & special characters > here')));
foreach ($items as $i) echo $i->input->safe()->forceescape();
--EXPECT--
&lt;some html &amp; special characters &gt; here
