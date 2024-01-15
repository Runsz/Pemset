<?php
    $host = "localhost";
    $username = "root";
    $pw = "";
    $db_name = "lsp_pemset2";

    $db = mysqli_connect($host, $username, $pw, $db_name);

    if( !$db ){
        die("Gagal terhubung dengan database: " . mysqli_connect_error());
    }
?>