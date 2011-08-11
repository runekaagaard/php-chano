--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'http://www.google.co.uk/search?hl=en&q=some+long+url&btnG=Search&meta=')));
foreach ($items as $i) echo $i->input->urlizetrunc(20);
--EXPECT--
<a href="http://www.google.co.uk/search?hl=en&q=some+long+url&btnG=Search&meta=" rel="nofollow">www.google.co.uk/...</a>
