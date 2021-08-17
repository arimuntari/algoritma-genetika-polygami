<?php
$dataId = $_POST?$_POST["dataId"]:1;
$cluster = $_POST?$_POST["cluster"]:5;
$rowData = getDataList();
$rowCluster = getDataCluster();
$dataKMean =getChartResult($dataId, $cluster, "K-Mean");
$dataGA =getChartResult($dataId, $cluster, "GAP");

?>
<div class="container mt-3">
    <form action="" method="POST" autocomplete="off" >
        <div class="row">
            <div class="col-md-5">
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
            <div class="col-md-5">
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
    </div>
</div>
<script>
Highcharts.chart('kmean', {
    title: {
        text: 'Perbandingan K-mean dan GAP'
    },
    plotOptions: {
        series: {
            pointStart: 1
        }
    },
    series: [{
        name: 'K-Mean',
        data: <?php echo  json_encode($dataKMean);?>
    },{
        name: 'GAP',
        data: <?php echo  json_encode($dataGA);?>
    }],
});
</script>
AIzaSyC26NNAiCGTMWFKLyLuv0PHbJ4D-Wx5OpQ