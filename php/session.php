<?php
    session_start();
    if(!isset($_SESSION["id"])){
        header("Location: login");
        die();
    }else{
        $id = $_SESSION["id"];
    }
?>