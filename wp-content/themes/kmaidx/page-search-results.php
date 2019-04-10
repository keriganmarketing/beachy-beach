<?php
/**
 * @package idx
 */

get_header(); ?>
<div class="container-fluid" >
	<div class="row">
		<div class="col">
		<?php get_template_part( 'template-parts/mls', 'searchbar' ); ?>
		</div>
	</div>
	<div class="row">
		<?php get_template_part( 'template-parts/mls', 'searchlisting' ); ?>
	</div>
</div>

<?php
wp_enqueue_script( 'search-ajax' );
wp_enqueue_script( 'chart-js' );
wp_enqueue_script( 'mortgage-calc' );
get_footer();
