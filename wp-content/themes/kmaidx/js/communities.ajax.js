/* Functions used in community map */
//go get pins for community map
function loadCommMap(){
    $.ajax({
        type : 'post',
        dataType : 'json',
        url : wpAjax.ajaxurl,
        data : {
            action: 'loadCommMapPins'
        },
        success: function(data) {
            //console.log(data);

            for (i = 0; i < data.length; i++) {
                var lat = data[i].lat,
                    lng = data[i].lng,
                    type = data[i].type,
                    name = data[i].name;
                    link = data[i].link;

                addMarker(lat,lng,type,name,link);
                addTableRow(type,name,link);
            }
        }

    });
}

$( document ).ready(function(){

    loadCommMap();

});