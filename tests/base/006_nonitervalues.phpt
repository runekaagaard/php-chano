--TEST--
Testing that items can be a stdClass.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
define ('CHANO_TESTS_NOWTIME', 946684800);
echo Chano::set(42)->add(2) . "\n";
echo Chano::set("joel isnt a slug")->upper()->truncatechars(6) . "\n";
echo Chano::set()->now("%B") . "\n";
echo Chano::set()->cycle('you', 'rock');
echo Chano::set()->cycle('you', 'rock');
--EXPECT--
44
JOE...
January
yourock