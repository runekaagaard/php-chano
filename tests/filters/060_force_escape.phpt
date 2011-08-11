--TEST--
A generated testfile for the "forceescape" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => '<some html & special characters > here ĐÅ€£')));
foreach ($items as $i) echo $i->input->forceescape();
--EXPECT--
&lt;some html &amp; special characters &gt; here Đ&Aring;&euro;&pound;
