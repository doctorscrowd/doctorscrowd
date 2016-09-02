<?php
$title = 'スケジュール';
include_once 'header.php';
?>
<div class="container">
<?php
require_once '../sql.php';
$sql = new SQL();
$timestamp = floor(time() / 86400 + 1) * 86400;
$tommorow = date('w', $timestamp);
$nextday = new DateTime();
$nextday->setTimestamp($timestamp);
$nextweek = new DateTime();
$nextweek->setTimestamp($timestamp + 86400 * 7);
$query = <<< EOQ
SELECT weekday, date, time, MAX(open::Integer) open FROM (
SELECT weekday, '99999999' date, time, true open FROM openings WHERE member = $1
UNION
SELECT weekday, date, time, open FROM schedules WHERE member = $1 AND date > $2 AND date < $3
) temporary
GROUP BY weekday, date, time
ORDER BY date DESC, time, weekday
EOQ;
$schedules = $sql->query($query, [$id, $nextday->format('Ymd'), $nextweek->format('Ymd')]);
$period = 10;
$open = 700;
$close = 2300;
$days = ['日', '月', '火', '水', '木', '金', '土'];
$weekdays = range(0, 6);
echo '<h2 class="h3">スケジュール<span class="small glyphicon glyphicon-question-sign" data-toggle="popover" data-placement="right" data-content="ここでチェックした時間帯が空き（相談受付可能）時間として確定します。特に指定がなければ、デフォルト空き時間で指定したものが設定されます。"></span></h2>';
echo '<table class="table table-striped table-condensed text-center">';
echo '<tr><td></td>';
$loop = $timestamp;

foreach ($weekdays as $weekday) {
  echo '<th class="text-center">', date('m/d ', $loop), $days[date('w', $loop)], '</th>';
  $loop += 86400;
}

echo '</tr>';
$times = array_filter(range($open, $close - $period, $period), function ($time) {
  return $time % 100 < 60;
});
$index = 0;

foreach ($times as $time) {
  echo '<tr>';
  printf('<th>%02d:%02d</th>', $time / 100, $time % 100);
  
  foreach ($weekdays as $weekday) {
    echo '<td><a href="#" data-toggle="tooltip" data-placement="right" data-time="', $time, '" data-weekday="', ($weekday + date('w', $timestamp)) % 7, '" data-date="', date('Ymd', $timestamp + $weekday * 86400), '"  class="h3">';
    $icon = 'unchecked text-muted';

    foreach ($schedules as $schedule) {
      if ($schedule->date == '99999999' && $schedule->weekday == ($weekday + date('w', $timestamp)) % 7 && $schedule->time == $time && $schedule->open == true) {
        $icon = 'check text-primary';
      }
      
      if ($schedule->date == date('Ymd', $timestamp + $weekday * 86400)) {
        if ($schedule->time == $time) {
          $icon = ($schedule->open ? 'check text-primary' : 'unchecked text-muted');
        }
      }
    }

    echo '<span class="glyphicon glyphicon-', $icon, '"></span></a></td>';
    $index++;
  }
  
  echo '</tr>';
}

echo '</table>';
?>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip({
    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner h4"></div></div>',
    title: function () {
      var index = $(this).data('weekday')
      return $(this).parents('table').find('tr').eq(0).find('th').eq(index).text() + ' ' + $(this).parents('tr').find('th').text() + '-' + $(this).parents('tr').next().find('th').text()
    }
  })
  $('[data-toggle="popover"]').popover({ trigger: 'hover' })
  $('a[data-time][data-weekday]').on('click', function (event) {
    var $self = $(this)
    var open = $self.find('span').hasClass('glyphicon-unchecked')
    var body = JSON.stringify({
      date: $self.data('date'),
      open: (open ? 1 : 0),
      time: $self.data('time'),
      weekday: $self.data('weekday'),
      member: '<?=Security::text($id); ?>'
    })

    $.ajax({
      cache: false,
      contentType: 'application/json',
      data: body,
      method: 'POST',
      success: function (data) {
        var post = $self.find('span').hasClass('glyphicon-unchecked')
        
        $self.find('span')
          .removeClass((post ? 'glyphicon-unchecked' : 'glyphicon-check'))
          .removeClass((post ? 'text-muted' : 'text-primary'))
          .addClass((post ? 'glyphicon-check': 'glyphicon-unchecked'))
          .addClass((post ? 'text-primary' : 'text-muted'))
      },
      url: '../api/schedule.php'
    })
    event.preventDefault()
    return false
  })
})
</script>
