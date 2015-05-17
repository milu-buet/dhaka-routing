<html>
		<head>
			<title>Map</title>
			<script type="text/javascript" 
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js">
	</script>
			<script type="text/javascript" src="jquery.svg.package/jquery.svg.js">
	</script>
			<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
			<script src="json2.js"></script>
		</head>
		<body>
			<div id="ourMap"></div>
	<script>
    map = new OpenLayers.Map("ourMap");
    map.addLayer(new OpenLayers.Layer.OSM());
    var markers = new OpenLayers.Layer.Markers("Markers");
    map.addLayer(markers);
    //read the osm file
    var id_points = new Array();
    var lat_points = new Array();
    var lon_points = new Array();
    var map_points = new Array();
    var lonlat;
    var index = 0;
    var timer_start = 0;
    var count = 0;
    var s_lat;
    var s_lon;
    var d_lat;
    var d_lon;
    var a_lat = 0;
    var a_lon = 0;
    var select_source = 1;
    var select_dest = 0;
    var select_alter = 0;
    var data;
    var vector;
    var style = {
        strokeColor: '#0000ff',
        strokeOpacity: 0.8,
        strokeWidth: 4
    };
    var counter = setInterval(timer, 1000);

    function timer() {
        if (timer_start == 1) {
            count = count + 1;

            //Do code for showing the number of seconds here
            document.getElementById("timer").innerHTML =
                "Dijkstra running " + count + " secs";
        }
    }
    $(function() {
        $("#load").click(function() {


            timer_start = 1;

            var source_id = $("#source").val();
            var destination_id = $("#destination").val();

            $.ajax({
                method: 'get',
                url: 'LoadGraph.php',
                success: function(data_json) {
                    timer_start = 0;
					console.log(data_json);
                    alert("Graph Loaded");
                }

            });
        });
        //broken
        $("#dijkstra").click(function() {
            timer_start = 1;

            var source_id = $("#source").val();
            var destination_id = $("#destination").val();

            $.ajax({
                method: 'get',
                url: 'AltRunTest.php',
                data: {
                    's_lon': s_lon,
                    's_lat': s_lat,
                    'd_lon': d_lon,
                    'd_lat': d_lat,
                    'a_lon': a_lon,
                    'a_lat': a_lat,
                    'ajax': true
                },
                success: function(data_json) {
                    //alert("ended");
					
                    if (data_json == "error") {
                        alert("No path exists");
                    } else {
                        var data = new Array();
                        //data[0] = $path, $data[1] = $lon, $data[1] = $lat;
						
						try{
							data = JSON.parse(data_json);
						}
						catch(err){
								console.log("Error in parsing");
								console.log(data_json);
								return;
						}
                        
                        var i = 0;
                        var length = data[0].length;
                        for (; i < length; i++) {
                            map_points[i] = new OpenLayers.Geometry.Point(data[1][data[0][i]], data[2][data[0][i]]);
                        }

                        vector = new OpenLayers.Layer.Vector();
                        var featureVector = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(map_points)
                            .transform(new OpenLayers.Projection("EPSG:4326"),
                                new OpenLayers.Projection("EPSG:900913")), null, style);
                        vector.addFeatures([featureVector]);
                        map.addLayers([vector]);

                        timer_start = 0;
                    }
                }
            });
        });
    }); //close $(	
    //read the osm file end
    //marker drawing ends

    ourpoint1 = new OpenLayers.LonLat(90.3989, 23.7937);
    ourpoint1.transform(new OpenLayers.Projection("EPSG:4326"),
        map.getProjectionObject());
    map.setCenter(ourpoint1, 12);

    /// get position from click
    OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
            'single': true,
            'double': false,
            'pixelTolerance': 0,
            'stopSingle': false,
            'stopDouble': false
        },

        initialize: function(options) {
            this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
            OpenLayers.Control.prototype.initialize.apply(
                this, arguments
            );
            this.handler = new OpenLayers.Handler.Click(
                this, {
                    'click': this.trigger
                }, this.handlerOptions
            );
        },

        trigger: function(e) {
            lonlat = map.getLonLatFromPixel(e.xy);
            lonlat.transform(new OpenLayers.Projection("EPSG:900913"),
                new OpenLayers.Projection("EPSG:4326"));
            if (select_source == 1) {
                s_lat = lonlat.lat;
                s_lon = lonlat.lon;

                var ourpoint1  =  new  OpenLayers.LonLat(s_lon, s_lat)
                ourpoint1.transform(new  OpenLayers.Projection("EPSG:4326"),  
                    map.getProjectionObject());

                markers.addMarker(new OpenLayers.Marker(ourpoint1));

                select_dest = 1;
                select_source = 0;
            } else if (select_dest == 1) {
                d_lat = lonlat.lat;
                d_lon = lonlat.lon;

                var ourpoint2  =  new  OpenLayers.LonLat(d_lon, d_lat)
                ourpoint2.transform(new  OpenLayers.Projection("EPSG:4326"),  
                    map.getProjectionObject());

                markers.addMarker(new OpenLayers.Marker(ourpoint2));

                select_dest = 0;
                select_alter = 1;
            } else if (select_alter == 1) {
                a_lat = lonlat.lat;
                a_lon = lonlat.lon;

                var ourpoint3  =  new  OpenLayers.LonLat(a_lon, a_lat)
                ourpoint3.transform(new  OpenLayers.Projection("EPSG:4326"),  
                    map.getProjectionObject());

                markers.addMarker(new OpenLayers.Marker(ourpoint3));

                style = {
                    strokeColor: '#ff0000',
                    strokeOpacity: 0.8,
                    strokeWidth: 4
                };

                select_alter = 0;

            }
        }

    });

    var click = new OpenLayers.Control.Click();
    map.addControl(click);
    click.activate();
</script>
	<button type="button" id="load" name="load">Load Graph</button>
	<button type="button" id="dijkstra" name="dijkstra">Dijkstra</button>
	<label id="timer">Dijkstra not started<label/>
	</body>
	</html>