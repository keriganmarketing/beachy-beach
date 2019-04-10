<?php
use Includes\Modules\Agents\Agents;

$wpTeam = new Agents();
$team = $wpTeam->getTeam();

get_header(); ?>
<div id="content">

git pull
    <div id="primary" class="content-area">
        <main id="main" class="site-main" >

            <?php while ( have_posts() ) : the_post();

                get_template_part( 'template-parts/content', 'page' );

            endwhile; ?>

        </main><!-- #main -->
    </div><!-- #primary -->

    <div class="container wide">
        <div class="filter-buttons text-center">
            <p><?php
            $taxonomies = get_categories('taxonomy=office&type=agent');
            foreach($taxonomies as $office){
                echo ' <a class="filter-button btn btn-primary mb-2 xs-block" data-filter="'.$office->slug.'" >'.$office->name.'</a> ';
            }
                ?><a class="filter-button btn btn-primary mb-2 xs-block" data-filter="all" >All</a></p>
        </div>
        <div class="row">
            <?php include(locate_template('template-parts/mls-agents.php')); ?>
        </div>
    </div>


</div>
<?php
wp_enqueue_script( 'team-js' );
get_footer();
