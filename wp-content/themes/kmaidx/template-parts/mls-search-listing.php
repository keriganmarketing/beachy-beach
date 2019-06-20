        <div class="listing-tile-container <?php echo $result->class; ?>">
            <a class="listing-link" href="/listing?mls=<?php echo $result->mls_account; ?>"></a>
            <div class="embed-responsive embed-responsive-16by9">
                <div class="embed-responsive-item listing-tile-photo">
                    <?php if ($result->status == 'Sold') { ?>
                        <span class="status-flag sold">Sold</span>
                    <?php } ?>
                    <?php if ($result->status == 'Pending') { ?>
                        <span class="status-flag under-contract">SALE PENDING</span>
                    <?php } ?>
                    <?php if ($result->status == 'Active-Contingent') { ?>
                        <span class="status-flag contingent">SALE CONTINGENT</span>
                    <?php } ?>
                    <?php if ($result->has_open_houses == 1) { ?>
                        <span class="status-flag contingent">OPEN HOUSE</span>
                    <?php } ?>
                    <img 
                        src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                        data-src="<?php echo ($result->preferred_image != '' ? str_replace('http://','//', $result->preferred_image) : get_template_directory_uri() . '/img/beachybeach-placeholder.png' ); ?>"
                        class="img-fluid lazy"
                        alt="MLS Property <?php echo $result->mls_account; ?> for sale in <?php echo $result->city; ?>"/>
                </div>
            </div>
            <div class="tile-info">

                <div class="tile-section">
                    <span class="addr1"><?php echo $result->street_number . ' ' . $result->street_name .' '. $result->street_suffix; ?></span>
                    <?php if ($result->unit_number != '') { ?><span class="unit">, <?php echo $result->unit_number; ?></span><?php } ?>
                    <br><span class="city"><?php echo $result->city; ?></span>,
                    <span class="state"><?php echo $result->state; ?></span>
                </div>

                <div class="tile-section price">
                    <p><span class="price"><?php echo ($result->price>0 ? '$'.number_format($result->price) : ''); ?></span></p>
                </div>

                <div class="tile-section">
                    <div class="row">
                        <?php if ($result->bedrooms > 0 || $result->bathrooms > 0) { //RESIDENTIAL LISTINGS ?>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo getSvg('beds'); ?>" alt="bedrooms" class="img-fluid lazy" type="image/svg+xml"></span>
                                <span class="baths-num icon-data"><?php echo $result->bedrooms; ?></span>
                                <span class="icon-label">BEDS</span>
                            </div>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo getSvg('baths'); ?>" alt="bathrooms" class="img-fluid lazy" type="image/svg+xml"></span>
                                <span class="baths-num icon-data"><?php echo $result->bathrooms; ?></span>
                                <span class="icon-label">BATHS</span>
                            </div>
                            <div class="col text-xs-center">
                                <span class="icon"><img src="<?php echo getSvg('sqft'); ?>" alt="sqft" class="img-fluid lazy" type="image/svg+xml"></span>
                                <span class="baths-num icon-data"><?php echo number_format($result->sq_ft); ?></span>
                                <span class="icon-label">SQFT</span>
                            </div>
                        <?php } elseif ($result->sq_ft > 0) { //RESIDENTIAL LISTINGS ?>
                            <div class="col text-xs-center">
                                <span class="icon"><img src="<?php echo getSvg('sqft'); ?>" alt="sqft" class="img-fluid lazy" type="image/svg+xml"></span>
                                <span class="baths-num icon-data"><?php echo number_format($result->sq_ft); ?></span>
                                <span class="icon-label">SQFT</span>
                            </div>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo getSvg('lotsize'); ?>" alt="lot size" class="img-fluid lazy" type="image/svg+xml"></span>
                                <span class="lot-dim-num icon-data"><?php ?></span>
                                <span class="icon-label">LOT SIZE</span>
                            </div>
                        <?php } else { //LOTS & LAND ?>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo getSvg('lotsize'); ?>" alt="lot size" class="img-fluid lazy" type="image/svg+xml"></span>
                                <span class="lot-dim-num icon-data"><?php echo (strlen($result->lot_dimensions) > 8 ? substr($result->lot_dimensions,0,8).'...' : $result->lot_dimensions); ?></span>
                                <span class="icon-label">LOT SIZE</span>
                            </div>
                            <div class="col text-center">
                                <span class="icon"><img src="<?php echo getSvg('acres'); ?>" alt="acres" class="img-fluid lazy" type="image/svg+xml"></span>
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
