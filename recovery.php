<?php
	session_start();
	if (isset($_SESSION['user'])) {
		if($_SESSION['user'] != -1) {
			include("./settings/connect_datebase.php");
			
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
			while($user_read = $user_query->fetch_row()) {
				if($user_read[3] == 0) header("Location: user.php");
				else if($user_read[3] == 1) header("Location: admin.php");
			}
		}
 	}
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<meta charset="utf-8">
		<title> Восстановление пароля </title>
		
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=# class = "singin"><img src = "img/ic-login.png"/></a>
		
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">Электронная приемная комиссия</div>
					Пермского авиационного техникума им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class="input-error">
					<img src="img/ic-close.png" class="close" onclick="DisableError()"/>
					<img src = "img/ic-error.png"/>
					Непредвиденная ошибка.
					<div class="message">Указанный вами адрес электронной почты не существует в системе, проверьте правильность ввода данных.</div>
				</div>
			
				<div class="success" style="display: none;">
					<img src = "img/ic_success.png">
					<div class = "name">Успешно!</div>
					<div class = "description">
						На указанный вами адрес будет отправлено письмо с новым паролем.
					</div>
				</div>
			
				<div class = "login">
					<div class="name">Восстановление пароля</div>
				
					<div class = "sub-name">Почта (логин):</div>
					<div style="font-size: 12px; margin-bottom: 10px;">На указанную вами почту будет выслан новый пароль, для входа в систему.</div>
					<input name="_login" type="text" placeholder="E-mail@mail.ru"/>
					
					<input type="button" class="button" value="Отправить" onclick="LogIn()" style="margin-top: 0px;"/>
					<img src = "img/loading.gif" class="loading" style="margin-top: 0px;"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			var errorWindow = document.getElementsByClassName("input-error")[0];
			var loading = document.getElementsByClassName("loading")[0];
			var button = document.getElementsByClassName("button")[0];
			
			errorWindow.style.display = "none";
		
			function DisableError() {
				errorWindow.style.display = "none";
			}
			
			function EnableError() {
				errorWindow.style.display = "block";
			}
			
			function LogIn() {
				var _login = document.getElementsByName("_login")[0].value;
				loading.style.display = "block";
				button.className = "button_diactive";
				
				var data = new FormData();
				data.append("login", _login);
				
				// AJAX запрос
				$.ajax({
					url         : 'ajax/recovery.php',
					type        : 'POST', // важно!
					data        : data,
					cache       : false,
					dataType    : 'html',
					// отключаем обработку передаваемых данных, пусть передаются как есть
					processData : false,
					// отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
					contentType : false, 
					// функция успешного ответа сервера
					success: function (_data) {
						
						if(_data == -1) {
							EnableError();
							loading.style.display = "none";
							button.className = "button";
						} else {
							console.log("Пароль изменён, ID абитуриента: " +_data);
							document.getElementsByClassName('success')[0].style.display = "block";
							document.getElementsByClassName('description')[0].innerHTML = "На указанный вами адрес <b>"+_login+"</b> будет отправлено письмо с новым паролем.";
							
							document.getElementsByClassName('login')[0].style.display = "none";
						}
					},
					// функция ошибки
					error: function( ){
						console.log('Системная ошибка!');
						loading.style.display = "none";
						button.className = "button";
					}
				});
			}
		</script>
	</body>
</html>