<?php
define('APP_CONST', '1');

/************************************
mysql database credentials
************************************/
$host = 'testapp';
$user = 'mysql';
$pass = 'mysql';
$name = 'testapp';
/************************************
mysql database credentials /z
************************************/

class DbController {

	protected $host;
	protected $user;
	protected $pass;
	protected $name;

	private $con;

	/*
	constructor
	*/
	public function __construct($host,$user,$pass,$name) {
		$this->host=$host;
		$this->user=$user;
		$this->pass=$pass;
		$this->name=$name;
		$this->DBconnect();
	}

	/*
	Establishes connection to MySQL server and selects a database
	*/
	private function DBconnect() {
		// Make connection to MySQL server
		$this->con = mysqli_connect($this->host, $this->user, $this->pass, $this->name);
	}

	/*
	sql query
	*/
	public function query($sql) {

		if ($result = mysqli_query($this->con, $sql)) {



			if (strpos($sql, 'SELECT') !== false) {

				// return mysqli_fetch_assoc($result);
				return mysqli_fetch_all($result, MYSQLI_ASSOC);

				// while($row = mysqli_fetch_assoc($result)) {
				// // while($row = mysqli_fetch_all($result, MYSQLI_ASSOC)) {
				// 	$json[] = $row;
				// }

				// return $json;


				// Free result set
				mysqli_free_result($result);
			}
		} else {
			echo "Error: " . mysqli_error($this->con);
		}

		// // Close connection
		// mysqli_close($this->con);

	}

		/*
	sql query
	*/
	public function selectUser($login) {

		if ($result = mysqli_query($this->con, "SELECT * FROM Users WHERE login='$login'")) {

			return mysqli_fetch_assoc($result);
				// Free result set
			mysqli_free_result($result);

		} else {
			echo "Error: " . mysqli_error($this->con);
		}

		// // Close connection
		// mysqli_close($this->con);

	}

	public function addUser($login){

		if ($result = mysqli_query($this->con, "INSERT INTO Users (login) VALUES ('$login')")) {
			// echo 'Создан новый пользователь';
		} else {
			echo "Error: " . mysqli_error($this->con);
		}

	}

	public function __destruct() {
		// Close connection
		mysqli_close($this->con);
	}

}

$db = new DbController($host,$user,$pass,$name);
?>