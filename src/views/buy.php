<?php  
require_once(__DIR__."/src/includes/db.php"); 

if(!isset($_SESSION['logged_user'])){ 
      header('Location: ./login.php'); 
      exit; 
} 

$uid = $_SESSION['logged_user']->id; 
$item_id = (int)$_GET['id']; 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<title>Покупка... | Patrego</title> 
<?php  

require "src/includes/head.php";  
require "src/content/header.php"; 

if(!R::count('catalog', "id = :item_id", [ 
      ":item_id" => $item_id 
])){?> 
      <div class="product"> 
            <div class="col-100">Товар не знайдений</div> 
            <div class="col-100 pointer bold" onclick="window.history.back()">« Назад</div> 
     </div> 
<?php }elseif(isset($_GET['remove'])){ 
      $order = R::load('catalog', (int)$_GET['id']); 
      if(R::count('catalog', "id = ? AND status = ?", [ 
            $item_id, 
            "deleted" 
     ])){ 
            $text = "Товар не знайдено!"; 
     }elseif((int)$order->uploaded_by != (int)$_SESSION['logged_user']->id){ 
            $text = "<span style=\"color:indianred\">Помилка! Ви не є власником цього товару</span>"; 
     }else{ 
            $order->status = "deleted"; 
            R::store($order); 
            $text = "Успішно видалено!"; 
     } 

?> 
<div class="product"> 
     <div class="col-100"><?=$text?></div> 
</div> 
<?php } else {
       $item = R::load('catalog', $item_id);
       $item->status = "disabled";
       $item->sold_to = $_SESSION['logged_user']->id; 
       R::store($item); 
       
       $order = R::dispense('orders'); 
       $order->user_id = $uid; 
       $order->item_id = $item_id; 
       $order->date = time(); 
       R::store($order); 
       ?> 
       <div class="product"> 
            <div class="col-100">Покупка успішна!</div> 
     </div> 
<?php } ?> 


<?php require "src/content/footer.php"; ?> 
</html>