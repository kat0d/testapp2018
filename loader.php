<?php
spl_autoload_register(function ($class_name) {
	include 'controllers/' . $class_name . '.php';
});
?>