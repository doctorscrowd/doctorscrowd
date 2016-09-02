<?php
if ($_POST) {
  require_once '../sql.php';
  
  $email = htmlspecialchars($_POST['email']);
  $password = htmlspecialchars($_POST['password']);
  $sql = new SQL();
  $query = 'SELECT * FROM dieticians WHERE email = $1';
  $dietician = $sql->query($query, [$email])[0];
  mb_convert_variables('UTF-8', 'EUC-JP', $dietician);
  
  if (password_verify($_POST['password'], trim($dietician->password))) {
    session_start();
    $_SESSION['id'] = $dietician->id;
    header('Location: index.php');
  }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<title>Doctors Crowd</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" media="all">
<title>DoctorsCrowd - Dietician</title>
<header class="container">
  <h1 class="h3">管理栄養士　管理画面</h1>
</header>
<div class="container">
  <form class="form form-group" method="post">
    <label for="email">メールアドレス</label><input class="form-control" name="email" value="ushio.kawamura@medcare.jp">
    <label for="password">パスワード</label><input class="form-control" type="password" name="password"><br>
    <button class="btn btn-primary" type="submit">ログイン</button>
  </form>
</div>
