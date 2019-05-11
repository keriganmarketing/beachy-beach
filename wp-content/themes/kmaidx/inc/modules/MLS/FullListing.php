<?php
namespace Includes\Modules\MLS;

use GuzzleHttp\Client;
use Includes\Modules\Agents\Agents;
use Includes\Modules\MLS\BeachyBucket;

/**
* MLS Listing - Made by Daron Adkins
*/
class FullListing
{
    protected $mlsNumber;
    protected $listingInfo;

    /**
     * Search Constructor
     * @param string $mlsNumber - Basically just the $_GET variables
     */
    public function __construct($mlsNumber)
    {
        $this->mlsNumber   = $mlsNumber;
    }

    public function create()
    {
        $client = new Client([
            'base_uri' => 'https://mothership.kerigan.com/api/v1/listing/',
            'http_errors' => false,
            'headers' => [
                'Referrer' => $_SERVER['HTTP_USER_AGENT']
            ]
        ]);

        // make the API call
        $raw = $client->request(
            'GET',
            $this->mlsNumber
        );

        $results = json_decode($raw->getBody());
        return $results;
    }

	public function isOurs($listingInfo)
    {
        $agents = new Agents();

        $agent = $agents->getAgentByIds(
            [
                $listingInfo->listing_member_shortid,
                // $listingInfo->colisting_member_shortid
            ]
        );

        return (is_array($agent) ? $agent : false);
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

    public function setListingSeo( $listingInfo )
    {

        $this->listingInfo = $listingInfo;
        $image = ($this->listingInfo->preferred_image != '' ? $this->listingInfo->preferred_image : get_template_directory_uri() . '/img/beachybeach-placeholder.jpg');
        $imageParts = getimagesize ( $image );

        add_filter('wpseo_title', function () {
            $title = $this->listingInfo->street_number . ' ' . $this->listingInfo->street_name .' '. $this->listingInfo->street_suffix;
            $title = ($this->listingInfo->unit_number != '' ? $title . ' ' . $this->listingInfo->unit_number : $title);
            $metaTitle = $title . ' | $' . number_format($this->listingInfo->price) . ' | ' . $this->listingInfo->city . ' | ' . get_bloginfo('name');
            return $metaTitle;
        });

        add_filter('wpseo_metadesc', function () {
            return strip_tags($this->listingInfo->description);
        });

        add_filter('wpseo_opengraph_image', function () {
            return null;
        });

        add_action( 'wpseo_add_opengraph_images', function() {

            //echo '<meta property="og:latitude" content="' .  $this->listingInfo->latitude . '" />', "\n";
            //echo '<meta property="og:longitude" content="' .  $this->listingInfo->longitude . '" />', "\n";
            //echo '<meta property="og:street_address" content="' .  $this->listingInfo->full_address . ', ' . $this->listingInfo->state . '" />', "\n";

            if(is_array($this->listingInfo->photos)){
                foreach($this->listingInfo->photos as $image){
                    echo '<meta property="og:image" content="' .  $image->url . '" />', "\n";
                    echo '<meta property="og:image:secure_url" content="' .  str_replace('http://','https://' , $this->listingInfo->preferred_image) . '" />', "\n";
                }
            }

            $image = ($this->listingInfo->preferred_image != '' ? $this->listingInfo->preferred_image : get_template_directory_uri() . '/img/beachybeach-placeholder.jpg');
            $imageParts = getimagesize ( $image );

            echo '<meta property="og:image" content="' .  $this->listingInfo->preferred_image . '" />', "\n";
            echo '<meta property="og:image:secure_url" content="' .  str_replace('http://','https://' , $this->listingInfo->preferred_image) . '" />', "\n";
            echo '<meta property="og:image:width" content="' .  $imageParts['0'] . '" />', "\n";
            echo '<meta property="og:image:height" content="' .  $imageParts['1'] . '" />', "\n";
        });

        add_filter( 'wpseo_og_og_image_width', function (){
            return null;          
        });

        add_filter( 'wpseo_og_og_image_height', function (){
            return null;
        });

        add_filter('wpseo_canonical',  function () {
            return get_the_permalink() . '?mls=' . $this->listingInfo->mls_account;
        });

        add_filter('wpseo_opengraph_url', function ($ogUrl) {
            return get_the_permalink() . '?mls=' . $this->listingInfo->mls_account;
        }, 100, 1);
    }
}
