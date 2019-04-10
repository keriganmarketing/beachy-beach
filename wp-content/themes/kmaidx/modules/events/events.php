<?php
//CREATE SERVICE CPT
$events = new Custom_Post_Type(
    'Event',
    array(
        'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
        'menu_icon'			 => 'dashicons-calendar-alt',
        'rewrite'            => array( 'slug' => 'events' ),
        'hierarchical'       => false,
        'has_archive' 		 => false,
        'menu_position'      => null,
        'public'             => true,
        'publicly_queryable' => true,
    )
);

$events->add_taxonomy( 'Event Category' );

$events->add_meta_box(
    'Event Info',
    array(
        'Start Date'	=> 'date',
        'End Date' 		=> 'date',
        'Featured'      => 'boolean'
    )
);

function caldata($edata){
	echo '
	    <script>
	    $( document ).ready(function() {
		    $(\'#calendar\').fullCalendar({
		    	header: {
					left:   \'prev,next\',
				    center: \'title\',
				    right:  \'month,listMonth \'
			    }
	        });
		    addEventsToCal();
	    });
        </script>';
}

function getevents_func( $atts, $content = null ){
	$output = '';
    $debugevents = FALSE;

    $a = shortcode_atts(array(
        'category' => '',
        'truncate' => 0,
        'format' => 'list',
        'featuredonly' => false,
        'sortby' => 'meta_value',
        'sort' => 'ASC',
    ), $atts);

    if ($debugevents) {
        $output .= '<p>category = ' . $a['category'] . '</p>';
    }

    $request = array(
	    'posts_per_page'   => -1,
	    'post_type'        => 'event',
        'order'            => 'ASC',
        'orderby'   	   => 'meta_value',
	    'meta_key'         => 'event_info_start_date',
        'post_status'      => 'publish',
    );

    if($a['category']!= ''){
        $categoryarray = array(
            array(
                'taxonomy' => 'event_category',
                'field' => 'slug',
                'terms' => $a['category'],
                'include_children' => false,
            ),
        );
        $request['tax_query'] = $categoryarray;
    }

    if($a['featuredonly'] === TRUE){
        $metaarray = array(
            array(
                'key' => 'event_info_featured',
                'value'   => '1',
                'compare' => '!='
            ),
        );
        $request['meta_query'] = $metaarray;
    }

    if($debugevents){
        print_r($request);
    }

	$events = get_posts( $request );

	function truncate($text, $chars = 25) {
		$text = $text." ";
		$text = substr($text,0,$chars);
		$text = substr($text,0,strrpos($text,' '));
		$text = $text."...";
		return $text;
	}

	add_action('wp_ajax_add_event', 'add_event');
	add_action('wp_ajax_nopriv_add_event', 'add_event');
	function add_event(){

		//php request
		$something = 'something in wordpress';
		$result[] = $something;

		//result
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$result = json_encode($result);
			echo $result;
		}

		die();

	}

	$eventdata = '';
	$output = '';

	if($a['format']=='calendar'){
		$output .= '<div class="events-calendar">';
		$output .= '<div id="calendar"></div>';
		$output .= '</div>';

		$eventdata = '';

		wp_enqueue_style('fullcalendar-style' );
		wp_enqueue_script( 'moment-js' );
		wp_enqueue_script( 'fullcalendar-js' );
		add_action('wp_footer','caldata', 100, 1);
	}

    foreach($events as $event){
    	$eventid = $event->ID;
    	$title = $event->post_title;
	    $startdate = get_post_meta($eventid, 'event_info_start_date', true );
	    $enddate = get_post_meta($eventid, 'event_info_end_date', true );
	    $link = get_permalink($eventid);
	    $event_content = $event->post_content;
	    $photo_id = get_post_thumbnail_id( $eventid );
	    $thumb = wp_get_attachment_image_src( $photo_id, 'medium');
	    $photogallery_thumb_url = $thumb[0];

	    if($a['format']=='list') {
		    $output .= '<div class="row event-item event-' . $eventid . '">';
		    $output .= '<div class="event-image col-md-4">';
		    $output .= '<img src="' . $photogallery_thumb_url . '" alt="' . $title . '" class="img-fluid" >';
		    $output .= '</div>';
		    $output .= '<div class="content-area col-md-8" >';
		    $output .= '<span class="dates">';
		    $output .= '<span class="box-date start-date">
							<span class="start-month month" >' . date( 'M', strtotime( $startdate ) ) . '</span>
							<span class="start-day day" >' . date( 'd', strtotime( $startdate ) ) . '</span>
						</span>';
		    if ( $enddate != $startdate ) {
			    $output .= '<span class="seperator">-</span>';
			    $output .= '<span class="box-date end-date">
								<span class="end-month month" >' . date( 'M', strtotime( $enddate ) ) . '</span>
								<span class="end-day day" >' . date( 'd', strtotime( $enddate ) ) . '</span>
							</span>';
		    }
		    $output .= '</span>';
		    $output .= '<span class="title"><a href="' . $link . '">' . $title . '</a></span>';
		    $output .= '<div class="content" >' . truncate( $event_content, 350 ) . '</div>';
		    $output .= '</div>';
		    $output .= '</div>';
	    }elseif($a['format']=='calendar'){
            $eventdata .= 'addEvent( \''.$eventid.'\', \''.$title.'\', \''.$startdate.'\', \''.$enddate.'\', \''.$link.'\' );
            ';
	    }

    }

	if($a['format']=='calendar') {

		echo '
		    <script>
		    function addEventsToCal(){
			    ' . $eventdata . '
			}
			</script >';
	}

    return $output;

}


add_shortcode( 'getevents', 'getevents_func' );