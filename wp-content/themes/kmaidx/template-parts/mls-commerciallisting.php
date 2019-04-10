<?php //mini-listing template

global $paged;
global $wpdb;

$paged  = (get_query_var('paged')) ? abs((int)get_query_var('paged')) : 1;
$mls    = new MLS();

$query = $mls->buildQuery(array(
    'property_type' => array(
        'E',
	    'F'
    )
));

$sortBy          = isset($_GET['sortBy']) ? ' ORDER BY ' . $_GET['sortBy'] : '';
$orderBy         = isset($_GET['orderBy']) ? ' ' . $_GET['orderBy'] : '';
$total_query     = $mls->getTotalQuery($query);
$total           = $wpdb->get_var($total_query);
$listingsPerPage = 36;
$page            = $mls->determinePagination();
$offset          = $mls->determineOffset($page, $listingsPerPage);
$finalQuery      = $query . $sortBy . $orderBy . " LIMIT " . $offset . ", " . $listingsPerPage;
$results         = $wpdb->get_results($finalQuery);

foreach ($results as $result) { ?>
    <div class="listing-tile col-md-6 col-lg-3 text-center">
        <div class="listing-tile-container">
            <a class="listing-link" href="/listing?mls=<?php echo $result->mls_account; ?>"></a>
            <div class="embed-responsive embed-responsive-16by9">
                <div class="embed-responsive-item listing-tile-photo">

					<?php if ( $result->status == 'Sold' ) { ?>
                        <span class="status-flag sold">Sold on <?php ?></span>
					<?php } ?>
					<?php if ( $result->status == 'Pending' ) { ?>
                        <span class="status-flag under-contract">SALE PENDING</span>
					<?php } ?>
					<?php if ( $result->status == 'Contingent' ) { ?>
                        <span class="status-flag contingent">SALE CONTINGENT</span>
					<?php } ?>
                    <img src="<?php echo $result->preferred_image; ?>" class="img-fluid lazy" alt="MLS Property <?php echo $result->mls_account; ?> for sale in <?php echo $result->city; ?>"/>
                </div>
            </div>
            <div class="tile-info">

                <div class="tile-section">
                    <span class="addr1"><?php echo $result->street_number . ' ' . $result->street_name .' '. $result->street_suffix; ?></span>
					<?php if ( $result->unit_number != '' ) { ?>
                        <span class="unit"><?php echo $result->unit_number; ?></span><?php } ?>
                    <br><span class="city"><?php echo $result->city; ?></span>,
                    <span class="state"><?php echo $result->state; ?></span>
                </div>

                <div class="tile-section price">
                    <p><span class="price">$<?php echo number_format( $result->price ); ?></span></p>
                </div>

                <div class="tile-section">
                    <div class="row">
	                    <?php if ($result->bedrooms > 0 || $result->bathrooms > 0) { //RESIDENTIAL LISTINGS ?>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('beds'); ?>" alt="bedrooms" class="img-fluid lazy"></span>
                                <span class="baths-num icon-data"><?php echo $result->bedrooms; ?></span>
                                <span class="icon-label">BEDS</span>
                            </div>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('baths'); ?>" alt="bathrooms" class="img-fluid lazy"></span>
                                <span class="baths-num icon-data"><?php echo $result->bathrooms; ?></span>
                                <span class="icon-label">BATHS</span>
                            </div>
                            <div class="col text-xs-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('sqft'); ?>" alt="sqft" class="img-fluid lazy"></span>
                                <span class="baths-num icon-data"><?php echo number_format($result->sq_ft); ?></span>
                                <span class="icon-label">SQFT</span>
                            </div>
	                    <?php } elseif ($result->sq_ft > 0) { //RESIDENTIAL LISTINGS ?>
                            <div class="col text-xs-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('sqft'); ?>" alt="sqft" class="img-fluid lazy"></span>
                                <span class="baths-num icon-data"><?php echo number_format($result->sq_ft); ?></span>
                                <span class="icon-label">SQFT</span>
                            </div>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('lotsize'); ?>" alt="lot size" class="img-fluid lazy"></span>
                                <span class="lot-dim-num icon-data"><?php ?></span>
                                <span class="icon-label">LOT SIZE</span>
                            </div>
	                    <?php } else { //LOTS & LAND ?>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('lotsize'); ?>" alt="lot size" class="img-fluid lazy"></span>
                                <span class="lot-dim-num icon-data"><?php ?></span>
                                <span class="icon-label">LOT SIZE</span>
                            </div>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo $mls->getSvg('acres'); ?>" alt="acres" class="img-fluid lazy"></span>
                                <span class="acres-num icon-data"><?php echo $result->acreage; ?></span>
                                <span class="icon-label">ACRES</span>
                            </div>
	                    <?php } ?>

                    </div>
                </div>

                <div class="tile-section text-center">
                    <span class="mlsnum">MLS# <?php echo $result->mls_account; ?></span>
                </div>

            </div>
        </div>
    </div>
<?php } ?>

<nav aria-label="Search results navigation" class="text-center mx-auto">
    <ul class="pagination">
		<?php
		echo paginate_links(array(
			'base'      => add_query_arg('pg', '%#%'),
			'format'    => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total'     => ceil($total / $listingsPerPage),
			'current'   => $page,
		));
		?>
    </ul>
</nav>