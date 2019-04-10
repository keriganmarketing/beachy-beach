<?php
/**
 * @package idx
 */

use GuzzleHttp\Client;
use Includes\Modules\MLS\QuickSearch;

$client  = new Client(['base_uri' => 'https://mothership.kerigan.com/api/v1/']);
$raw     = $client->request(
    'GET',
    'allMapListings'
);
$results = json_decode($raw->getBody());


get_header(); ?>
<div id="content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" >

			<?php while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );

			endwhile; ?>

        </main><!-- #main -->
    </div><!-- #primary -->

    <div class="container wide" >
        <div class="row">
            <div class="col">
				<?php get_template_part( 'template-parts/mls', 'searchbar' ); ?>
            </div>
        </div>
        <div id="map-search"></div>
        <?php get_template_part( 'template-parts/mls', 'disclaimer' ); ?>
    </div>
</div>
<script type="text/javascript">
    var map,
        bounds,
        marker,
        infowindow,
        mapElement,
        markers = [],
        markerClusterer,
        styles = [[{
            url: '<?php echo get_template_directory_uri() ?>/img/m1.png',
            height: 50,
            width: 50,
            anchor: [0, 0],
            textColor: '#333333',
            textSize: 12
        }, {
            url: '<?php echo get_template_directory_uri() ?>/img/m2.png',
            height: 60,
            width: 60,
            anchor: [0, 0],
            textColor: '#333333',
            textSize: 12
        }, {
            url: '<?php echo get_template_directory_uri() ?>/img/m3.png',
            width: 70,
            height: 70,
            anchor: [0, 0],
            textColor: '#333333',
            textSize: 13
        }, {
            url: '<?php echo get_template_directory_uri() ?>/img/m4.png',
            width: 80,
            height: 80,
            anchor: [0, 0],
            textColor: '#333333',
            textSize: 13
        }, {
            url: '<?php echo get_template_directory_uri() ?>/img/m5.png',
            width: 90,
            height: 90,
            anchor: [0, 0],
            textColor: '#333333',
            textSize: 14
        }]];

    //load property template on click
    function bindPropertyWindow(marker, mlsnum, pinLocation){
        marker.addListener('click', function() {
            var requestedDoc = '<?php echo get_template_directory_uri() ?>/template-parts/mls-map-listing.php',
              xhttp = new XMLHttpRequest();

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
    }

    function refreshMap(data) {
        if (markerClusterer) {
            markerClusterer.clearMarkers();
        }
        var markers = [];

        for (i = 0; i < data.length; i++) {
            var lat = data[i].latitude,
                lng = data[i].longitude,
                type = data[i].class,
                mlsnum = data[i].mls_account,
                status = data[i].status.toLowerCase();
            if(lat > 29 && lat < 32 && lng > -90 && lng < -83) {

                var pinLocation =  new google.maps.LatLng(parseFloat(lat),parseFloat(lng)),
                pin;

                switch(type) {
                    case 'G':
                    case 'A':
                    case 'H':
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

                marker = new google.maps.Marker({
                    position: pinLocation,
                    map: map,
                    icon: pin
                });

                bindPropertyWindow(marker, mlsnum, pinLocation);
                markers.push(marker);

            }
        }

        infowindow = new google.maps.InfoWindow({
            padding: 0,
            borderRadius: 0,
            arrowSize: 10,
            borderWidth: 0,
            pixelOffset: new google.maps.Size(12, -30),
            backgroundClassName: 'transparent',
        });

        markerClusterer = new MarkerClusterer(map, markers, {
            maxZoom: 14,
            gridSize: 60,
            styles: styles[0]
        });

    }

    function initMap() {

        var mapOptions = {
            zoom: 11,
            center: {lat: 30.250795, lng: -85.940390 },
            disableDefaultUI: true,
            zoomControl: true,
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

        mapElement = document.getElementById('map-search');
        map = new google.maps.Map(mapElement, mapOptions);
        bounds = new google.maps.LatLngBounds();

        refreshMap([]);

    }

</script>
<?php get_template_part( 'template-parts/mls', 'mortgage-calulator' ); ?>
<?php
wp_enqueue_script( 'search-ajax' );
wp_enqueue_script( 'chart-js' );
wp_enqueue_script( 'mortgage-calc' );
get_footer();
?>
<script async defer>

    <?php
    $propertyType = (isset($_GET['propertyType']) && $_GET['propertyType'] != '') ? implode('|', QuickSearch::getPropertyTypes($_GET['propertyType'])) : '';
    ?>
    //get mothership data
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: 'https://mothership.kerigan.com/api/v1/allMapListings',
        data: {
            qs: true,
            city: '<?php echo (isset($_GET['omniField']) ? $_GET['omniField'] : null); ?>',
            propertyType: '<?php echo $propertyType ?>',
            minPrice: '<?php echo (isset($_GET['minPrice']) ? $_GET['minPrice'] : null); ?>',
            maxPrice: '<?php echo (isset($_GET['maxPrice']) ? $_GET['maxPrice'] : null); ?>',
            sq_ft: '<?php echo (isset($_GET['sq_ft']) ? $_GET['sq_ft'] : null); ?>',
            acreage: '<?php echo (isset($_GET['acreage']) ? $_GET['acreage'] : null); ?>',
            bathrooms: '<?php echo (isset($_GET['bathrooms']) ? $_GET['bathrooms'] : null); ?>',
            bedrooms: '<?php echo (isset($_GET['bedrooms']) ? $_GET['bedrooms'] : null); ?>',
            //status: '<?php echo (isset($_GET['status']) ? $_GET['status'] : null); ?>', convert to array
            status: 'Active',
            waterfront: '<?php echo (isset($_GET['waterfront']) ? $_GET['waterfront'] : null); ?>',
            waterfront: '<?php echo (isset($_GET['pool']) ? $_GET['pool'] : null); ?>',
        },
        success: function (data) {
            refreshMap(data);
        }
    });

    //add the pins
    function addPin(lat, lng, type, mlsnum, status){
        var pinLocation = new google.maps.LatLng(parseFloat(lat), parseFloat(lng)),
            pin;

        switch(type) {
            case 'G':
            case 'A':
            case 'H':
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

        marker = new google.maps.Marker({
            position: pinLocation,
            map: map,
            icon: pin
        });
        markers.push(marker);
        marker.addListener('click', function(){
            openProperty(mlsnum, pinLocation);
        });
        //bounds.extend(pinLocation);
        //map.fitBounds(bounds);

    }



</script>
<script src="<?php echo get_template_directory_uri() ?>/js/markerclusterer.js" ></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&callback=initMap" ></script>
<!-- <script async defer src="https://maps.googleapis.com/maps/api/js?callback=initMap" ></script> -->