<div id="map-search" class="mini"></div>
<script type="text/javascript">
    var map,
      bounds,
      mapElement;

    //init map using script include callback
    function initMap() {

        var myLatLng = {lat: 30.250795, lng: -85.940390 };
        var mapOptions = {
            zoom: 11,
            center: myLatLng,
            disableDefaultUI: true,
            styles: [
                {
                    "featureType": "all",
                    "elementType": "labels",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "administrative",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        },
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "poi.attraction",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "poi.business",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "poi.government",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#dfdcd5"
                        }
                    ]
                },
                {
                    "featureType": "poi.medical",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#dfdcd5"
                        }
                    ]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#bad294"
                        }
                    ]
                },
                {
                    "featureType": "poi.place_of_worship",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "poi.school",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "poi.sports_complex",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#efebe2"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry.fill",
                    "stylers": [
                        {
                            "color": "#ffffff"
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry.stroke",
                    "stylers": [
                        {
                            "visibility": "on"
                        },
                        {
                            "color": "#dedede"
                        }
                    ]
                },
                {
                    "featureType": "road.highway.controlled_access",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "geometry.fill",
                    "stylers": [
                        {
                            "color": "#ffffff"
                        }
                    ]
                },
                {
                    "featureType": "road.local",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road.local",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#a5d7e0"
                        }
                    ]
                }
            ]
        };

        mapElement = document.getElementById('map-search');
        map = new google.maps.Map(mapElement, mapOptions);
        bounds = new google.maps.LatLngBounds();

    }

    //add the pins
    function addMarker(lat,lng,type,mlsnum,status) {
        var pinLocation = new google.maps.LatLng(parseFloat(lat),parseFloat(lng)),
          contentString = '',
          mls = mlsnum,
          pin;

        switch(type) {
            case 'G':
            case 'A':
                pin = '<?php echo get_template_directory_uri() ?>/img/residential-'+status+'-pin.png';
                break;
            case 'E':
            case 'J':
            case 'F':
                pin = '<?php echo get_template_directory_uri() ?>/img/commercial-'+status+'-pin.png';
                break;
            case 'C':
                pin = '<?php echo get_template_directory_uri() ?>/img/land-'+status+'-pin.png';
                break;
            default:
                pin = 'http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1';
        }

        var infowindow = new google.maps.InfoWindow();

        var marker = new google.maps.Marker({
            position: pinLocation,
            map: map,
            icon: pin
        });

        marker.addListener('click', function(){
            var requestedDoc = '<?php echo get_template_directory_uri() ?>/template-parts/mls-map-listing.php',
              xhttp = new XMLHttpRequest();

            console.log (requestedDoc);

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var response = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                    infowindow.close(); // Close previously opened infowindow
                    infowindow.setContent('<div class="listing-tile map-search">' + response + '</div>');
                    infowindow.setPosition(pinLocation);
                    infowindow.open(map);
                }
            };
            xhttp.open("GET", requestedDoc + '?mls=' + mlsnum, true);
            xhttp.send();

        });

        bounds.extend(pinLocation);
        map.fitBounds(bounds);

    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?v=3&amp;key=AIzaSyDxp-f8wIi_TJuU5ZRg4Z3KS-T3nkLXYKM&callback=initMap" ></script>
<script>
<?php
    foreach ($listings as $result) {
    $latBounds = ( $result->latitude > 29 && $result->latitude < 32 ? true : false );
    $lngBounds = ( $result->longitude > - 90 && $result->longitude < - 83 ? true : false );
    if($latBounds && $lngBounds){ ?>
    addMarker('<?php echo $result->latitude; ?>', '<?php echo $result->longitude; ?>', '<?php echo $result->class; ?>', '<?php echo $result->mls_account; ?>', '<?php echo strtolower( $result->status ); ?>');
<?php } } ?>
</script>
