<?php
$dataId = $_POST?$_POST["dataId"]:1;
$cluster = $_POST?$_POST["cluster"]:5;
$iterasi = $_POST?$_POST["iterasi"]:500;
$rowData = getDataList();
$rowCluster = getDataCluster();
$rowIterasi = getDataIterasi();
$dataKMean =getChartResult($dataId, $cluster, $iterasi, "GA");
$dataGA =getChartResult($dataId, $cluster, $iterasi,  "GAP");

$getDataGAP =getDataGAP($dataId, $cluster, $iterasi,  "GAP");
$getDataGA =getDataGAP($dataId, $cluster, $iterasi,  "GA");
$getDataGAPInd =getDataGAPIndividu($dataId, $cluster, $iterasi,  "GAP");
$getDataGAPCluster =getDataGAPCluster($dataId, $cluster, $iterasi,  "GAP");
$getDataGAPCompareGAP =getDataGAPCompare($dataId, $cluster, $iterasi,  "GAP");
$getDataGAPCompareKmean =getDataGAPCompare($dataId, $cluster, $iterasi,  "GA");
?>
<div class="container mt-3">
    <form action="" method="POST" autocomplete="off" >
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="totalCluster">Pilih Data</label>
                    <select name="dataId" class="form-control">
                        <option value="">-Pilih Data-</option>
                        <?php
                        if($rowData!=null){
                            foreach($rowData as $row){
                                ?>
                                    <option <?php echo $dataId==$row["data_id"]?"selected":"";?> value="<?php echo $row["data_id"];?>"><?php echo $row["name"];?></option>

                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="totalCluster">Pilih Cluster</label>
                    <select name="cluster" class="form-control">
                        <option value="">-Pilih Data-</option>
                        <?php
                        if($rowCluster!=null){
                            foreach($rowCluster as $row){
                                ?>
                                    <option <?php echo $cluster==$row["cluster"]?"selected":"";?> value="<?php echo $row["cluster"];?>"><?php echo $row["cluster"];?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="totalCluster">Pilih Iterasi</label>
                    <select name="iterasi" class="form-control">
                        <option value="">-Pilih Data-</option>
                        <?php
                        if($rowIterasi!=null){
                            foreach($rowIterasi as $row){
                                ?>
                                    <option <?php echo $iterasi==$row["iterasi"]?"selected":"";?> value="<?php echo $row["iterasi"];?>"><?php echo $row["iterasi"];?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label for="totalCluster">&nbsp;</label>
                <button class="btn btn-primary btn-block">Detail</button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <div id="kmean"></div>
        </div>
        <div class="col-md-12 mt-2">
            <div id="gapind"></div>
        </div>
        <div class="col-md-12 mt-2">
            <div id="gapcluster"></div>
        </div>
        <div class="col-md-12 mt-2">
            <div id="gap"></div>
        </div>
        <div class="col-md-12 mt-2">
            <div id="gapmean"></div>
        </div>
    </div>
</div>
<script>
Highcharts.chart('kmean', {
    title: {
        text: 'Perbandingan GA dan GAP'
    },
    plotOptions: {
        series: {
            pointStart: 1
        }
    },
    series: [{
        name: 'GA',
        data: <?php echo  json_encode($dataKMean);?>
    },{
        name: 'GAP',
        data: <?php echo  json_encode($dataGA);?>
    }],
});


Highcharts.chart('gapind', {
    title: {
        text: 'Perbandingan GAP berdasarkan jumlah  individu'
    },
    xAxis: {
        categories: <?php echo json_encode($getDataGAPInd["name"]);?>,
    },
    series:[{
        name: 'GAP',
        data: <?php echo json_encode($getDataGAPInd["data"]);?>
    }]
});
Highcharts.chart('gapcluster', {
    title: {
        text: 'Perbandingan GAP berdasarkan jumlah cluster'
    },
    xAxis: {
        categories: <?php echo json_encode($getDataGAPCluster["name"]);?>,
    },
    series:[{
        name: 'GAP',
        data: <?php echo json_encode($getDataGAPCluster["data"]);?>
    }]
});
Highcharts.chart('gap', {
    title: {
        text: 'Perbandingan GAP berdasarkan mutation rate dan crossover rate'
    },
    xAxis: {
        categories: <?php echo json_encode($getDataGAP["name"]);?>,
    },
    series:[{
        name: 'GAP',
        data: <?php echo json_encode($getDataGAP["data"]);?>
    },
    {
        name: 'GA',
        data: <?php echo json_encode($getDataGA["data"]);?>
    }]
});
Highcharts.chart('gapmean', {
    title: {
        text: 'Perbandingan GAP dengan GA'
    },
    xAxis: {
        categories: <?php echo json_encode($getDataGAPCompareKmean["name"]);?>,
    },
    series:[{
        name: 'GAP',
        data: <?php echo json_encode($getDataGAPCompareGAP["data"]);?>
    },
    {
        name: 'GA',
        data: <?php echo json_encode($getDataGAPCompareKmean["data"]);?>
    }]
});
</script>
