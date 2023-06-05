<?php

$_INCLUDED = true;

require(__DIR__."/../src/includes/db.php");

if(!isset($_SESSION['logged_user']) || (int)$_SESSION['logged_user']->status != 2){
	header('Location: ../');
	die();
}

require('ajax.php');
?>
<!DOCTYPE html>
<html lang="en">
<title>Про нас | Patrego</title>
<style>

table{width:100%;border:1px solid #1b1b1b;border-collapse: collapse;}
table td:first-child,table th:first-child{text-align: center;}
form{width: 246px; display: flex; margin: 0 auto}
</style>
<?php 
require(__DIR__."/../src/includes/head.php"); 
?>

<nav class="admin">
	<a href="./" class="admin__title">Адмін-панель</a>
	<ul>
		<li <?=isset($_GET['orders']) || (!isset($_GET['users']) && !isset($_GET['catalog'])) ? 'class="active"': ''?>><a href="?orders">Замовлення</a></li>
		<li <?=isset($_GET['users']) ? 'class="active"': ''?>><a href="?users">Користувачі</a></li>
		<li <?=isset($_GET['catalog']) ? 'class="active"': ''?>><a href="?catalog">Товари</a></li>
		<li><a style="color: #ccc" href="../">Повернутись</a></li>
	</ul>
</nav>
<?php
require('users.php');
require('orders.php');
require('catalog.php');
require(__DIR__."/../src/content/footer.php");  ?>
</html>