<?php
# microtime 2 datetime
function convert2MTDT($duration){
	$result = '';
	$hours = (int)($duration/60/60);
	$minutes = (int)($duration/60)-$hours*60;
	$seconds = (int)$duration-$hours*60*60-$minutes*60;

return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
?>