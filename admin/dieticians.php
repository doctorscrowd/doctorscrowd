<!DOCTYPE html>
<meta charset="utf-8">
<title>Doctors Crowd</title>
<link rel="stylesheet" href="css/bootstrap.css" type="text/css" media="all">
<?php
require_once '../sql.php';
$sql = new SQL();

if ($_POST) {
  $values = $_POST;
  mb_convert_variables('EUC-JP', 'UTF-8', $values);
  $query = 'INSERT INTO dieticians (id, "name", reading, email, password) VALUES (uuid_generate_v4(), $1, $2, $3, $4)';
  $hash = password_hash($values['password'], PASSWORD_DEFAULT);
  $sql->query($query, [$values['name'], $values['reading'], $values['email'], $hash]);
}
?>
<form method="post">
  <label for="name">名前</label><input name="name"><br>
  <label for="reading">かな</label><input name="reading"><br>
  <label for="email">メール</label><input type="email" name="email"><br>
  <label for="password">パスワード</label><input type="password" name="password"><br>
  <button type="submit">登録</button>
</form>
<hr>
<table border="1">
<?php
$query = 'SELECT * FROM dieticians ORDER BY reading';
$dieticians = $sql->query($query, []);

foreach ($dieticians as $dietician) {
  mb_convert_variables('UTF-8', 'EUC-JP', $dietician);
  echo '<tr>';
  echo '<td><img src="../img/', $dietician->id, '.jpg"></td>';
  echo '<td>', $dietician->reading, '<br>', $dietician->name, '</td>';
  echo '</tr>';
}
?>
</table>
