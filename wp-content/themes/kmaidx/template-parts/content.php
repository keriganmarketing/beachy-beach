<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

$photo_id = get_post_thumbnail_id($post->ID);
$mast_url = wp_get_attachment_url( $photo_id );
if($mast_url!=''){
	$large_array = image_downsize( $photo_id, 'large' );
	$mast_url = $large_array[0];
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header">
        <div class="container">
	        <?php if ( is_single() ) :
		        the_title( '<h1 class="entry-title">', '</h1>' );
	        else :
		        the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	        endif; ?>
        </div>
    </header><!-- .entry-header -->

	<div class="entry-content">
        <div class="container">
            <?php if($mast_url){ ?>
            <div class="row">
                <div class="single-image col-lg-4">
                    <img src="<?php echo $mast_url; ?>" class="img-fluid" alt="<?php echo $post->name; ?>" style="width:100%" />
                </div>
                <div class="single-post copy col-lg-8">
            <?php } ?>

                <?php if ( 'post' === get_post_type() ) : ?>
                <div class="entry-meta">
                    <?php kmaidx_posted_on(); ?>
                </div><!-- .entry-meta -->
                <?php endif; ?>

                <?php
                    the_content( sprintf(
                        /* translators: %s: Name of current post. */
                        wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'kmaidx' ), array( 'span' => array( 'class' => array() ) ) ),
                        the_title( '<span class="screen-reader-text">"', '"</span>', false )
                    ) );
                ?>

	        <?php if($mast_url){ ?>
                </div>
            </div>
	        <?php } ?>

            <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kmaidx' ),
                    'after'  => '</div>',
                ) );
            ?>
        </div>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
        <div class="container">
		<?php //kmaidx_entry_footer(); ?>
        </div>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
