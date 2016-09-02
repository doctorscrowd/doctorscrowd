<?php
$title = 'SNS';
include_once 'header.php';
require_once '../security.php';
require_once '../sql.php';

$sql = new SQL();
$today = date('Ymd');
$from = floor(date('Hi') / 10) * 10;
$to = $from + 10;
$id = Security::text($_SESSION['id']);

if ($from % 100 == 50) {
  $to = floor($to / 100) * 100 + 100;
}

$query = 'SELECT *, (yykymd = $2 AND keyyyktime BETWEEN $3 AND $4) AS current, (yykymd > $2 OR (yykymd = $2 AND keyyyktime > $4)) AS plan FROM tbl_yyk JOIN tbl_ptinf ON tbl_yyk.ptid = tbl_ptinf.ptid WHERE dietician = $1 ORDER BY current DESC, plan DESC, yykymd, keyyyktime LIMIT 1';
$appointments = $sql->query($query, [$id, $today, $from, $to]);

if (!$appointments) {
  exit;
}

$appointment = $appointments[0];
mb_convert_variables('UTF-8', 'EUC-JP', $appointment);
$query = 'SELECT * FROM messages JOIN tbl_ptinf ON messages.user = tbl_ptinf.ptid WHERE "user" = $1 AND member = $2 ORDER BY "timestamp" DESC LIMIT 20';
$patient = empty($_GET['patient']) ? $appointment->ptid : Security::text($_GET['patient']);
$messages = $sql->query($query, [$patient, $id]);
$messages = array_reverse($messages);
mb_convert_variables('UTF-8', 'EUC-JP', $messages);
$query = 'SELECT * FROM tbl_ptinf WHERE ptid = $1';
$patient = $sql->query($query, [$patient])[0];
mb_convert_variables('UTF-8', 'EUC-JP', $patient);
$today = new DateTime();
$birthday = DateTime::createFromFormat('Ymd', $patient->birthday);
$age = $today->diff($birthday);
?>
<div class="container" id="board">
  <h2 class="h3">
    <?=Security::text($patient->name); ?>
    <span class="small"><?=Security::text($patient->kananame); ?></span>
    <span class="small"><?=Security::text($age->y); ?>歳</span>
  </h2>
  <?php foreach ($messages as $message): ?>
  <hr>
  <div <?php if ($message->member != $id) { echo 'class="well"'; } ?>>
    <p><?=Security::text($message->body); ?></p>
    <div class="text-right"><?=Security::text(substr($message->timestamp, 0, -7)); ?></div>
    <?php if ($message->member != $id): ?>
    <div class="text-right"><?=Security::text($message->name); ?></div>
    <?php endif; ?>
  </div>
  <hr/>
  <?php endforeach; ?>
</div>
<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
  <form role="search">
    <div class="form-group container">
      <hr>
      <div class="input-group">
        <input id="message" class="form-control" type="search" placeholder="メッセージ">
        <span class="input-group-btn">
          <!--button type="button" class="btn"><span class="glyphicon glyphicon-pic"></span></button-->
          <button type="submit" class="btn"><span class="glyphicon glyphicon-send"></span></button>
        </span>
      </div>
    </div>
  </form>
</nav>
<script>
$('html,body').animate({ scrollTop:0 }, 'slow');
$('form').submit(function () {
  body = $('#message').val()
  $.ajax({
    contentType: 'application/json',
    data: JSON.stringify({'body': body, 'user': '<?=$appointment->ptid; ?>', 'member': '<?=$id; ?>'}),
    success: function (data, dataType) {
      location.reload()
    },
    type: 'POST',
    url: 'send.php'
  })
  return false
})
</script>
