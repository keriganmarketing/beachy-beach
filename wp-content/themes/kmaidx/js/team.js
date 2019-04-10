/**
 * Created by Bryan on 5/25/2017.
 */
$(document).ready(function() {
    $('.filter-button').click( function() {
        var filter = $(this).attr('data-filter');
        console.log(filter + ' clicked');
        $('.agent-card').addClass('inactive');
        $('.agent-card.' + filter + '-filter').removeClass('inactive');
        $('.agent-card.' + filter + '-filter').addClass('active');
    });
});