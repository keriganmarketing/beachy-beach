<?php
/**
 * KMA IDX functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package KMA_DEMO
 */

use Includes\Modules\MLS\Offices;
use Includes\Modules\Agents\Agents;
use Includes\Modules\Helpers\CleanWP;
use Includes\Modules\Layouts\Layouts;
use Includes\Modules\Members\Members;
use Includes\Modules\MLS\Communities;
use Includes\Modules\MLS\QuickSearch;
use Includes\Modules\MLS\BeachyBucket;
use Includes\Modules\Leads\AdminLeads;
use Includes\Modules\MLS\AdminSettings;
use Includes\Modules\Leads\RequestInfo;
use Includes\Modules\Leads\HomeValuation;
use Includes\Modules\Social\SocialSettingsPage;
use Includes\Modules\Notifications\ListingUpdated;

require('vendor/autoload.php');
require('inc/editor-filters.php');

new CleanWP();

$members = new Members();

$socialLinks = new SocialSettingsPage();
if (is_admin()) {
    $socialLinks->createPage();
}

$layouts = new Layouts();
$layouts->createPostType();
$layouts->createDefaultFormats();

$agents = new Agents();
$agents->createPostType();

$leads = new RequestInfo;
$leads->setupAdmin();

$leads = new HomeValuation;
$leads->setupAdmin();

$offices = new Offices();
$offices->createPostType();

$communities = new Communities();
$communities->createPostType();

function getSvg($file = '')
{
    $activeTemplateDir     = get_template_directory_uri() . '/inc/modules/MLS/assets/';
    $templateFileRequested = $file . '.svg';

    return $activeTemplateDir . $templateFileRequested;
}

if ( ! function_exists('kmaidx_setup')) :
    function kmaidx_setup()
    {
        add_action('init', 'startSession', 1);
        add_action('wp_logout', 'endSession');
        add_action('wp_login', 'endSession');

        ini_set('session.bug_compat_warn', 0);
        ini_set('session.bug_compat_42', 0);

        function startSession()
        {
            if ( ! session_id()) {
                session_start();
            }
        }

        function endSession()
        {
            session_destroy();
        }

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on KMA IDX, use a find and replace
         * to change 'kmaidx' to the name of your theme in all the template files.
         */
        load_theme_textdomain('kmaidx', wp_normalize_path(get_template_directory() . '/languages'));

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in these locations.
        register_nav_menus(array(
            'menu-1' => esc_html__('Primary', 'kmaidx'),
            'menu-2' => esc_html__('Footer', 'kmaidx'),
            'menu-3' => esc_html__('Mobile', 'kmaidx'),
        ));

        add_theme_support( 'post-formats', array( 'quote', 'video' ) );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        require wp_normalize_path(get_template_directory() . '/inc/bootstrap-wp-navwalker.php');

    }
endif;
add_action('after_setup_theme', 'kmaidx_setup');

