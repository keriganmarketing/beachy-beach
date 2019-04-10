<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 3/24/2017
 * Time: 4:56 PM
 */

class mlsTeam {

	public $queryvar;
	public $agentArray;

    /**
     * Leads constructor.
     */
    public function __construct() {}

    /**
     * @return null
     */
    public function createPostType() {

        $team = new Custom_Post_Type( 'agent',
            array(
                'supports'			    => array( 'title', 'editor', 'thumbnail', 'author' ),
                'menu_icon'			    => 'dashicons-businessman',
                'has_archive' 		    => false,
                'menu_position'         => null,
                'public'                => true,
                'publicly_queryable'    => true,
                'hierarchical'          => true,
                'show_ui'               => true,
                'show_in_nav_menus'     => true,
                '_builtin'              => false,
                'rewrite'               => array(
                    'slug' 			=> 'team',   		//string Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable.
                    'with_front' 	=> true, 				//bool Should the permalink structure be prepended with the front base. <br>
                    //(example: if your permalink structure is /blog/, then your links will be: false-> /news/, true->/blog/news/). Defaults to true
                    'feeds' 		=> true, 				//bool Should a feed permalink structure be built for this post type. Defaults to has_archive value
                    'pages' 		=> false				//bool Should the permalink structure provide for pagination. Defaults to true
                ),
                'capability_type'    => array('agent','agents'),
//                'capabilities' => array(
//                    'edit_post'          => 'edit_agents',
//                    'read_post'          => 'read_agents',
//                    'publish_posts'      => 'publish_agents',
//                    'edit_others_posts'  => 'edit_others_agents'
//                ),
            )
        );
        $team->add_taxonomy( 'office' );

        $team->add_meta_box(
            'Contact Info',
            array(
                'AKA'                  => 'text',
                'Title'                => 'text',
                'Photo'                => 'image',
                'Email'                => 'text',
                'Website'              => 'text',
                'Phone'                => 'text',
                'Additional MLS Names' => 'text'
            )
        );

        $team->add_meta_box(
            'Social Media Info',
            array(
                'Facebook'      => 'text',
                'Twitter'       => 'text',
                'LinkedIn'      => 'text',
                'Instagram'     => 'text',
                'YouTube'       => 'text',
                'Google Plus'   => 'text'
            )
        );

    }

	public function getAgentNames() {
		///set up agent array for forms
		$getagents = get_posts( array(
			'post_type'      => 'agent',
			'posts_per_page' => - 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'offset'         => 0,
			'post_status'    => 'publish',
		) );

		$agentArray = array();
		foreach ( $getagents as $member ) {
			array_push( $agentArray, (isset($member->post_title) ? $member->post_title : null) );
		}

		return $agentArray;
	}

	public function getTeam(){
		///set up agent array for forms
		$getagents  = get_posts( array(
            'post_type'         => 'agent',
            'posts_per_page'	=> -1,
            'orderby'			=> 'menu_order',
            'order'             => 'ASC',
            'offset'			=> 0,
            'post_status'		=> 'publish',
        ) );

		$agentArray = array();
		foreach ( $getagents as $member ){

            $agentTerms                     = wp_get_object_terms( $member->ID, 'office' );
            $agentCategories = array();
            foreach($agentTerms as $term){
                array_push($agentCategories, array(
                        'category-id'       => (isset($term->term_id)   ? $term->term_id : null),
                        'category-name'     => (isset($term->name)      ? $term->name : null),
                        'category-slug'     => (isset($term->slug)      ? $term->slug : null),
                    )
                );
            }

            $additionalMLSNames = (isset($member->contact_info_additional_mls_names)  ? $member->contact_info_additional_mls_names : null);
            $additionalMLSNames = explode(',',$additionalMLSNames);

			array_push($agentArray, array(
				'id'            => (isset($member->ID)                  ? $member->ID : null),
				'name'          => (isset($member->post_title)          ? $member->post_title : null),
                'aka'           => (isset($member->contact_info_aka)    ? $member->contact_info_aka : null),
				'title'         => (isset($member->contact_info_title)  ? $member->contact_info_title : null),
				'email'         => (isset($member->contact_info_email)  ? $member->contact_info_email : null),
				'website'       => (isset($member->contact_info_website)? $member->contact_info_website : null),
				'phone'         => (isset($member->contact_info_phone)  ? $member->contact_info_phone : null),
				'slug'          => (isset($member->post_name)           ? $member->post_name : null),
				'thumbnail'     => (isset($member->contact_info_photo)  ? $member->contact_info_photo : null),
				'link'          => get_permalink($member->ID),
                'social'        => array(
                    'facebook'      => (isset($member->social_media_info_facebook)  ? $member->social_media_info_facebook : null),
                    'twitter'       => (isset($member->social_media_info_twitter)   ? $member->social_media_info_twitter : null),
                    'linkedin'      => (isset($member->social_media_info_linkedin)  ? $member->social_media_info_linkedin : null),
                    'instagram'     => (isset($member->social_media_info_instagram) ? $member->social_media_info_instagram : null),
                    'youtube'       => (isset($member->social_media_info_youtube)   ? $member->social_media_info_youtube : null),
                    'google_plus'   => (isset($member->social_media_info_google)    ? $member->social_media_info_google : null),
                ),
                'categories'    => $agentCategories,
                'mls_names'     => $additionalMLSNames
			));

            //echo '<pre>',print_r($agentArray),'</pre>';

		}

		return $agentArray;
	}

	/*
	 * @param $name
	 */
	public function getSingleAgent($name){

		$getagents  = get_posts( array(
			'title'             => $name,
			'post_type'         => 'agent',
			'posts_per_page'	=> 1,
			'orderby'			=> 'menu_order',
			'order'             => 'ASC',
			'offset'			=> 0,
			'post_status'		=> 'publish',
		) );

		$agentArray = array();

		foreach ( $getagents as $member ){
			array_push($agentArray, array(
				'id'            => (isset($member->ID)                  ? $member->ID : null),
				'name'          => (isset($member->post_title)          ? $member->post_title : null),
				'aka'           => (isset($member->contact_info_aka)    ? $member->contact_info_aka : null),
				'title'         => (isset($member->contact_info_title)  ? $member->contact_info_title : null),
				'email'         => (isset($member->contact_info_email)  ? $member->contact_info_email : null),
				'website'       => (isset($member->contact_info_website)? $member->contact_info_website : null),
				'phone'         => (isset($member->contact_info_phone)  ? $member->contact_info_phone : null),
				'slug'          => (isset($member->post_name)           ? $member->post_name : null),
				'thumbnail'     => (isset($member->contact_info_photo)  ? $member->contact_info_photo : null),
				'link'          => get_permalink($member->ID),
				'social'        => array(
					'facebook'      => (isset($member->social_media_info_facebook)  ? $member->social_media_info_facebook : null),
					'twitter'       => (isset($member->social_media_info_twitter)   ? $member->social_media_info_twitter : null),
					'linkedin'      => (isset($member->social_media_info_linkedin)  ? $member->social_media_info_linkedin : null),
					'instagram'     => (isset($member->social_media_info_instagram) ? $member->social_media_info_instagram : null),
					'youtube'       => (isset($member->social_media_info_youtube)   ? $member->social_media_info_youtube : null),
					'google_plus'   => (isset($member->social_media_info_google)    ? $member->social_media_info_google : null),
				)
			));
		}

		//echo '<pre>',print_r($getagents),'</pre>';

		return $agentArray[0];
	}

}

?>