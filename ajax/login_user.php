<?php
    session_start();
    include("../settings/connect_datebase.php");
    include("../settings/log_functions.php");
    
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // ищем пользователя
    $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
    
    $id = -1;
    while($user_read = $query_user->fetch_row()) {
        $id = $user_read[0];
    }
    
    if($id != -1) {
        $_SESSION['user'] = $id;

        $Ip = $_SERVER["REMOTE_ADDR"];
        $DateStart = date(format: "Y-m-d H:i:s");

        # Пользователь авторизовался
        # 1. Создать сессию
        $Sql = "INSERT INTO `session`(`IdUser`, `Ip`, `DateStart`, `DateNow`) VALUES ('{$id}', '{$Ip}', '{$DateStart}' , '{$DateStart}')";
        $mysqli->query(query: $Sql);

        $Sql = "SELECT `Id` FROM `session` WHERE `DateStart` = '{$DateStart}';";
        $Query = $mysqli->query(query: $Sql);
        $Read = $Query->fetch_assoc();
        $_SESSION["IdSession"] = $Read["Id"];

        # 2. Записать событие авторизации
        $Sql = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`) VALUES ('{$Ip}','{$id}','{$DateStart}','00:00:00','Пользователь ($login) авторизовался.')";
        $mysqli->query(query: $Sql);
        
        # 3. Записать в файл log.txt
        writeToLog("[LOGIN_SUCCESS] Пользователь '$login' (ID: $id) успешно авторизовался");
    } else {
        writeToLog("[LOGIN_FAILED] Неудачная попытка авторизации с логином '$login'");
    }
    
    echo md5(string: md5(string: $id));
?>