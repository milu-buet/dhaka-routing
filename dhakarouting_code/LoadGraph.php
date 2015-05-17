    <?php
    
    set_time_limit(1000);
    
    require("Dijkstra.php");
    
    function loadGraph() {
        //read xml
        $lonlat = array();
        $lonlat[0] = array();//lon
        $lonlat[1] = array();//lat
    
        $doc_read = new DOMDocument(); 
        $doc_read->load( 'mapgraph_new.xml' ); 
        $items = $doc_read->getElementsByTagName("item");
        $g = new Graph();
        //LOOP
        foreach ($items as $item)
        {
            $prev_id = $item->getAttribute("prev_id");
            $prev_lon = $item->getAttribute("prev_lon");
            $lonlat[0][$prev_id] = $prev_lon;
            $prev_lat = $item->getAttribute("prev_lat");
            $lonlat[1][$prev_id] = $prev_lat;
            
            $current_id = $item->getAttribute("current_id");
            $current_lon = $item->getAttribute("current_lon");
            $lonlat[0][$current_id] = $current_lon;
            $current_lat = $item->getAttribute("current_lat");
            $lonlat[1][$current_id] = $current_lat;
            
            $distance = $item->getAttribute("distance");
            
            $g->addedge($prev_id, $current_id, $distance);
            $g->addedge($current_id, $prev_id, $distance);	
        }
        session_start();
        $_SESSION["graph"] = serialize($g);
        $_SESSION["lonlat"] = serialize($lonlat);
   
        
       
            echo "Loaded Ok";
    }
    
    
    loadGraph();
    
    ?>
    
