<?php

$year = 2019;
$days = [1,2,3,6,7,8,9,10,13,14,15,16,17,20,21,22,23,24,27,28];
$dataList = [1,2,3,6,7,8,9,10,13,14,15,16,17,20,21,22,23,24,27,28];

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>line chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
    <script>
      window.onload = function () {
        let context = document.querySelector("#nikkei_chart").getContext('2d')
        new Chart(context, {
          type: 'line',
          data: {
            // labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            labels: [
            <?php foreach($days as $day) {
              if($day === end($days)) {
                echo $day;
              } else {
                echo $day . ",";
              }
            } ?>
            ],
            datasets: [{
              // label: "2019年",
              label: "<?php echo $year; ?>",
              // data: [8.0, 9.4, 11.9, 15.4, 21.1, 23.4, 26.4, 28.0, 25.9, 20.5, 14.9, 10.3],
              data: [
                <?php foreach($dataList as $data) {
                  if($data === end($dataList)) {
                    echo $data;
                  } else {
                    echo $data . ",";
                  }
                } ?>
              ],
            }],
          },
          options: {
            responsive: false,
          }
        })
      }
    </script>
  </head>
  <body>
    <canvas id="nikkei_chart" width="500" height="500"></canvas>
  </body>
</html>