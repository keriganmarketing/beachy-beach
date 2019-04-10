<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 4/13/2017
 * Time: 4:36 PM
 */

//CREATE Slider CPT
$slider = new Custom_Post_Type(
	'Slide Image',
	array(
		'labels'             => array(
			'name' 		         => _x( 'Slider Images', 'post type general name' ),
			'singular_name'      => _x( 'Slider Image', 'post type singular name' ),
			'menu_name'          => _x( 'Slideshows', 'admin menu' ),
			'name_admin_bar'     => _x( 'Slider Images', 'add new on admin bar' ),
			'add_new'            => _x( 'Add New', 'photo' ),
			'add_new_item'       => __( 'Add New Photo' ),
			'new_item'           => __( 'New Slider Image' ),
			'edit_item'          => __( 'Edit Slider Image' ),
			'view_item'          => __( 'View Slider Image' ),
			'all_items'          => __( 'All Slider Images' ),
			'search_items'       => __( 'Search Slider Images' ),
			'parent_item_colon'  => __( 'Parent Slider Image:' ),
			'not_found'          => __( 'No slider images found.' ),
			'not_found_in_trash' => __( 'No slider image found in Trash.' )
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-images-alt2',
		'query_var'          => true,
		'rewrite'            => array( 'with_front' => FALSE ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title','revisions' )
	)
);

$slider->add_taxonomy( 'Slideshow' );

$slider->add_meta_box(
	'Photo Details',
	array(
		'Photo File' 		=> 'image',
		'Caption' 			=> 'text',
		'Alt Tag'           => 'text',
		'Description' 		=> 'longtext',
	)
);

// [getslider category="" ]
function getslider_func( $atts, $content = null ) {
	$debugslider = FALSE;

	$a = shortcode_atts( array(
		'category' => '',
		'truncate' => 0,
	), $atts );

	if($debugslider){
		$output = '<p>category = '.$a['category'].'</p>';
	}else{
		$output = '';
	}

	$request = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'order'            => 'ASC',
		'orderby'          => 'menu_order',
		'post_type'        => 'slide_image',
		'post_status'      => 'publish',
	);

	if($a['category']!= ''){
		$categoryarray = array(
			array(
				'taxonomy' => 'slider_slideshow',
				'field' => 'slug',
				'terms' => $a['category'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $categoryarray;
	}

	if($debugslider){
		print_r($request);
	}

	$slidelist = get_posts( $request );

	if($debugslider){
		print_r($slidelist);
	}

	$i = 0;
	foreach($slidelist as $slide){
		$slideid = $slide->ID;
		$title = $slide->post_title;
		$headline = get_post_meta($slideid, 'slider_title', true );
		$caption = get_post_meta($slideid, 'slider_cap', true );
		$description = get_post_meta($slideid, 'slider_desc', true );
		$link = get_post_meta( $slideid, 'slider_link', true );
		$linktext = get_post_meta( $slideid, 'slider_linktext', true );
		$newwindow = get_post_meta( $slideid, 'open_link_in_new_window', true );
		$category = wp_get_post_terms( $slideid, 'slider_slideshow', array("fields" => "names"));
		$slider_thumb_url = get_post_meta($slideid, 'photo_details_photo_file', true);

		//$post_thumbnail_id = get_post_thumbnail_id($slideid);
		//$slider_thumb_url = wp_get_attachment_image_url( $post_thumbnail_id, 'large' );

		//print_r($slider_thumb_url);

		$slides .= '<div class="carousel-item'; if($i < 1){ $slides .= ' active'; } $slides .= ' slide-'.$i.'">';
		if( $link_url!='' ){ echo '<a href="'.$link_url.'" >'; }
		$slides .= '<img src="'.$slider_thumb_url.'" alt="'.$caption.'" class="slider-image" />';
		if( $link_url!='' ){ echo '</a>'; }

		$slides .= '	<div class="carousel-caption" >';
		if($a['truncate']>0){ $description = shortensliderdesc($description, $a['truncate']); }
		$slides .= '<p class="slider-headline">'.$headline.'</p>';
		$slides .= '<p class="slider-description">'.$description.$readmore.'</p>';

		if( $link!='' && $link_text!='' ){
			$slides .= '<p class="carousel-more"><a href="'.$link.'"';
				if($newwindow){ $slides .= ' target="_blank"'; }
			$slides .= ' >'.$linktext.'</a></p>';
		}

		$slides .= '	</div>';

		$slides .= '</div>';

		$dots .= '<li data-target="#home-carousel" data-slide-to="'.$i.'" ';
		if($i < 1){ $dots .= 'class="active"'; } $dots .= '></li>';

		$i++;
	}

	$output .='    
	<div id="home-carousel" class="carousel slide carousel-fade" data-ride="carousel">
		<div class="slider-control">
		  <a class="left carousel-control" href="#home-carousel" role="button" data-slide="prev">
			<span class="icon-prev" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		  </a>
		  <ol class="carousel-indicators">'.$dots.'</ol>
		  <a class="right carousel-control" href="#home-carousel" role="button" data-slide="next">
			<span class="icon-next" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		  </a>
		</div>
		
    	<div class="carousel-inner" role="listbox">
		'.$slides.'
		</div>
		
	</div></div>
	';

	return $output;

}
add_shortcode( 'getslider', 'getslider_func' );