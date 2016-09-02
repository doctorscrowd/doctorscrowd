<?php
session_start();

if (@preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $_SESSION['id']) !== 1) {
  header('Location: login.php');
}

require_once '../security.php';
$id = Security::text($_SESSION['id']);
?>
<!DOCTYPE html>
<title>Doctors Crowd | 管理栄養士 | 管理画面 | <?=Security::text($title); ?></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" media="all">
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<meta charset="utf-8">
<header class="container">
  <h1 class="h3"><span class="glyphicon glyphicon-cutlery text-primary"></span> 管理栄養士 管理画面</h1>
</header>
<div class="container">
  <ul class="nav nav-tabs">
    <li role="presentation"<?=($title == '予定表' ? ' class="active"' : ''); ?>><a href="index.php">予定表</a></li>
    <li role="presentation"<?=($title == '患者情報' ? ' class="active"' : ''); ?>><a href="habit.php">患者情報</a></li>
    <li role="presentation"<?=($title == 'SNS' ? ' class="active"' : ''); ?>><a href="sns.php">SNS</a></li>
    <li role="presentation"<?=($title == 'スケジュール' ? ' class="active"' : ''); ?>><a href="schedule.php">スケジュール</a></li>
    <li role="presentation"<?=($title == '空き時間' ? ' class="active"' : ''); ?>><a href="timetable.php">空き時間</a></li>
  </ul>
</div>