function kmaidx_scripts()
{

    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js', false, false,
        true);

    //styles
    wp_register_style('fullcalendar-style', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.3.1/fullcalendar.min.css',
        null);
    wp_register_style('lightbox-styles', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.9.0/css/lightbox.min.css',
        false);
    wp_register_style('select2-styles', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
        false);

    //scripts
    wp_register_script('tether', '//cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js',
        array('jquery'), null, true);
    wp_register_script('bootstrap-js', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js',
        array('jquery'), null, true);
    wp_register_script('images-loaded',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.min.js', array('jquery'), null,
        true);
    wp_register_script('custom-scripts', get_template_directory_uri() . '/js/scripts.js', array(), null, true);
    wp_register_script('lightbox', '//cdnjs.cloudflare.com/ajax/libs/lightbox2/2.9.0/js/lightbox.min.js',
        array('jquery'), null, true);
    // wp_register_script('lazy-js', '//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.4/jquery.lazy.min.js',
    //     array('jquery'), null, true);
    wp_register_script('select2-js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
        array('jquery'), null, true);
    wp_register_script('jquery-ui-slider', get_template_directory_uri() . '/js/jquery-ui.min.js', array('jquery'), null,
        true);
    //wp_register_script('chart-js', get_template_directory_uri() . '/js/chartjs/Chart.js', array('jquery'), null, true);
    wp_register_script('chart-js', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js',
        array('jquery'), null, true);
    wp_register_script('mortgage-calc', get_template_directory_uri() . '/js/mortgagecalc.js', array('jquery'), null,
        true);
    wp_register_script('listing-js', get_template_directory_uri() . '/js/listing.js', array('jquery'), null, true);
    wp_register_script('team-js', get_template_directory_uri() . '/js/team.js', array('jquery'), null, true);
    wp_register_script('lazy-js', '//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js',
        array('jquery'), null, true);
    wp_register_script('lazy-js-plugins', '//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js',
        array('jquery'), null, true);

    //wp ajax scripts
    wp_register_script('communities-ajax', get_template_directory_uri() . '/js/communities.ajax.js', array('jquery'),
        null, true);
    wp_localize_script('communities-ajax', 'wpAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_register_script('search-ajax', get_template_directory_uri() . '/js/search.ajax.js', array('jquery'), null, true);
    wp_localize_script('search-ajax', 'wpAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_register_script('office-ajax', get_template_directory_uri() . '/js/office.ajax.js', array('jquery'), null, true);
    wp_localize_script('office-ajax', 'wpAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

    //enqeue
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('tether');
    wp_enqueue_script('bootstrap-js');
    wp_enqueue_script('custom-scripts');
    wp_enqueue_script('lazy-js');
    wp_enqueue_script('lazy-js-plugins');
    wp_enqueue_style('select2-styles');
    wp_enqueue_script('select2-js');

}

add_action('wp_enqueue_scripts', 'kmaidx_scripts');

function prefix_add_footer_styles()
{
    wp_enqueue_style('kmaidx-footer-styles', get_template_directory_uri() . '/style.css');
}

;
add_action('get_footer', 'prefix_add_footer_styles');

/*
* Pull in our favorite KMA add-ons.
*/
function loadModules()
{

    if (is_admin()) {

        $idxSettings = new AdminSettings();
        $idxSettings->setupPage();

        $beachyBuckets = new AdminLeads();
        $beachyBuckets->createNavLabel();
        $beachyBuckets->addUserRole('Author', [
            'edit_agent'              => true,
            'publish_agents'          => true,
            'delete_published_agents' => false,
            'edit_agents'             => true,
            'delete_agents'           => false,
            'edit_others_agents'      => false,
            'delete_others_posts'     => false,
        ]);
        $beachyBuckets->addUserRole('Administrator', [
            'read_agents'             => true,
            'edit_agent'              => true,
            'edit_agents'             => true,
            'edit_others_agents'      => true,
            'delete_agents'           => true,
            'publish_agents'          => true,
            'read_offices'            => true,
            'edit_offices'            => true,
            'edit_office'             => true,
            'edit_others_offices'     => true,
            'delete_offices'          => true,
            'publish_offices'         => true,
            'read_communities'        => true,
            'edit_communities'        => true,
            'edit_communitiess'       => true,
            'edit_others_communities' => true,
            'delete_communities'      => true,
            'publish_communities'     => true,
            'read_leads'              => true,
            'edit_leads'              => true,
            'edit_lead'               => true,
            'edit_others_leads'       => true,
            'delete_leads'            => true,
            'publish_leads'           => true,
        ]);
    }

}

add_action('after_setup_theme', 'loadModules');

require wp_normalize_path(get_template_directory() . '/inc/template-tags.php');
require wp_normalize_path(get_template_directory() . '/inc/extras.php');
require wp_normalize_path(get_template_directory() . '/inc/customizer.php');

if ( ! function_exists('kmaidx_inline')) :
    function kmaidx_inline()
    {
        ?>
        <style type="text/css">
            <?php echo file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css' ) ?>
        </style>
        <style type="text/css">
            <?php echo file_get_contents(wp_normalize_path(get_template_directory() . '/modules/modulestyles.php' )) ?>
        </style>
        <style type="text/css">
            <?php echo file_get_contents(wp_normalize_path(get_template_directory() . '/css/jquery-ui.min.css' )) ?>
        </style>
        <style type="text/css">
            <?php echo file_get_contents(wp_normalize_path(get_template_directory() . '/css/inline.css' )) ?>
        </style>
        <style type="text/css">
            <?php echo file_get_contents(wp_normalize_path(get_template_directory() . '/css/ie.css' )) ?>
        </style>
        <?php
    }
endif;
add_action('wp_head', 'kmaidx_inline');

function in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {
        if (is_array($item)) {
            foreach ($item as $arr) {
                if (is_array($arr)) {
                    if (in_array($needle, $arr, $strict)) {
                        return true;
                    }
                } else {
                    if ($strict ? $arr === $needle : $arr == $needle) {
                        return true;
                    }
                }
            }
        } else {
            if ($strict ? $item === $needle : $item == $needle) {
                return true;
            }
        }

    }

    return false;
}

add_action('wp_ajax_loadCommMapPins', 'loadCommMapPins');
add_action('wp_ajax_nopriv_loadCommMapPins', 'loadCommMapPins');
function loadCommMapPins()
{

    if (isset($_SESSION['communitymap'])) {

        $result = $_SESSION['communitymap'];

    } else {

        $communities   = new Communities();
        $communitylist = $communities->getCommunities();

        foreach ($communitylist as $community) {

            if ($community['latitude'] == '' || $community['longitude'] == '') {

            } else {
                $return[] = array(
                    'name' => $community['title'],
                    'lat'  => $community['latitude'],
                    'lng'  => $community['longitude'],
                    'type' => 'neighborhood', //name of pin (_-pin.png)
                    'link' => get_post_permalink($community['id'])
                );
            }

        }

        $offices      = new Offices();
        $locationlist = $offices->getAllOffices();

        foreach ($locationlist as $location) {
            $return[] = $location;
        }

        $return[] = array(
            'name' => 'Laguna Beach',
            'lat'  => '30.2387797',
            'lng'  => '-85.9252252',
            'type' => 'beach' //name of pin (_-pin.png)30.2119524,-85.8720894
        );

        $return[] = array(
            'name' => 'Edgewater Beach',
            'lat'  => '30.2119524',
            'lng'  => '-85.8720894',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'Grand Lagoon',
            'lat'  => '30.1713572',
            'lng'  => '-85.8000543',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'Thomas Drive',
            'lat'  => '30.1520455',
            'lng'  => '-85.769862',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'West Panama City Beach',
            'lat'  => '30.2584549',
            'lng'  => '-85.9703253',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'Seacrest',
            'lat'  => '30.2895669',
            'lng'  => '-86.0503214',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'Beaches Near Seagrove',
            'lat'  => '30.3098054',
            'lng'  => '-86.1085854',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'Blue Mountain Beaches',
            'lat'  => '30.3375421',
            'lng'  => '-86.2007431',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $return[] = array(
            'name' => 'Beaches Near Draper Lake',
            'lat'  => '30.3421559',
            'lng'  => '-86.2178834',
            'type' => 'beach' //name of pin (_-pin.png)
        );

        $_SESSION['communitymap'] = json_encode($return);
        $result                   = json_encode($return);

    }

    if ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo $result;
    }

    wp_die();
}

add_action('wp_ajax_loadOfficePins', 'loadOfficePins');
add_action('wp_ajax_nopriv_loadOfficePins', 'loadOfficePins');
function loadOfficePins()
{

    if (isset($_SESSION['officemap'])) {

        $result = $_SESSION['officemap'];

    } else {

        $return = array();

        $offices      = new Offices();
        $locationlist = $offices->getAllOffices();

        foreach ($locationlist as $location) {
            $return[] = $location;
        }

        $_SESSION['officemap'] = json_encode($return);
        $result                = json_encode($return);
    }

    if ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo $result;
    }

    wp_die();
}

