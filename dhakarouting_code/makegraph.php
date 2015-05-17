<?php
	set_time_limit(1000);
  //read osm
  $doc_read = new DOMDocument(); 
  $doc_read->load( 'map.osm' ); 
  $doc_write = new DOMDocument();
  $doc_write->formatOutput = true;
   
$nodes = $doc_read->getElementsByTagName( "node" ); 
$nodes_array = array();
foreach($nodes as $node)
{
	$nodes_array[$node->getAttribute("id")] = $node;
}
$ways = $doc_read->getElementsByTagName("way");

$root = $doc_write->createElement("graph");
$doc_write->appendChild($root);
$edge_count = 0;
foreach ($ways as $way)
{
	$nds = $way->getElementsByTagName("nd");
	$index = 0;
	$prev_node = 0;
	$current_node = 0;
	foreach($nds as $nd)
	{
		$current_node_id = $nd->getAttribute("ref");
		if($index==0) $prev_node_id = $current_node_id;
		
		$prev_node = $nodes_array[$prev_node_id];
		$current_node = $nodes_array[$current_node_id]; 
			
		$prev_lon = $prev_node->getAttribute("lon");
		$prev_lat = $prev_node->getAttribute("lat");
	
		$current_lon = $current_node->getAttribute("lon");
		$current_lat = $current_node->getAttribute("lat");
	
		//echo $prev_lon."and".$prev_lat;
		//echo "Connected to ";
		//echo $current_lon."and".$current_lat;
		
		//now we have to determine the euclidean distance between the prev and current nodes
		$square_lon = ($current_lon-$prev_lon) * ($current_lon-$prev_lon);
		$square_lat = ($current_lat - $prev_lat) * ($current_lat - $prev_lat);
		
		$distance = sqrt($square_lon + $square_lat);
		//$distance_rounded = sprintf ("%.2f", $distance);
		
		
		if($prev_node_id != $current_node_id)
		{
			// create child element
			$item = $doc_write->createElement("item");
			$root->appendChild($item);
			
			// create attribute node
			$prev_id_xml = $doc_write->createAttribute("prev_id");
			$item->appendChild($prev_id_xml);
			
			// create attribute value node
			$prev_id_xml_text = $doc_write->createTextNode($prev_node_id);
			$prev_id_xml->appendChild($prev_id_xml_text);
			
			// create attribute node
			$current_id_xml = $doc_write->createAttribute("current_id");
			$item->appendChild($current_id_xml);
			
			// create attribute value node
			$current_id_xml_text = $doc_write->createTextNode($current_node_id);
			$current_id_xml->appendChild($current_id_xml_text);
			
			// create attribute node
			$prev_lon_xml = $doc_write->createAttribute("prev_lon");
			$item->appendChild($prev_lon_xml);
			
			// create attribute value node
			$prev_lon_xml_text = $doc_write->createTextNode($prev_lon);
			$prev_lon_xml->appendChild($prev_lon_xml_text);
			
			// create attribute node
			$prev_lat_xml = $doc_write->createAttribute("prev_lat");
			$item->appendChild($prev_lat_xml);
			
			// create attribute value node
			$prev_lat_xml_text = $doc_write->createTextNode($prev_lat);
			$prev_lat_xml->appendChild($prev_lat_xml_text);
			
			// create attribute node
			$current_lon_xml = $doc_write->createAttribute("current_lon");
			$item->appendChild($current_lon_xml);
			
			// create attribute value node
			$current_lon_xml_text = $doc_write->createTextNode($current_lon);
			$current_lon_xml->appendChild($current_lon_xml_text);
			
			// create attribute node
			$current_lat_xml = $doc_write->createAttribute("current_lat");
			$item->appendChild($current_lat_xml);
			
			// create attribute value node
			$current_lat_xml_text = $doc_write->createTextNode($current_lat);
			$current_lat_xml->appendChild($current_lat_xml_text);
			
			// create attribute node
			$distance_xml = $doc_write->createAttribute("distance");
			$item->appendChild($distance_xml);
			
			// create attribute value node
			$distance_xml_text = $doc_write->createTextNode($distance);
			$distance_xml->appendChild($distance_xml_text);
			
			// create attribute node
			$cluster_id_prev_xml = $doc_write->createAttribute("cluster_id_prev");
			$item->appendChild($cluster_id_prev_xml);
			
			// create attribute value node
			$cluster_id_prev_xml_text = $doc_write->createTextNode("0");
			$cluster_id_prev_xml->appendChild($cluster_id_prev_xml_text);
			
			// create attribute node
			$cluster_id_current_xml = $doc_write->createAttribute("cluster_id_current");
			$item->appendChild($cluster_id_current_xml);
			
			// create attribute value node
			$cluster_id_current_xml_text = $doc_write->createTextNode("0");
			$cluster_id_current_xml->appendChild($cluster_id_current_xml_text);
			
			$edge_count++;
		}
		
		$prev_node_id = $current_node_id;
		$index++;
		//NEW$node = $doc_read->getElementsByAttribute("id",$nd->getAttribute("ref"));
		//var_dump($nodes->getElementById($node_id));
		//var_dump($node);
	}
} 
  	// create child element
	$init = $doc_write->createElement("init");
	$root->appendChild($init);
	
	// create attribute node
	$number_nodes = $doc_write->createAttribute("node");
	$init->appendChild($number_nodes);
	
	// create attribute value node
	$number_nodes_text = $doc_write->createTextNode(count($nodes_array));
	$number_nodes->appendChild($number_nodes_text);
	
	// create attribute node
	$number_edges = $doc_write->createAttribute("edge");
	$init->appendChild($number_edges);
	
	// create attribute value node
	$number_edges_text = $doc_write->createTextNode($edge_count);
	$number_edges->appendChild($number_edges_text);
	
  //read osm ends
  echo $doc_write->saveXML();
  $doc_write->save("mapgraph_new.xml");
  
  
?>