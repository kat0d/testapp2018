<?php
$login = htmlspecialchars($_SERVER['PHP_AUTH_USER']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Test App</title>

	<link rel="stylesheet" href="/_css/normalize.css" type="text/css">
	<link rel="stylesheet" href="/_css/main.css" type="text/css">
	<link rel="stylesheet" href="/_css/blog.css" type="text/css">
	<link rel="stylesheet" href="/_css/styles.css" type="text/css">
	<link rel="stylesheet" href="/_css/icons.css" type="text/css">
	<link rel="stylesheet" href="/_css/mobile.css" type="text/css">
	<link rel="stylesheet" href="/_css/animation.css" type="text/css">
	<link rel="stylesheet" href="/_css/tooltipster.bundle.css" type="text/css">

	<link rel="stylesheet" href="/_css/test.css" type="text/css">
	<script src="/_js/test.js" defer></script>
</head>
<body>
	<header id="header" class="white aic">
		<div>
			<?php
			if($result = $db->selectUser($_SERVER['PHP_AUTH_USER'])){
				// print_r($result);
				foreach ($result as $key => $value) {
					echo '<span class="m1"> ' . $key . ': <em class="green">'. $value . '</em></span>';
				}

				$user_points = $result['points'];
				$old_temp_gifts = $result['gifts'];
			}
			?>
		</div>
	</header>

	<div id="t131">

		<aside id="side_b">
			<p>
				Пароль <?php echo htmlspecialchars($_SERVER['PHP_AUTH_PW']); ?>
			</p>
			<p>
				Логин: <?php echo htmlspecialchars($_SERVER['PHP_AUTH_USER']); ?>
			</p>
			<p>
				Предыдущий логин: <?php echo htmlspecialchars($_REQUEST['OldAuth']); ?>
			</p>
			<form class="dfw" action='' method='post'>
				<input type='hidden' name='SeenBefore' value='1'>
				<input type='hidden' name='OldAuth' value="<?php echo htmlspecialchars($_SERVER['PHP_AUTH_USER']); ?>">
				<button type="submit">Авторизоваться под другим именем</button>
			</form>
		</aside>

		<main>

			<?php
			if(!is_null($old_temp_gifts)){
				echo '<div id="old_temp_gifts"></div>';
			}
			?>

			<section id="prizes">

				<div class="tac">
					<button id="get-prize" type="button">Получить случайный приз</button>
				</div>

				<div id="ajax-result"></div>
				<div id="for_token" data-tkn=""></div>

			</section>

		</main>

		<div id="side_a">

		</div>

	</div>

	<div>
		<h4>Все посылки в БД:</h4>
		<div class="tac">
			<div class="dib tal">
				<?php
				if($result = $db->query("SELECT * FROM gifts2post")){
				// print_r($result);
					foreach ($result as $result1){
						echo '<div>';
						foreach ($result1 as $key => $value) {
							echo '<span class="m1"> ' . $key . ': <em class="green">'. $value . '</em></span>';
						}
						echo '</div>';
					}
				}
				?>
			</div>
		</div>
	</div>

	<footer id="footer">
	</footer>

	<div id="z3_modal"></div>
</body>
</html>