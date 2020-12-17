<?php
/** ======================================================
# purpose : 출력(output)
# 클래스와 기능이 동일
# OutPane.class.php
----------------------------------------------------------*/
use Fus3\Out\OutPane;

function window_location($url,$msg=''){OutPane::window_location($url,$msg);}
function history_go($msg,$num=-1){OutPane::history_go($msg,$num);}
function window_close($msg=''){OutPane::window_close($msg);}
function opener_location($url){OutPane::opener_location($url);}
function input_prompt($title,$defaultval=''){OutPane::input_prompt($title,$defaultval);}
function window_confirm($msg,$true_url,$false_url=''){OutPane::window_confirm($msg,$true_url,$false_url);}
?>