<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 3/24/2017
 * Time: 4:46 PM
 */

//CREATE SERVICE CPT
$work = new Custom_Post_Type(
	'Service',
	array(
		'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'menu_icon'			 => 'dashicons-admin-customizer',
		'rewrite'            => array( 'slug' => 'services' ),
		'hierarchical'       => true,
		'has_archive' 		 => true,
		'menu_position'      => null,
		'public'             => true,
		'publicly_queryable' => true,
	)
);

$work->add_taxonomy( 'Service Category' );