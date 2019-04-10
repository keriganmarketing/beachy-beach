<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
        <div class="container wide">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        </div>
	</header><!-- .entry-header -->

	<div class="entry-content">
        <div class="container wide">
            <div class="row justify-content-center">
                <div class="col-xl-11">
	                <?php the_content(); ?>
                </div>
            </div>
        </div>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