add_filter('get_the_archive_title', function ($title) {

    if (is_category()) {

        $title = single_cat_title('Beachy Blog ', false);

    } elseif (is_tag()) {

        $title = single_tag_title('Beachy Tag: ', false);

    } elseif (is_author()) {

        $title = '<span class="vcard">' . get_the_author() . '</span>';

    }

    return $title;

});

//if(isset($_GET['test_email']) && $_GET['test_email'] == 'send') {
//    $listingUpdated = new ListingUpdated();
//    $listingUpdated->notify();
//}

if (! wp_next_scheduled('notifications_hook')) {
    wp_schedule_event(time(), 'daily', 'notifications_hook');
}

add_action('notifications_hook', function()
{
    $listingUpdated = new ListingUpdated();
    $listingUpdated->notify();
});

// Creates a shortcode for a custom search page
// Author: Opey 9-26-18
function custom_searchpage_shortcode( $atts ) {
    $a = shortcode_atts( [
        'omni'      => 'Panama City',
        'type'      => 'Single Family Home',
        'status'    => 'Active',
        'min_price' => 0,
        'max_price' => 9000000000
    ], $atts );

    $currentPage  = (isset($_GET['pg']) ? $_GET['pg'] : 1);
    $searchCriteria = (isset($_GET['qs']) ? $_GET : [
        'omniField'    => $a['omni'],
        'status'       => $a['status'],
        'propertyType' => $a['type'],
        'minPrice'     => $a['min_price'],
        'maxPrice'     => $a['max_price'],
        'pg'           => $currentPage
    ]);

    $qs           = new QuickSearch($searchCriteria);
    $results      = $qs->create();
    $listings     = $results->data;
    $lastPage     = $results->last_page;
    $totalResults = $results->total;

    $currentUrl   = preg_replace("/&pg=\d+/", "", $_SERVER['REQUEST_URI']) . (isset($_GET['qs']) ? '' : '?browse=true');

    $output = '';

    ob_start(); ?>
    <div class="row pt-4">
    <?php foreach ($listings as $result) { ?>
        <div class="listing-tile property-search col-sm-6 col-lg-3 text-center mb-5">
            <?php include( locate_template( 'template-parts/mls-search-listing.php' ) ); ?>
        </div>
    <?php } ?>
    </div>
    <nav aria-label="Search results navigation" class="text-center mx-auto">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" <?php echo(1 != $currentPage ? 'href="'.$currentUrl.'&pg=1"' : 'disabled'); ?> aria-label="First">
                    <span>First</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" <?php echo(1 != $currentPage ? 'href="'.$currentUrl.'&pg='.($currentPage - 1).'"' : 'disabled'); ?> aria-label="Previous">
                    <span>Previous</span>
                </a>
            </li>
            <li class="page-item">
                <span class="page-link disabled" ><?php echo $currentPage; ?></span>
            </li>
            <li class="page-item">
                <a class="page-link" <?php echo($lastPage != $currentPage ? 'href="'.$currentUrl.'&pg='.($currentPage + 1).'"' : 'disabled'); ?> aria-label="Next">
                    <span>Next</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" <?php echo($lastPage != $currentPage ? 'href="'.$currentUrl.'&pg='.$lastPage.'"' : 'disabled'); ?> aria-label="Next">
                    <span>Last</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php get_template_part( 'template-parts/mls', 'disclaimer' ); ?>
    <?php
  
    return ob_get_clean();
}
add_shortcode( 'custom_searchpage', 'custom_searchpage_shortcode' );

