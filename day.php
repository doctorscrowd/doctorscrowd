<!DOCTYPE html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<title>Doctors Crowd</title>
<link rel="stylesheet" href="css/default.css" type="text/css" media="all">
<link rel="stylesheet" href="css/page.css" type="text/css" media="all">
<link rel="stylesheet" href="css/day.css" type="text/css" media="all">
<header>
  <a href="dieticians.php"><img src="img/return.png"></a>
  <a href="index.php"><img src="img/logo.png"></a>
</header>
<?php
class Date {
  static function thisMonth() {
    $now = new DateTime();
    $interval = new DateInterval('P' . ($now->format('d') - 1) . 'D');
    $now->sub($interval);
    $now->setTime(0, 0);
    return $now;
  }
}

require_once 'security.php';
require_once 'sql.php';

$sql = new SQL();
?>
<p class="text-center">相談日を指定してください</p>
<?php
$current = DateTime::createFromFormat('Ymd', Security::text($_GET['day']) . '01');
$now = DateTime::createFromFormat('Ymd', Security::text($_GET['day']) . '01');
$previous = DateTime::createFromFormat('Ymd', Security::text($_GET['day']) . '01');
$next = DateTime::createFromFormat('Ymd', Security::text($_GET['day']) . '01');
$day = new DateInterval('P1D');
$month = new DateInterval('P1M');
$query = <<< EOQ
SELECT weekday, date, MAX(open::Integer) open FROM (
SELECT weekday, '99999999' date, true open FROM openings WHERE member = $1
UNION
SELECT weekday, date, open FROM schedules WHERE member = $1 AND date > $2 AND date < $3
) temporary
GROUP BY weekday, date
ORDER BY date, weekday
EOQ;
$previous->sub($day);
$next->add($month);
$openings = $sql->query($query, [Security::text($_GET['dietician']), $previous->format('Ymd'), $next->format('Ymd')], 'Date');
$index = 0;
$weekdays = new DateInterval('P' . $current->format('w') . 'D');
$current->sub($weekdays);
?>
<table>
  <tr>
    <th class="text-center">日</th>
    <th class="text-center">月</th>
    <th class="text-center">火</th>
    <th class="text-center">水</th>
    <th class="text-center">木</th>
    <th class="text-center">金</th>
    <th class="text-center">土</th>
  </tr>
  <?php
  while ($now->format('Ym') >= $current->format('Ym') || $current->format('w') != 0) {
    $opened = false;

    if ($index % 7 == 0) {
      ?><tr><?php
    }
    
    if ($current->format('Y-m-d') >= date('Y-m-d') && $now->format('Ym') == $current->format('Ym')) {
      
      foreach ($openings as $opening) {
        if ($opening->date == $current->format('Ymd')) {
          $opened = $opening->open == "1";
        }
        else if ($opening->weekday == $current->format('w') && $opening->date == '99999999') {
          $opened = true;
        }
      }
    }

    if ($opened) {
      ?><td class="text-center">
        <a href="time.php?dietician=<?=Security::text($_GET['dietician']); ?>&day=<?=$current->format('Ymd'); ?>">
          <div class="text-center"><?=$current->format('j'); ?></div>
        </a>
      </td><?php
    }
    else {
    ?><td class="text-center disabled">
      <?=$current->format('j'); ?>
    </td><?php
    }
    
    if ($index % 7 == 6) {
      ?></tr><?php
    }
    
    $current->add($day);
    $index++;
  }
  ?>
</table>
<div id="month" class="text-center">
  <?php
  $interval = new DateInterval('P1M');
  $now->sub($interval);
  $today = new DateTime();
  $disabled = $now->format('Ym') < $today->format('Ym');
  ?>
  <a <?=($disabled ? 'class="disabled"' : ''); ?> href="?dietician=<?=Security::text($_GET['dietician']); ?>&day=<?=$now->format('Ym'); ?>"><img src="img/left_chevron.png"></a>
  <?php
  $now->add($interval);
  echo $now->format('n');
  ?>月
  <?php $now->add($interval); ?>
  <a href="?dietician=<?=Security::text($_GET['dietician']); ?>&day=<?=$now->format('Ym'); ?>"><img src="img/right_chevron.png"></a>
</div>
