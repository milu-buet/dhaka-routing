<?php
error_reporting(-1);
set_time_limit(1000);
session_start();
require("Dijkstra.php");


$graph = unserialize($_SESSION["graph"]);
$lonlat = unserialize($_SESSION["lonlat"]);
//$source_id = $_GET["source_id"];
//$source_id = 1778185456;
//$destination_id = $_GET["destination_id"];
//$destination_id = 1778185464;

//$s_lat=23.7249233;  //1778185456
//$s_lon=90.4119726;
//$d_lat=23.7253504; //1778185464
//$d_lon=90.4107634;

//var_dump($lonlat);
$s_lat=$_GET["s_lat"];
$s_lon=$_GET["s_lon"];
$d_lat=$_GET["d_lat"];
$d_lon=$_GET["d_lon"];


$s_val = 200.0;
$d_val = 200.0;
foreach($lonlat[0] as $node_id=>$lon)
{
	$slond = $lon - $s_lon;
	$slatd = $lonlat[1][$node_id] - $s_lat;

	$fsd = sqrt($slond*$slond + $slatd*$slatd);
	
	if($fsd < $s_val){
	
		$source_id = $node_id;
		$s_val = $fsd;
	}
	
	
	
	$dlond = $lonlat[0][$node_id] - $d_lon;
	$dlatd = $lonlat[1][$node_id] - $d_lat;

	$fdd = sqrt($dlond*$dlond + $dlatd*$dlatd);
	
	if($fdd < $d_val){
	
		$destination_id = $node_id;
		$d_val = $fdd;
	}

}



function runTest($source_id,$destination_id,$graph,$lonlat) {
	//read xml
	$result_array = array();
	$result_array[0] = array();//path
	$result_array[1] = $lonlat[0];//lon
	$result_array[2] = $lonlat[1];//lat
	$g = new Graph();
	$g = $graph;
        //var_dump($graph);
	$slon=100;
	$slat=100;
	$dlon=100;
	$dlat=100;

        //echo "starting";
	list($distances, $prev) = $g->paths_from($source_id);
	
	$path = $g->paths_to($prev, $destination_id);
	
	//echo "ended";
	
	$result_array[0] = $path;
	
	if(count($path)==0){
		 print_r("error");
		 die();
	}
	
        //echo "paths $path";
	//var_dump($path);
	print_r(json_encode($result_array));
	
}


runTest($source_id,$destination_id,$graph,$lonlat);

?>
