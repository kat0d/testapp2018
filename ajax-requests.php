<?php

defined( 'APP_CONST' ) or die( 'Restricted access' );

if($result = $db->selectUser(filter_var($_SERVER['PHP_AUTH_USER'],FILTER_SANITIZE_SPECIAL_CHARS))){

	$user_id = $result['id'];
	$user_login = $result['login'];
	$user_points = $result['points'];
	$user_cash = $result['cash'];
	$user_temp_gifts = $result['gifts'];

	if(!is_null($user_temp_gifts)){
		$user_gift_arr = explode('=', $user_temp_gifts);
		$user_gift_name = $user_gift_arr[0];
		$user_gift_value = $user_gift_arr[1];
	}

}

?>

<h4>
	<?php


	// add prize to temporary column(Gift) in Users table and take it from Cash or Prizes
	if( isset($_POST['get-prize']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['refer']) ){


		function prizeFunc() {

			global $db, $user_id;

			$random_num = random_int(1, 3);

			if($random_num == 1){ // money
				if($result = $db->query("SELECT Number FROM Cash")){

					$cash_total = $result[0][Number]; //total amount of money

					if($cash_total > 100){ // check amount of money
						$money_random_num = random_int(1, 100);
					} elseif ($cash_total > 2) {
						$money_random_num = random_int(1, $cash_total/2);
					} else {
						prizeFunc();
						return;
					}


					$prize_win = 'деньги: ' . $money_random_num;
					$prize_action_buttons = '
					<button id="money2bank" type="button">Перечислить на банковский счет</button>
					<button id="money2site" type="button">Перечислить на игровой счет</button>
					<button id="money2points" type="button">Конвертировать в баллы</button>
					';

					$for_db_gifts = 'Cash=' . $money_random_num;
					$cash_left = (int)$cash_total-(int)$money_random_num;
					$db->query("UPDATE Cash SET Number='$cash_left' WHERE id=1");

				}
			}

			if($random_num == 2){ // gifts
				if($result = $db->query("SELECT * FROM Gifts")){

					$gifts_arr = array();

					$total_all_gifts = 0;
					$i;
					foreach ($result as $value) { // check number of gifts
						$total_all_gifts += $value['Number'];
						if((int)$value['Number']){ // create array only with existed gifts
							array_push($gifts_arr, $value);
						}
					}
					if(!$total_all_gifts){
						prizeFunc();
						return;
					}

					$gifts_count = count($gifts_arr);
					$rand = random_int(0, $gifts_count - 1);

					$gift_id = $gifts_arr[$rand]['id'];
					$gift_name = $gifts_arr[$rand]['Gift'];
					$gift_total = $gifts_arr[$rand]['Number'];

					$prize_win = 'подарок: ' . $gift_name;
					$prize_action_buttons = '
					<button id="gift2user" type="button">Отправить по почте</button>
					';

					$for_db_gifts = 'Gifts=' . $gift_id;
					$gifts_left = (int)$gift_total-1;
					$db->query("UPDATE Gifts SET Number='$gifts_left' WHERE id='$gift_id'");

				}
			}

			if($random_num == 3){ // points

				$points_random_num = random_int(100, 200);

				$prize_win = 'Бонусные баллы: ' . $points_random_num;

				$prize_action_buttons = '
				<button id="point2site" type="button">Забрать</button>
				';

				$for_db_gifts = 'points=' . $points_random_num;

			}

			echo '<h1>Вы выиграли - <span class="green">' . $prize_win . '</span></h1>';
			echo '<div id="prize-action" class="win-action dfwsa fs">' . $prize_action_buttons . '<button id="abort-prize" type="button">Отказаться</button></div>';

			if($prize_win){
				$db->query("UPDATE Users SET gifts='$for_db_gifts' WHERE id='$user_id'");
			}

		}

		prizeFunc();

	}


	if( isset($_POST['prize-action']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['refer']) ){

		// remove prize from temporary column(Gift) in Users table and return to Cash or Prizes
		if(isset($_POST['abort-prize'])){

			if (strpos($user_temp_gifts, 'Cash') !== false) {

				if($result = $db->query("SELECT Number FROM $user_gift_name WHERE id=1")){
					$cur_cash = $result[0]['Number'];
					$new_cash = (int)$user_gift_value + (int)$cur_cash;
					$db->query("UPDATE $user_gift_name SET Number=$new_cash WHERE id=1");
				}

			} elseif (strpos($user_temp_gifts, 'Gifts') !== false) {

				if($result = $db->query("SELECT Number FROM $user_gift_name WHERE id='$user_gift_value'")){
					$cur_gifts = $result[0]['Number'];
					$new_gifts = (int)$cur_gifts + 1;
					$db->query("UPDATE $user_gift_name SET Number=$new_gifts WHERE id='$user_gift_value'");
				}

			}

			$db->query("UPDATE Users SET gifts=NULL WHERE id='$user_id'");

		}

		//transfer cash and points to site account
		if(isset($_POST['point2site'])){
			$user_new_points = (int)($user_points + $user_gift_value);
			$db->query("UPDATE Users SET points='$user_new_points' WHERE id='$user_id'");
			$db->query("UPDATE Users SET gifts=NULL WHERE id='$user_id'");
		}
		if(isset($_POST['money2points'])){
			$user_new_points = (int)($user_points + ($user_gift_value*2));
			$db->query("UPDATE Users SET points='$user_new_points' WHERE id='$user_id'");
			$db->query("UPDATE Users SET gifts=NULL WHERE id='$user_id'");
		}
		if(isset($_POST['money2site'])){
			$user_new_cash = (int)($user_cash + $user_gift_value);
			$db->query("UPDATE Users SET cash='$user_new_cash' WHERE id='$user_id'");
			$db->query("UPDATE Users SET gifts=NULL WHERE id='$user_id'");
		}
		//transfer cash from prize to bank
		if(isset($_POST['money2site'])){
			$user_new_cash = (int)($user_cash + $user_gift_value);
			include 'money2bank.php';
			// $db->query("UPDATE Users SET cash='$user_new_cash' WHERE id='$user_id'");
			$db->query("UPDATE Users SET gifts=NULL WHERE id='$user_id'");
		}
		//send gift from prize to user
		if(isset($_POST['gift2user'])){
			// $user_new_cash = (int)($user_cash + $user_gift_value);
			include 'gift2user.php';
			$db->query("UPDATE Users SET cash='$user_new_cash' WHERE id='$user_id'");
		}
		if(isset($_POST['gift2user2db'])){
			$gift2send = $user_gift_name. '=' .$user_gift_value;
			$db->query("INSERT INTO gifts2post (status, login, user_id, gifts, address) VALUES ('not send', '$user_login', '$user_id', '$gift2send', 'На Марс')");
			$db->query("UPDATE Users SET gifts=NULL WHERE id='$user_id'");
		}

	}


	?>
</h4>