<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package MCCALL_SOD
 */

get_header(); ?>
<div id="content" class="site-content support">

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
        <?php if ( have_posts() ) :

            if ( is_home() && ! is_front_page() ) : ?>
            <header id="blogheader">
                <div class="container wide">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
                </div>
            </header>
            <?php endif; ?>

            <div class="blog-container">
                <div class="container wide">
                    <div class="row">
                        <?php while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/blogfeed', get_post_format() );
                        endwhile; ?>
                    </div>
                </div>
            </div>

            <?php the_posts_navigation();

        else :
            get_template_part( 'template-parts/content', 'none' );
        endif; ?>
    </main><!-- #main -->

</div>

<?php
get_footer();
