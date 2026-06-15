<?php

$t = 850291200;
$second = $t % 60;
$t = floor($t / 60);
$minute = $t % 60;
$t = floor($t / 60);
$hour = $t % 24;
$t = floor($t / 24);
$day = $t % 31 + 1;
$t = floor($t / 31);
$month = $t % 12 + 1;
$t = floor($t / 12);
$year = $t + 2000;
echo "Date: $year-$month-$day $hour:$minute:$second\n";
