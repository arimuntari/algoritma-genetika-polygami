<?php
$dataId = $_POST?$_POST["dataId"]:1;
$totalCluster = $_POST?$_POST["totalCluster"]:"";
$totalIterasi = $_POST?$_POST["totalIterasi"]:"";
$rowData = getDataList();
$loopNotChanged = 20;
if ($_POST) {
    $listData = getDataDetail($dataId);
    $clusterData = generateCluster($listData, $totalCluster);
    $chartData = buildChart($listData, $clusterData);
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
                        <label for="totalCluster">Jumlah Cluster</label>
                        <input type="text" name="totalCluster" class="form-control" id="totalCluster" placeholder="Masukkan Jumlah Cluster" value="<?php echo $totalCluster;?>">
                    </div>
                    <div class="form-group">
                        <label for="totalIterasi">Jumlah Iterasi</label>
                        <input type="text" name="totalIterasi" class="form-control" id="totalIterasi" placeholder="Masukkan Jumlah Iterasi" value="<?php echo $totalIterasi;?>">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">Submit</button>
                </form>
                <?php
                if ($_POST != null) {
                    ?>
                    <table class="table table-bordered">
                        <tr class="bg-primary">
                            <td colspan="3">
                                List Cluster
                            </td>
                        </tr>
                        <tr class="bg-primary">
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
            </div>
            <div class="col-md-6">
            <?php
                /*if ($_POST != null) {
                    ?>
                    <table class="table table-bordered">
                        <tr class="bg-primary">
                            <td colspan="3">
                                List Data
                            </td>
                        </tr>
                        <tr class="bg-primary">
                            <td>Cluster</td>
                            <td>Latitude</td>
                            <td>Longitude</td>
                        </tr>
                        <?php
                        foreach ($listData as $key => $data) {
                            ?>
                            <tr class="bg-default">
                                <td>Data <?php echo $key+1?></td>
                                <td><?php echo $data["latitude"]?></td>
                                <td><?php echo $data["longitude"]?></td>
                            </tr>
                            <?php
                        } ?>
                    </table>
                    <?php
                }*/
                ?>
            </div>
        </div>
        <?php
        if ($_POST != null) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div id="chartData">
                </div>
            </div>
        </div>
        <script>
            Highcharts.chart('chartData', {
            chart: {
                type: 'scatter',
            },
            title: {
                text: 'Preview Data Awal'
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
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'top',
                x: 100,
                y: 70,
                floating: true,
                backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
                borderWidth: 1
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
        $tmpResultDistance = 0;
        $countLoopNotChanged=0;
        ?>
        <?php for ($i=0; $i < $totalIterasi;$i++) { ?>
            <?php

            $countLoopNotChanged=0;
            ?>
            <?php
                $resultData = calculateDistance($listData, $clusterData);
                $chartData = buildChartGroup($resultData, $clusterData);
                $sumResultDistance = 0;
                foreach ($resultData as $key => $result) {
                    $sumResultDistance+= $result["resultDistance"];
                    $avgList[$result["resultCluster"][0]]["latitude"][] = $result["latitude"];
                    $avgList[$result["resultCluster"][0]]["longitude"][] = $result["longitude"];
                }
                foreach ($avgList as $key => $avg) { ?>
                    <?php $clusterData[$key]["latitude"] = array_sum($avg["latitude"])/count($avg["latitude"]);?>
                    <?php $clusterData[$key]["longitude"] = array_sum($avg["longitude"])/count($avg["longitude"]);?>
                <?php }?>
        <?php
            //echo $sumResultDistance."<br>";
            if($tmpResultDistance == $sumResultDistance){
                $countLoopNotChanged++;
            }else{
                $tmpResultDistance = $sumResultDistance;
                $countLoopNotChanged=0;
            }
            if($countLoopNotChanged == $loopNotChanged){
                break;
            }
        }
        ?>

    <div class="row">
            <div class="col-md-12">
                <div class="mb-2">
                    <table class="table table-bordered">
                        <tr class="bg-primary">
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
                        $sumResultDistance = 0;
                        foreach ($resultData as $key => $result) { ?>
                            <?php
                                $sumResultDistance+= $result["resultDistance"];
                                $avgList[$result["resultCluster"][0]]["latitude"][] = $result["latitude"];
                                $avgList[$result["resultCluster"][0]]["longitude"][] = $result["longitude"];
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
                            <td colspan="<?php echo 3+count($clusterData)?>"></td>
                            <td >Fungsi Objektif: </td>
                            <td><?php echo $sumResultDistance;?> </td>
                        </tr>
                        <tr>
                            <td colspan="<?php echo 3+count($clusterData)?>"></td>
                            <td >Total Iterasi: </td>
                            <td><?php echo $i+1;?> </td>
                        </tr>
                        <?php foreach ($avgList as $key => $avg) {?>
                            <?php $clusterData[$key]["latitude"] = array_sum($avg["latitude"])/count($avg["latitude"]);?>
                            <?php $clusterData[$key]["longitude"] = array_sum($avg["longitude"])/count($avg["longitude"]);?>
                        <tr>
                            <td colspan="<?php echo 3+count($clusterData)?>"></td>
                            <td>Cluster <?php echo $key+1;?></td>
                            <td><?php echo "(".array_sum($avg["latitude"])/count($avg["latitude"]).", ".array_sum($avg["longitude"])/count($avg["longitude"]).")";?></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
                <div id="scatter-result"></div>
                <div id="map"></div>
            </div>
        </div>

    <script>
        function initMap() {
            const myLatLng = { lat: -1.625758, lng: -247.777179 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 5,
                center: myLatLng,
            });
            <?php
            foreach ($chartData as $key => $listData) {
                    ?>
                    var icon = {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: "<?php echo $colors[$key]?>",
                        fillOpacity: 1,
                        strokeWeight: 1
                    };
                    var content = '<?php echo "Cluster ". ($key+1);?>';
                    var title = '<?php echo "Cluster ". ($key+1);?>';
                    var latlong = { lat: <?php echo $listData['latitude'];?>, lng: <?php echo $listData['longitude'];?> };
                    addMarker(latlong, map, content, title, icon);
                    <?php
                    foreach($listData["data"] as $key1 => $data){
                        ?>
                           var icon = {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 5,
                                fillColor: "<?php echo $colors[$key]?>",
                                fillOpacity: 0.4,
                                strokeWeight: 0.4
                            };
                            var content = '<?php echo "Data ". ($key+1);?>';
                            var title = '<?php echo "Data ". ($key+1);?>';
                            var latlong = { lat: <?php echo $data['x'];?>, lng: <?php echo $data['y'];?> };
                            addMarker(latlong, map, content, title, icon);
                        <?php
                    }
                ?>
                <?php
            }
            ?>
        }
        var mapsmarker;
        function addMarker(location, map, content, title, icon) {
            var marker = new google.maps.Marker({
                position: location,
                title: title,
                content: content,
                icon: icon,
                map: map
            });
            var infowindow = new google.maps.InfoWindow({
                content: content,
                position: mapsmarker
            });
            marker.addListener('click', function() {
            // tampilkan info window di atas marker
                infowindow.open(map, marker);
            });
            /*const bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 3,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
            });

            bermudaTriangle.setMap(map);*/
        }
        /*    Highcharts.chart('scatter-result', {
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
        });*/
        </script>
        <?php
            insertResult($dataId, $totalCluster, $sumResultDistance, $totalIterasi, "K-Mean");
        ?>
        <?php }?>
    </div>