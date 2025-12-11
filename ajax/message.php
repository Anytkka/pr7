<?php
    session_start();
    include("../settings/connect_datebase.php");
    include("../settings/log_functions.php");

    $IdUser = $_SESSION['user'];
    $Message = $_POST["Message"];
    $IdPost = $_POST["IdPost"];
    $IdSession = $_SESSION["IdSession"];

    $mysqli->query(query: "INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES ('{$IdUser}', '{$IdPost}', '{$Message}');");

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

    $Sql = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`) VALUES ('{$Ip}','{$IdUser}','{$Date}','{$TimeDelta}','Пользователь {$Login} оставил комментарий к записи [Id: {$IdPost}]: {$Message}')";
    $mysqli->query(query: $Sql);
    
    // Запись в файл log.txt
    writeToLog(message: "[COMMENT] Пользователь '{$Login}' (ID: {$IdUser}) оставил комментарий к записи [Id: {$IdPost}]: {$Message}");
?>