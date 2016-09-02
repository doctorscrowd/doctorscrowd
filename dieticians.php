<!DOCTYPE html>
<meta charset="utf-8">
<title>Doctors Crowd</title>
<link rel="stylesheet" href="css/default.css" type="text/css" media="all">
<link rel="stylesheet" href="css/page.css" type="text/css" media="all">
<link rel="stylesheet" href="css/dieticians.css" type="text/css" media="all">
<header>
  <a href="index.php"><img src="img/return.png"></a>
  <a href="index.php"><img src="img/logo.png"></a>
</header>
<p class="text-center">管理栄養士をお選びください</p>
<ul>
<?php
require_once 'sql.php';
$sql = new SQL();
$query = 'SELECT * FROM dieticians ORDER BY reading';
$dieticians = $sql->query($query, []);
$current = new DateTime();

foreach ($dieticians as $dietician) {
  mb_convert_variables('UTF-8', 'EUC-JP', $dietician);
  echo '<a href="day.php?dietician=', $dietician->id, '&day=', $current->format('Ym'), '">';
  echo '<li class="list-item">';
  echo '<img src="img/', $dietician->id, '.jpg">';
  echo '<div>', $dietician->reading, '<br>', $dietician->name, '</div>';
  echo '<div class="clearfix"></div>';
  echo '</li>';
  echo '</a>';
}
?>
</ul>
