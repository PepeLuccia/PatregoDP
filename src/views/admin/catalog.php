<?php

if(!$_INCLUDED) die();

if(isset($_GET['catalog'])){

	$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
	$count = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['count'] : 20;
	$offset = ($page-1)*$count;


	$orders = R::getAll("SELECT * FROM catalog WHERE status != ?", [
		"deleted"
	]);

	$statuses = [
		"enabled",
		"disabled",
		"moderation",
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
				<th>Фото</th>
				<th>Найменування</th>
				<th>Опис</th>
				<th>Ціна</th>
				<th>Статус</th>
				<th>Дата Модерації</th>
				<th>Дата Створення</th>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($orders)){ ?>
				<tr>
					<td colspan="8">Не знайдено</td>
				</tr>
			<?php }else { 
				foreach($orders as $o){?>
					<tr>
						<td><?=$o['id']?></td>
						<td><img src="../uploads/<?=$o['image']?>" alt="Image" style="max-height: 100px;display:block;margin: 0 auto;"></td>
						<td><?=$o['title']?></td>
						<td><?=$o['description']?></td>
						<td><?=$o['price']?></td>
						<td>
							<select name="status" data-id="<?=$o['id']?>" value="<?=$o['status']?>">
								<?php foreach($statuses as $k => $s){ ?>
									<option <?=$o['status'] == $s ? 'selected' : ''?> value="<?=$s?>"><?=$s?></option>
								<?php } ?>
							</select>
						</td>
						<td><?=isset($o['moderation_date']) ? date("d-m-Y H:i:s", $o['moderation_date']) : ''?></td>
						<td><?=isset($o['creation_date'])   ? date("d-m-Y H:i:s", $o['creation_date'])   : ''?></td>
					</tr>
				<?php }
			} ?>
		</tbody>
	</table>
	<form action="?">
		Сторінка: <input type="number" name="page" value="<?=$page?>" style="width:100px">
		<input type="hidden" name="count" value="<?=$count?>" style="width:100px">
		<input type="hidden" name="catalog" style="width:100px">
		<button type="submit">Перейти</button>
	</form>
</div>
<script>
	(function(){
		Array.from(document.querySelectorAll('[name="status"]')).map(q => 
			q.addEventListener('change', function(e){
				let id = this.dataset.id;
				fetch(`?update=catalog&id=${id}&value=${this.value}`)
				.then(e => e.text())
				.then(alert);
			}))
	})()
</script>
<?php
die();
}