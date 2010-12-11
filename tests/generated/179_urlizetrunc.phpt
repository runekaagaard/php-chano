--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'http://31characteruri.com/test/')));
foreach ($items as $i) echo $i->input->urlizetrunc(30);
--EXPECT--
<a href="http://31characteruri.com/test/" rel="nofollow">http://31characteruri.com/t...</a>