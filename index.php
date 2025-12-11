<?php
	session_start();
	include("./settings/connect_datebase.php");
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<meta charset="utf-8">
		<title> WEB-безопасность </title>
		
		<link rel="stylesheet" href="style.css">
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
	</head>
	<body>
		<div class="top-menu">
			<a class="button" href = "./login.php">Войти</a>
		
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
			
			
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class="name">Новости:</div>
				
				<div>
					<?php
						$query_news = $mysqli->query("SELECT * FROM `news`;");
						while($read_news = $query_news->fetch_assoc()) {
							$QueryMessages = $mysqli->query("SELECT * FROM `comments` WHERE `IdPost` = {$read_news["id"]}");

							echo '
								<div class="specialty">
									<div class = "slider">
										<div class = "inner">
											<div class="name">'.$read_news["title"].'</div>
											<div class="description" style="overflow: auto;">
												<img src = "'.$read_news["img"].'" style="width: 50px; box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.12), 0 1px 5px 0 rgba(0,0,0,.2); float: left; margin-right: 10px;">
												'.$read_news["text"].'
												
											</div>
											<div class="messages">
												';
												while($ReadMessages = $QueryMessages->fetch_assoc()) {
													echo "<div>".$ReadMessages["Messages"]."</div>";
												}
											echo '</div>';

											
											if (isset($_SESSION['user'])) {
												echo 
													'<div class="messages" id="'.$read_news["id"].'">
														<input type="text">
														<div class="button" style="float: right; margin-top: 0px; margin-right: 0px;" onclick="SendMessage(this)">Отправить</div>
													</div>';
											}
											
										echo 
										'</div>
									</div>
								</div>
							';
						}
					?>
					<div class="footer">
						© КГАПОУ "Авиатехникум", 2020
						<a href=#>Конфиденциальность</a>
						<a href=#>Условия</a>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script>
		function SendMessage(sender) {
			let Message = sender.parentElement.children[0].value;
			let IdPost = sender.parentElement.id;
			if(Message == "") return;

			var Data = new FormData();
			Data.append("Message", Message);
			Data.append("IdPost", IdPost);
			
			$.ajax({
					url         : 'ajax/message.php',
					type        : 'POST',
					data        : Data,
					cache       : false,
					dataType    : 'html',
					processData : false,
					contentType : false, 
					success: function (_data) {
						console.log(_data);
						sender.parentElement.children[0].value = "";
						sender.parentElement.parentElement.children[2].innerHTML += "<div>" + Message + "</div>";

					},
					// функция ошибки
					error: function( ){
						console.log('Системная ошибка!');
					}
				});
		}
	</script>
</html>