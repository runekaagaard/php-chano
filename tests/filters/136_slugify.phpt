--TEST--
A generated testfile for the "slugify" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => ' Jack & Jill like numbers 1,2,3 and 4 and silly characters ?%.$!/')));
foreach ($items as $i) echo $i->input->slugify();
--EXPECT--
jack-jill-like-numbers-123-and-4-and-silly-characters