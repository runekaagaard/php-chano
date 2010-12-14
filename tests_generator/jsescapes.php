<?php
echo '$from = ';
var_export(json_decode(file_get_contents('jsescape_from.json')));
echo ";\n\n";
echo '$to = ';
var_export(json_decode(file_get_contents('jsescape_to.json')));
echo ";\n\n";