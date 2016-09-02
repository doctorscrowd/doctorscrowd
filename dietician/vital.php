<?php
$title = '患者情報';
include_once 'header.php';
$nav = 'バイタルデータ';
include_once 'patient.php';
$patient = empty($_GET['patient']) ? $appointment->ptid : Security::text($_GET['patient']);
$id = Security::text($_SESSION['id']);

if ($_POST) {
  mb_convert_variables('EUC-JP', 'UTF-8', $_POST);
  $query = 'INSERT INTO cards (id, created, "user", member) VALUES (uuid_generate_v4(), NOW(), $1, $2) RETURNING id';
  $card = $sql->query($query, [$patient, $id])[0];
  
  foreach ($_POST as $key => $value) {
    $key = Security::text($key);
    $value = Security::text($value);
    
    if ($key == 'a' || $key == 's' || $key == 'p') {
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
  (SELECT * FROM cards WHERE "user" = $1 AND member = $2 ORDER BY created DESC LIMIT 5) cards
  JOIN texts ON cards.id = texts.card
UNION
SELECT created, type, value, NULL AS text
FROM
  (SELECT * FROM cards WHERE "user" = $1 AND member = $2 ORDER BY created DESC LIMIT 5) cards
  JOIN values ON cards.id = values.card
ORDER BY created DESC
EOQ;
$items = $sql->query($query, [$patient, $id]);
?>
<form class="form" method="post">
  <div class="list-group">
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">血圧</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-5">
            <div class="input-group">
              <input name="pressure-low" type="number" class="form-control" placeholder="最低">
              <span class="input-group-addon">mmHg</span>
            </div>
          </div>
          <div class="col-xs-2 h5 text-center">〜</div>
          <div class="col-xs-5">
            <div class="input-group">
              <input name="pressure-high" type="number" class="form-control" placeholder="最高">
              <span class="input-group-addon">mmHg</span>
            </div>
          </div>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'pressure-low';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          $low = array_filter($items, function ($item) {
            return $item->type == 'pressure-low';
          });
          $high = array_filter($items, function ($item) {
            return $item->type == 'pressure-high';
          });
          $high = array_values($high);
          $index = 0;
          
          foreach ($low as $element) {
            echo '<td>', $element->value, '〜', $high[$index]->value, '</td>';
            $index++;
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">HbA1c</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="hba1c" type="number" class="form-control">
          <span class="input-group-addon">%</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'hba1c';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">空腹時血糖</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="glucose-level" type="number" class="form-control">
          <span class="input-group-addon">mg/dl</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'glucose-level';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">総コレステロール</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="total-cholesterol" type="number" class="form-control">
          <span class="input-group-addon">mg/dl</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'total-cholesterol';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">HDL / LDL</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-6">
            <div class="input-group">
              <span class="input-group-addon">HDL</span>
              <input name="hdl" type="number" class="form-control">
              <span class="input-group-addon">mg/dl</span>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="input-group">
              <span class="input-group-addon">LDL</span>
              <input name="ldl" type="number" class="form-control">
              <span class="input-group-addon">mg/dl</span>
            </div>
          </div>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'hdl';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          $low = array_filter($items, function ($item) {
            return $item->type == 'hdl';
          });
          $high = array_filter($items, function ($item) {
            return $item->type == 'ldl';
          });
          $high = array_values($high);
          $index = 0;
          
          foreach ($low as $element) {
            echo '<td>', $element->value, '〜', $high[$index]->value, '</td>';
            $index++;
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">中性脂肪</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="fat" type="number" class="form-control">
          <span class="input-group-addon">mg/dl</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'fat';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">AST(GOT) / ALT(GPT)</h3>
      <p class="list-group-item-text">
        <div class="row">
          <div class="col-xs-6">
            <div class="input-group">
              <span class="input-group-addon">AST(GOT)</span>
              <input name="ast" type="number" class="form-control">
              <span class="input-group-addon">mg/dl</span>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="input-group">
              <span class="input-group-addon">ALT(GPT)</span>
              <input name="alt" type="number" class="form-control">
              <span class="input-group-addon">mg/dl</span>
            </div>
          </div>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'ast';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          $low = array_filter($items, function ($item) {
            return $item->type == 'ast';
          });
          $high = array_filter($items, function ($item) {
            return $item->type == 'alt';
          });
          $high = array_values($high);
          $index = 0;
          
          foreach ($low as $element) {
            echo '<td>', $element->value, '〜', $high[$index]->value, '</td>';
            $index++;
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">γ-GTP</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="gtp" type="number" class="form-control">
          <span class="input-group-addon">IU/l</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'gtp';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">尿素窒素(UN)</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="un" type="number" class="form-control">
          <span class="input-group-addon">mg/dl</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'un';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">クレアニチン(CRE)</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="cre" type="number" class="form-control">
          <span class="input-group-addon">mg/dl</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'cre';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">血清鉄</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="fe" type="number" class="form-control">
          <span class="input-group-addon">μg/dl</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'fe';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">体重</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="weight" type="number" class="form-control">
          <span class="input-group-addon">kg</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'weight';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">BMI</h3>
      <p class="list-group-item-text">
        <input name="bmi" type="number" class="form-control">
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'bmi';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">体脂肪率</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="fat-percentage" type="number" class="form-control">
          <span class="input-group-addon">%</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'fat-percentage';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">腹囲</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="ac" type="number" class="form-control">
          <span class="input-group-addon">cm</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'ac';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">体温（起床時）</h3>
      <p class="list-group-item-text">
        <div class="input-group">
          <input name="body-temperature" type="number" class="form-control">
          <span class="input-group-addon">℃</span>
        </div>
      </p>
      <table class="list-group-item-text table table-condensed">
        <tr>
          <?php
          $list = array_filter($items, function ($item) {
            return $item->type == 'body-temperature';
          });
          
          foreach ($list as $item) {
            $date = substr($item->created, 0, 10);
            $date = str_replace('-', '/', $date);
            echo '<th>', $date, '</th>';
          }
          ?>
        </tr>
        <tr>
          <?php
          foreach ($list as $element) {
            echo '<td>', $element->value, '</td>';
          }
          ?>
        </tr>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">対象者からヒアリングできたこと</h3>
      <p class="list-group-item-text" style="margin-bottom: 1em">
        <textarea class="form-control" rows="12" name="s"></textarea>
      </p>
      <table class="list-group-item-text table table-condensed table-striped">
        <?php
        $list = array_filter($items, function ($item) {
          return $item->type == 's';
        });
        
        foreach ($list as $item) {
          echo '<tr><th>';
          $date = substr($item->created, 0, 10);
          $date = str_replace('-', '/', $date);
          echo $date, '</th></tr>';
          echo '<tr><td><p>', $item->text, '</p></td></tr>';
        }
        ?>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">評価（アセスメント）</h3>
      <p class="list-group-item-text" style="margin-bottom: 1em">
        <textarea class="form-control" rows="12" name="a"></textarea>
      </p>
      <table class="list-group-item-text table table-condensed table-striped">
        <?php
        $list = array_filter($items, function ($item) {
          return $item->type == 'a';
        });
        
        foreach ($list as $item) {
          echo '<tr><th>';
          $date = substr($item->created, 0, 10);
          $date = str_replace('-', '/', $date);
          echo $date, '</th></tr>';
          echo '<tr><td><p>', $item->text, '</p></td></tr>';
        }
        ?>
      </table>
    </div>
    <div class="list-group-item">
      <h3 class="h4 list-group-item-heading">目標・実施計画</h3>
      <p class="list-group-item-text" style="margin-bottom: 1em">
        <textarea class="form-control" rows="12" name="p"></textarea>
      </p>
      <table class="list-group-item-text table table-condensed table-striped">
        <?php
        $list = array_filter($items, function ($item) {
          return $item->type == 'p';
        });
        
        foreach ($list as $item) {
          echo '<tr><th>';
          $date = substr($item->created, 0, 10);
          $date = str_replace('-', '/', $date);
          echo $date, '</th></tr>';
          echo '<tr><td><p>', $item->text, '</p></td></tr>';
        }
        ?>
      </table>
    </div>
  </div>
  <div class="container text-right">
    <button class="btn btn-primary" type="submit">登録</button>
  </div>
  <hr>
</form>
