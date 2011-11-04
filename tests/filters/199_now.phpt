--TEST--
Testing date locale
--FILE--
<?php
// Needs on ubuntu to pass: sudo locale-gen da_DK.utf8.
date_default_timezone_set('UTC');
require dirname(__FILE__) . '/../bootstrap.php';
define ('CHANO_TESTS_NOWTIME', 946684800);
$items = array(
    array('d' => "2000-01-01"),
    array('d' => new DateTime("2000-01-01")),
    array('d' => 946684800),
);
foreach (new Chano($items) as $i) echo $i->d->now('%B %e, %Y, %R %P') . "\n";
--EXPECT--
January  1, 2000, 00:00 am
January  1, 2000, 00:00 am
January  1, 2000, 00:00 am