function agent_app_shortcode( $atts ){

    $a = shortcode_atts( [
        'agent_id' => '',
    ], $atts ); 

    $output = '
    <a name="download-app" id="download-app">&nbsp;</a>
    <div class="container wide pb-md-4" >
        <div class="row align-items-center justify-content-between no-gutters">
            <div class="col-12 col-lg-6 pr-4">
                <h3>
                    <a class="bebas" href="http://app.beachybeach.com/'.$a['agent_id'].'" target="_new">Download My Free Real Estate Search App</a>
                </h3>
                <ul>
                    <li>Easily search for properties by area, price, bedrooms, square footage and more.</li>
                    <li>GPS enabled to show you which properties are for sale or rent near you.</li>
                    <li>Easy to use Map Search</li>
                    <li>See close by Open Houses at a glance!</li>
                    <li>Save Searches and be notified when new properties that match your search come on the market</li>
                    <li>It\'s FREE FOREVER!</li>
                </ul>
                <p>Better than Zillow, Trulia or Realtor.com because the listings come directly from the local MLS in Real Time! <strong>Share it with your Friends!</strong></p>
                <a class="btn btn-primary" href="http://app.beachybeach.com/'.$a['agent_id'].'" target="_new" >Download Now</a>
            </div>
            <div class="col-md-auto"> 
                <div class="row py-2 py-md-0">
                    <div class="col-4 col-sm-auto p-2 text-center justify-content-center">
                        <a href="http://app.beachybeach.com/'.$a['agent_id'].'" target="_new">
                            <img class="img-fluid" src="https://beachybeach.com/wp-content/uploads/2017/05/AppCap3-169x300.png" alt="Beachy Beach Search Mobile App" srcset="https://beachybeach.com/wp-content/uploads/2017/05/AppCap3-169x300.png 169w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap3-768x1366.png 768w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap3-576x1024.png 576w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap3.png 1125w" sizes="(max-width: 169px) 100vw, 169px" > 
                        </a>
                    </div>
                    <div class="col-4 col-sm-auto p-2 text-center justify-content-center">
                        <a href="http://app.beachybeach.com/'.$a['agent_id'].'" target="_new">
                            <img class="img-fluid" src="https://beachybeach.com/wp-content/uploads/2017/05/AppCap2-169x300.png" alt="Beachy Beach Search Mobile App" srcset="https://beachybeach.com/wp-content/uploads/2017/05/AppCap2-169x300.png 169w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap2-768x1366.png 768w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap2-576x1024.png 576w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap2.png 1125w" sizes="(max-width: 169px) 100vw, 169px" > 
                        </a>
                    </div>
                    <div class="col-4 col-sm-auto p-2 text-center justify-content-center">
                        <a href="http://app.beachybeach.com/'.$a['agent_id'].'" target="_new">
                            <img class="img-fluid" src="https://beachybeach.com/wp-content/uploads/2017/05/AppCap1-169x300.png" alt="Beachy Beach Search Mobile App" srcset="https://beachybeach.com/wp-content/uploads/2017/05/AppCap1-169x300.png 169w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap1-768x1366.png 768w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap1-576x1024.png 576w, https://beachybeach.com/wp-content/uploads/2017/05/AppCap1.png 1125w" sizes="(max-width: 169px) 100vw, 169px" >
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';

    return $output;
}

