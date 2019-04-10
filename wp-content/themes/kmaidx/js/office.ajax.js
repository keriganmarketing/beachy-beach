/* Functions used in community map */
//go get pins for community map
function loadOfficeMap(){
    $.ajax({
        type : 'post',
        dataType : 'json',
        url : wpAjax.ajaxurl,
        data : {
            action: 'loadOfficePins'
        },
        success: function(data) {
            console.log(data);

            for (i = 0; i < data.length; i++) {
                var lat = data[i].lat,
                    lng = data[i].lng,
                    type = data[i].type,
                    name = data[i].name;

                addMarker(lat,lng,type,name);
            }
        }

    });
}

$( document ).ready(function(){

    loadOfficeMap();

});