--TEST--
Testing date locale
--FILE--
<?php
// Needs on ubuntu to pass: sudo locale-gen da_DK.utf8.
setlocale(LC_ALL, 'da_DK.utf8');
date_default_timezone_set('UTC');
require dirname(__FILE__) . '/../bootstrap.php';
$items = array(
    array('d' => "2000-01-01"),
    array('d' => new DateTime("2000-01-01")),
    array('d' => 946684800),
);
foreach (new Chano($items) as $i) echo $i->d->date('%a %d %b %Y') . "\n";
--EXPECT--
lør 01 jan 2000
lør 01 jan 2000
lør 01 jan 2000
