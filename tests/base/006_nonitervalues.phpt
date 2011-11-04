--TEST--
Testing that items can be a stdClass.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
define ('CHANO_TESTS_NOWTIME', 946684800);
echo Chano::with(42)->add(2) . "\n";
echo Chano::with("joel isnt a slug")->upper()->truncatechars(6) . "\n";
echo Chano::with()->now("%B") . "\n";
echo Chano::with()->cycle('you', 'rock');
echo Chano::with()->cycle('you', 'rock');
--EXPECT--
44
JOE...
January
yourock