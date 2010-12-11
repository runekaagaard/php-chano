<?php
$pattern = '#self.assertEqual\(([\n ]|)+([a-z0-9_]+)\(#Uis';
echo preg_replace_callback($pattern, function($ms) {
   #var_dump($ms); die;
   return "create_test(set_args('$ms[2]', ";
}, file_get_contents(realpath(dirname(__FILE__)) . '/tests.py'));