<html>
<head>
<title>My first embedded OpenStreetMap page</title>
</head>
<body>
<div id="ourMap"></div>
<iframe width="1000" height="600" frameborder="0" scrolling="no"
marginheight="0" marginwidth="0"
src="http://www.openstreetmap.org/export/embed.html?bbox=
90.3000000,23.6670000,90.4500000,23.9000000&layer=mapnik"
style="border: 1px solid black"></iframe>
<br />
<small>
</small>
</div>
<script
src="http://www.openlayers
.org/api/OpenLayers.js">
</script>
<script type="text/javascript">
var map = new OpenLayers.Map("map", {
projection: "EPSG:900913",
displayProjection: "EPSG:4326"});
map.addLayer(new
OpenLayers.Layer.OSM());
var markers = new
OpenLayers.Layer.Text(
"text", {location: "./markers.txt",
projection: map.displayProjection});
map.addLayer(markers);
map.zoomToMaxExtent();
</script>
</body>
</html>