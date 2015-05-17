<?php
error_reporting(-1);
set_time_limit(1000);
session_start();
require("Dijkstra.php");

$graph = unserialize($_SESSION["graph"]);
$lonlat = unserialize($_SESSION["lonlat"]);
$latest_path = unserialize($_SESSION["path"]);

$s_lat=$_GET["s_lat"];
$s_lon=$_GET["s_lon"];
$d_lat=$_GET["d_lat"];
$d_lon=$_GET["d_lon"];
$a_lat=$_GET["a_lat"];
$a_lon=$_GET["a_lon"];


$mid_lon = ($s_lon+$d_lon)/2;
$mid_lat = ($s_lat+$d_lat)/2;

$radius=($mid_lon-$s_lon)*($mid_lon-$s_lon)+($mid_lat-$s_lat)*($mid_lat-$s_lat)+.001;

function find_nearest_node($s_lon,$s_lat,$d_lon,$d_lat,$a_lon,$a_lat,$lonlat,
$mid_lon,$mid_lat,$radius,$g,$latest_path)
{
   $clicked_node = array();
   $clicked_node[2] = array();//the graph will be here
   $slon=100;
   $dlon=100;
   $alon=100;
   if (is_array($lonlat[0])) {
	   foreach($lonlat[0] as $id=>$lon)
	   {
		$distance_check = ($lon-$mid_lon)*($lon-$mid_lon)
		+($lonlat[1][$id]-$mid_lat)*($lonlat[1][$id]-$mid_lat);
	    if($distance_check>$radius)
		{
			//unset($g->nodes[$id]);
			//continue;
		}
		//now we have to determine the euclidean distance between 
		//the prev and curr nodes
		$square_lon = ($lon-$s_lon) * ($lon-$s_lon);
		$square_lat = ($lonlat[1][$id] - $s_lat) * ($lonlat[1][$id] - $s_lat);
		
		$sd = $square_lon + $square_lat;
		//$distance_rounded = sprintf ("%.2f", $distance);
		
		if($sd<$slon)
		{
			$slon = $sd;
			$source_id = $id ;
		}
		
		//now we have to determine the euclidean distance between 
		//the prev and current nodes
		$square_lon = ($lon-$d_lon) * ($lon-$d_lon);
		$square_lat = ($lonlat[1][$id] - $d_lat) * ($lonlat[1][$id] - $d_lat);
		
		//$distance_rounded = sprintf ("%.2f", $distance);
		$dd = $square_lon + $square_lat;
		if($dd<$dlon)
		{
			$dlon = $dd;
			$destination_id = $id;
		}
		
	   } 
   }
   
   if(is_array($latest_path)){
   	
   
	foreach($latest_path as $id){
				
		$square_lon = ($lonlat[0][$id]-$a_lon) * ($lonlat[0][$id]-$a_lon);
		$square_lat = ($lonlat[1][$id] - $a_lat) * ($lonlat[1][$id] - $a_lat);
							
		$ad = $square_lon + $square_lat;
		if($ad<$alon)
		{
			$alon = $ad;
			$alter_id = $id;
		}
	}
		   
		   if(isset($alter_id)){
				unset($g->nodes[$alter_id]);
		   }
		    
}
   
		   
 
   $clicked_node[0] = $source_id;
   $clicked_node[1] = $destination_id;
   $clicked_node[2] = $g;
   
   return $clicked_node;
}


function runTest($s_lon,$s_lat,$d_lon,$d_lat,$a_lon,$a_lat,
$graph,$lonlat,$mid_lon,$mid_lat,$radius,$latest_path) {
	//read xml
	$result_array = array();
	$result_array[0] = array();//path
	$result_array[1] = $lonlat[0];//lon
	$result_array[2] = $lonlat[1];//lat
	$g = new Graph();
	$g = $graph;
        //var_dump($graph);
	$clicked_node = array();
	
        ///finding source_id and destination_id
    	$clicked_node = find_nearest_node($s_lon,$s_lat,$d_lon,$d_lat,
	$a_lon,$a_lat,$lonlat,$mid_lon,$mid_lat,$radius,$g,$latest_path);
        $source_id = $clicked_node[0];
        $destination_id = $clicked_node[1];
	$g = $clicked_node[2];	
	list($distances, $prev) = $g->paths_from($source_id);
	$path = $g->paths_to($prev, $destination_id);
	$result_array[0] = $path;
	$_SESSION["path"] = serialize($path);
	
	if(count($path)==0){
		 print_r("error");
		 die();
	}
        //echo "paths $path";
	//var_dump($path);
	print_r(json_encode($result_array));	
}
runTest($s_lon,$s_lat,$d_lon,$d_lat,$a_lon,$a_lat,$graph,
$lonlat,$mid_lon,$mid_lat,$radius,$latest_path);
?>

