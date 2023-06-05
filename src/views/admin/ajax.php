<?php

if(!$_INCLUDED) die();

if(isset($_GET['update'])){
	$allowed = [
		"0",
		"1",
		"2",
		"enabled",
		"disabled",
		"moderation",
	];

	if(!isset($_GET['id']) || !isset($_GET['value'])){
		die("id or value not found");
	}else if(!in_array($_GET['update'], ['orders', 'users', 'catalog'])){
		die('Invalid type [orders, users, catalog]');
	}else if(!in_array($_GET['value'], $allowed)) die("Not allowed value");

	try{
		$e = R::load($_GET['update'], (int)$_GET['id']);
		$e->status = $_GET['value'];

		if($_GET['update'] == "order"){
			$e->moderation_date = time();
		}

		R::store($e);
		echo "Успішно збережено!";
	}catch(Exception $e){
		die("Something went wrong");
	}
	die();
}

?>