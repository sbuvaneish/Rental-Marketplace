<?php

$a = [['brand'=>'nike', 'color'=>'blue'], ['brand'=>'nike', 'color'=>'red'], ['brand'=>'adidas', 'color'=>'blue'], ['brand'=>'adidas', 'color'=>'red']];
$b = ['brand'=>'nike', 'color'=>'red'];

$c = json_encode($a);
$d = json_encode($b);

$c = addslashes($c);
$d = addslashes($d);

$op = "";
exec("python2.7 /home/ec2-user/environment/project/Rental-Marketplace/sort.py \"{$c}\" \"{$d}\"", $op);
print_r($op);

