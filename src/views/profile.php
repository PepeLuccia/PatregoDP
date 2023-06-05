<?php 
require_once "src/includes/db.ejs";

if(isset($_GET['load'])){
	$status_map = [
		"enabled" => "Активний",
		"moderation" => "На перевірці",
		"disabled" => "Не активний",
	];	
	if($_GET['load'] == "sells"){
		$orders = R::getAll("SELECT * FROM catalog WHERE uploaded_by = ? AND status != ?", [
			$_SESSION['logged_user']->id,
			"deleted"
		]);
		foreach($orders as $o){?>
			<tr>
				<td><?=$o['id']?></td>
				<td><a href="./product.ejs?id=<?=$o['id']?>&preview"><img src="uploads/<?=$o['image']?>" alt="Image" style="max-height: 100px;display:block;margin: 0 auto;"></a></td>
				<td><?=$o['title']?></td>
				<td><?=$o['description']?></td>
				<td><?=$o['price']?></td>
				<td class="item-<?=$o['status']?>"><?=$status_map[$o['status']]?></td>
				<td><?=isset($o['moderation_date']) && $o['status'] == "enabled" ? date("d-m-Y H:i:s", $o['moderation_date']) : ''?></td>
				<td><?=isset($o['creation_date'])   ? date("d-m-Y H:i:s", $o['creation_date'])   : ''?></td>
				<td>
					<a href="./buy.ejs?remove&id=<?=$o['id']?>&preview" class="btn btn-danger">Видалити</a>
				</td>
			</tr>
			<?php 
		} 
	}else if($_GET['load'] == "purchases"){
		$orders = R::getAll("SELECT * FROM catalog WHERE sold_to = ? AND status = ?", [
			(string)$_SESSION['logged_user']->id,
			"disabled"
		]);
		foreach($orders as $o){?>
			<tr>
				<td><?=$o['id']?></td>
				<td><a href="./product.php?id=<?=$o['id']?>&preview"><img src="uploads/<?=$o['image']?>" alt="Image" style="max-height: 100px;display:block;margin: 0 auto;"></a></td>
				<td><?=$o['title']?></td>
				<td><?=$o['description']?></td>
				<td><?=$o['price']?></td>
				<td class="item-enabled">Куплено</td>
				<td><?=isset($o['moderation_date']) && $o['status'] == "enabled" ? date("d-m-Y H:i:s", $o['moderation_date']) : ''?></td>
				<td><?=isset($o['creation_date'])   ? date("d-m-Y H:i:s", $o['creation_date'])   : ''?></td>
				<td>
				</td>
			</tr>
			<?php 
		} 
	}
	die();
}

?>
<!DOCTYPE html>
<html lang="en">
<title>Особистий кабінет | Patrego</title>
<?php 
require "src/includes/head.php"; 
require "src/content/header.php";
?>

<div class="profile-form container mt-4">
	<?php if ( isset($_SESSION['logged_user']) ) : ?>
		<div class="hello">
			Вітаємо, <?php echo $_SESSION['logged_user']->login, "!"; ?><br>
		</div>
		<div class="label">
			E-mail
		</div>
		<div class="email">
			<?php echo $_SESSION['logged_user']->email; ?>
		</div>
		<div class="label">
			Номер телефону
		</div>
		<div class="phonenum">
			<?php echo $_SESSION['logged_user']->phonenum; ?>
		</div>
		<div class="label">
			Дата реєстрації
		</div>
		<div class="reg_date">
			<?= date("d-m-Y H:i", $_SESSION['logged_user']->reg_date) ?>
		</div>
		<?php if((int)$_SESSION['logged_user']->status == 2) {?>
			<br><a class="logout_link" href="./admin">Адмін-панель</a>
		<?php } ?>
		<br><a class="logout_link" href="add.php">Додати товар</a>
		<br><br><a class="logout_link" href="logout.php">Вихід</a>
	<?php endif; ?>
</div>
<div class="container">
	<div class="profile-tabs">
		<div class="profile-tab" data-type="sells">Ваші товари</div>
		<div class="profile-tab" data-type="purchases">Історія Покупок</div>
	</div>
	<div class="profile-catalog">
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
					<th>Дія</th>
				</tr>
			</thead>
			<tbody id="data-content"></tbody>
		</table>
	</div>
</div>
<script>
	let active_tab = "";
	let loading = false;
	$('.profile-tab').on('click', function(e){
		let type = $(this).data('type');

		if(type == active_tab || loading) return;
		loading = true;
		$('.profile-tab').removeClass('active');
		$(this).addClass('active');

		let url = "?load="+type;
		$('#data-content').html('<tr><td colspan="9">Завантаження...</td></tr>');

		fetch(url)
		.then(e => e.text())
		.then(r => {
			setTimeout(function(){
				loading = false;
				if(!r.trim().length)
					r = '<tr><td colspan="9">Нічого не знайдено</td></tr>';
				$('#data-content').html(r)
			}, 500);
		});
	});
	$('.profile-tab')[0].click();
</script>

<?php require "src/content/footer.php";	?>
</html>