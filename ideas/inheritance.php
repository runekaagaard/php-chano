<?php

/*
Below an idea, showing a working example of how template inheritance
in pure non compiled PHP could be achieved. This example performs the
same kind of inheritance described at https://docs.djangoproject.com/en/dev/topics/templates/#template-inheritance.

It should be possible to implement the "super" feature as well.
*/
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);

class Chano {
	private static $active_block = null;
	private static $blocks = array();

	private static $inside_blocks = false;

	static function blocks() {
		self::$inside_blocks = true;
		ob_start();
	}

	static function endblocks() {
		self::$inside_blocks = false;
		ob_end_clean();
	}

	static function block($name) {
		ob_start();
		self::$active_block = $name;
	}

	static function block_is_on() {
		return !isset(self::$blocks[self::$active_block]);
	}

	static function endblock() {
		if (self::$inside_blocks) {
			if (empty(self::$blocks[self::$active_block]))
				self::$blocks[self::$active_block] = ob_get_clean();
		} else {
			if (isset(self::$blocks[self::$active_block])) {
				ob_end_clean();
				echo self::$blocks[self::$active_block];
			} else {
				echo ob_get_clean();
			}
		}
		
	}
}

require 'blog.php';