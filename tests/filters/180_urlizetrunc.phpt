--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'http://31characteruri.com/test/')));
foreach ($items as $i) echo $i->input->urlizetrunc(2);
--EXPECT--
<a href="http://31characteruri.com/test/" rel="nofollow">...</a>