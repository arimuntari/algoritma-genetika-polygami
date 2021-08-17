<?php
$detailId = isset($_REQUEST["detail_id"])?$_REQUEST["detail_id"]:"";
?>
<div class="container mt-3">
    <?php
    if (strlen($detailId)>0) {
        $listData = getDataDetail($detailId);
        $chartData = buildChart($listData, []) ?>
            <a href="index.php?act=data" class="btn btn-warning pull-right"> Kembali</a>
            <table class="table table-bordered">
                <tr class="bg-primary text-white">
                    <td colspan="3">
                        List Data
                    </td>
                </tr>
                <tr class="bg-primary text-white">
                    <td>No</td>
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
        <div id="map"></div>

        <script>
            function initMap() {
                const cairo = { lat: 30.064742, lng: 31.249509 };
                const map = new google.maps.Map(document.getElementById("map"), {
                    scaleControl: true,
                    center: cairo,
                    zoom: 10,
                });
                const infowindow = new google.maps.InfoWindow();
                infowindow.setContent("<b>القاهرة</b>");
                const marker = new google.maps.Marker({ map, position: cairo });
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });
            }
            Highcharts.chart('scatter', {
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
    } else {
        ?>
    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Import Data
                </button>
            </div>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <form action="data-excel-process.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="name">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Masukkan File</label>
                                <input type="file" class="form-control-file" name="file">
                            </div>
                            <button type="submit" class="btn btn-primary "> Simpan</button>
                        </div>
                        <div class="col-md-6">
                            <a href="excel/data.xlsx" class="btn btn-success pull-right"> Download Template Import</a>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
    <br>
    <table class="table table-bordered">
        <tr class="bg-primary text-white">
            <td>Nama</td>
            <td>Jumlah</td>
            <td>Aksi</td>
        </tr>
        <?php
            $query = mysqli_query($conn, "select d.data_id, name, count(data_detail_id) as jml_data from data d inner join data_detail dd on d.data_id = dd.data_id group by d.data_id");
        while ($row = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?php echo $row["name"]; ?></td>
                    <td><?php echo $row["jml_data"]; ?></td>
                    <td><a href="index.php?act=data&detail_id=<?php echo $row["data_id"]; ?>" class="btn btn-success btn-sm">Detail</a></td>
                </tr>
                <?php
        } ?>
    </table>
    <?php
    }?>
</div>