<?php 
require_once(__DIR__."/src/includes/db.php");

if(!isset($_SESSION['logged_user'])){
	header('Location: ./login.php');
	exit;
}

$errors = [];

if(isset($_GET['add'])){
	if(!isset($_SESSION['uploading'])){
		header('Location: ?');
		die();
	}


	if(!isset($_POST['title']) || !trim($_POST['title'])){
		$errors[] = "Назва не вказана";
	}elseif(!isset($_POST['city']) || !trim($_POST['city'])){
		$errors[] = "Місто не вказане";
	}elseif(!isset($_POST['description']) || !trim($_POST['description'])){
		$errors[] = "Опис не вказаний";
	}elseif(!isset($_POST['price']) || strpos('e', $_POST['price']) || (float)($_POST['price']) <= 0){
		$errors[] = "Невірна ціна";
	}elseif(!isset($_FILES['file'])){
		$errors[] = "Додайте зображення товару";
	}elseif($_FILES['file']['error']){
		$errors[] = "Помилка при завантаженні зображення [1]";
	}

	if(empty($errors)){
		$file = $_FILES['file'];
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$filename_hash = md5($file['name'].$file['size']);

		try{
			if(!file_exists(__DIR__."/uploads/$filename_hash.$extension"))
				move_uploaded_file($file['tmp_name'], __DIR__."/uploads/$filename_hash.$extension");

			$item                  = R::dispense('catalog');

			$item->title           = $_POST['title'];
			$item->city           = $_POST['city'];
			$item->description     = $_POST['description'];
			$item->price           = (float)$_POST['price'];
			$item->image           = "$filename_hash.$extension";
			$item->status          = "moderation";
			$item->moderation_date = time();
			$item->creation_date   = time();
			$item->uploaded_by     = $_SESSION['logged_user']->id;
			$item->sold_to         = 0;

			$id = R::store($item);
			unset($_SESSION['uploading']);
			header("Location: ./product.php?id=$id&preview");
			die();
		}catch(Exception $e){
			$errors[] = "Помилка при завантаженні зображення [2]";
		}
	}
}

$_SESSION['uploading'] = 1;

?>
<!DOCTYPE html>
<html lang="en">
<title>Додати товар | Patrego</title>
<?php 

require "src/includes/head.php"; 
require "src/content/header.php";

?>

<form action="?add" method="POST" enctype="multipart/form-data">
	<div class="container add-product">
		<div class="error" style="color:indianred"><?=array_shift($errors)?></div>

		<div>
			<div class="label">Назва: <br></div>
			<input type="text" class="form-control" name="title" required placeholder="HP 255 G6" value="<?=@$_POST['title']?>">
		</div>

		<div>
			<div class="label">Місто відправника: <br></div>
			<input type="text" class="form-control" name="city" required placeholder="Київ" value="<?=@$_POST['city']?>">
		</div>

		<div>
			<div class="label">Опис: </div>
			<textarea  name="description" class="form-control" required placeholder="Core i3, GTX1060, 8 RAM, 1TB ssd" style="resize: vertical; width:100%"><?=@$_POST['description']?></textarea>
		</div>

		<div>
			<div class="label">Ціна: </div>
			<input type="number" class="form-control" name="price" required placeholder="Ціна"  value="<?=@$_POST['number']?>">
		</div>

		<div>
			<div class="label">Фото: </div>
			<input type="file" class="form-control" name="file"  required placeholder="Назва" accept=".jpg, .jpeg, .png">
		</div>

		<img src="about:blank" alt="" id="preivew" style="display:none;max-height: 300px;">
		<br>
		<div><button type="submit" class="btn btn-success">Додати</button></div>

	</div>
</form>
<script>
	// preview image
	let preivew = document.getElementById('preivew');
	let file = document.querySelector('[name="file"]');

	file.addEventListener('change', function(e){
		if(!file.files.length)
			return preivew.style.display = "none";

		preivew.style.display = "block";
		preivew.src = window.URL.createObjectURL(file.files[0]);
	})
</script>


<?php require "src/content/footer.php";	?>
</html>