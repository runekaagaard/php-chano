--TEST--
Testing that __call redirects to function on $this->v.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';

class myIterator implements Iterator {
    private $position = 0;
    private $array = array(
        array('title' => 'foo'),
        array('title' => 'bars'),
    );

    public function __construct() {
        $this->position = 0;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->array[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->array[$this->position]);
    }
}

foreach (new Chano(new myIterator) as $i) {
    echo $i->title->upper() . "\n";
    echo $i->title->length() . "\n";
}
--EXPECT--
FOO
3
BARS
4
