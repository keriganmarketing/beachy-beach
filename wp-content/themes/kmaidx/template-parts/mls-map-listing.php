<?php
use Includes\Modules\MLS\FullListing;

$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
remove_filter('template_redirect','redirect_canonical');

if(isset($_GET['mls'])) {

    $fullListing = new FullListing($_GET['mls']);
    $result      = $fullListing->create();
    include( locate_template( 'template-parts/mls-search-listing.php' ) );

}