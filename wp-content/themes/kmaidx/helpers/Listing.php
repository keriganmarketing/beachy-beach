<?php

class Listing
{

    protected $mls_number;

    /**
     * Listing constructor.
     * @param $mls_number
     */
    function __construct($mls_number)
    {
        $this->mls_number = $mls_number;
    }

    /**
     *
     */
    public function getInfo()
    {

        $listingInfo = '';
        if (isset($_GET['mls'])) {
            $mlsNumber   = $_GET['mls'];
            $fullListing = new FullListing($mlsNumber);
            $listingInfo = $fullListing->create();
        }

        return $listingInfo;
    }

    private function getPhotosForListing($listingId, $association)
    {
        global $wpdb;
        $table = ($association == 'bcar') ? 'wp_bcar_photos' : 'wp_ecar_photos';

        $results = $wpdb->get_results("SELECT mls_account, url, photo_description FROM {$table} WHERE listing_id LIKE {$listingId}");

        return $results;
    }

    public function isOurs($listingInfo)
    {
        $mls = new MLS();

        $agents   = $mls->getAllAgents();
        $mlsArray = array();

        foreach ($agents as $agent) {
            $mlsArray[] = $agent->short_id;
        }

        if (in_array($listingInfo->listing_member_shortid, $mlsArray) ||
            in_array($listingInfo->colisting_member_shortid, $mlsArray)
        ) {
            return true;
        }

        return false;
    }

    public function isInBucket($user_id, $mls_number)
    {
        $bb = new BeachyBucket();

        $results = $bb->findBucketItem($user_id, $mls_number);

        if (empty($results)) {
            return false;
        }

        return true;
    }
}
