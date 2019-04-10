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

use Includes\Modules\MLS\QuickSearch;
use Includes\Modules\MLS\BeachyBucket;

$currentPage  = (isset($_GET['pg']) ? $_GET['pg'] : 1);
$searchCriteria = (isset($_GET['qs']) ? $_GET : [
    'omniField'    => 'Panama City Beach',
    'status'       => 'Active',
    'propertyType' => 'Single Family Home',
    'minPrice'     => 0,
    'maxPrice'     => 9000000000,
    'pg'           => $currentPage,
    ''
]);

$qs           = new QuickSearch($searchCriteria);
$results      = $qs->create();
$listings     = $results->data;
$lastPage     = $results->last_page;
$totalResults = $results->total;

$currentUrl   = preg_replace("/&pg=\d+/", "", $_SERVER['REQUEST_URI']) . (isset($_GET['qs']) ? '' : '?browse=true');

get_header(); ?>
<div id="content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" >

            <?php while ( have_posts() ) : the_post();

                get_template_part( 'template-parts/content', 'page' );

            endwhile; ?>

        </main><!-- #main -->
    </div><!-- #primary -->
    <div class="container wide" >
        <div class="row">
            <div class="col">
                <?php include(locate_template('template-parts/mls-searchbar.php')); ?>
            </div>
        </div>
        <div class="row">

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

    </div>
</div>
<?php get_template_part( 'template-parts/mls', 'mortgage-calulator' ); ?>
<?php
wp_enqueue_script( 'search-ajax' );
wp_enqueue_script( 'chart-js' );
wp_enqueue_script( 'mortgage-calc' );
get_footer();
