<?php
use Includes\Modules\Agents\Agents;



$wpTeam     = new Agents();
$agentData  = $wpTeam->assembleAgentData( $post->post_title );
$wpTeam->setAgentSeo($agentData);
$agentData['listings'] = ($agentData['short_ids'] != '' ? $wpTeam->getAgentListings($agentData['short_ids']) : '' );

$agentTerms = wp_get_object_terms( $post->ID, 'office' );

$agentCategories = array();
foreach ($agentTerms as $term) {
    array_push($agentCategories, array(
            'category-id'   => (isset($term->term_id) ? $term->term_id : null),
            'category-name' => (isset($term->name) ? $term->name : null),
            'category-slug' => (isset($term->slug) ? $term->slug : null),
        )
    );
}

$referrer = (isset($_SERVER['HTTP_REFERER']) ? (strpos('https://beachybeach.com', $_SERVER['HTTP_REFERER']) == 0 ? true : false ) : false);
if (!$referrer){
    $_SESSION['agent_override'] = $post->post_title;
}

get_header(); ?>
    <!-- <?php //print_r($agentIds); ?> -->
<div id="content">

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php include(locate_template('template-parts/content-realtor.php')); ?>

        <?php endwhile; ?>

        </main><!-- #main -->
    </div><!-- #primary -->

</div>
<?php get_footer();
