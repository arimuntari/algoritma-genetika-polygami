<?php
include "config/connection.php";
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$name = $_REQUEST["name"];
$file = $_FILES['file']['tmp_name'];
$data = PhpOffice\PhpSpreadsheet\IOFactory::load($file)->getActiveSheet();
$hasildata = $data->toArray(null, true, true, true);

$jml  =count($hasildata);
if ($jml > 1) {
    $query = mysqli_query($conn, "insert into data (name) values ('$name')");

    $dataId = mysqli_insert_id($conn);
    var_dump($dataId);

    for ($i=2;$i<=$jml;$i++) {
        $latitude = $data->getCell('A'.$i)->getValue();
        $longitude = $data->getCell('B'.$i)->getValue();
        $query = mysqli_query($conn, "insert into data_detail (data_id, latitude, longitude) values ('$dataId', '$latitude', '$longitude')");
    }
}
header("Location: index.php?act=data");
