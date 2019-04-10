<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/16/2017
 * Time: 5:23 PM
 */

?>

        <script type="text/javascript">

            var map,
                bounds,
                mapElement;

            //init map using script include callback
            function initMap() {

                var myLatLng = {lat: 30.250795, lng: -85.940390 };
                // Basic options for a simple Google Map
                // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
                var mapOptions = {
                    // How zoomed in you want the map to start at (always required)
                    zoom: 11,
                    // The latitude and longitude to center the map (always required)
                    center: myLatLng,
                    disableDefaultUI: true,
                    // This is where you would paste any style found on Snazzy Maps.
                    // styles: [
                    //     {
                    //         "featureType": "all",
                    //         "elementType": "labels",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "off"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "administrative",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "off"
                    //             },
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "landscape",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.attraction",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.business",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.government",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#dfdcd5"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.medical",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#dfdcd5"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.park",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#bad294"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.place_of_worship",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.school",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "poi.sports_complex",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#efebe2"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "on"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.highway",
                    //         "elementType": "geometry.fill",
                    //         "stylers": [
                    //             {
                    //                 "color": "#ffffff"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.highway",
                    //         "elementType": "geometry.stroke",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "on"
                    //             },
                    //             {
                    //                 "color": "#dedede"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.highway.controlled_access",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "on"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.arterial",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "on"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.arterial",
                    //         "elementType": "geometry.fill",
                    //         "stylers": [
                    //             {
                    //                 "color": "#ffffff"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.local",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "on"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "road.local",
                    //         "elementType": "labels.icon",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "off"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "transit",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "visibility": "off"
                    //             }
                    //         ]
                    //     },
                    //     {
                    //         "featureType": "water",
                    //         "elementType": "all",
                    //         "stylers": [
                    //             {
                    //                 "color": "#a5d7e0"
                    //             }
                    //         ]
                    //     }
                    // ]
                };

                // Get the HTML DOM element that will contain your map
                // We are using a div with id="map" seen below in the <body>
                mapElement = document.getElementById('office-map');

                // Create the Google Map using our element and options defined above
                map = new google.maps.Map(mapElement, mapOptions);
                bounds = new google.maps.LatLngBounds();

            }

            //add the pins
            function addMarker(lat,lng,type,name) {
                var pinLocation = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));

                switch(type) {
                    case 'neighborhood':

                        var contentString =
                            '<div class="community-map-info ' + type + '">' +
                                '<h3 class="comm-title">' + name + '</h3>' +
                                '<div class="comm-text"><a class="btn btn-block btn-primary" href="/property-search/?q=search&city[]=' + name + '" >View Properties in ' + name + '</div>' +
                            '</div>';

                        break;

                    case 'office':

                        var contentString =
                            '<div class="community-map-info ' + type + '">' +
                                '<h3 class="comm-title">' + name + '</h3>' +
                                '<div class="comm-text">' +
                                    '<a class="btn btn-block btn-primary" href="/contact/" >Contact Us</a>' +
                                    '<a class="btn btn-block btn-primary" href="/team/" >View the Team</a>' +
                                '</div>' +
                            '</div>';

                        break;

                    default:

                        var contentString =
                            '<div class="community-map-info ' + type + '">' +
                                '<h3 class="comm-title">' + name + '</h3>' +
                            '</div>';
                }

                var infowindow = new google.maps.InfoWindow({
                    maxWidth: 279,
                    content: contentString
                });

                var marker = new google.maps.Marker({
                    position: pinLocation,
                    map: map,
                    icon: '<?php echo get_template_directory_uri() ?>/img/'+type+'-pin.png'
                });

                marker.addListener('click', function(){
                    infowindow.open(map, marker);
                });

                bounds.extend(pinLocation);
                map.fitBounds(bounds);

            }

        </script>
        <div id="office-map"></div>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&callback=initMap" ></script>
        <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?callback=initMap" ></script> -->
