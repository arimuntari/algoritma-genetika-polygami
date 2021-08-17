<?php
require "config/connection.php";
require "config/helper.php";
$act = $_REQUEST?$_REQUEST["act"]:"";
?>
<html>
<head>
<title>Skripsi | Polygami GA-PSO-mean</title>
<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css"></link>
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="bower_components/highcharts/highcharts.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap&libraries=&v=weekly&language=ar&region=EG" async></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <a class="navbar-brand" href="#">Skripsi</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item <?php echo $act==""?"active":"";?>">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item <?php echo $act=="data"?"active":"";?>">
                <a class="nav-link" href="index.php?act=data">Data</a>
            </li>
            <li class="nav-item <?php echo $act=="kmean"?"active":"";?>">
                <a class="nav-link" href="?act=kmean">K-Mean</a>
            </li>
            <li class="nav-item <?php echo $act=="gamean"?"active":"";?>">
                <a class="nav-link" href="?act=gamean1">Genethic Algorithm Polygami</a>
            </li>
        </ul>
    </div>
    </nav>
    <?php
        switch ($act) {
            case "data":
                include "data.php";
                break;
            case "kmean":
                include "kmean.php";
                break;
            case "gamean":
                include "gamean.php";
                break;
            case "gamean1":
                include "gamean1.php";
                break;
            default:
                include "dashboard.php";
        }
    ?>
</body>
</html>