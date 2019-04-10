<?php

use Includes\Modules\MLS\QuickSearch;
use Includes\Modules\MLS\BeachyBucket;

$currentPage  = (isset($_GET['pg']) ? $_GET['pg'] : 1);
$searchCriteria = (isset($_GET['pg']) ? $_GET : [
    'omniField' => get_post_meta( $post->ID, 'community_info_database_name', true ),
    'pg'   => $currentPage
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
        <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php get_template_part( 'template-parts/content', 'page' ); ?>

        <?php endwhile; ?>

        </main><!-- #main -->
    </div><!-- #primary -->

    <div class="container wide" >
        <div class="row">
            <div class="col">
			    <?php if(count($listings)> 0) {
				    include( locate_template( 'template-parts/mls-mini-map.php' ) );
			    } ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
				<?php if(count($listings)> 0) {
					include( locate_template( 'template-parts/mls-sortbar.php' ) );
				} ?>
            </div>
        </div>
        <div class="row">
			<?php
                if(count($listings)> 0) {
                    foreach ( $listings as $result ) { ?>
                        <div class="listing-tile col-sm-6 col-lg-3 text-center mb-5">
                            <?php include( locate_template( 'template-parts/mls-search-listing.php' ) ); ?>
                        </div>
                    <?php }
                }else{
                    echo '<div class="col" ><p class="text-center">There are currently no properties available for this community.</p></div>';
                }
			?>
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
        <p class="footnote disclaimer" style="font-size: .9em; text-align: center; color: #aaa;">Real estate property information provided by Bay County Association of REALTORS® and Emerald Coast Association of REALTORS®. IDX information is provided exclusively for consumers personal, non-commercial use, and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. This data is deemed reliable but is not guaranteed accurate by the MLS.</p>

    </div>

</div>
<?php get_footer();
