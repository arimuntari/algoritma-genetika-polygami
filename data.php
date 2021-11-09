<?php
$detailId = isset($_REQUEST["detail_id"])?$_REQUEST["detail_id"]:"";
$polygon = [];
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
                    $polygontemp = [];
                    $polygontemp["lat"] = (double)$data['latitude'];
                    $polygontemp["lng"] = (double)$data['longitude'];
                    $polygon[] = $polygontemp;
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
            const myLatLng = { lat: -1.625758, lng: -247.777179 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 5,
                center: myLatLng,
            });
            <?php

            foreach ($listData as $key => $data) {
                ?>
                 var icon = {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 5,
                    fillColor: "#F00",
                    fillOpacity: 0.4,
                    strokeWeight: 0.4
                };
                var content = '<?php echo "Data ". ($key+1);?>';
                var title = '<?php echo "Data ". ($key+1);?>';
                var latlong = { lat: <?php echo $data['latitude'];?>, lng: <?php echo $data['longitude'];?> };
                addMarker(latlong, map, content, title, icon);
                <?php
            }
            ?>
        }
        var mapsmarker;
        function addMarker(location, map, content, title, icon) {
            var triangleCoords = <?php echo json_encode($polygon);?>;


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