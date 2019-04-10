<?php

class BeachyBucket
{
    public function handleFavorite($user_id, $mls_account)
    {
        global $wpdb;

        $returned_object = $wpdb->get_results(
            "SELECT * FROM wp_beachy_buckets
             WHERE user_id = {$user_id}
             AND mls_account LIKE '{$mls_account}'"
        );

        if (empty($returned_object)) {
            $this->addListingToBucket($user_id, $mls_account);
            $count    = $wpdb->get_results("SELECT COUNT(id) as items FROM wp_beachy_buckets WHERE user_id = {$user_id}");
            $listings = $count[0]->items;

            $response = [
                'status'  => 'Success',
                'message' => 'Listing ' . $mls_account . ' has been added to your Beachy Bucket!',
                'count'   => $listings
            ];

            return json_encode($response);
        }

        $this->removeListingFromBucket($user_id, $mls_account);
        $count    = $wpdb->get_results("SELECT COUNT(id) as items FROM wp_beachy_buckets WHERE user_id = {$user_id}");
        $listings = $count[0]->items;

        $response = [
            'status'  => 'Success',
            'message' => 'Listing ' . $mls_account . ' has been removed from your Beachy Bucket!',
            'count'   => $listings
        ];

        return json_encode($response);
    }

    private function addListingToBucket($user_id, $mls_account)
    {
        global $wpdb;

        $wpdb->insert(
            'wp_beachy_buckets',
            array(
                'user_id'     => $user_id,
                'mls_account' => $mls_account
            ),
            array(
                '%d',
                '%s'
            )
        );
    }

    private function removeListingFromBucket($user_id, $mls_account)
    {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM wp_beachy_buckets
             WHERE user_id={$user_id} AND mls_account LIKE '{$mls_account}'"
        );
    }

    public function findBucketItem($user_id, $mls_account)
    {
        global $wpdb;

        $query = "SELECT * FROM wp_beachy_buckets
                  WHERE user_id={$user_id}
                  AND mls_account LIKE '{$mls_account}'";

        $results = $wpdb->get_results($query);

        return $results;
    }

    public function getNumberOfBucketItems($user_id)
    {
        global $wpdb;
        $count    = $wpdb->get_results("SELECT COUNT(id) as items FROM wp_beachy_buckets WHERE user_id = {$user_id}");
        $listings = $count[0]->items;

        return $listings;
    }

    /**
     * Returns array of mls numbers that were saved by the given user
     * @param  integer $user_id
     * @return array
     */
    public function listingsSavedByUser($user_id)
    {
        global $wpdb;
        $mls = new MLS();

        $mlsNumbers = [];
        $query      = "SELECT mls_account FROM wp_beachy_buckets WHERE user_id = {$user_id}";
        $results    = $wpdb->get_results($query);

        foreach ($results as $result) {
            array_push($mlsNumbers, $result->mls_account);
        }

        return $mlsNumbers;
    }

    /**
     * Returns user information for the items in the beachy bucket so that agents can see who likes their stuff
     * @param  string $agentName
     * @return mixed|array
     */
    public function clientBeachyBuckets($agentName)
    {
        global $wpdb;
        $mls        = new MLS();
        $userIDs    = [];
        $userData   = [];
        $mlsNumbers = [];
        $query      = '';

        $results    = $wpdb->get_results("SELECT user_id from wp_usermeta WHERE meta_value LIKE '{$agentName}'");
        if (!empty($results)) {
            foreach ($results as $result) {
                array_push($userIDs, $result->user_id);
            }

            // We need to use 2 functions to get all the data we need because...Wordpress...yeah...
            for ($i = 0; $i < sizeOf($userIDs); $i++) {
                $userData[$i]            = get_user_meta($userIDs[$i]);
                $userData[$i]['id']      = $userIDs[$i];
                $userData[$i]['email']   = get_userdata($userIDs[$i])->user_email;
                $userData[$i]['buckets'] = $this->savedProperties($userIDs[$i]);
            }
        }

        return $userData;
    }

    public function allBuckets()
    {
        global $wpdb;

        $userIDs  = [];
        $userData = [];

        $results = $wpdb->get_results("SELECT DISTINCT user_id from wp_beachy_buckets WHERE 1=1");

        if (! empty($results)) {
            foreach ($results as $result) {
                array_push($userIDs, $result->user_id);
            }

            // We need to use 2 functions to get all the data we need because...Wordpress...yeah...
            for ($i = 0; $i < sizeOf($userIDs); $i++) {
                $userData[$i]            = get_user_meta($userIDs[$i]);
                $userData[$i]['id']      = $userIDs[$i];
                $userData[$i]['email']   = isset(get_userdata($userIDs[$i])->user_email) ? get_userdata($userIDs[$i])->user_email : '';
                $userData[$i]['buckets'] = $this->savedProperties($userIDs[$i]);
            }
        }

        return $userData;
    }


    protected function savedProperties($userId)
    {
        global $wpdb;
        $mlsNumbers = [];

        $query = "SELECT mls_account FROM wp_beachy_buckets WHERE user_id = {$userId}";

        $results = $wpdb->get_results($query);

        if (count($results) > 0) {
            foreach ($results as $result) {
                array_push($mlsNumbers, $result->mls_account);
            }
        }

        return $mlsNumbers;
    }


    public function beachyBucketResults($mlsNumberArray)
    {
        global $wpdb;

        $query = $this->buildBeachyBucketQuery($mlsNumberArray);

        $results = $wpdb->get_results($query);

        return $results;
    }

    /**
     * @param $mlsNumberArray
     * @return string
     */
    public function buildBeachyBucketQuery($mlsNumberArray)
    {
        $query =
            "SELECT * FROM wp_bcar WHERE 1 AND ";

        for ($i = 0; $i < count($mlsNumberArray); $i++) {
            $query .= "mls_account LIKE '{$mlsNumberArray[$i]}'";

            if ($i < count($mlsNumberArray) - 1) {
                $query .= ' OR ';
            }
        }

        $query .= " UNION ";

        $query .=
            "SELECT * FROM wp_ecar WHERE 1 AND ";

        for ($i = 0; $i < count($mlsNumberArray); $i++) {
            $query .= "mls_account LIKE '{$mlsNumberArray[$i]}'";

            if ($i < count($mlsNumberArray) - 1) {
                $query .= ' OR ';
            }
        }

        return $query;
    }
}
