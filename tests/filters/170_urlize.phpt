--TEST--
A generated testfile for the "urlize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'www.google.com')));
foreach ($items as $i) echo $i->input->urlize();
--EXPECT--
<a href="http://www.google.com" rel="nofollow">www.google.com</a>