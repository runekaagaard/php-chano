--TEST--
A generated testfile for the "urlize" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'djangoproject.org')));
foreach ($items as $i) echo $i->input->urlize();
--EXPECT--
<a href="http://djangoproject.org" rel="nofollow">djangoproject.org</a>