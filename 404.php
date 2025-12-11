<?php
	session_start();
	include("./settings/connect_datebase.php");
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Упс( Что-то пошло не так </title>
		
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
			<div class="left-menu">
				<div class = "puncts">
					<a href="index.php">
						<div class="img-main"></div>
						<div class="name">Главная</div>
					</a>
				</div>
				<?php
					if (isset($_SESSION['user'])) {
						if($_SESSION['user'] != -1) {
							echo '
								<div class = "puncts-log">
									<a href="login.php">
										<div class="img-statement"></div>
										<div class="name">Личный кабинет</div>
									</a>
								</div>
							';
						} else {
							echo '
								<div class = "puncts">
									<a href="login.php">
										<div class="img-statement"></div>
										<div class="name">Личный кабинет</div>
									</a>
								</div>
							';
						}
					} else {
						echo '
							<div class = "puncts">
								<a href="login.php">
									<div class="img-statement"></div>
									<div class="name">Личный кабинет</div>
								</a>
							</div>
						';
					}
				?>
				
				<?php
					if (isset($_SESSION['user'])) {
						if($_SESSION['user'] != -1) {
							$user_to_query = $mysqli->query("SELECT `roll` FROM `user` WHERE `id` = ".$_SESSION['user']);
							$user_to_read = $user_to_query->fetch_row();
							
							if($user_to_read[0] != 1) {
								// Проверяем кол-во сообщений входящих
								// получаем ID (и получаем отправленные от этого ID)
								$message_query = $mysqli->query("SELECT COUNT(`id`) FROM `messages` WHERE `user_to` = ".$_SESSION['user']." AND `visible` = 1");
								$message_read = $message_query->fetch_row();
								
								echo '
									<div class = "puncts-log">
										<form style="cursor:pointer;" action = "message.php" method="POST">
											<input type="hidden" name="id" value="1" />
											<a onclick="this.parentNode.submit()">
												<div class="img-message"></div>
												<div class="name">Сообщения ('.$message_read[0].')</div>
											</a>
										</form>
									</div>
								';
							} else {
								$message_query = $mysqli->query("SELECT COUNT(*) FROM `messages` WHERE  `user_to` = 1 AND `visible` = 1");
								$message_read = $message_query->fetch_row();
								
								echo '
									<div class = "puncts-log">
										<a href="dialogs.php"> 
											<div class="img-message"></div>
											<div class="name">Сообщения ('.$message_read[0].')</div>
										</a>
									</div>
								';
							}
						} 
					}
				?>
				<div class = "puncts">
					<a href="hostel.php">
						<div class="img-hostel"></div>
						<div class="name">Заявка на общежитие</div>
					</a>
				</div>
				<div class = "puncts">
					<a href="http://www.permaviat.ru/" target = "_blank">
						<div class="img-official-site"></div>
						<div class="name">Официальный сайт Авиатехникума</div>
					</a>
				</div>
				<div class = "puncts">
					<a href="contacts.php">
						<div class="img-contacts"></div>
						<div class="name">Контакты</div>
					</a>
				</div>
				<div class = "puncts">
					<a href="instruction.php">
						<div class="img-instruction"></div>
						<div class="name">Инструкция</div>
					</a>
				</div>
			</div>
			<div class="content">
				<div>
					<div class="not-found">404</div>
					<div class="not-found-desc">К сожалению запрашиваемая вами страница не найдена.</div>
				</div>
			</div>
		</div>
	
		
		<script>
			var oldIndexBackground = [1,1,1,1,1,1,1,1,1,1,1,1, 1]; // увеличить
		
			var imageBackground = document.getElementsByClassName('specialty')[0];
			var imageBackground_2 = document.getElementsByClassName('slider')[0];	
			
			
			
			var indexBackgroundImage = 1; // указываем индекс изображение (по началу 1)
			var timeSwap = 10; // указываем время для отображения одной картинки
						
			var DeltaX = 0; // задаём дельту прозрачности (по стандарту)
			var thisWidth =  getComputedStyle(imageBackground).width;
			var maxWidth = thisWidth.replace("px", "");
			var maxCountElements = 11;
			var idBlock = 0;
			
			document.getElementsByClassName('slider')[0].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[1].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[2].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[3].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[4].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[5].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[6].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[7].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[8].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[9].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[10].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[11].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			document.getElementsByClassName('slider')[12].style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%";
			
			function randomInteger(min, max) {
			  let rand = min + Math.random() * (max - min);
			  return Math.round(rand);
			}
			
			function SelectElements() {
				oldIndexBackground[idBlock] = indexBackgroundImage;
				
				idBlock = randomInteger(0, maxCountElements);
			
				imageBackground = document.getElementsByClassName('specialty')[idBlock]; // получаем объект (первый контейнер)
				imageBackground_2 = document.getElementsByClassName('slider')[idBlock]; // получаем объект (второй контейнер)
				indexBackgroundImage = oldIndexBackground[idBlock];
				
				setTimeout(StartChangeHaderImage, timeSwap*1000); // запускаем функцию старта смены изображения через заданное время
			}
			
			SelectElements();
			
				
			function StartChangeHaderImage() { // функция старта смены изображения
				imageBackground.style.backgroundPosition = "0px 50%"; // присваеваем нуливые координаты для первой картинки
				imageBackground_2.style.backgroundPosition = -maxWidth + " 50%"; // присваеваем координаты для второй кортинки, так, чтобы она была смещена
				imageBackground.style.backgroundImage = 'URL("img/specialty_'+(idBlock+1)+'(' + indexBackgroundImage + ').png")'; // задаем изображение для первой картинки в зависимости от индеса изображения		
								
				if(indexBackgroundImage == 3) indexBackgroundImage = 1; // если индекс дастиг максимального количества картинок (в моём случае 3), обнуляем
				else indexBackgroundImage++; // если нет, показываем следующее изображение
										
				imageBackground_2.style.backgroundImage = 'URL("img/specialty_'+(idBlock+1)+'(' + indexBackgroundImage + ').png")'; // задаём следующее изображение для второго объекта
				
				DeltaX = 0; // обнуляем координату движения
				ChangeHaderImage(); // запускаем функцию смены картинки
			}
			function ChangeHaderImage() { // функция смены картинки
				if(DeltaX + 10 < maxWidth) { // если кооржината движения меньше чем ширина самой картинки (тобиш 1920)
					DeltaX += 10; // двигаем картинку на 20 пикселей
					setTimeout(ChangeHaderImage, 10); // перезапускаем функцию через 10 милисекунд
					imageBackground.style.backgroundPosition = DeltaX+"px 50%"; // сдвигаем первое изображение на координату движения
					imageBackground_2.style.backgroundPosition = (-maxWidth) + DeltaX+"px 50%"; // сдвигаем второе изображение на координату движения
				} else { // если изображение сдвинулось максимум
					imageBackground.style.backgroundPosition = maxWidth+"px 50%"; // сдвигаем первое изображение на координату движения
					imageBackground_2.style.backgroundPosition = "0px 50%"; // сдвигаем второе изображение на координату движения
					SelectElements();
				}
			}
		</script>
	</body>
</html>