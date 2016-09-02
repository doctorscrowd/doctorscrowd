<?php
$title = '患者情報';
include_once 'header.php';
$nav = '生活習慣';
include_once 'patient.php';

if ($_POST) {
  mb_convert_variables('EUC-JP', 'UTF-8', $_POST);
  $query = 'INSERT INTO habits (id, created, "user", member) VALUES (uuid_generate_v4(), NOW(), $1, $2) RETURNING id';
  $card = $sql->query($query, [$appointment->ptid, $id])[0];
  
  foreach ($_POST as $key => $value) {
    $key = Security::text($key);
    $value = Security::text($value);
    
    if ($key == 'job' || $key == 'family' || $key == 'cook' || $key == 'liquor' || $key == 'dislike' || $key == 'snack' || $key == 'health' || $key == 'feces-frequency' || $key == 'feces-detail') {
      $query = 'INSERT INTO texts (card, text, type) VALUES ($1, $2, $3)';
    }
    else {
      $query = 'INSERT INTO values (card, value, type) VALUES ($1, $2, $3)';
    }

    $sql->query($query, [$card->id, $value, $key]);
  }
}

$query = <<<EOQ
SELECT created, type, NULL AS value, text
FROM
  (SELECT * FROM habits WHERE "user" = $1 AND member = $2 ORDER BY created DESC LIMIT 1) habits
  JOIN texts ON habits.id = texts.card
UNION
SELECT created, type, value, NULL AS text
FROM
  (SELECT * FROM habits WHERE "user" = $1 AND member = $2 ORDER BY created DESC LIMIT 1) habits
  JOIN values ON habits.id = values.card
ORDER BY created DESC
EOQ;
$id = Security::text($_SESSION['id']);
$items = $sql->query($query, [$patient, $id]);
$elements = [];

foreach ($items as $item) {
  if ($item->value != NULL) {
    $elements[$item->type] = $item->value;
  }
  else {
    $elements[$item->type] = $item->text;
  }
}
?>
<form method="post" class="form">
  <div class="list-group">
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">職業</h3>
      <p class="list-group-item-text">
        <input name="job" type="text" class="form-control" placeholder="職種" value="<?=Security::coalesce($elements, 'job'); ?>">
      </p>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">家族</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-6">
            <input name="family" type="text" class="form-control" placeholder="家族構成" value="<?=Security::coalesce($elements, 'family'); ?>">
          </div>
          <div class="col-xs-6">
            <input name="cook" type="text" class="form-control" placeholder="調理担当者" value="<?=Security::coalesce($elements, 'cook'); ?>">
          </div>
        </div>
      </p>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">喫煙</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-4">
            <div class="input-group">
              <input name="smoking-from" type="number" class="form-control" value="<?=Security::coalesce($elements, 'smoking-from'); ?>">
              <span class="input-group-addon">歳</span>
            </div>
          </div>
          <div class="col-xs-1 h5 text-center">〜</div>
          <div class="col-xs-4">
            <div class="input-group">
              <input name="smoking-to" type="number" class="form-control" value="<?=Security::coalesce($elements, 'smoking-to'); ?>">
              <span class="input-group-addon">歳</span>
            </div>
          </div>
          <div class="col-xs-3 h5">
            <input name="smoking" type="checkbox" value="<?=Security::coalesce($elements, 'smoking'); ?>"> 継続中
          </div>
        </div>
      </p>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-5">
            <div class="input-group">
              <input name="tar" type="number" class="form-control" value="<?=Security::coalesce($elements, 'tar'); ?>">
              <span class="input-group-addon">mg</span>
            </div>
          </div>
          <div class="col-xs-1 h5 text-center">を</div>
          <div class="col-xs-6">
            <div class="input-group">
              <span class="input-group-addon">１日に</span>
              <input name="number" type="text" class="form-control" value="<?=Security::coalesce($elements, 'number'); ?>">
              <span class="input-group-addon">本</span>
            </div>
          </div>
        </div>
      </p>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">飲酒</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-4">
            <div class="input-group">
              <input name="drinking-from" type="number" class="form-control" value="<?=Security::coalesce($elements, 'drinking-from'); ?>">
              <span class="input-group-addon">歳</span>
            </div>
          </div>
          <div class="col-xs-1 h5 text-center">〜</div>
          <div class="col-xs-4">
            <div class="input-group">
              <input name="drinking-to" type="number" class="form-control" value="<?=Security::coalesce($elements, 'drinking-to'); ?>">
              <span class="input-group-addon">歳</span>
            </div>
          </div>
          <div class="col-xs-3 h5">
            <input class="drinking" type="checkbox" value="<?=Security::coalesce($elements, 'drinking'); ?>"> 継続中
          </div>
        </div>
      </p>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-4">
            <div class="input-group">
              <span class="input-group-addon">週に</span>
              <input name="times" type="number" class="form-control" value="<?=Security::coalesce($elements, 'times'); ?>">
              <span class="input-group-addon">回程度</span>
            </div>
          </div>
          <div class="col-xs-4">
            <select name="liquor" class="form-control" value="<?=Security::coalesce($elements, 'liquir'); ?>">
              <option>生ビール</option>
              <option>日本酒</option>
              <option>ワイン</option>
            </select>
          </div>
          <div class="col-xs-1 h5 text-center">を</div>
          <div class="col-xs-3">
            <div class="input-group">
              <input name="glasses" type="number" class="form-control" value="<?=Security::coalesce($elements, 'glasses'); ?>">
              <span class="input-group-addon">杯</span>
            </div>
          </div>
        </div>
      </p>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">嗜好</h3>
      <p class="list-group-item-text">
        <div class="container row">
          <input name="dislike" type="text" class="form-control" placeholder="好き嫌い" value="<?=Security::coalesce($elements, 'dislike'); ?>">
        </div>
      </p>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-6">
            <input name="snack" type="text" class="form-control" placeholder="間食" value="<?=Security::coalesce($elements, 'snack'); ?>">
          </div>
          <div class="col-xs-6">
            <input name="health" type="text" class="form-control" placeholder="健康のため" value="<?=Security::coalesce($elements, 'health'); ?>">
          </div>
        </div>
      </p>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">便通</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-6">
            <input name="feces-frequency" type="text" class="form-control" placeholder="頻度・タイミング" value="<?=Security::coalesce($elements, 'feces-frequency'); ?>">
          </div>
          <div class="col-xs-6">
            <input name="feces-detail" type="text" class="form-control" placeholder="硬さ・形状・色" value="<?=Security::coalesce($elements, 'feces-detail'); ?>">
          </div>
        </div>
      </p>
    </div>
  </div>
  <div class="text-right">
    <button type="submit" class="btn btn-primary">登録</button>
  </div>
  <hr>
</form>
