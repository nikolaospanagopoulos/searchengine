<?php include dirname(__DIR__) . '/header.php'; ?>
<script src='https://cdn.plot.ly/plotly-2.9.0.min.js'></script>

<div id='myDiv'> </div>

<script>
    var data = <?= json_encode($data)  ?>
</script>
<script>
    

    var res = data
    var dateArray = res.map((array) => array[0]);
    var dataArray = res.map((array) => array[1]);
    var trace1 = {
        x: dateArray,
        y: dataArray,
        mode: "lines",
        connectgaps: true,
    };

    var layout = {
        title: 'views per day',
        font: {
            size: 18
        }
    };
    var data = [trace1];

    Plotly.newPlot("myDiv", data, layout, {
        responsive: true
    });
</script>



<?php include dirname(__DIR__) . '/footer.php'; ?>