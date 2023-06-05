<?php

if(!$_INCLUDED) die();

if(isset($_GET['users'])){

	$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
	$count = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['count'] : 20;
	$offset = ($page-1)*$count;

	$users = R::getAll("SELECT * FROM users WHERE id > $offset LIMIT $count");

	$statuses = [
		0 => "Banned",
		1 => "User",
		2 => "Admin",
	];

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
				<th>Email</th>
				<th>Логін</th>
				<th>Номер телефону</th>
				<th>Дата реєстрації</th>
				<th>Статус</th>
				<th>Придбано</th>
				<th>Продано</th>
				<th>Сума Проданих</th>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($users)){ ?>
				<tr>
					<td colspan="6">Не знайдено</td>
				</tr>
			<?php }else { 
				foreach($users as $u){

					$total_sold = (int)R::getAll("SELECT count(*) as count FROM catalog WHERE uploaded_by = ? AND status = ?", [
						$u['id'],
						"disabled"
					])[0]['count'];

					$total_bought = (int)R::getAll("SELECT count(*) as count FROM catalog WHERE sold_to = ? AND status = ?", [
						$u['id'],
						"disabled"
					])[0]['count'];

					$total_sold_sum = (int)R::getAll("SELECT sum(price) as price FROM catalog WHERE uploaded_by = ? AND status = ?", [
						$u['id'],
						"disabled"
					])[0]['price'];

					?>
					<tr>
						<td><?=$u['id']?></td>
						<td><?=$u['email']?></td>
						<td><?=$u['login']?></td>
						<td><?=$u['phonenum']?></td>
						<td><?=date("d-m-Y H:i:s", $u['reg_date'])?></td>
						<td>
							<select name="status" data-id="<?=$u['id']?>" value="<?=$u['status']?>">
								<?php foreach($statuses as $k => $s){ ?>
									<option <?=(int)$u['status'] == $k ? 'selected' : ''?> value="<?=$k?>"><?=$s?></option>
								<?php } ?>
							</select>
						</td>
						<td><?=$total_sold?></td>
						<td><?=$total_bought?></td>
						<td><?=$total_sold_sum?></td>

					</tr>
				<?php }
			} ?>
		</tbody>
	</table>

	<form action="?">
		Сторінка: <input type="number" name="page" value="<?=$page?>" style="width:100px">
		<input type="hidden" name="count" value="<?=$count?>" style="width:100px">
		<input type="hidden" name="users" style="width:100px">
		<button type="submit">Перейти</button>
	</form>
</div>

<script>
	(function(){
		Array.from(document.querySelectorAll('[name="status"]')).map(q => 
			q.addEventListener('change', function(e){
				let id = this.dataset.id;
				fetch(`?update=users&id=${id}&value=${this.value}`)
				.then(e => e.text())
				.then(alert);
			}))
	})()
</script>

<?php
die();
}