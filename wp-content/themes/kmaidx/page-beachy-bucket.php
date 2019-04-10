<?php
use Includes\Modules\MLS\BeachyBucket;
use Includes\Modules\MLS\FullListing;

get_header();

$bb      = new BeachyBucket();
$user_id = (isset($_GET['users_bucket']) ? $_GET['users_bucket'] : get_current_user_id());

$mlsNumbers = $bb->listingsSavedByUser($user_id);

if (isset($_POST['user_id']) && isset($_POST['mls_account'])) {
    $bb->handleFavorite($_POST['user_id'], $_POST['mls_account']);
    header("Refresh:0");
}
?>

    <div id="content">


    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php
            if(!is_user_logged_in()) {
                while (have_posts()) : the_post();

                    get_template_part('template-parts/content', 'page');

                endwhile;
            }else{ ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <div class="container wide">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </div>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <div class="container wide">
                        <p>&nbsp;</p>
                    </div>
                </div><!-- .entry-content -->

            </article><!-- #post-## -->
            <div class="container wide">
                <div class="account-actions text-right">
                    <a class="btn btn-sm btn-primary mr-1" href="/beachy-bucket/edit-account/">Edit my account information</a>
                    <a class="btn btn-sm btn-primary" href="/beachy-bucket/change-password/">Change my password</a>
                </div>
                <hr>
            </div>
            <?php } ?>
        </main><!-- #main -->
    </div><!-- #primary -->
    <?php if(is_user_logged_in()) { ?>
    <div class="container wide">
        <p>&nbsp;</p>
        <div class="row justify-content-center">
            <?php foreach ($mlsNumbers as $mlsNumber) {
                $fullListing = new FullListing($mlsNumber);
                $result      = $fullListing->create();
                if($result){  ?>
                <div class="col-sm-6 col-lg-3 text-center">
                    <div class="listing-tile bucket">
                        <?php include( locate_template( 'template-parts/mls-search-listing.php' ) ); ?>
                        <form class="form form-inline" method="post" style="display: inline-block;width: 100%;z-index: 5;padding: 1rem 0 2rem;">
                            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>" />
                            <input type="hidden" name="mls_account" value="<?php echo $mlsNumber; ?>" />
                            <button type="submit" class="btn btn-primary mb-2" >Remove from bucket</button>
                        </form>
                    </div>
                </div>
            <?php }else{ ?>
            <div class="col-sm-6 col-lg-3 text-center">
                <div class="listing-tile bucket">
                    <div class="listing-tile-container">
                        <p style="padding: 20px 20px 15px; color:#999999; font-size:1.5em; line-height: 1.5em;" class="listing-not-available">
                            Listing #<?php echo $mlsNumber; ?> has been sold or removed.
                        </p>
                    </div>
                    <form class="form form-inline" method="post" style="display: inline-block;width: 100%;z-index: 5;padding: 1rem 0 2rem;">
                        <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>" />
                        <input type="hidden" name="mls_account" value="<?php echo $mlsNumber; ?>" />
                        <button type="submit" class="btn btn-primary mb-2" >Remove from bucket</button>
                    </form>
                </div>
                <p>&nbsp;</p>
            </div>
            <?php }
            } ?>
        </div>
    </div>
    <?php } ?>
</div>

<?php get_footer();
