--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'http://31characteruri.com/test/')));
foreach ($items as $i) echo $i->input->urlizetrunc(31);
--EXPECT--
<a href="http://31characteruri.com/test/" rel="nofollow">http://31characteruri.com/test/</a>