commute
=======

A Symfony project created on February 29, 2016

At the moment these are just developer notes. I'll replace with something more 
useful when this project is more usable.

Installation from scratch
-------------------------

> git clone git@github.com:edwardcrompton/commute.git

> cd commute

> composer install

[You will be prompted to specify some local database server details]

> php app/console doctrine:database:create

> php app/console doctrine:schema:update --force

To run the server:

> php app/console server:run

Then go to http://127.0.0.1:8000/feed to fetch data from transportAPI.

Go to http://127.0.0.1:8000/map to view the map of stations.

Developer notes
---------------

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

To Do Next
----------

- Work out how it's possible to get the journey time between a pair of stations
using the transportapi.
- It seems that blank responses are obtained if we make request too often.
- Create a journey entity that stores times between a pair of stations.
- Add a form to the map that allows a user to select a city to travel to.
- Create a popup so we can see which station is which on mouse over.
- Remove the existing station icons from the map layers?

Possible reorganisation for classes
-----------------------------------

* Controller
     * Contains only controllers for routers
* Entity
     * Contains only entity classes
* Other stuff could be split by feature / functionality
     * DataFeed : Only for stuff that handles the retrieval of data from the third party service.
     * Storage : For stuff that handles getting and setting persistent variables in the database.
     * Geo : Stuff that handles geographical data that has been pulled from the database and gets it ready to display on the map.
     * Utils : Possibly for stuff that doesn't fit in anywhere else.

 
