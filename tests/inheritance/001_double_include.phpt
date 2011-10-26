--TEST--
Including the same file twice should yield the same results.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
require dirname(__FILE__) . '/b.php';
require dirname(__FILE__) . '/b.php';
--EXPECT--
B_TITLE B_CONTENT A_FOOTER B_TITLE B_CONTENT A_FOOTER