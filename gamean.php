<?php
$dataId = $_POST?$_POST["dataId"]:1;
$totalIndividu = $_POST?$_POST["totalIndividu"]:"";
$totalCluster = $_POST?$_POST["totalCluster"]:"";
$mutationRate = $_POST?$_POST["mutationRate"]:"";
$crossoverRate = $_POST?$_POST["crossoverRate"]:"";
$tempPopultationData=[];
$result =[];
$maxLoop = 100;
$maxLoopNotChange = 10;
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
                    <input type="text" name="dataId" class="form-control" id="dataId" placeholder="Pilih Data" value="<?php echo $dataId?>">
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
                <button type="submit" class="btn btn-success btn-sm">Submit</button>
            </form>
            <?php
            /*if ($_POST != null) {
                foreach ($populationData as $key => $clusterData) {
                    ?>
                <table class="table table-bordered">
                    <tr class="bg-primary text-white">
                        <td colspan="3">
                            List Cluster Individu <?php echo $key+1; ?>
                        </td>
                    </tr>
                    <tr class="bg-primary text-white">
                        <td>Cluster</td>
                        <td>Latitude</td>
                        <td>Longitude</td>
                    </tr>
                    <?php
                    foreach ($clusterData as $key=> $cluster) {
                        ?>
                        <tr>
                            <td>Cluster <?php echo $key+1?></td>
                            <td><?php echo $cluster["latitude"]?></td>
                            <td><?php echo $cluster["longitude"]?></td>
                        </tr>
                        <?php
                    } ?>
                </table>
                <?php
                }
            }*/
            ?>
        </div>
    </div>

    <?php
    if ($_POST != null) {
        ?>
    <div class="row">
        <div class="col-md-12">
            <?php
                foreach ($populationData as $key => $clusterData) {
                    ?>
                <table class="table table-bordered">
                    <tr class="bg-primary text-white">
                        <td colspan="3">
                            List Cluster Individu <?php echo $key+1; ?>
                        </td>
                    </tr>
                    <tr class="bg-primary text-white">
                        <td>Cluster</td>
                        <td>Latitude</td>
                        <td>Longitude</td>
                    </tr>
                    <?php
                    foreach ($clusterData as $key=> $cluster) {
                        ?>
                        <tr>
                            <td>Cluster <?php echo $key+1?></td>
                            <td><?php echo $cluster["latitude"]?></td>
                            <td><?php echo $cluster["longitude"]?></td>
                        </tr>
                        <?php
                    } ?>
                </table>
                <?php
                }
            ?>

        <?php foreach ($populationData as $key1 => $clusterData) {?>
            <?php
            $resultData = calculateDistance($listData, $clusterData);
            $chartData = buildChartGroup($resultData, $clusterData);
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
                            <!--<?php foreach ($avgList as $key => $avg) {?>
                                <?php $clusterData[$key]["latitude"] = array_sum($avg["latitude"])/count($avg["latitude"]);?>
                                <?php $clusterData[$key]["longitude"] = array_sum($avg["longitude"])/count($avg["longitude"]);?>
                            <tr>
                                <td colspan="<?php echo 3+count($clusterData)?>"></td>
                                <td>Cluster <?php echo $key+1;?></td>
                                <td><?php echo "(".array_sum($avg["latitude"])/count($avg["latitude"]).", ".array_sum($avg["longitude"])/count($avg["longitude"]).")";?></td>
                            </tr>
                            <?php }?>-->
                        </table>
                    </div>
                    <div id="scatter<?php echo $i;?>"></div>
                </div>
            </div>
        <?php } ?>
        <?php
        $loop = 0;
        $loopNotChange = 0;
        while ($loop!= $maxLoop && $loopNotChange != $maxLoopNotChange) {
            $loop++; ?>

        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr class="bg-primary text-white">
                        <td>Fungsi Objektif</td>
                        <td>fit</td>
                        <td>FN</td>
                        <td>FKN</td>
                        <td>nfnk</td>
                        <td>% roulete</td>
                    </tr>
                <?php
                $resultRoulete= calculateRoulette($fungsiObjective["fObjective"]); ?>
                <?php foreach ($fungsiObjective["fObjective"] as $key => $nObjective) { ?>
                    <tr>
                        <td><?php echo $nObjective;?></td>
                        <td><?php echo nf($resultRoulete["fit"][$key]);?></td>
                        <td><?php echo nf($resultRoulete["fn"][$key], 5);?></td>
                        <td><?php echo nf($resultRoulete["fkn"][$key], 4);?></td>
                        <td><?php echo nf($resultRoulete["nfnk"][$key]);?></td>
                        <td><?php echo nf($resultRoulete["percent"][$key]);?></td>
                    </tr>
                <?php } ?>
                </table>
            </div>
            <div class="col-md-6">
                    <?php $dataPie = buildPie($resultRoulete["fn"]); ?>
                <div id="pie-roulette<?php echo $loop; ?>"></div>
            </div>

                <script>
                    Highcharts.chart('pie-roulette<?php echo $loop; ?>', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: ''
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                }
                            }
                        },
                        series: [{
                            name: 'Brands',
                            colorByPoint: true,
                            data: <?php echo json_encode($dataPie); ?>
                        }]
                    });

                </script>
        </div>
            <?php
            $bestKey = array_search(min($fungsiObjective["fObjective"]), $fungsiObjective["fObjective"]);
            $populationData = regenerateCrossover($totalIndividu, $crossoverRate, $populationData, $populationData[$bestKey], $resultRoulete);
            $populationData = mutation($populationData, $totalIndividu, $totalCluster, $mutationRate, $listData);
            $tempPopultationData[$loop-1] = $populationData;
            ?>
            <hr>
            <?php
                foreach ($populationData as $key => $clusterData) {
                        ?>
                    <table class="table table-bordered">
                        <tr class="bg-primary text-white">
                            <td colspan="3">
                                List Cluster Individu <?php echo $key+1; ?>
                            </td>
                        </tr>
                        <tr class="bg-primary text-white">
                            <td>Cluster</td>
                            <td>Latitude</td>
                            <td>Longitude</td>
                        </tr>
                        <?php
                        foreach ($clusterData as $key=> $cluster) {
                            ?>
                            <tr>
                                <td>Cluster <?php echo $key+1?></td>
                                <td><?php echo $cluster["latitude"]?></td>
                                <td><?php echo $cluster["longitude"]?></td>
                            </tr>
                            <?php
                        } ?>
                    </table>
                    <?php
                    }
                ?>
                <?php foreach ($populationData as $key1 => $clusterData) {?>
                <?php
                $resultData = calculateDistance($listData, $clusterData);
                $chartData = buildChartGroup($resultData, $clusterData);
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
                                    <!--<?php foreach ($avgList as $key => $avg) {?>
                                        <?php $clusterData[$key]["latitude"] = array_sum($avg["latitude"])/count($avg["latitude"]);?>
                                        <?php $clusterData[$key]["longitude"] = array_sum($avg["longitude"])/count($avg["longitude"]);?>
                                    <tr>
                                        <td colspan="<?php echo 3+count($clusterData)?>"></td>
                                        <td>Cluster <?php echo $key+1;?></td>
                                        <td><?php echo "(".array_sum($avg["latitude"])/count($avg["latitude"]).", ".array_sum($avg["longitude"])/count($avg["longitude"]).")";?></td>
                                    </tr>
                                    <?php }?>-->
                                </table>
                            </div>
                            <div id="scatter<?php echo $i;?>"></div>
                        </div>
                    </div>
                <?php } ?>
            <?php
        } ?>
        </div>
    </div>
    <?php
    }
    ?>
</div>