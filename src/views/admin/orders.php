<?php

if(!$_INCLUDED) die();

if(isset($_GET['orders']) || (!isset($_GET['users']) && !isset($_GET['catalog']))){

	$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
	$count = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['count'] : 20;
	$offset = ($page-1)*$count;


	$orders = R::getAll("
		SELECT
		orders.id,
		orders.date,
		catalog.title,
		catalog.description,
		catalog.price,
		catalog.image,
		users.login
		FROM orders
		INNER JOIN catalog
		ON catalog.id = orders.item_id

		INNER JOIN users
		ON users.id = orders.user_id

		");

		?>
		<style>
		body{
			width: 100%;
		}
	</style>
	<div class="container">
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>Фото</th>
					<th>Логін</th>
					<th>Найменування</th>
					<th>Ціна</th>
					<th>Дата</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($orders)){ ?>
					<tr>
						<td colspan="6">Не знайдено</td>
					</tr>
				<?php }else { 
					foreach($orders as $o){?>
						<tr>
							<td><?=$o['id']?></td>
							<td><img src="../uploads/<?=$o['image']?>" alt="Image" style="max-height: 100px;display:block;margin: 0 auto;"></td>
							<td><?=$o['login']?></td>
							<td><?=$o['title']?></td>
							<td><?=$o['price']?></td>
							<td><?=isset($o['date']) ? date("d-m-Y H:i:s", $o['date']) : ''?></td>
						</tr>
					<?php }
				} ?>
			</tbody>
		</table>

		<form action="?">
			Сторінка: <input type="number" name="page" value="<?=$page?>" style="width:100px">
			<input type="hidden" name="count" value="<?=$count?>" style="width:100px">
			<input type="hidden" name="orders" style="width:100px">
			<button type="submit">Перейти</button>
		</form>
	</div>
	<?php
	die();
}