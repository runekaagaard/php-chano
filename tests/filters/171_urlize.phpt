--TEST--
A generated testfile for the "urlize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'djangoproject.org')));
foreach ($items as $i) echo $i->input->urlize();
--EXPECT--
<a href="http://djangoproject.org" rel="nofollow">djangoproject.org</a>