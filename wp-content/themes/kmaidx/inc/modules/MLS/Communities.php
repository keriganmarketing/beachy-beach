<?php
namespace Includes\Modules\MLS;

use Includes\Modules\CPT\CustomPostType;

class Communities {

	/**
	 * Community constructor.
	 */
	function __construct() {

	}

	/**
	 * @return null
	 */
	public function createPostType() {

		$communities = new CustomPostType(
			'Communities',
			array(
				'supports'           => array('title', 'editor', 'thumbnail', 'revisions'),
				'menu_icon'          => 'dashicons-location',
				'has_archive'        => true,
				'menu_position'      => null,
				'public'             => true,
				'publicly_queryable' => true,
                'capability_type'    => array('communities','communitiess'),
			)
		);

		$communities->addMetaBox(
			'Community Info',
			array(
				'Database Name' => 'text',
				'Latitude'      => 'text',
				'Longitude'     => 'text'
			)
		);

	}

	/*
	 * @return $communities
	 */
	public function getCommunities(){

		$communitylist = get_posts(array(
			'posts_per_page' => -1,
			'offset'         => 0,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_type'      => 'communities',
			'post_status'    => 'publish',
		));

		$communities = array();

		foreach ($communitylist as $community) {

			$communities[] = array(
				'id'          => $community->ID,
				'title'       => $community->post_title,
				'name'        => get_post_meta( $community->ID, 'community_info_database_name', true ),
				'latitude'    => get_post_meta( $community->ID, 'community_info_latitude', true ),
				'longitude'   => get_post_meta( $community->ID, 'community_info_longitude', true )
			);

		}

		return $communities;

	}

}
