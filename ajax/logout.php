<?php
    session_start();
    require_once("../settings/connect_datebase.php");
    include("../settings/log_functions.php");

    $IdUser = $_SESSION["user"];
    $IdSession = $_SESSION["IdSession"];

    $Sql = "SELECT `session`.*, `users`.`login` ".
    "FROM `session` `session` ".
    "JOIN `users` `users` ON `users`.`id` = `session`.`IdUser`". 
    "WHERE `session`.`Id` = {$IdSession}";

    $Query = $mysqli->query(query: $Sql);
    $Read = $Query->fetch_array();

    $TimeStart = strtotime(datetime: $Read["DateStart"]);
    $TimeNow = time();
    $Ip = $Read["Ip"];
    $TimeDelta = gmdate(format: "H:i:s", timestamp: ($TimeNow - $TimeStart));
    $Date = date(format: "Y-m-d H:i:s");
    $Login = $Read["login"];

    $Sql = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`) VALUES ('{$Ip}','{$IdUser}','{$Date}','{$TimeDelta}','Пользователь {$Login} покинул сайт')";
    $mysqli->query(query: $Sql);
    
   

    session_destroy();
?>