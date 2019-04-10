<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 3/24/2017
 * Time: 4:56 PM
 */
///CREATE TEAM CPT
$team = new Custom_Post_Type(
	'Team Member',
	array(
		'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'menu_icon'			 => 'dashicons-groups',
		'hierarchical'       => true,
		'has_archive' 		 => false,
		'menu_position'      => null,
		'public'             => true,
		'publicly_queryable' => true,
		'rewrite'            => array( 'slug' => 'team', 'with_front' => FALSE ),
	)
);