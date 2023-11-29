<?php
if (isset($_GET['ip'])) $_SERVER['REMOTE_ADDR']=$_GET['ip'];

require_once($_SERVER['DOCUMENT_ROOT']."/roscomsos/roscomsos.php");
$Roscomsos=new Roscomsos();
$check_gos_ip=$Roscomsos->check_ip($_SERVER['REMOTE_ADDR']);

if ($check_gos_ip==true) { echo  '<b>IP в черном списке</b><br><img src="http://roscenzura.com/upload/img/grey.jpg">'; } else echo 'Не наш клиент';

?>
<!DOCTYPE html>
<html id="XenForo" lang="ru-RU" dir="LTR" class="Public LoggedOut Sidebar  Responsive" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>

	<meta charset="utf-8" />
</head>

<body>
<form name="form">
Введите IP который надо проверить: <input name="ip" value=""> <input type="submit" value="ОК">

</form>
</body>
</html>