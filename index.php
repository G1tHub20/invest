<?php
require('library.php');
?>

<?php
$when = ""; // 西暦年と月 (例）"2022-11"
$switch = "recent";
$today = date("Y-m-d");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ■ 指定月の1ヶ月間の場合
  $when = $_POST['when'];
  $switch = "month";
  $year = substr($when, 0, 4);
  $month = substr($when, 5, 2);
  $label = $month . "月"; //(例）"11月"

  $period = " LIKE '%" . $when . "-%'";

} else {
  // ■ 直近1ヶ月の場合
  $label = "直近1ヶ月";

  $old = date("Y-m-d",strtotime("-1 month"));
  $period = " BETWEEN '" . $old . "' AND '" . $today ."'";

}

$sql1 = "SELECT * FROM invest_nikkei WHERE date {$period} ORDER BY id DESC LIMIT 30";
$sql2 = "SELECT AVG( close ) AS 'avg', MAX( close ) AS 'max', MIN( close ) AS 'min'
          FROM invest_nikkei
          WHERE date {$period}";

$dbh = dbconnect();
$stmt1 = $dbh->query($sql1);
$all = $stmt1->fetchAll();

$stmt2 = $dbh->query($sql2);
$data = $stmt2->fetch();
$avg = number_format($data['avg'],0); //小数点第三位以下を切り捨て
$max = number_format($data['max'],0);
$min = number_format($data['min'],0);
// echo "<pre>"; var_export($all); echo "</pre>";

$days = array();
$dataList = array();

foreach($all as $row) {
  $days[] = substr($row['date'], -2); //日にち部分
  $dataList[] = $row['close'];
};
// echo "<pre>"; var_export($dataList); echo "</pre>";


$days = array_reverse($days); //グラフをラベルを時系列順にする
$dataList = array_reverse($dataList);
$maxMonth = 11;
// echo "<pre>"; var_export($dataList); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css\style.css">
  <title>日経平均チャート</title>
</head>
<body>

<h1>日経平均チャート</h1>
<form action="" method="POST">
<p>月を選択してください</p>
<input type="month" name="when" min="2019-01" max="<?php echo date('Y-m'); ?>" value="<?php echo $when; ?>" required>
<button type="submit">表示</button>
</form>

<!-- ---------- グラフ描画ここから ---------- -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
<script>
window.onload = function () {
  let context = document.querySelector("#nikkei_chart").getContext('2d');
  // context.canvas.height = "100%";
  new Chart(context, {
    type: 'line', //折れ線グラフ
    data: {
      labels: [ //ラベル
      <?php foreach($days as $day) {
        if($day === end($days)) {
          echo $day;
        } else {
          echo $day . ",";
        }
      } ?>
      ],
      datasets: [{
        label: "<?php echo $label; ?>",
        data: [ //データ
          <?php foreach($dataList as $data) {
            if($data === end($dataList)) {
              echo $data;
            } else {
              echo $data . ",";
            }
          } ?>
        ],
        borderColor: '#ff6347',
      }],
    },
    options: {
      responsive: false,
      // maintainAspectRatio: true,
      scales: {
        y: {
          // min: 30000, //y軸の値の最小値／最大値
          // max: 25000,
        }
      },
    },
  })
}
</script>

<canvas id="nikkei_chart" width="500" height="300"></canvas>
<!-- ---------- グラフ描画ここまで ---------- -->
<?php 

$all = array_reverse($all); //表では日付の降順

?>

<div>
  <p>平均値： <?= $avg; ?></p>
  <p>最高値： <?= $max; ?></p>
  <p>最安値： <?= $min; ?></p>
</div>

<table>
  <tr><th>日付</th><th>終値</th><th>前日比</th><th>購入</th></tr>
  <?php foreach($all as $row): ?>
  <tr>
    <td><?php echo $row['date'] ?></td><td><?php echo number_format($row['close'],0) ?></td>
    <td><?php echo $row['chg'] ?>%</td><td><?php if(isset($row['buy'])) echo $row['buy']/10000 . "万" ?></td>
  </tr>
  <?php endforeach ?>
</table>

</body>
</html>