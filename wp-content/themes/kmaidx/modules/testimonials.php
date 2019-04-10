<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 3/24/2017
 * Time: 4:57 PM
 */
//CREATE TESTIMONIAL CPT
$quote = new Custom_Post_Type(
	'Testimonial',
	array(
		'supports'			 => array( 'title', 'editor', 'revisions' ),
		'menu_icon'			 => 'dashicons-format-quote',
		'rewrite'            => array( 'slug' => 'testimonials' ),
		'has_archive' 		 => true,
		'menu_position'      => null,
		'public'             => true,
		'publicly_queryable' => true,
	)
);

$quote->add_taxonomy( 'Testimonial Category' );

$quote->add_meta_box(
	'Author Info',
	array(
		'Name' 			=> 'text',
		'Company' 		=> 'text',
		'Short Version' => 'longtext',
		'Featured'      => 'boolean'
	)
);