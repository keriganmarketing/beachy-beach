/**
 * Created by Bryan on 4/25/2017.
 */
function setWeatherIcon(condid) {
    var icon = '';
    switch(condid) {
        case '0': icon  = 'wi-tornado';
            break;
        case '1': icon = 'wi-storm-showers';
            break;
        case '2': icon = 'wi-tornado';
            break;
        case '3': icon = 'wi-thunderstorm';
            break;
        case '4': icon = 'wi-thunderstorm';
            break;
        case '5': icon = 'wi-snow';
            break;
        case '6': icon = 'wi-rain-mix';
            break;
        case '7': icon = 'wi-rain-mix';
            break;
        case '8': icon = 'wi-sprinkle';
            break;
        case '9': icon = 'wi-sprinkle';
            break;
        case '10': icon = 'wi-hail';
            break;
        case '11': icon = 'wi-showers';
            break;
        case '12': icon = 'wi-showers';
            break;
        case '13': icon = 'wi-snow';
            break;
        case '14': icon = 'wi-storm-showers';
            break;
        case '15': icon = 'wi-snow';
            break;
        case '16': icon = 'wi-snow';
            break;
        case '17': icon = 'wi-hail';
            break;
        case '18': icon = 'wi-hail';
            break;
        case '19': icon = 'wi-cloudy-gusts';
            break;
        case '20': icon = 'wi-fog';
            break;
        case '21': icon = 'wi-fog';
            break;
        case '22': icon = 'wi-fog';
            break;
        case '23': icon = 'wi-cloudy-gusts';
            break;
        case '24': icon = 'wi-cloudy-windy';
            break;
        case '25': icon = 'wi-thermometer';
            break;
        case '26': icon = 'wi-cloudy';
            break;
        case '27': icon = 'wi-night-cloudy';
            break;
        case '28': icon = 'wi-day-cloudy';
            break;
        case '29': icon = 'wi-night-cloudy';
            break;
        case '30': icon = 'wi-day-cloudy';
            break;
        case '31': icon = 'wi-night-clear';
            break;
        case '32': icon = 'wi-day-sunny';
            break;
        case '33': icon = 'wi-night-clear';
            break;
        case '34': icon = 'wi-day-sunny-overcast';
            break;
        case '35': icon = 'wi-hail';
            break;
        case '36': icon = 'wi-day-sunny';
            break;
        case '37': icon = 'wi-thunderstorm';
            break;
        case '38': icon = 'wi-thunderstorm';
            break;
        case '39': icon = 'wi-thunderstorm';
            break;
        case '40': icon = 'wi-storm-showers';
            break;
        case '41': icon = 'wi-snow';
            break;
        case '42': icon = 'wi-snow';
            break;
        case '43': icon = 'wi-snow';
            break;
        case '44': icon = 'wi-cloudy';
            break;
        case '45': icon = 'wi-lightning';
            break;
        case '46': icon = 'wi-snow';
            break;
        case '47': icon = 'wi-thunderstorm';
            break;
        case '3200': icon = 'wi-cloud';
            break;
        default: icon = 'wi-cloud';
            break;
    }

    return '<i class="wi '+icon+'"></i>';
}

function loadWeatherAjax( location, format, days ){
    $.ajax({
        type : 'post',
        dataType : 'json',
        url : wpAjax.ajaxurl,
        data : {
            action: 'loadWeather',
            location : location,
            format : format,
            days : days
        },
        success: function(data) {
            console.dir(data);

            var wCode = data.query.results.channel.item.condition.code,
                temp = data.query.results.channel.item.condition.temp,
                condition = data.query.results.channel.item.condition.condition,
                forecast = data.query.results.channel.item.forecast;
                icon = setWeatherIcon( wCode );
                //console.log(format);
                //console.log(days);

            if(days == 1){

                switch(format) {
                    case 'mini':
                        $(".weather-module .weather .weather-container").replaceWith(
                            '<div class="weather-inline-item w-location">' + location + '</div>' +
                            '<div class="weather-inline-item w-icon">' + icon + '</div>' +
                            '<div class="weather-inline-item w-temp">' + temp + '&deg;<span class="deg">F</span></div>'
                        );
                        break;

                    default:
                        $(".weather-module .weather .weather-container").replaceWith(
                            '<div class="weather-block-item w-location text-center">' + location + '</div>' +
                            '<div class="weather-block-item w-icon text-center">' + icon + '</div>' +
                            '<div class="weather-block-item w-temp text-center">' + temp + '&deg;<span class="deg">F</span></div>' +
                            '<div class="weather-block-item w-day text-center">' + condition + '</div>'
                        );
                }
            }else{

                $(".weather-module .weather .weather-container").addClass( "row" );
                $(".weather-module .weather .weather-container").addClass( format );

                switch(format) {
                    case 'mini':
                        for (i = 0; i < days; i++) {

                            var dicon = setWeatherIcon( forecast[i].code );

                            $(".weather-module .weather .weather-container").append(
                                '<div class="day-' + i + ' col" >' +
                                    '<div class="weather-block-item w-day text-center">' + forecast[i].day + '</div>' +
                                    '<div class="weather-block-item w-icon text-center">' + dicon + '</div>' +
                                    '<div class="weather-block-item w-temp text-center">' + forecast[i].high + '&deg;<span class="deg">F</span></div>' +
                                '</div>'
                            )
                        }
                        break;

                    default:
                        for (i = 1; i < days; i++) {

                            var dicon2 = setWeatherIcon( forecast[i].code );

                            $(".weather-module .weather .weather-container").append(
                                '<div class="day-' + i + ' col" >' +
                                '<div class="weather-block-item w-day text-center">' + forecast[i].day + '</div>' +
                                '<div class="weather-block-item w-icon text-center">' + dicon2 + '</div>' +
                                '<div class="weather-inline-item w-temp label">High</div><div class="weather-inline-item w-temp text-center">' + forecast[i].high + '&deg;<span class="deg">F</span></div>' +
                                '<div class="weather-inline-item w-temp label">Low</div><div class="weather-inline-item w-temp text-center">' + forecast[i].low + '&deg;<span class="deg">F</span></div>' +
                                '<div class="weather-block-item w-day text-center">' + forecast[i].text + '</div>' +
                                '</div>'
                            )

                        }

                }
            }

        }

    });
}

$( document ).ready(function(){
    var location = $('.weather').attr('data-location'),
        format = $('.weather').attr('data-format'),
        days = $('.weather').attr('data-days');

    //console.log(location + '|' + format + '|' + days);

    loadWeatherAjax(location, format, days);
});