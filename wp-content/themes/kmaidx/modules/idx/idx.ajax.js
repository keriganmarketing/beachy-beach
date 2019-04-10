/**
 * Created by Bryan on 4/21/2017.
 */
function toggler(menuVar){
    $('#'+menuVar).toggle();
}

function loadIdxAjax(){
    $.ajax({
        type : 'post',
        dataType : 'json',
        url : wpAjax.ajaxurl,
        data : {
            action: 'loadIdx'
        },
        success: function(data) {
            console.dir(data);

            $(".area-select").select2({
                placeholder: 'City / Area / Subdivision / Zip',
                dataType: 'json',
                width: '100%',
                tags: true,
                data: data.areaArray
            });

            $('.prop-type-input').select2({
                placeholder: 'Property Type',
                dataType: 'json',
                width: '100%',
                data: data.typeArray
            });

        }

    });
}


$( document ).ready(function(){

    loadIdxAjax();

    $('.rembutton').click(function(){

        $.ajax({
            type : 'post',
            dataType : 'json',
            url : wpAjax.ajaxurl,
            data : {
                action: 'removeVar',
                remove: $(this).attr('data-call')
            },
            success: function(data) {
                console.dir(data);
            }

        });

    });

    $(".lazy").Lazy({
        scrollDirection: 'vertical',
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function(element) {
            console.log('error loading ' + element.data('src'));
        }
    })

});


