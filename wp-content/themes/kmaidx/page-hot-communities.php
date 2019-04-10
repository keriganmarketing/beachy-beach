<?php
use Includes\Modules\MLS\Communities;

get_header(); ?>
<div id="content">


        <div id="primary" class="content-area">
            <main id="main" class="site-main" >

				<?php while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', 'page' );

				endwhile; ?>

            </main><!-- #main -->
        </div><!-- #primary -->


</div>
<div class="full-width-map" style="margin-top:0;">
    <div class="container xwide">
    <div id="map-header" class="hidden-md-down">
        <span id="office-toggle" class="map-toggles">Beachy Beach Office</span>
        <span id="neighborhood-toggle" class="map-toggles">Search by neighborhood</span>
        <span id="beach-toggle" class="map-toggles">View the beaches</span>
    </div>
    <?php get_template_part( 'template-parts/mls', 'community-map' ); ?>
    </div>
</div>
<div class="community-buttons">
    <div class="container wide">
        <div class="row justify-content-center align-items-center">
            <?php
                $communities = new Communities();
                $communitylist = $communities->getCommunities();

                foreach ($communitylist as $community) {
                    //echo '<pre>',print_r($community),'</pre>';
                    $link = get_post_permalink($community['id']);
                    ?>
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 text-center">
                        <div class="community-button-container">
                            <div class="aligner"></div>
                            <div class="community-button">
                                <h2 class=""><?php echo $community['title']; ?></h2>
                                <a href="<?php echo $link; ?>" class="community-button" ></a>
                            </div>
                        </div>
                    </div>

                <?php } ?>
        </div>
    </div>
</div>

<?php
wp_enqueue_script( 'communities-ajax' );
get_footer();