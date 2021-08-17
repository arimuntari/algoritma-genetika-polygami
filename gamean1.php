<?php
$dataId = $_POST?$_POST["dataId"]:1;
$totalIndividu = $_POST?$_POST["totalIndividu"]:"";
$totalCluster = $_POST?$_POST["totalCluster"]:"2";
$mutationRate = $_POST?$_POST["mutationRate"]:"0.3";
$crossoverRate = $_POST?$_POST["crossoverRate"]:"0.6";
$maxLoop = $_POST?$_POST["maxLoop"]:"500";
$rowData = getDataList();
$tempPopultationData=[];
$result =[];
$maxLoopNotChange = 100;
$listData = [];
$populationData = [];
$fungsiObjective = [];
$resultRoulete = [];
if ($_POST) {
    $listData = getDataDetail($dataId);
    $populationData = generatePopulation($listData, $totalIndividu, $totalCluster);
}
?>
<div class="container mt-3">
    <div class="row">
        <div class="col-6">
            <form action="" method="POST" autocomplete="off" >
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
                <div class="form-group">
                    <label for="totalCluster">Jumlah Individu</label>
                    <input type="text" name="totalIndividu" class="form-control" id="totalIndividu" placeholder="Masukkan jumlah individu" value="<?php echo $totalIndividu;?>">
                </div>
                <div class="form-group">
                    <label for="totalCluster">Jumlah Cluster</label>
                    <input type="text" name="totalCluster" class="form-control" id="totalCluster" placeholder="Masukkan jumlah cluster" value="<?php echo $totalCluster;?>">
                </div>
                <div class="form-group">
                    <label for="mutationRate">Mutasi Rate</label>
                    <input type="text" name="mutationRate" class="form-control" id="mutationRate" placeholder="Masukkan mutasi rate" value="<?php echo $mutationRate;?>">
                </div>
                <div class="form-group">
                    <label for="crossoverRate">Crossover Rate</label>
                    <input type="text" name="crossoverRate" class="form-control" id="crossoverRate" placeholder="Masukkan crossover rate" value="<?php echo $crossoverRate;?>">
                </div>
                <div class="form-group">
                    <label for="crossoverRate">Maksimum Iterasi</label>
                    <input type="text" name="maxLoop" class="form-control" id="maxLoop" placeholder="Masukkan maksimum iterasi" value="<?php echo $maxLoop;?>">
                </div>
                <button type="submit" class="btn btn-success btn-sm">Submit</button>
            </form>
        </div>
    </div>

<?php
    if ($_POST != null) {
        foreach ($populationData as $key1 => $clusterData) {
            $resultData = calculateDistance($listData, $clusterData);
            $fungsiObjective["fObjective"][$key1] = 0;
            foreach ($resultData as $key => $result) {
                    $fungsiObjective["fObjective"][$key1] += $result["resultDistance"];
            }
        }
        $loop = 0;
        $loopNotChange = 0;
        $resultObjective =null;
        $tempObjective = 0;
        $resultChartData = [];
        while ($loop!= $maxLoop && $loopNotChange != $maxLoopNotChange) {
            $loop++;
            $resultRoulete= calculateRoulette($fungsiObjective["fObjective"]);
            if($resultObjective == null){
               $resultObjective = min($fungsiObjective["fObjective"]);
            }else{
                if($resultObjective > min($fungsiObjective["fObjective"])){
                    $resultObjective = min($fungsiObjective["fObjective"]);
                }
            }
            $bestKey = array_search(min($fungsiObjective["fObjective"]), $fungsiObjective["fObjective"]);
            $populationData = regenerateCrossover($totalIndividu, $crossoverRate, $populationData, $populationData[$bestKey], $resultRoulete);
            $populationData = mutation($populationData, $totalIndividu, $totalCluster, $mutationRate, $listData);
            foreach ($populationData as $key1 => $clusterData) {
                $resultData = calculateDistance($listData, $clusterData);
                $chartData = buildChartGroup($resultData, $clusterData);
                $fungsiObjective["fObjective"][$key1] = 0;
                foreach ($resultData as $key => $result) {
                        $fungsiObjective["fObjective"][$key1] += $result["resultDistance"];
                }
            }
            if($resultObjective == $tempObjective){
                $loopNotChange++;
            }else{
                $tempObjective = $resultObjective;
                $resultChartData = $chartData;
            }
        }
        ?>
        <?php
        foreach ($populationData as $key1 => $clusterData) {
                ?>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-2">
                    <table class="table table-bordered">
                        <tr class="bg-primary text-white">
                            <td>No</td>
                            <td>Latitude</td>
                            <td>Longitude</td>
                            <?php foreach ($clusterData as $key => $cluster) { ?>
                                <td><?php echo "Cluster ". ($key+1); ?><br> <?php echo "(".$cluster["latitude"].", ".$cluster["longitude"].")";?></td>
                                <?php
                            }?>
                            <td>Result Cluster</td>
                            <td>Result Distance</td>
                        </tr>
                        <?php
                        $fungsiObjective["fObjective"][$key1] = 0;
                        foreach ($resultData as $key => $result) { ?>
                            <?php
                                $avgList[$result["resultCluster"][0]]["latitude"][] = $result["latitude"];
                                $avgList[$result["resultCluster"][0]]["longitude"][] = $result["longitude"];

                                $fungsiObjective["fObjective"][$key1] += $result["resultDistance"];
                            ?>
                            <tr>
                                <td><?php echo $key+1;?></td>
                                <td><?php echo $result["latitude"];?></td>
                                <td><?php echo $result["longitude"];?></td>
                                <?php foreach ($clusterData as $key => $cluster) { ?>
                                    <td><?php echo $result["result"][$key]; ?></td>
                                    <?php
                                }?>
                                <td><?php echo "Cluster ".($result["resultCluster"][0]+1);?></td>
                                <td><?php echo $result["resultDistance"];?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td align="right" colspan="<?php echo 4+count($clusterData)?>">Fungsi Objektif</td>
                            <td><?php echo $fungsiObjective["fObjective"][$key1];?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    Iterasi = <?php echo $loop;?><br>
    Hasil Fungsi Objektif terkecil = <?php echo $tempObjective;?>

    <div id="scatter-result"></div>
    <script>
            Highcharts.chart('scatter-result', {
            chart: {
                type: 'scatter',
            },
            title: {
                text: 'Preview Data'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                title: {
                    enabled: true,
                    text: 'Latitude'
                },
                startOnTick: true,
                endOnTick: true,
                showLastLabel: true
            },
            yAxis: {
                title: {
                    text: 'Longitude'
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'bottom',
            },
            plotOptions: {
                scatter: {
                    marker: {
                        radius: 5,
                        states: {
                            hover: {
                                enabled: true,
                                lineColor: 'rgb(100,100,100)'
                            }
                        }
                    },
                    states: {
                        hover: {
                            marker: {
                                enabled: false
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<b>{series.name}</b><br>',
                        pointFormat: '{point.x} latitude, {point.y} longitude'
                    }
                }
            },
            series: <?php echo json_encode($chartData);?>
        });
        </script>
        <?php
            insertResult($dataId, $totalCluster, $tempObjective, $maxLoop, "GAP");
        ?>
    <?php
    }
?>
</div>