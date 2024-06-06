<?php
//Das ist notwendig, damit deleteRecord ausgeführt wird.
require_once 'conf.php';
include_once 'database_functions.php';
global $conn;
//DELETE
if (isset($_GET['site'])) {
        $param = $_GET['site'];
        $separator = "?";
        if(str_contains($param,$separator) && !str_contains($param,"edit_id"))
        {
            $parts = explode($separator, $param);
            $paramName = getUrlParamName($parts[1]);
            $paramValue = getUrlParam($parts[1]);
            $tableName = getUrlParam($parts[2]);
            deleteRecord($conn, $tableName,$paramName, $paramValue);
            header('Location: index');
exit();
        }
}