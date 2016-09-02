<?php
$title = '患者情報';
include_once 'header.php';
?>
<div class="container">
<?php
require_once '../sql.php';
$sql = new SQL();
$today = date('Ymd');
$from = floor(date('Hi') / 10) * 10;
$to = $from + 10;

if ($from % 100 == 50) {
  $to = floor($to / 100) * 100 + 100;
}

$query = 'SELECT *, (yykymd = $2 AND keyyyktime BETWEEN $3 AND $4) AS current, (yykymd > $2 OR (yykymd = $2 AND keyyyktime > $4)) AS plan FROM tbl_yyk JOIN tbl_ptinf ON tbl_yyk.ptid = tbl_ptinf.ptid WHERE dietician = $1 ORDER BY current DESC, plan DESC, yykymd, keyyyktime LIMIT 1';
$appointments = $sql->query($query, [$id, $today, $from, $to]);

if (!$appointments) {
  exit;
}

$appointment = $appointments[0];

if (!empty($_GET['patient'])) {
  $id = Security::text($_GET['patient']);
  $query = 'SELECT *, \'f\' AS current, \'f\' AS plan FROM tbl_yyk JOIN tbl_ptinf ON tbl_yyk.ptid = tbl_ptinf.ptid WHERE tbl_ptinf.ptid = $1';
  $appointment = $sql->query($query, [$id])[0];
}

mb_convert_variables('UTF-8', 'EUC-JP', $appointment);
$formatter = DateTime::createFromFormat('Ymd', $appointment->yykymd);
$date = $formatter->format('Y/m/d');
$from = floor($appointment->keyyyktime / 100) * 100;
$to = $from + 10;

if ($from % 100 == 50) {
  $to = floor($to / 100) * 100 + 100;
}

$from = sprintf('%02d:%02d', $from / 100, $from % 100);
$to = sprintf('%02d:%02d', $to / 100, $to % 100);
$today = new DateTime();
$birthday = DateTime::createFromFormat('Ymd', $appointment->birthday);
$age = $today->diff($birthday);
$patient = empty($_GET['patient']) ? $appointment->ptid : Security::text($_GET['patient']);
?>
<div class="h4">
  <?php if ($appointment->current == 't'): ?>
    <span class="label label-primary">相談中</span>
  <?php endif; ?>
  <?php if ($appointment->plan == 't'): ?>
    <span class="label label-warning">予約</span>
  <?php endif; ?>
  <?=Security::text($date), ' ', Security::text($from), '〜', Security::text($to); ?>
</div>
<h2 class="h3">
  <?=Security::text($appointment->name); ?>
  <span class="small"><?=Security::text($appointment->kananame); ?></span>
  <span class="small"><?=Security::text($age->y); ?>歳</span>
</h2>
<ul class="nav nav-pills" style="margin-bottom: 1em">
  <li class="nav-item<?=($nav == '生活習慣' ? ' active' : ''); ?>">
    <a class="nav-link" href="habit.php<?=(empty($_GET['patient']) ? '' : '?patient=' . Security::text($_GET['patient'])); ?>">生活習慣</a>
  </li>
  <li class="nav-item<?=($nav == 'バイタルデータ' ? ' active' : ''); ?>">
    <a class="nav-link" href="vital.php<?=(empty($_GET['patient']) ? '' : '?patient=' . Security::text($_GET['patient'])); ?>">バイタルデータ</a>
  </li>
</ul>
