<?php
namespace Includes\Modules\MLS;

use Includes\Modules\CPT\CustomPostType;

class Offices {

	function __construct() {

	}

	public function createPostType(){

		$locations = new CustomPostType(
			'Location',
			array(
				'supports'           => array('title', 'revisions'),
				'menu_icon'          => 'dashicons-location',
				'has_archive'        => false,
				'menu_position'      => null,
				'public'             => false,
				'publicly_queryable' => false,
                'capability_type'    => array('office','offices'),
			)
		);

		$locations->addMetaBox(
			'Location Info',
			array(
				'Name'       => 'text',
				'Address'    => 'text',
				'Phone'      => 'text',
				'Fax'        => 'text',
				'Email'      => 'text',
				'Map Coords' => 'text'
			)
		);

	}

	/*
	 * @return $officeArray
	 */
	public function getOffices(){

		$offices = get_posts(
			array(
				'posts_per_page' => -1,
				'offset'         => 0,
				'order'          => 'ASC',
				'orderby'        => 'menu_order',
				'post_type'      => 'location',
				'post_status'    => 'publish',
			)
		);

		return $offices;
	}

	/*
	 * @return $officeArray
	 */
	public function getAllOffices(){

		$offices = get_posts(
			array(
				'posts_per_page' => -1,
				'offset'         => 0,
				'order'          => 'ASC',
				'orderby'        => 'menu_order',
				'post_type'      => 'location',
				'post_status'    => 'publish',
			)
		);

		$officeArray = array();
		foreach($offices as $location) {

			$coords      = get_post_meta($location->ID, 'location_info_map_coords', true);
			$coordsParts = explode(',', $coords);

			$officeArray[] = array(
				'id'        => $location->ID,
				'title'     => $location->post_title,
				'name'      => get_post_meta( $location->ID, 'location_info_name', true ),
				'address'   => get_post_meta( $location->ID, 'location_info_address', true ),
				'phone'     => get_post_meta( $location->ID, 'location_info_phone', true ),
				'fax'       => get_post_meta( $location->ID, 'location_info_fax', true ),
				'email'     => get_post_meta( $location->ID, 'location_info_email', true ),
				'lat'       => $coordsParts[0],
				'lng'       => $coordsParts[1],
				'type'      => 'office' //name of pin (_-pin.png)
			);
		}
		return $officeArray;

	}

	/*
	 * @param $location OBJECT
	 * @return clean array
	 */
	public function getOffice($location){

		$location_id = $location->ID;
		$title       = $location->post_title;
		$name        = get_post_meta($location_id, 'location_info_name', true);
		$address     = get_post_meta($location_id, 'location_info_address', true);
		$phone       = get_post_meta($location_id, 'location_info_phone', true);
		$fax         = get_post_meta($location_id, 'location_info_fax', true);
		$email       = get_post_meta($location_id, 'location_info_email', true);
		$coords      = get_post_meta($location_id, 'location_info_map_coords', true);
		$coordsParts = explode(',', $coords);

		return array(
			'name'    => $name,
			'address' => $address,
			'phone'   => $phone,
			'fax'     => $fax,
			'email'   => $email,
			'lat'     => $coordsParts[0],
			'lng'     => $coordsParts[1],
			'type'    => 'office' //name of pin (_-pin.png)
		);

	}

}