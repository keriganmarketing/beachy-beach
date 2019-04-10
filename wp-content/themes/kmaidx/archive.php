<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

get_header();

$category = get_category( get_query_var( 'cat' ) );
$cat_id = $category->cat_ID;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$category_query = new WP_Query(array(
	'cat'       => $cat_id,
	'paged'     => $paged
));

?>
<div id="content" class="site-content support">

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php if ( $category_query->have_posts() ) : ?>

                <header id="blogheader">
                    <div class="container wide">
                        <h1 class="page-title"><?php the_archive_title(); ?></h1>
                    </div>
                </header>

                <div class="blog-container">
                    <div class="container wide">
                        <div class="row">
                            <?php while ( $category_query->have_posts() ) : $category_query->the_post();
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

</div>
<?php
//get_sidebar();
get_footer();
