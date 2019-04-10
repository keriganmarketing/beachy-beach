<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_IDX
 */
preg_match('/\[embed(.*)](.*)\[\/embed]/', $post->post_content, $video);
?>
<div class="col-sm-6 col-lg-4 col-xl-3 text-center">
    <div class="blog-article">
        <div class="blog-image">
            <div class="embed-responsive embed-responsive-16by9">
            <?php 
                //echo do_shortcode($video[0]); 
                echo wp_oembed_get($video[2]);
                ?>
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