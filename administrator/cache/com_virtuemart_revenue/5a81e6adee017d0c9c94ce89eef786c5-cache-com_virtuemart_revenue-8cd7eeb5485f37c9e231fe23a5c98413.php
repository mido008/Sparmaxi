<?php die("Access Denied"); ?>#x#a:2:{s:6:"output";s:0:"";s:6:"result";a:2:{s:6:"report";a:0:{}s:2:"js";s:1414:"
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Day', 'Orders', 'Total Items sold', 'Revenue net'], ['2017-05-09', 0,0,0], ['2017-05-10', 0,0,0], ['2017-05-11', 0,0,0], ['2017-05-12', 0,0,0], ['2017-05-13', 0,0,0], ['2017-05-14', 0,0,0], ['2017-05-15', 0,0,0], ['2017-05-16', 0,0,0], ['2017-05-17', 0,0,0], ['2017-05-18', 0,0,0], ['2017-05-19', 0,0,0], ['2017-05-20', 0,0,0], ['2017-05-21', 0,0,0], ['2017-05-22', 0,0,0], ['2017-05-23', 0,0,0], ['2017-05-24', 0,0,0], ['2017-05-25', 0,0,0], ['2017-05-26', 0,0,0], ['2017-05-27', 0,0,0], ['2017-05-28', 0,0,0], ['2017-05-29', 0,0,0], ['2017-05-30', 0,0,0], ['2017-05-31', 0,0,0], ['2017-06-01', 0,0,0], ['2017-06-02', 0,0,0], ['2017-06-03', 0,0,0], ['2017-06-04', 0,0,0], ['2017-06-05', 0,0,0], ['2017-06-06', 0,0,0]  ]);
        var options = {
          title: 'Report for the period from Tuesday, 09 May 2017 to Wednesday, 07 June 2017',
            series: {0: {targetAxisIndex:0},
                   1:{targetAxisIndex:0},
                   2:{targetAxisIndex:1},
                  },
                  colors: ["#00A1DF", "#A4CA37","#E66A0A"],
        };

        var chart = new google.visualization.LineChart(document.getElementById('vm_stats_chart'));

        chart.draw(data, options);
      }
";}}