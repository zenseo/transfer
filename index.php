<?php

$debug = 1;
if($debug > 0) {
	ini_set('error_reporting', E_ALL);
	ini_set ('display_errors', 1);
}


define("PATH_MODXTRANSFER", $_SERVER['DOCUMENT_ROOT'].'/assets/modules/transfer/'); 

require_once(PATH_MODXTRANSFER.'model/transfer.class.php');

$config = array(
  'domen' => 'www.stroy-b.ru',
);

$transfer = new transfer($config);

$option = isset($_POST['option']) ? intval($_POST['option']) : 0;

$options = array(
  0 => '',
  1 => 'Редиректы',
  2 => 'Перенести контент',
  3 => 'Создание страниц',
  4 => 'Парсинг текста',
  5 => 'Парсинг документа'
);
 
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>Перенос на MODX</title>

    <!-- Bootstrap core CSS -->
    <link href="/assets/modules/transfer/template/css/bootstrap.min.css" rel="stylesheet">
 
  </head>

  <body>
    <div class="container">

      <div class="page-header">
        <h1>Перенос на MODX</h1>
        <p class="lead">Скрипт переноса сайта на MODX.</p>
      </div>

      <h3>Выберите опцию:</h3>

<p>
<form action="" method="post">

  <select name="option" id="">
<?php

  foreach ($options as $key => $value) {
      if($option == $key) {
        echo '<option value="'.$key.'" selected>'.$value.'</option>';
      } else {
        echo '<option value="'.$key.'" >'.$value.'</option>';
      }
  }

?> 
  </select>

  <input class="btn" type="submit" name="submit" value="Выполнить" />

</form>

</p>

<hr />

<?



if($option == 1) {
 echo $transfer->set_redirect_url();
} else if($option == 2) {
 echo $transfer->set_parser_content();
} else if($option == 3) {
 echo $transfer->set_page();
} else if($option == 4) {
 echo $transfer->update_content_from_chunk();
} else if($option == 5) {
 echo $transfer->update_content_from_document();
}


?>
   <hr />  
   
    </div> <!-- /container -->
 
   </body>
</html>
