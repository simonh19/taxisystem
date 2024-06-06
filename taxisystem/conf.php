<?php
$server = 'localhost:3306'; // svt. <localhost:3306
$schema = 'taxi';
$user = 'admin';
$pwd = 'Admin123.';

try {
    $conn = new PDO('mysql:host=' .$server.';dbname='.$schema.';charset=utf8',$user, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e){
    echo 'Eoor '.$e->getCode().': '. $e->getMessage();
}