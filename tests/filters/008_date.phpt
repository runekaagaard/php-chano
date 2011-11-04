--TEST--
A generated testfile for the "date" filter.
--FILE--
<?php
date_default_timezone_set('UTC');
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '1135810800')));
foreach ($items as $i) echo $i->input->date('%d %B %Y');
--EXPECT--
28 December 2005
