<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 3/24/2017
 * Time: 4:46 PM
 */

//CREATE SERVICE CPT
$services = new Custom_Post_Type(
	'Service',
	array(
		'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'menu_icon'			 => 'dashicons-admin-customizer',
		'rewrite'            => array( 'slug' => 'services' ),
		'hierarchical'       => true,
		'has_archive' 		 => false,
		'menu_position'      => null,
		'public'             => true,
		'publicly_queryable' => true,
	)
);

$services->add_taxonomy( 'Service Category' );

function getservices_func( $atts, $content = null ) {
	$debugservice = FALSE;

	$a = shortcode_atts( array(
		'category' => '',
		'truncate' => 0,
		'format' => 'list',
	), $atts );

	if($debugservice){
		$output = '<p>category = '.$a['category'].'</p>';
	}else{
		$output = '';
	}

	$request = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'order'            => 'ASC',
		'orderby'   	   => 'menu_order',
		'post_type'        => 'service',
		'post_status'      => 'publish',
	);

	if($a['category']!= ''){
		$categoryarray = array(
			array(
				'taxonomy' => 'service_category',
				'field' => 'slug',
				'terms' => $a['category'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $categoryarray;
	}

	if($debugservice){
		print_r($request);
	}

	$servicelist = get_posts( $request );

	$output = '<div class="all-services '.$a['format'].' row justify-content-center align-items-middle">';
	foreach($servicelist as $service){
		$serviceid = $service->ID;
		$title = $service->post_title;
		$description = $service->post_content;
		$thumb_id = get_post_thumbnail_id( $serviceid );
        $thumb = wp_get_attachment_image_src( $thumb_id, 'large');
        $thumb_url = $thumb[0];

        if($a['format'] == 'tile') {

	        $output .= '<div class="service col-md-6 col-lg-4 text-center">
	                        <div class="image-container">
	                          <div class="image"><a href="' . get_permalink( $serviceid ) . '" ><img src="' . $thumb_url . '" alt="' . $title . '" class="img-fluid" ></a></div>
	                        </div>
	                        <div class="info">
	                            <h3><a href="' . get_permalink( $serviceid ) . '" >' . $title . '</a></h3>
	                        </div>
	                    </div>';

        }elseif($a['format'] == 'list'){

        	$output .= '<div class="service col"><div class="row">';

			if($thumb_url != ''){
				$output .= '<div class="col-lg-4"><div class="image-container">
	                          <div class="image"><a href="' . get_permalink( $serviceid ) . '" ><img src="' . $thumb_url . '" alt="' . $title . '" class="img-fluid" ></a></div>
	                        </div></div>';
			}
	        $output .= '<div class="col">
	                        <div class="info">
	                            <h3>' . $title . '</a></h3>
	                            <p>'.$description.'</p>
	                            <a href="' . get_permalink( $serviceid ) . '" >' . $title . '</a></h3>
	                        </div>
	                    </div>';

	        $output .= '</div></div>';
        }

	}
	$output .= '</div>';

	return $output;

}
add_shortcode( 'getservices', 'getservices_func' );