<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=700">
<meta name="format-detection" content="telephone=no">
<title>Панель управления Roscomsos</title>
</head>

<body>
<?PHP
error_reporting(0);
session_start();
$password='123321123'; // Поменять пароль на свой


if (isset($_POST['pass']))
{ 
	$_SESSION['pass']='';
	
	if ($_POST['pass']==$password) $_SESSION['pass']=$_POST['pass'];
}

if (isset($_POST['update']))
{
	$arr=explode(chr(13).chr(10),  $_POST['list']);
 
	sort($arr);
	
	file_put_contents("./gosip.txt", implode(chr(13).chr(10),$arr));
	
	$gosip=array();	
	$gosip_short='';
	foreach ($arr as $i=>$str)
 	{
		$b=explode('.', $str);
		
		$gosip[$b[0].'.'.$b[1]].=$str.'|';
	}
	
	$gosip_file='|';
 	foreach ($gosip as $k=>$str)  $gosip_file.='|'.$k.'||'.$str;
	
	$gosip_short='|'.implode('|', array_keys($gosip)).'|';
 
    file_put_contents('gosip_short.data', $gosip_short);
 	file_put_contents('gosip.data', $gosip_file.'|');

	echo '<script>alert("Список обновлен");</script>';
}
elseif (isset($_POST['check_ip']))
{
	include_once("roscomsos.php");
	$Roscomsos=new Roscomsos();
	$check_gos_ip=$Roscomsos->check_ip($_POST['ip']); 

	if ($check_gos_ip==true) echo '<b>IP из реестра</b><br>'; else echo '<b>IP не найден в реестре</b><br>';
}

 
 
if ($_SESSION['pass']!=$password) 
{ 
  echo '<form id="pass" name="form" method="post"><input type="text" name="pass"> <input type="submit" name="ОК"></form>';
  exit;

}

  
?>
<a href="admin.php">Главная</a> | <a href="admin.php?show=check">Проверить IP</a> | <a href="admin.php?show=contact">Связь с нами</a><br><br>
<form id="form" name="form" method="post">
<?PHP 
if (!$_GET['show'])
{
?>
<textarea name="list" style="width:500px; height:600px;">
<?PHP
echo file_get_contents("./gosip.txt");


?>
</textarea><br>
<input name="update" type="submit" value="Обновить список">
<?PHP 
}
elseif ($_GET['show']=='check')
{
?>

<input name="ip" value=""> 
<input name="check_ip" type="submit" value="Проверить IP">
<br><br>
Ваш IP определяется как <?=$_SERVER['REMOTE_ADDR']?>. Если это не ваш IP, значит с настройками сервера что-то не так.
<?PHP 
}
elseif ($_GET['show']=='contact')
{
?>
Если вы хотите добавить IP в общую базу, пишите нам на adm@roscenzura.com, либо в <a href="http://roscenzura.com/threads/713/">теме скрипта</a>. 
<?PHP 
}
?>

</form>
<br><br><br><br><br><br>

Любые вопросы, замечания и предложения по скрипту пишите <a href="http://roscenzura.com/threads/713/">здесь</a>.
</body>
</html>