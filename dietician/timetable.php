<?php
$title = '空き時間';
include_once 'header.php';
?>
<div class="container">
<?php
class Opening {
  private $time;
  private $weekday;
  
  function into($associative) {
    $associative[$this->weekday][$this->time] = true;
    return $associative;
  }
}

require_once '../sql.php';
$sql = new SQL();
$query = 'SELECT * FROM openings';
$openings = $sql->query($query, [], 'Opening');
$doctor = (Object)['name' => ''];
$items = [];
$schedule = array_reduce($openings, function ($carry, $opening) {
  return $opening->into($carry);
}, $items);
$period = 10;
$open = 700;
$close = 2300;
$days = ['日', '月', '火', '水', '木', '金', '土'];
echo '<h2 class="h3">空き時間<span class="small glyphicon glyphicon-question-sign" data-toggle="popover" data-placement="right" data-content="ここでチェックした時間帯がデフォルトの空き（相談受付可能）時間となります。「スケジュール」で日時を指定して受付時間を設定した場合はそちらが優先されます。"></span></h2>';
echo '<table class="table table-striped table-condensed text-center">';
echo '<tr><td></td><th class="text-center">日</th><th class="text-center">月</th><th class="text-center">火</th><th class="text-center">水</th><th class="text-center">木</th><th class="text-center">金</th><th class="text-center">土</th></tr>';
$weekdays = range(0, 6);
$times = array_filter(range($open, $close - $period, $period), function ($time) {
  return $time % 100 < 60;
});
$index = 0;

foreach ($times as $time) {
  echo '<tr>';
  printf('<th>%02d:%02d</th>', $time / 100, $time % 100);
  
  foreach ($weekdays as $weekday) {
    echo '<td><a href="#" data-toggle="tooltip" data-placement="right" data-time="', $time, '" data-weekday="', $weekday, '" class="h3">';
    $icon = 'unchecked text-muted';

    if (array_key_exists($weekday, $schedule)) {
      if (array_key_exists($time, $schedule[$weekday])) {
        $icon = 'check text-primary';
      }
    }
    
    echo '<span class="glyphicon glyphicon-', $icon, '"></span></a></td>';
    $index++;
  }
  
  echo '</tr>';
}

echo '</table>';
?>
</div>
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
    var body = JSON.stringify({
      time: $(this).data('time'),
      weekday: $(this).data('weekday'),
      member: '<?=Security::text($id); ?>'
    })
    var post = $self.find('span').hasClass('glyphicon-unchecked')
    
    $.ajax({
      cache: false,
      contentType: 'application/json',
      data: body,
      method: post ? 'POST' : 'DELETE',
      success: function (data) {
        var post = $self.find('span').hasClass('glyphicon-unchecked')
        
        $self.find('span')
          .removeClass((post ? 'glyphicon-unchecked' : 'glyphicon-check'))
          .removeClass((post ? 'text-muted' : 'text-primary'))
          .addClass((post ? 'glyphicon-check': 'glyphicon-unchecked'))
          .addClass((post ? 'text-primary' : 'text-muted'))
      },
      url: '../api/opening.php'
    })
    event.preventDefault()
    return false
  })
})
</script>
