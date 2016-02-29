var mapbox_id = 'mapbox.streets';
var mapbox_token = 'pk.eyJ1IjoiZWRkaWVjIiwiYSI6ImNpbDg1Z3BsMjAwMWl2ZmtyZm95Z21nMmsifQ.qwiuVtl85Gtwo762qubtOA';        
var centre_lat = 52.4500;
var centre_long = -3.1472;

var map = L.map('map').setView([centre_lat, centre_long], 8);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    maxZoom: 18,
    id: mapbox_id,
    accessToken: mapbox_token
}).addTo(map);

