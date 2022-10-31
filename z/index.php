<?php
session_start();
error_reporting(0);
include("../core/functions.php");

if(@$_GET["code"] != ""){
    $code = secure_input(@$_GET["code"]);
    $url = gld('url',$code);
    if(!empty($url)){
        if(!isset($_SESSION['viewed'])){
            ulv($code);
            $_SESSION['viewed'] = 'isViewedz';
            $_SESSION['viewIP'] = $_SERVER['REMOTE_ADDR'];
        }
        header("Location: $url");
    }else{
        header("HTTP/1.1 403 Forbidden");
    }
}else{header("Location: ../");}

?>
