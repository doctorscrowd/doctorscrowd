<?php
require_once 'security.php';
$day = Security::text($_GET['day']);
$month = substr($day, 0, -2);
$date = DateTime::createFromFormat('Ymd', $day);
?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<title>Doctors Crowd</title>
<link rel="stylesheet" href="css/default.css" type="text/css" media="all">
<link rel="stylesheet" href="css/page.css" type="text/css" media="all">
<link rel="stylesheet" href="css/time.css" type="text/css" media="all">
<header>
  <a href="day.php?dietician=<?=Security::text($_GET['dietician']); ?>&day=<?=$month; ?>"><img src="img/return.png"></a>
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

$_SESSION['dietician'] = Security::text($_GET['dietician']);
$sql = new SQL();
?>
<p class="text-center">相談時刻を指定してください</p>
<table>
  <tr><td>09:00</td></tr>
  <tr><td>10:00</td></tr>
  <tr><td>11:00</td></tr>
  <tr><td>12:00</td></tr>
  <tr><td>13:00</td></tr>
  <tr><td>14:00</td></tr>
  <tr><td>15:00</td></tr>
  <tr><td>16:00</td></tr>
  <tr><td>17:00</td></tr>
  <tr><td>18:00</td></tr>
  <tr><td>19:00</td></tr>
  <tr><td>20:00</td></tr>
  <tr><td>09:00</td></tr>
  <tr><td>10:00</td></tr>
  <tr><td>11:00</td></tr>
  <tr><td>12:00</td></tr>
  <tr><td>13:00</td></tr>
  <tr><td>14:00</td></tr>
  <tr><td>15:00</td></tr>
  <tr><td>16:00</td></tr>
  <tr><td>17:00</td></tr>
  <tr><td>18:00</td></tr>
  <tr><td>19:00</td></tr>
  <tr><td>20:00</td></tr>
<?php
/*$query = <<< EOQ
SELECT weekday, date, MAX(open::Integer) open FROM (
SELECT weekday, '99999999' date, true open FROM openings WHERE member = $1
UNION
SELECT weekday, date, open FROM schedules WHERE member = $1 AND date > $2 AND date < $3
) temporary
GROUP BY weekday, date
ORDER BY date, weekday
EOQ;
$openings = $sql->query($query, [Security::text($_GET['dietician']), $previous->format('Ymd'), $next->format('Ymd')], 'Date');
*/?>
</table>
<div class="text-center" id="day"><?=$date->format('Y.m.d'); ?></div>
<div id="done"><a class="text-center" href="">決定</a></div>
