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
<div id="content">


        <div id="primary" class="content-area">
            <main id="main" class="site-main" >

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <div class="container wide">
				            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                        </div>
                    </header><!-- .entry-header -->
                    <div class="entry-content"></div>
                </article>
                <div class="container wide">
                    <div class="row">
                        <div class="col-lg-6">
	                        <?php the_content(); ?>
	                        <?php get_template_part( 'template-parts/content', 'contact-form' ); ?>
                        </div>
                        <div class="col-lg-6">
	                        <?php get_template_part( 'template-parts/mls', 'office-map' ); ?>
                        </div>
                    </div>
                </div>

            </main><!-- #main -->
        </div><!-- #primary -->


</div>
<?php
wp_enqueue_script( 'office-ajax' );
get_footer();