add_shortcode( 'bb_app', 'agent_app_shortcode' );

// Change og:type of episodes and videos to video
// function yoast_change_opengraph_type( $type ){
//     if ( get_post_format() == 'video' ) {
//         return 'video';
//     }else{
//         return get_post_type();
//     }
    
// }
// add_filter( 'wpseo_opengraph_type', 'yoast_change_opengraph_type', 10, 1 );

// Add og:video meta tag for episodes and videos
function yoast_add_og_video() {
    if ( get_post_format() == 'video' ) {
        $post = get_post();
        preg_match('/\[embed(.*)](.*)\[\/embed]/', $post->post_content, $video);
        if(isset($video[2])){
            echo '<meta property="og:video" content="' .  $video[2] . '" />', "\n";
            echo '<meta property="og:video:secure_url" content="' .  str_replace('http://','https://' , $video[2]) . '" />', "\n";
            echo '<meta property="og:video:height" content="1080" />', "\n";
            echo '<meta property="og:video:width" content="1920" />', "\n";
        }
        //echo '<meta property="og:image" content="https://img.youtube.com/vi/'.$videoParts[3].'/maxresdefault.jpg" />', "\n";
    }
}
add_action( 'wpseo_opengraph', 'yoast_add_og_video', 10, 1 );


add_filter('wpseo_opengraph_image', function () {
    if ( get_post_format() == 'video' ) {
        $post = get_post();
        preg_match('/\[embed(.*)](.*)\[\/embed]/', $post->post_content, $video);
        if(isset($video[2])){
            $videoParts = explode('/',$video[2]);

            if(isset($videoParts[3])){
                return 'https://img.youtube.com/vi/'.$videoParts[3].'/maxresdefault.jpg';
            }
        }
    }
});