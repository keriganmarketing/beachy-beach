<?php

use Includes\Modules\Agents\Agents;
use Includes\Modules\MLS\FullListing;
use Includes\Modules\MLS\BeachyBucket;

if (isset($_GET['mls'])) {
    $mlsNumber   = $_GET['mls'];
    $fullListing = new FullListing($mlsNumber);
    $listingInfo = $fullListing->create();

    // echo '<pre>',print_r($listingInfo),'</pre>';

    if ($listingInfo) {

        $buttonText = ($fullListing->isInBucket(get_current_user_id(),
            $listingInfo->mls_account) ? 'REMOVE FROM BUCKET' : 'SAVE TO BUCKET');
        if (isset($_POST['user_id']) && isset($_POST['mls_account'])) {
            $bb = new BeachyBucket();
            $bb->handleFavorite($_POST['user_id'], $_POST['mls_account']);
            header("Refresh:0");
        }

        $isOurs = $fullListing->isOurs($listingInfo);

        // echo '<pre>',print_r($isOurs),'</pre>';

        if (is_array($isOurs)) {
            // $agents  = new Agents;
            // $mlsData = $agents->getAgentByIds($listingInfo->listing_member_shortid);
            // echo '<pre>',print_r($mlsData),'</pre>';
            // $agentData = $agents->assembleAgentData($mlsData->data[0]->first_name . ' ' . $mlsData->data[0]->last_name);
            $agentData = $isOurs[0];
        }

        $title = $listingInfo->street_number . ' ' . $listingInfo->street_name .' '. $listingInfo->street_suffix;
        if ($listingInfo->unit_number != '') {
            $title = $title . ' ' . $listingInfo->unit_number;
        }

        $fullListing->setListingSeo($listingInfo);

        $openHouses = $listingInfo->open_houses;

    }

}

get_header(); ?>
    <div id="content">
        <article id="post-<?php echo $listingInfo->mls_account; ?>" class="listing">
            <header class="entry-header">
                <div class="container wide">
                    <?php if ($listingInfo) { ?>
                        <h1 class="entry-title"><?php echo $title; ?> <span
                                    class="subhead small">MLS# <?php echo $listingInfo->mls_account; ?></span></h1>
                    <?php } else { ?><h1>404</h1><?php } ?>
                </div>
            </header><!-- .entry-header -->
            
            <div class="entry-content">
                <div class="container wide">
                    <?php if ($listingInfo) { ?>
                        <div class="row">
                            <div class="col-lg-5 listing-left">
                                <div class="listing-slider">
                                    <?php include(locate_template('template-parts/listing-photos.php')); ?>
                                </div>
                            </div>
                            <div class="col-lg-7 listing-right">
                                <div class="listing-core">
                                    <?php include(locate_template('template-parts/listing-core.php')); ?>
                                </div>
                                <?php if (isset($openHouses)) { ?>
                                    <div class="open-houses">
                                        <?php include(locate_template('template-parts/listing-open-houses.php')); ?>
                                    </div>
                                <?php } ?>
                                <h2>Property Features</h2>
                                <div class="row">
                                    <div class="col">
                                        <?php if (in_array($listingInfo->class, array('G', 'A'), false)) { ?>
                                            <div class="listing-residential">
                                                <?php include(locate_template('template-parts/listing-residential.php')); ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (in_array($listingInfo->class, array('C'), false)) { ?>
                                            <div class="listing-land">
                                                <?php include(locate_template('template-parts/listing-land.php')); ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (in_array($listingInfo->class, array('E', 'J', 'F'), false)) { ?>
                                            <div class="listing-commercial">
                                                <?php include(locate_template('template-parts/listing-commercial.php')); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if ($isOurs && isset($agentData['name'])) { ?>
                                        <div class="col-md-5">
                                            <div class="listing-agent-box">
                                                <?php include(locate_template('template-parts/listing-agent.php')); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <hr>
                                <?php include(locate_template('template-parts/listing-features.php')); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row location-info">
                            <?php include(locate_template('template-parts/listing-location.php')); ?>
                        </div>
                        <?php get_template_part( 'template-parts/mls', 'disclaimer' ); ?>
                    <?php } else { ?>
                        <p class="center">The requested listing is no longer available.</p>
                        <a class="btn btn-primary bebas text-white" style="font-size:1.5em;" href="/property-search/" >Search Active Properties</a>
                    <?php } ?>
                </div>
            </div>
        </article>
    </div>
<?php
wp_enqueue_script('listing-js');
get_footer();
