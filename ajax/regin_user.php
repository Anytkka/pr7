<?php
    session_start();
    include("../settings/connect_datebase.php");
    include("../settings/log_functions.php");
    
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // ищем пользователя с таким логином
    $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
    $id = -1;
    
    if($user_read = $query_user->fetch_row()) {
        // проверка существования
        writeToLog("[REGISTRATION_FAILED] Пользователь с логином '$login' уже существует");
        echo $id;
    } else {
        // рег пользователя
        $mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$password."', 0)");
        
        // получаем его id
        $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
        $user_new = $query_user->fetch_row();
        $id = $user_new[0];
            
        if($id != -1) {
            $_SESSION['user'] = $id; 

            $Ip = $_SERVER["REMOTE_ADDR"];
            $DateStart = date(format: "Y-m-d H:i:s");

            # Пользователь зарег
            # 1. Создаём ему сессию
            $Sql = "INSERT INTO `session`(`IdUser`, `Ip`, `DateStart`, `DateNow`) VALUES ('{$id}', '{$Ip}', '{$DateStart}' , '{$DateStart}')";
            $mysqli->query(query: $Sql);

            $Sql = "SELECT `Id` FROM `session` WHERE `DateStart` = '{$DateStart}';";
            $Query = $mysqli->query(query: $Sql);
            $Read = $Query->fetch_assoc();
            $_SESSION["IdSession"] = $Read["Id"];

            # 2. Записываем событие регистрации
            $Sql = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`) VALUES ('{$Ip}','{$id}','{$DateStart}','00:00:00','Пользователь ($login) зарегистрировался и авторизовался.')";
            $mysqli->query(query: $Sql);
			
        # 3. Записать в файл log.txt
            writeToLog("[REGISTRATION_SUCCESS] Пользователь '$login' (ID: $id) успешно зарегистрировался");
        } else {
            writeToLog("[REGISTRATION_ERROR] Ошибка при регистрации пользователя '$login'");
        }
        echo $id;
?>