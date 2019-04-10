<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package MCCALL_SOD
 */

?>
<div class="col-sm-6 col-lg-4 col-xl-3 text-center">
    <div class="blog-article">
        <div class="blog-image">
            <?php
            $thumb_id    = get_post_thumbnail_id( $post->ID );
            $thumb       = wp_get_attachment_image_src( $thumb_id, 'medium' );
            $thumb_url   = $thumb[0];
            ?>
            <div class="embed-responsive embed-responsive-4by3">
                <a href="<?php echo get_permalink($post->ID); ?>"><img src="<?php echo ($thumb_url != '' ? $thumb_url : get_template_directory_uri().'/img/beachybeach-placeholder.jpg' ); ?>" alt="<?php the_title(); ?>" class="embed-responsive-item img-fluid border-bottom" ></a>
            </div>
        </div>

        <header class="blog-header">
            <?php
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            if ( 'post' === get_post_type() ) : ?>
            <div class="entry-meta pb-2">
                <?php kmaidx_posted_on(); ?>
            </div><!-- .entry-meta -->
            <?php
            endif; ?>
        </header><!-- .entry-header -->

    </div>
    <div class="blog-link">
        <a href="<?php echo get_permalink($post->ID); ?>">Read article</a>
    </div><!-- .entry-content -->
</div>