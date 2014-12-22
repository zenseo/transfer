<?php

$debug = 0;
if($debug > 0) {
	ini_set('error_reporting', E_ALL);
	ini_set ('display_errors', 1);
}

define("PATH_MODXTRANSFER", $_SERVER['DOCUMENT_ROOT'].'/modx_transfer/'); 

require_once(PATH_MODXTRANSFER.'model/modx_transfer.class.php');

$modxtransfer = new modx_transfer($config);
 
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>Перенос на MODX</title>

    <!-- Bootstrap core CSS -->
    <link href="modx_transfer/template/css/bootstrap.min.css" rel="stylesheet">
 
  </head>

  <body>
    <div class="container">

      <div class="page-header">
        <h1>Перенос на MODX</h1>
        <p class="lead">Скрипт переноса сайта на MODX.</p>
      </div>

      <h3>Выберите опцию:</h3>

<p>
	<select name="" id="">
		<option value=""></option>
		<option value="1">Показать ссылки</option>
		<option value="2">Перенести контент</option>
	</select>
</p>

<hr />

<?

echo $modxtransfer->set_redirect_url();

?>
   <hr />  
   
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
