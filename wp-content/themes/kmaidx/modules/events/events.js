/**
 * Created by Bryan on 4/10/2017.
 */
function addEvent ( event_id, event_title, event_start, event_end, event_url ){
    $.ajax({
        type : 'post',
        dataType : 'json',
        url : wpAjax.ajaxurl,
        data : {
            action: 'add_event',
            event_id : event_id,
            event_title : event_title,
            event_start : event_start,
            event_end : event_end,
            event_url : event_url
        },
        success: function(response) {
            //console.log(response);

            $("#calendar").fullCalendar( 'addEventSource', {
                    events: [
                        {
                            title: event_title,
                            start: event_start,
                            end: event_end,
                            url: event_url
                        }
                    ]
                }
            );


        }
    });
}