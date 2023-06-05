<?php
require_once "src/includes/db.php";

$product = R::getAll("SELECT * FROM catalog WHERE id = ? AND status = ? LIMIT 1", [
	(int)$_GET['id'],
	"enabled"
]);

if (isset($_GET['preview'])) {
	$product = R::getAll("SELECT * FROM catalog WHERE id = ? AND status = ? AND uploaded_by = ? LIMIT 1", [
		(int)$_GET['id'],
		"moderation",
		$_SESSION['logged_user']->id
	]);
}


$exists = !empty($product);

if ($exists) {
	$product    = $product[0];
	$my_product = (int)$product['uploaded_by'] == (int)$_SESSION['logged_user']->id;
	$data = R::getAll("SELECT login, phonenum, email from users WHERE id = ?", [
		(int)$product['uploaded_by']
	])[0];
	$product['login']    = $data['login'];
	$product['phonenum'] = $data['phonenum'];
	$product['email']    = $data['email'];

	if ($product['phonenum'][0] != '+')
		$product['phonenum'] = '+' . $product['phonenum'];
}

?>
<!DOCTYPE html>
<html lang="en">
<title><?= $exists ? $product['title'] : "Товар не знайдений" ?> | Patrego</title>
<?php
require "src/includes/head.php";
require "src/content/header.php";

if (empty($product)) { ?>
	<div class="product">
		<div class="col-100">Товар не знайдений</div>
		<div class="col-100 pointer bold" onclick="window.history.back()">« Назад</div>
	</div>

<?php } else { ?>

	<div class="product">
		<div class="col-40">
			<div class="product__image" style="background-image: url(/PatregoDP/uploads/<?= $product['image'] ?>)"></div>
			<div class="product__price"><?= $product['price'] ?></div>
			<?php if ($my_product) { ?>
				<a href="buy.php?remove&id=<?= $product['id'] ?>" class="product__buy confirm error" data-text="Ви впевнені, що хочете видалити цей товар?">Видалити</a>
			<?php } else { ?>
				<a href="buy.php?id=<?= $product['id'] ?>" class="product__buy confirm" data-text="Ви впевнені, що хочете придбати цей товар?">Придбати</a>
			<?php } ?>
		</div>

		<div class="col-60 is-flex">
			<div class="product__left">
				<div class="product__date">
					<?= date("d-m-Y H:i:s", $product['moderation_date']) ?>
				</div>

				<div class="product__title">
					<?= $product['title'] ?>
				</div>

				<div class="product__city">
					<div class="product__city__text">Відправка з:</div>&nbsp;<?= $product['city'] ?>
				</div>

				<div class="product__description">
					<div class="product__description__text">Опис:</div>&nbsp;<?= $product['description'] ?>
				</div>
			</div>
			<div class="product__right">
				<div class="product__seller">
					@<?= @$product['login'] ?>
				</div>
				<div class="product__phone">
					<a href="tel:<?= @$product['phonenum'] ?>"><?= @$product['phonenum'] ?></a>
				</div>
				<div class="product__email">
					<a href="mailto:<?= @$product['email'] ?>"><?= @$product['email'] ?></a>
				</div>
			</div>
		</div>

	</div>
	<script>
		Array.from(document.querySelectorAll('.confirm')).map(e => {
			e.addEventListener('click', function(e) {
				if (!confirm(this.dataset.text || "Ви впевнені?")) {
					e.preventDefault();
				}
			})
		})
	</script>
<?php } ?>



<?php require "src/content/footer.php";	?>

</html>