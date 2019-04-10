<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

get_header(); ?>
<div id="illustration" class="text-center">
    <div class="container nopad">
        <h1 id="main-title">Beachy is a Way of Life.</h1>
        <p id="sub-title">Bringing families and the beach together.</p>
	    <?php get_template_part( 'template-parts/mls', 'quick-search' ); ?>
    </div>
    <div class="container">
        <div id="map-header" >
            <p class="text-center text-md-left" >Explore Our Communities</p>
            <span class="hidden-md-down">
            <span id="office-toggle" class="map-toggles">Beachy Beach Office</span>
            <span id="neighborhood-toggle" class="map-toggles">Search by neighborhood</span>
            <span id="beach-toggle" class="map-toggles">View the beaches</span>
            </span>
        </div>
        <div class="hidden-sm-down">
            <?php get_template_part( 'template-parts/mls', 'community-map' ); ?>
        </div>
        <div class="hidden-md-up" style="margin-bottom:1rem;">
            <a href="https://beachybeach.com/hot-communities/" class="btn btn-primary">View Map</a>
        </div>
    </div>
</div>
<?php
wp_enqueue_script( 'search-ajax' );
wp_enqueue_script( 'communities-ajax' );
get_template_part( 'template-parts/mls', 'mortgage-calulator' );
wp_enqueue_script( 'chart-js' );
wp_enqueue_script( 'mortgage-calc' );
get_footer();