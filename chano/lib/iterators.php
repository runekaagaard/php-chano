<?php

interface Chano_Iterators_Interface {
    /**
     * Is the $items passed to Chano a match for this iterator.
     *
     * @param mixed $items
     * @return bool
     */
    static function is_match($items);

    /**
     * Returns an instance of an Iterator with the passed $items.
     *
     * @param Iterator $items
     * @return Traversable
     */
    static function get_instance($items);
}

class Chano_Iterators_Array implements Chano_Iterators_Interface {
    static function is_match($items) {
        return is_array($items);
    }

    static function get_instance($items) {
        return new ArrayIterator($items);
    }
}
// Register this iterator with Chano.
Chano::register_iterator('Chano_Iterators_Array');

class Chano_Iterators_Iterator implements Chano_Iterators_Interface {
    static function is_match($items) {
        return $items instanceof Traversable;
    }

    static function get_instance($items) {
        // ArrayObjects can be used by foreach but cannot be used as an iterator
        // so we need to get the real iterator.
        if ($items instanceof ArrayObject)
            return $items->getIterator();
        else
            return $items;
    }
}
// Register this iterator with Chano.
Chano::register_iterator('Chano_Iterators_Iterator');

class Chano_Iterators_Object implements Chano_Iterators_Interface {
    static function is_match($items) {
        return $items instanceof stdClass;
    }

    static function get_instance($items) {
        return new ArrayIterator($items);
    }
}
// Register this iterator with Chano.
Chano::register_iterator('Chano_Iterators_Object');