--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '<p>oneøæå <a href="#">twoøæå - three <br>fourøæå</a> fiveøæå</p>')));
foreach ($items as $i) echo $i->input->truncatewordshtml(2);
--EXPECT--
<p>oneøæå <a href="#">twoøæå ...</a></p>