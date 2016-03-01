// We could put a closure around all this stuff as described in Drupal docs
// here: https://www.drupal.org/node/171213. Check that this is generic js
// practice.

var mapbox_id = 'mapbox.streets';
var mapbox_token = 'pk.eyJ1IjoiZWRkaWVjIiwiYSI6ImNpbDg1Z3BsMjAwMWl2ZmtyZm95Z21nMmsifQ.qwiuVtl85Gtwo762qubtOA';        
var centre_lat = 52.4500;
var centre_long = -3.1472;
var initial_zoom = 8;

var map = L.map('map').setView([centre_lat, centre_long], initial_zoom);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    maxZoom: 18,
    id: mapbox_id,
    accessToken: mapbox_token
}).addTo(map);

// The stations variable should have been previously defined in the template.
// @todo Doesn't seem like a very nice way to do this because it creates a
// dependency for this js file.
for (var key in stations) {
    // skip loop if the property is from prototype
    if (!stations.hasOwnProperty(key)) continue;
    
    var station = stations[key];
    var marker = L.marker([station.latitude, station.longitude]).addTo(map);
}