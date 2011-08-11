--TEST--
A generated testfile for the "urlize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'https://google.com')));
foreach ($items as $i) echo $i->input->urlize();
--EXPECT--
<a href="https://google.com" rel="nofollow">https://google.com</a>