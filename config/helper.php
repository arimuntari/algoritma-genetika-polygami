<?php
function connection()
{
    $conn = new mysqli("localhost", "root", "", "db_skripsi");
    return $conn;
}

function getDataList(){
    $data = [];
    $conn = connection();
    $query = mysqli_query($conn, "select * from data");
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    return $data;
}
function getDataCluster(){
    $data = [];
    $conn = connection();
    $query = mysqli_query($conn, "select distinct(cluster) from process");
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    return $data;
}

function getChartResult($dataId, $cluster, $type){
    $data = [];
    $conn = connection();
    $query = mysqli_query($conn, "select * from process where data_id = '$dataId' and cluster ='$cluster' and type='$type'");
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = (double)$row["f_objektif"];
    }
    return $data;
}

function getDataDetail($dataId = 1)
{
    $data = [];
    $conn = connection();
    $query = mysqli_query($conn, "select * from data_detail where data_id = '$dataId'");
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    return $data;
}

function generateCluster($data, $totalCluster, $isMutation=false)
{
    $minLat = min(array_column($data, 'latitude'));
    $maxLat = max(array_column($data, 'latitude'));
    $minLong = min(array_column($data, 'longitude'));
    $maxLong = max(array_column($data, 'longitude'));
    $cluster = [];
    for ($i = 0; $i<$totalCluster;$i++) {
        if($isMutation){
            $arr = [];
            $arr["latitude"] = array_rand(array_column($data, 'latitude'));
            $arr["longitude"] = array_rand(array_column($data, 'longitude'));
            $cluster[] = $arr;
        }else{
            $arr = [];
            $arr["latitude"] = rand($minLat, $maxLat);
            $arr["longitude"] = rand($minLong, $maxLong);
            $cluster[] = $arr;
        }
    }
    //var_dump($cluster);
    return $cluster;
}

function generatePopulation($data, $totalIndividu, $totalCluster)
{
    $listPopulation = [];
    for ($i=0;$i<$totalIndividu;$i++) {
        $cluster = generateCluster($data, $totalCluster);
        $listPopulation[] = $cluster;
    }
    return $listPopulation;
}

function buildChart($listData, $listCluster)
{
    $series = [];
    $arrData = [];
    $arrData["name"] = "Data";
    foreach ($listData as $data) {
        $arrData["data"][] = array((float)$data["latitude"], (float)$data["longitude"]);
    }
    $series[] = $arrData;
    foreach ($listCluster as $key => $cluster) {
        $arrData = [];
        $arrData["name"] = "Cluster ".($key+1);
        $arrData["data"][] = array((float)$cluster["latitude"], (float)$cluster["longitude"]);
        $series[] = $arrData;
    }
    //var_dump($series);
    return $series;
}
function euclidDistance($from, $to)
{
    $result = sqrt((pow($to["latitude"] - $from["latitude"], 2) + pow($to["longitude"] - $from["longitude"], 2)));
    //echo "result:".$result;
    return $result;
}
function calculateDistance($listData, $listCluster)
{
    $resultdata = [];
    foreach ($listData as $data) {
        $listResult = [];
        foreach ($listCluster as $key => $cluster) {
            $listResult[$key] =  euclidDistance($data, $cluster);
            $data["result"][$key] = euclidDistance($data, $cluster);
        }
        $data["resultDistance"] = min($listResult);
        $data["resultCluster"] = array_keys($listResult, min($listResult));
        $resultdata[] = $data;
    }
    //var_dump($resultdata);
    return $resultdata;
}

function buildChartGroup($resultDistance, $listCluster)
{
    $series = [];
    $arrData = [];
    foreach ($listCluster as $key => $cluster) {
        $arrData = [];
        $arrData["data"][] = array("selected"=> "true", "x" =>(float)$cluster["latitude"], "y"=> (float)$cluster["longitude"]);
        $j =0;
        foreach ($resultDistance as $result) {
            if ($result["resultCluster"][0] == $key) {
                $arrData["name"] = "Data ".($key+1);
                $arrData["data"][] = array("x" =>(float)$result["latitude"], "y"=> (float)$result["longitude"]);
            }
            $j++;
        }
        $series[] = $arrData;
    }
    //echo json_encode($series)."<br>";
    return $series;
}
function calculateFit($fObjective)
{
    $result = [];
    foreach ($fObjective as $key => $nObjective) {
        $fit = 1/ (1 + $nObjective);
        $result[] = $fit;
    }
    return $result;
}

function calculateFN($fitList)
{
    $result = [];
    foreach ($fitList as $key => $fit) {
        $fn = $fit - min($fitList);
        $result[]= $fn;
    }
    return $result;
}

