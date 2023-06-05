<div class="catalog">
<?php foreach(R::getAll( 'SELECT * FROM catalog WHERE status = "enabled"') as $row){ ?>
	<a href="./product.php?id=<?=$row['id']?>" class="catalog__item">
		<div class="catalog__item__image" style="background-image: url(./uploads/<?=$row['image']?>)"></div>
		<div class="catalog__item__title"><?=$row['title']?></div>
		<div class="catalog__item__price"><?=$row['price']?></div>
	</a>
	<?php } ?>
</div>