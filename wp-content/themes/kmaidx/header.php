<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package KMA_DEMO
 */

use Includes\Modules\MLS\BeachyBucket;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php
wp_head();
$current_user = wp_get_current_user();
$bb = new BeachyBucket();
?>

<script src="//cdn.jsdelivr.net/blazy/latest/blazy.min.js"></script>
</head>

<body <?php body_class(); ?>>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.1&appId=737945779737372&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'kmaidx' ); ?></a>
    <div id="top">
        <div class="maintenance-notification" >
            <p>October 5, 2018: Our property search is currently undergoing maintenance. We’ll be finished soon.</p>
        </div>
        <header id="masthead" class="site-header">
            
            <div class="container wide nopad">
                <div class="row no-gutters justify-content-center">
                    <div id="beach-bucket" class="col-md-5 d-lg-none" >
                        <div id="bucket-left" >
                            <p class="saved-num">
                                <?php echo (is_user_logged_in() ? $bb->getNumberOfBucketItems($current_user->ID) : '0'); ?>
                            </p>
                            <?php echo (is_user_logged_in() ? '<a href="/beachy-bucket/">' : ''); ?>
                            <img src="<?php echo get_template_directory_uri().'/img/beach-bucket.png'; ?>" alt="Save &amp; Compare Beach Properties">
                            <?php echo (is_user_logged_in() ? '</a>' : ''); ?>
                        </div>
                        <div id="bucket-right" class="text-left" >
                            <?php if(is_user_logged_in()){ ?>
                                <p class="logged-in"><a href="/beachy-bucket/"><span class="user-name" ><?php echo ( $current_user->user_firstname != '' ? $current_user->user_firstname : $current_user->user_login ); ?>'s</span>Beachy&nbsp;Bucket</a></p><p class="logout-link"><a class="logout-link" href="<?php echo wp_logout_url('/'); ?>">logout</a> </p>
                            <?php }else{ ?>
                                <p class="not-logged-in"><a class="login-link" href="/user-login/">Log In</a> to keep favorites in your Beachy Bucket</p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2 col-xl-auto text-center text-md-left mb-sm-2">
                        <a href="/" class="navbar-brand"><img src="<?php echo get_template_directory_uri().'/img/beachy-beach-logo.png'; ?>" alt="Beachy Beach Real Estate" ></a>
                    </div>
                    <div class="col-sm-4 col-md-5 text-center hidden-lg-up my-auto">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
                            <span class="btn-text" >MENU</span>
                            <span class="icon-box">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>
                        </button>
                    </div>
                    
                    <div class="col-12 col-md-6 col-xl hidden-md-down ml-auto d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-end ">
                            <div id="beach-bucket" >
                                <div id="bucket-left" >
                                    <p class="saved-num">
                                        <?php echo (is_user_logged_in() ? $bb->getNumberOfBucketItems($current_user->ID) : '0'); ?>
                                    </p>
                                    <?php echo (is_user_logged_in() ? '<a href="/beachy-bucket/">' : ''); ?>
                                    <img src="<?php echo get_template_directory_uri().'/img/beach-bucket.png'; ?>" alt="Save &amp; Compare Beach Properties">
                                    <?php echo (is_user_logged_in() ? '</a>' : ''); ?>
                                </div>
                                <div id="bucket-right" class="text-left" >
                                    <?php if(is_user_logged_in()){ ?>
                                        <p class="logged-in"><a href="/beachy-bucket/"><span class="user-name" ><?php echo ( $current_user->user_firstname != '' ? $current_user->user_firstname : $current_user->user_login ); ?>'s</span>Beachy&nbsp;Bucket</a></p><p class="logout-link"><a class="logout-link" href="<?php echo wp_logout_url('/'); ?>">logout</a> </p>
                                    <?php }else{ ?>
                                        <p class="not-logged-in"><a class="login-link" href="/user-login/">Log In</a> to keep favorites<br>in your Beachy Bucket</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="navbar-collapse navbar-toggleable-sm" id="navbar-header">
                            <?php wp_nav_menu(
                                array(
                                    'theme_location'  => 'menu-1',
                                    'container_class' => 'navbar-static',
                                    'container_id'    => 'navbarNavDropdown',
                                    'menu_class'      => 'navbar-nav justify-content-end',
                                    'fallback_cb'     => '',
                                    'menu_id'         => 'menu-1',
                                    'walker'          => new WP_Bootstrap_Navwalker(),
                                )
                            ); ?>
                        </div>
                    </div>


                </div>
            </div>
            <div class="clearfix"></div>
        </header>

    </div>
    <div class="hidden-lg-up">
        <div class="navbar-collapse navbar-toggleable-lg text-center" id="navbar-mobile">
            <?php wp_nav_menu(
                array(
                    'theme_location'  => 'menu-3',
                    'container_class' => 'navbar-static',
                    'container_id'    => 'navbarNavDropdown',
                    'menu_class'      => 'navbar-nav justify-content-end',
                    'fallback_cb'     => '',
                    'menu_id'         => 'menu-3',
                    'walker'          => new WP_Bootstrap_Navwalker(),
                )
            ); ?>
        </div>
    </div>
