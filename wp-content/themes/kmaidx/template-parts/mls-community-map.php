
<script type="text/javascript">
    function toggleADAversion(){        
        let map = window.document.getElementById("community-map");
        let adamap = window.document.getElementById("community-ada-map");
        let adabutton = window.document.getElementById("ada-compliant-button");

        if(adamap.style.display === 'none' ){
            map.style.display = 'none';
            adamap.style.display = 'block';
            adabutton.innerText = 'Back to map';
        }else{
            map.style.display = 'block';
            adamap.style.display = 'none';
            adabutton.innerText = 'View in List Format';
        }
    }

    var map,
        bounds,
        mapElement,
        currentInfoWindow = null;

    //init map using script include callback
    function initMap() {

        var myLatLng = {lat: 30.250795, lng: -85.940390 };
        var mapOptions = {
            zoom: 11,
            center: myLatLng,
            disableDefaultUI: true,
            zoomControl: true,
        };

        mapElement = document.getElementById('community-map');
        map = new google.maps.Map(mapElement, mapOptions);
        panorama = new google.maps.StreetViewPanorama(mapElement);
        bounds = new google.maps.LatLngBounds();

        panorama.setVisible(false);

    }

    //add the pins
    function addMarker(lat, lng, type, name, link) {
        var link = link!='' ? link : '';
        var pinLocation = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));

        switch(type) {
            case 'neighborhood':

                var contentString =
                    '<div class="community-map-info ' + type + '">' +
                        '<h3 class="comm-title">' + name + '</h3>' +
                        '<div class="comm-text"><a class="btn btn-block btn-primary" href="' + link + '" >View Properties in ' + name + '</div>' +
                    '</div>';

                break;

            case 'beach':

                var contentString =
                    '<div class="community-map-info ' + type + '">' +
                    '<h3 class="comm-title">' + name + '</h3>' +
                    '<div class="comm-text"><a class="btn btn-block btn-primary" onclick="openBeachViewer(' + parseFloat(lat) + ',' + parseFloat(lng) + ')" >View the Beach</div>' +
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
            title: name,
            // label: name,
            position: pinLocation,
            map: map,
            icon: '<?php echo get_template_directory_uri() ?>/img/'+type+'-pin.png'
        });

        marker.addListener('mouseover', function(){
            if (currentInfoWindow != null) {
                currentInfoWindow.close();
            }
            infowindow.open(map, marker);
            currentInfoWindow = infowindow;
        });

        bounds.extend(pinLocation);
        //map.fitBounds(bounds);

    }

    function openBeachViewer(lat, lng){

        var pinLocation = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));

        panorama = map.getStreetView();
        panorama.setPosition(pinLocation);
        panorama.setPov(({
            heading: 265,
            pitch: 0
        }));

        panorama.setVisible(true);
    }

    function addTableRow(type,name,link){
        let div = document.createElement('div');
        div.className = 'col-md-4 pt-4 text-left';

        switch(type) {
            case 'neighborhood':
                var contentString =
                        '<h3>' +
                        '<img src="<?php echo get_template_directory_uri() ?>/img/'+type+'-pin.png" alt="' + name + '" class="mr-4" >' +
                        name + '</h3>' +
                        '<div class="comm-text"><a class="btn btn-block btn-primary" style="text-decoration: none;" href="' + link + '" >View Properties</div>';
                break;

            case 'beach':
                var contentString = '';
                break;

            case 'office':
                var contentString =
                        '<h3>' +
                        '<img src="<?php echo get_template_directory_uri() ?>/img/'+type+'-pin.png" alt="' + name + '" class="mr-4" >' +
                        name + '</h3>' +
                        '<div class="comm-text">' +
                            '<a class="btn btn-block btn-primary" style="text-decoration: none;" href="/contact/" >Contact Us</a>' +
                            '<a class="btn btn-block btn-primary" style="text-decoration: none;" href="/team/" >View the Team</a>' +
                        '</div>';
                break;

            default:
                var contentString =
                        '<h3>' + name + '</h3>';
        }

        div.innerHTML = contentString;
        if(contentString !== ''){
            window.document.getElementById('data-table').appendChild(div);
        }
    }

</script>
<div id="community-map" ></div>
<div id="community-ada-map" style="display:none; background-color:#eee; padding: 2rem;" >
<div class="row" id="data-table"></div>
</div>
<p class="text-white text-center">
    <a 
        href="javascript:;"
        id="ada-compliant-button"
        class="btn btn-info" style="text-decoration: none;"
        onkeypress="toggleADAversion() this.preventDefault();" 
        onclick="toggleADAversion(); this.preventDefault();" 
        >View in List Format
    </a>
</p>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&callback=initMap" ></script>
<!-- <script async defer src="https://maps.googleapis.com/maps/api/js?callback=initMap" ></script> -->
