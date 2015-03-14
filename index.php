<?php

<<<<<<< HEAD
$debug = 1;
=======
$debug = 0;
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b
if($debug > 0) {
	ini_set('error_reporting', E_ALL);
	ini_set ('display_errors', 1);
}

<<<<<<< HEAD

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
=======
define("PATH_MODXTRANSFER", $_SERVER['DOCUMENT_ROOT'].'/modx_transfer/'); 

require_once(PATH_MODXTRANSFER.'model/modx_transfer.class.php');

$modxtransfer = new modx_transfer($config);
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b
 
?>

<!DOCTYPE html>
<<<<<<< HEAD
<html lang="ru">
=======
<html lang="en">
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>Перенос на MODX</title>

    <!-- Bootstrap core CSS -->
<<<<<<< HEAD
    <link href="/assets/modules/transfer/template/css/bootstrap.min.css" rel="stylesheet">
=======
    <link href="modx_transfer/template/css/bootstrap.min.css" rel="stylesheet">
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b
 
  </head>

  <body>
    <div class="container">

      <div class="page-header">
        <h1>Перенос на MODX</h1>
        <p class="lead">Скрипт переноса сайта на MODX.</p>
      </div>

      <h3>Выберите опцию:</h3>

<p>
<<<<<<< HEAD
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

=======
	<select name="" id="">
		<option value=""></option>
		<option value="1">Показать ссылки</option>
		<option value="2">Перенести контент</option>
	</select>
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b
</p>

<hr />

<?

<<<<<<< HEAD


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

=======
echo $modxtransfer->set_redirect_url();
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b

?>
   <hr />  
   
    </div> <!-- /container -->
<<<<<<< HEAD
 
   </body>
=======


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
>>>>>>> 5506eb3dd2665c5b30cc3bfc7559ff15bfa6a02b
</html>