function calculateFKN($fnList)
{
    $result = [];
    $fkn = $fnList[0];
    $result[] = $fkn;
    for ($i=1;$i<count($fnList);$i++) {
        $fkn = $result[$i-1]+$fnList[$i];
        $result[] = $fkn;
    }
    return $result;
}

function calculateNFNK($fknList)
{
    $result = [];
    foreach ($fknList as $key => $fkn) {
        $fn = $fkn/max($fknList);
        $result[]= $fn;
    }
    return $result;
}

function calculatePercent($fnList)
{
    $result = [];
    foreach ($fnList as $key => $fn) {
        $percent = $fn/array_sum($fnList) * 100;
        $result[]= $percent;
    }
    return $result;
}

function calculateRoulette($fObjective)
{
    $result = [];
    $result["fObjective"] = $fObjective;
    $result["fit"] = calculateFit($fObjective);
    $result["fn"] = calculateFN($result["fit"]);
    $result["fkn"] = calculateFKN($result["fn"]);
    $result["nfnk"] = calculateNFNK($result["fkn"]);
    $result["percent"] = calculatePercent($result["fn"]);
    return $result;
}
function nf($value, $val =2)
{
    return number_format($value, $val);
};

function buildPie($dataList)
{
    $result = [];
    foreach ($dataList as $key => $data) {
        $tmp = [];
        $tmp["name"]= "Individu ".($key+1);
        $tmp["y"]= $data;
        $result[]= $tmp;
    }
    return $result;
}
function randFloat($min, $max, $decimals)
{
    $scale = pow(10, $decimals);
    return rand($min, $max) / $scale;
}

function crossOverArithmetic($crossOverRate, $populationData, $bestPopulationData, $resultRoulete)
{
    $result["crossoverArithmetic"] =[];
    $result["populationData"] = $populationData;
    $r = randFloat(1, 10, 1);
    // echo $r." | ";
    if ($r < $crossOverRate) {
        $y1 =randFloat(1, 10, 1);
        $y2 =1 - $y1;
        $candidatePopulation=0;
        foreach ($resultRoulete["nfnk"] as $key => $nfnk) {
            if ($nfnk > $y1) {
                $candidatePopulation = $key;
                break;
            }
        }
        $currentPopulation = $populationData[$candidatePopulation];
        for($i = 0;$i<count($bestPopulationData);$i++){
            $result["crossoverArithmetic"]["valueCluster"][$i]["latitude"] = $bestPopulationData[$i]["latitude"] * $y1 + $currentPopulation[$i]["latitude"]* $y2;
            $result["crossoverArithmetic"]["valueCluster"][$i]["longitude"] = $bestPopulationData[$i]["longitude"] * $y1 + $currentPopulation[$i]["longitude"]* $y2;
        }

        $cy1 = $bestPopulationData[1]["latitude"] * $y1 + $currentPopulation[1]["latitude"]* $y2;
        $cy2 = $bestPopulationData[1]["longitude"] * $y1 + $currentPopulation[1]["longitude"]* $y2;


        $result["crossoverArithmetic"]["r"] =$r;
        $result["crossoverArithmetic"]["y1"] =$y1;
        $result["crossoverArithmetic"]["y2"] =$y2;
        $result["crossoverArithmetic"]["candidatePopulation"] = $candidatePopulation;
        $result["crossoverArithmetic"]["currentPopulation"] = $currentPopulation;
    }
    // echo "<br>";;
    return $result;
}
function regenerateCrossover($totalIndividu, $crossoverRate, $populationData, $bestPopulationData, $resultRoulete)
{
    $result = [];
    for($i=0;$i<$totalIndividu;$i++){
        $resultCrossover = crossOverArithmetic($crossoverRate, $populationData, $bestPopulationData, $resultRoulete);
        $crossoverAttribute = $resultCrossover["crossoverArithmetic"];
        if (count($crossoverAttribute) > 0) {
            $result[] = $crossoverAttribute["valueCluster"];
        }else{
            $result[] = $populationData[$i];
        }
    }
    return $result;
}
function mutation($populationData, $totalIndividu, $totalCluster, $mutationRate, $listData){
    $totalMutation = ceil($mutationRate * $totalIndividu);
    for($i=0;$i<$totalMutation;$i++){
        $rand = rand(1, $totalIndividu);
        $populationData[$rand-1] = generateCluster($listData, $totalCluster, false);
    }
    return $populationData;
}
function insertResult($dataId, $totalCluster, $sumResultDistance, $iterasi, $type){
    $conn = connection();
    $query = mysqli_query($conn, "insert into `process` (data_id, cluster, f_objektif, iterasi,  type, date)
                                    values ('$dataId','$totalCluster', '$sumResultDistance', '$iterasi', '$type', now())");
}