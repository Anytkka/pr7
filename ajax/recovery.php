<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../settings/log_functions.php");
	
	$login = $_POST['login'];
	
	// ищем пользователя
	$query_user = $mysqli->query(query: "SELECT * FROM `users` WHERE `login`='".$login."';");
	
	$id = -1;
	if($user_read = $query_user->fetch_row()) {
		// создаём новый пароль
		$id = $user_read[0];
	}
	
	function PasswordGeneration() {
		// создаём пароль
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; // матрица
		$max=10; // количество
		$size=StrLen(string: $chars)-1; // Определяем количество символов в $chars
		$password="";
		
		while($max--) {
			$password.=$chars[rand(min: 0, max: $size)];
		}
		
		return $password;
	}
	
	if($id != -1) {
		//обновляем пароль
		$password = PasswordGeneration();
		// проверяем не используется ли пароль 
		$query_password = $mysqli->query(query: "SELECT * FROM `users` WHERE `password`= '".md5(string: $password)."';");
		while($password_read = $query_password->fetch_row()) {
			// создаём новый пароль
			$password = PasswordGeneration();
		}
		// обновляем пароль
		$mysqli->query(query: "UPDATE `users` SET `password`='".md5(string: $password)."' WHERE `login` = '".$login."'");
		
		$Ip = $_SERVER["REMOTE_ADDR"];
		$Date = date(format: "Y-m-d H:i:s");
		
		if(isset($_SESSION['user'])) {
			$current_user_id = $_SESSION['user'];
		} else {
			$current_user_id = $id;
		}
		
		$Sql = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`) VALUES ('{$Ip}','{$current_user_id}','{$Date}','00:00:00','Пароль пользователя {$login} (ID: {$id}) был сброшен. Новый пароль: {$password}')";
		$mysqli->query(query: $Sql);
		
		// Запись в файл log.txt
		writeToLog(message: "[PASSWORD_RECOVERY] Пароль пользователя '{$login}' (ID: {$id}) был сброшен. Новый пароль: {$password}");
		
		// отсылаем на почту (раскомментировать когда будет настроена отправка)
		// mail($login, 'Безопасность web-приложений КГАПОУ "Авиатехникум"', "Ваш пароль был только что изменён. Новый пароль: ".$password);
		
		echo $password;
	} else {
		// Запись в файл log.txt
		writeToLog(message: "[PASSWORD_RECOVERY_FAILED] Пользователь с логином '{$login}' не найден");
		echo "error";
	}
?>