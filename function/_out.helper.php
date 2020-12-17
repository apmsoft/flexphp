<?php
/** ======================================================
| @UPDATE   : 2014-11-25
# purpose : 출력(output)
# 클래스와 기능이 동일
# Out.class.php
----------------------------------------------------------*/
use Fus3\Out\Out;

function out_($str){Out::prints($str);}
function out_ln($str){ Out::prints_ln($str); }
function out_array($arg){Out::prints_array($arg);}
function out_r($arg){Out::prints_r($arg);}
function out_json($arg){Out::prints_json($arg);}
function out_jsonobj($obj){Out::prints_json_obj($obj);}
function out_compress($data){Out::prints_compress($data);}
function out_xml($arg,$message){Out::prints_xml($arg,$message);}
function out_html($html){Out::prints_html($html);}
?>