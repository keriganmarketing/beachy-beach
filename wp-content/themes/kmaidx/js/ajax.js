/**
 * For Ajax Requests thru WP. Localized as wpAjax.
 */
function toggler(menuVar){
    $('#'+menuVar).toggle();
}

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

    loadIdxAjax();
    loadCommMap();

    $(".lazy").Lazy({
        scrollDirection: 'vertical',
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function(element) {
            console.log('error loading ' + element.data('src'));
        }
    })

});

