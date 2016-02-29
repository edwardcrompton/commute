commute
=======

A Symfony project created on February 29, 2016, 11:08 am.

At the moment these are just developer notes. I'll replace with something more useful when this project is more usable.

To run the server:
php app/console server:run

Then go to http://127.0.0.1:8000/feed

Using php-curl-class:
https://github.com/php-curl-class/php-curl-class

Documentation for the transport api:
http://docs.transportapi.com/index.html?raml=http://transportapi.com/v3/raml/transportapi.raml##request_uk_train_stations_bbox_json

Doctrine documentation for writing to the database:
http://symfony.com/doc/2.8/book/doctrine.html

Documentation for leafletjs    
https://github.com/bmatzner/BmatznerLeafletBundle
http://leafletjs.com/

Documentation for mapbox tiles
https://www.mapbox.com/developers/api/maps/

Running long processes in Symfony
http://blog.servergrove.com/2014/04/16/symfony2-components-overview-process/

To Do Next:

- Check out fatal error and then read 
http://symfony.com/doc/2.8/book/service_container.html and 
http://stackoverflow.com/questions/19281713/fatalerrorexception-error-call-to-a-member-function-has-on-a-non-object
- Station data is returned in multiple pages from the Transport API. We need to 
fetch all the pages. This can be done on a cron job if we persist a variable
between cron runs using the Variable entity I've created.
- Create a journey entity that stores times between a pair of stations.
- Add a form to the map that allows a user to select a city to travel to.
- Perhaps the class that fetches the data from the API should be a service?
- Create a popup so we can see which station is which on mouse over.
- Remove the existing station icons from the map layers?
