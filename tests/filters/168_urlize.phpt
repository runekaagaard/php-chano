--TEST--
A generated testfile for the "urlize" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'http://google.com')));
foreach ($items as $i) echo $i->input->urlize();
--EXPECT--
<a href="http://google.com" rel="nofollow">http://google.com</a>