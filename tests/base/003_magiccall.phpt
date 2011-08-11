--TEST--
Testing that __call redirects to function on $this->v.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';

class Model {
    static $i = 0;
    function getTitle() { return 'foo' . ++self::$i; }
}

$items = array(
    new Model,
    new Model,
);
foreach (new Chano($items) as $i) {
    echo $i->getTitle() . "\n";
    echo $i->getTitle()->upper() . "\n";
}
--EXPECT--
foo1
FOO2
foo3
FOO4

