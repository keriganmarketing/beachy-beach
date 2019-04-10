<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package KMA_DEMO
 */

get_header(); ?>
    <div id="content">


        <div id="primary" class="content-area">
            <main id="main" class="site-main" >
                <article>

                    <header class="entry-header">
                        <div class="container wide">
                            <h1 class="entry-title">404</h1>
                    </div>
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <div class="container wide">
                            <div class="row justify-content-center">
                                <div class="col-xl-11">
                                    <p>The page you requested does not exist. </p>
                                </div>
                            </div>
                        </div>
                    </div><!-- .entry-content -->
                </article>

            </main><!-- #main -->
        </div><!-- #primary -->


    </div>
<?php get_footer();
