<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

preg_match('/\[embed(.*)](.*)\[\/embed]/', $post->post_content, $video);


?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <div class="container">
	        <?php the_title( '<p style="color:#FFF; font-size:2.2rem;" class="bebas">', '</p>' ); ?>
        </div>
    </header><!-- .entry-header -->

	<div class="entry-content">
        <div class="container">
            <div class="row">
                <div class="single-post copy col-lg-8 py-4">
                    <?php the_content(); ?>
                </div>
                <div class="single-image col-lg-4">
                    <div class="entry-meta py-4">
                        <?php kmaidx_posted_on(); ?>
                    </div><!-- .entry-meta -->
                    <div class="social-sharing py-4">
                        <div class="fb-share-button" 
                            data-href="<?php echo "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" 
                            data-layout="button" 
                            data-size="large" 
                            data-mobile-iframe="false"
                            ><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a>
                        </div>
                        <div 
                            class="fb-like" 
                            data-href="<?php echo "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" 
                            data-layout="button" 
                            data-action="like" 
                            data-size="large" 
                            data-show-faces="false" 
                            data-share="false"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
