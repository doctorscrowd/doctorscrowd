<?php
$title = '予定表';
include_once 'header.php';
require_once '../sql.php';
$id = Security::text($_SESSION['id']);
$today = new DateTime();
$date = new DateTime();
$from = $date->format('Ymd');
$interval = new DateInterval('P7D');
$date->add($interval);
$to = $date->format('Ymd');
$query = <<< EOQ
SELECT *
FROM tbl_yyk
  JOIN tbl_ptinf ON tbl_yyk.ptid = tbl_ptinf.ptid
WHERE dietician = $1
  AND yykymd >= $2
  AND yykymd <= $3
EOQ;
$sql = new SQL();
$appointments = $sql->query($query, [$id, $from, $to]);
mb_convert_variables('UTF-8', 'EUC-JP', $appointments);
$dates = array_map(function ($item) {
  $date = new DateTime();
  $interval = new DateInterval("P{$item}D");
  $date->add($interval);
  return $date;
}, range(0, 6));
$columns = array_map(function ($date) {
  $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
  return $date->format('m/d') . ' ' . $weekdays[$date->format('w')];
}, $dates);
array_unshift($columns, '時刻');
?>
<div class="container">
  <h2 class="h4"><?=$columns[1]; ?></h2>
  <table class="table table-striped table-condensed table-bordered">
    <tr>
      <?php
      foreach ($columns as $column) {
        echo '<th colspan="3">', $column, '</th>';
      }
      ?>
    </tr>
    <?php
    for ($i = 700; $i < 2300; $i = $i + (($i % 100 == 50) ? 50 : 10)) {
      echo '<tr><th class="text-right" colspan="3">', sprintf('%02d:%02d', $i / 100, $i % 100), '</th>';
      
      foreach ($dates as $date) {
        $councel = false;
      
        foreach ($appointments as $appointment) {
          if ($appointment->yykymd == $date->format('Ymd') && $appointment->keyyyktime == $i) {
            $councel = $appointment;
            break;
          }
        }

        if ($councel) {
          echo '<td><span class="small">', $councel->kananame, '</span><br><a href="habit.php?patient=', $councel->ptid, '">', $councel->name, '</a></td>';
          echo '<td><a class="h1" href="facetime:', $councel->email, '"><span class="glyphicon glyphicon-phone"></span></a></td>';
          echo '<td><a class="h1" href="sns.php?patient=', $councel->ptid, '"><span class="glyphicon glyphicon-link"></span></a></td>';
        }
        else {
          echo '<td colspan="3">-</td>';
        }
      }
      
      echo '</tr>';
    }
    ?>
  </table>
</div>