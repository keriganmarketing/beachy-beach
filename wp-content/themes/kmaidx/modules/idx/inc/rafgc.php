<?php

/**
 * Connect to RAFGC DB
 * Updated: 04/21/17
 */

class rafgc_idx {

	private $db;
	private $host = "104.218.13.63";
	private $database = "kmaserv_mls_rafgc2";
	private $dbuser = "kmaserv_mls";
	private $pass = "6L!J@uRwGR!A";

	public function __construct() {

		try {
			$this->db = new wpdb($this->dbuser, $this->pass, $this->database, $this->host);
			$this->db->show_errors(); // Debug
		} catch (Exception $e) {    // Database Error
			echo $e->getMessage();
		}
		wp_enqueue_script( 'lazy-js' );
		add_action( 'wp_footer', 'idx_script_to_footer',100 );

	}

	// Get MLS Results
	public function num_search_mls($query) {
		return $this->db->get_var($query);
	}

	public function search_mls($query) {
		return $this->db->get_results($query, 'ARRAY_A');
	}

	function getMedia($mlsnum,$type,$limit = ''){

		$query = "SELECT * FROM media WHERE MEDIA_TYPE='".$type."' AND MLS_ACCT='".$mlsnum."' ";
		if($type == 'Virtual Tour'){
			$query .= "ORDER BY DATE_MODIFIED DESC LIMIT 1";
		}else{
			$query .= "ORDER BY MEDIA_ORDER ASC ";
			if($limit!=''){
				$query .= "LIMIT ".$limit;
			}
		}

		$mediaquery = $this->db->get_results($query, 'ARRAY_A');
		$mediagroup = array();

		foreach($mediaquery as $mediaresult){
			array_push($mediagroup,$mediaresult);
		}

		return $mediagroup;

	}

}