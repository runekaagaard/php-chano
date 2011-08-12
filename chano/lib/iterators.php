<?php

class Chano_Iterators_Array {
    static function is_match($items) {
        return is_array($items);
    }

    static function get_instance($items) {
        return new ArrayIterator($items);
    }
}
Chano::register_iterator('Chano_Iterators_Array');

class Chano_Iterators_Iterator {
    static function is_match($items) {
        return $items instanceof Traversable;
    }

    static function get_instance($items) {
        return $items;
    }
}
Chano::register_iterator('Chano_Iterators_Iterator');

class Chano_Iterators_Object {
    static function is_match($items) {
        return $items instanceof stdClass;
    }

    static function get_instance($items) {
        return new ArrayIterator($items);
    }
}
Chano::register_iterator('Chano_Iterators_Object');