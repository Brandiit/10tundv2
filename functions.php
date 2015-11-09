<?php
    
    require_once("../config_global.php");
    require_once("./User.class.php");
    
    $database = "if15_romil_1";
    
    session_start();
    
    $mysqli = new mysqli($servername, $server_username, $server_password, $database);
    
    $User = new User($mysqli);
    
 ?>