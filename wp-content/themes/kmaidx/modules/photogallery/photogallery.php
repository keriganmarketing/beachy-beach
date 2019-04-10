<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 4/7/2017
 * Time: 5:44 PM
 */

//CREATE Photo CPT
$photogallery = new Custom_Post_Type(
	'photogallery',
	array(
		'labels'             => array(
			'name' 		         => _x( 'Photo Gallery', 'post type general name' ),
			'singular_name'      => _x( 'Photo', 'post type singular name' ),
			'menu_name'          => _x( 'Photo Gallery', 'admin menu' ),
			'name_admin_bar'     => _x( 'Photo Gallery', 'add new on admin bar' ),
			'add_new'            => _x( 'Add New', 'photo' ),
			'add_new_item'       => __( 'Add New Photo' ),
			'new_item'           => __( 'New Photo' ),
			'edit_item'          => __( 'Edit Photo' ),
			'view_item'          => __( 'View Photo' ),
			'all_items'          => __( 'All Photos' ),
			'search_items'       => __( 'Search Photos' ),
			'parent_item_colon'  => __( 'Parent Photo:' ),
			'not_found'          => __( 'No photos found.' ),
			'not_found_in_trash' => __( 'No photos found in Trash.' ),
			'featured_image' => __( 'Photo' ),
			'set_featured_image' => __( 'Select Photo' ),
			'remove_featured_image' => __( 'Remove Photo' )
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-images-alt2',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'photogallery', 'with_front' => FALSE ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'thumbnail', 'revisions' )
	)
);

$photogallery->add_taxonomy( 'Category' );

$photogallery->add_meta_box(
	'Photo Details',
	array(
		'Caption' 			=> 'text',
		'Alt Tag'           => 'text',
		'Description' 		=> 'longtext',
	)
);

function lightbox_to_footer() { ?>
    <script>
        jQuery(function($) {
            $(document).ready(function () {
                var grid = $(".photogallery-masonry").masonry({
                    // options...
                    itemSelector: ".photogallery-item",
                    percentPosition: true,
                    columWidth: ".grid-sizer",
                });
                grid.imagesLoaded().progress( function(){
                    grid.masonry('layout');
                });
            });
        });
    </script>
<?php }

function shortencaption($string,$length=100,$append='&hellip;') {
	$string = trim($string);

	if(strlen($string) > $length) {
		//$string = wordwrap($string, $length);
		//$string = explode('\n', $string, 2);
		$string = substr( $string, 0, strrpos( substr( $string, 0, $length), ' ' ) );
		$string = $string . $append;
	}
	return $string;
}

function getphotogallery_func( $atts, $content = null ) {
	$debugphotogallery = FALSE;

	$a = shortcode_atts( array(
		'category' => '',
		'truncate' => 0,
		'class' => 'col-sm-6 col-md-4 col-lg-3',
		'format' => 'grid'
	), $atts );

	if($debugphotogallery){
		$output = '<p>category = '.$a['category'].'</p>';
	}else{
		$output = '';
	}

	$request = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'order'            => 'ASC',
		'orderby'   		 => 'menu_order',
		'post_type'        => 'photogallery',
		'post_status'      => 'publish',
	);

	if($a['category']!= ''){
		$categoryarray = array(
			array(
				'taxonomy' => 'photogallery_category',
				'field' => 'slug',
				'terms' => $a['category'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $categoryarray;
	}

	if($debugphotogallery){
		print_r($request);
	}

	$photogallery = get_posts( $request );

	$output .='
	<div id="photo-feed" class="photogallery">';

	if($a['format']== 'masonry'){
		$output .='
		<div class="photogallery-masonry row">
		';
	}elseif($a['format']== 'grid'){
		$output .='
		<div class="photogallery-grid row">
		';
	}elseif($a['format']== 'list'){
		$output .='
		<div class="photogallery-list">
		';
	}
	$output .= '<div class="grid-sizer"></div>';

	$i = 0;
	foreach($photogallery as $photo){
		$photoid = $photo->ID;
		$phototitle = $photo->post_title;
		$caption = get_post_meta($photoid, 'photo_details_caption', true );
		$alt = get_post_meta($photoid, 'photo_details_alt_tag', true );
		$link = get_permalink($photoid);
		$category = wp_get_post_terms( $photoid, 'photogallery_category', array("fields" => "names"));
		$photo_id = get_post_thumbnail_id( $photoid );
		$thumb = wp_get_attachment_image_src( $photo_id, 'medium');
		$photogallery_thumb_url = $thumb[0];

		$output .= '<div class="photogallery-item '.$a['class'].'">';
		if($photogallery_thumb_url){
			$output .= '<a href="'.$photogallery_thumb_url.'" class="thumbnail" title="'.$alt.'" data-lightbox="lightbox"><img src="'.$photogallery_thumb_url.'" alt="'.$alt.'" class="img-fluid" /></a>';
		}

		$output .= '</div>';
		$i++;
	}

	wp_enqueue_script( 'images-loaded' );
	wp_enqueue_script( 'masonry' );
	wp_enqueue_script( 'lightbox' );
	wp_enqueue_style( 'lightbox-styles' );
	add_action( 'wp_footer', 'lightbox_to_footer',100 );

	return $output;

}
add_shortcode( 'getphotogallery', 'getphotogallery_func' );




