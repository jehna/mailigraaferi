<?php
$data = explode("\n", file_get_contents("data.txt"));



$hours = array();
for($i = 0; $i <= 7 * 24; $i++) {
    $counter = new stdClass();
    $counter->val = 0;
    $counter->ID = $i;
    array_push(hours, $counter);
}

for($i = 0; $i < count(data); $i++) {    
    $hour = GetHourID(date);
    if(hours[hour]) hours[hour].val++;
}