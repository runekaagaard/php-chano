--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'http://short.com/')));
foreach ($items as $i) echo $i->input->urlizetrunc(20);
--EXPECT--
<a href="http://short.com/" rel="nofollow">http://short.com/</a>