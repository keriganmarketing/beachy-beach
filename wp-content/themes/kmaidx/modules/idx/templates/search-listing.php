<?php //mini-listing template ?>

	<div class="listing-tile col-md-6 col-xl-3 text-center">
		<div class="listing-tile-container">
				<a class="listing-link" href="/listing/?mls=<?php echo $currententry['MLS_ACCT']; ?>"></a>
				<div class="embed-responsive embed-responsive-16by9">
					<div class="embed-responsive-item listing-tile-photo">
                        <?php if ( $isNew && $currententry['STATUS'] == 'Active' ) { ?>
							<span
								class="status-flag just-listed">Just Listed</span>
						<?php } ?>
						<?php if ( $currententry['STATUS'] == 'Sold' ) { ?>
							<span class="status-flag sold">Sold on <?php echo date( 'M j, Y', strtotime( $currententry['SOLD_DATE'] ) ); ?>
								for $<?php echo number_format( $currententry['SOLD_PRICE'] ); ?></span>
						<?php } ?>
						<?php if ( $currententry['STATUS'] == 'Pending' ) { ?>
							<span class="status-flag under-contract">SALE PENDING</span>
						<?php } ?>
						<?php if ( $currententry['STATUS'] == 'Contingent' ) { ?>
							<span class="status-flag contingent">SALE CONTINGENT</span>
						<?php } ?>
						<img src="<?php echo $mainphoto; ?>" class="img-fluid lazy"
						     alt="MLS Property <?php echo $currententry['MLS_ACCT']; ?> for sale in <?php echo $currententry['CITY']; ?>"/>
					</div>
				</div>
				<div class="tile-info">

					<div class="tile-section">
						<span
							class="addr1"><?php echo $currententry['STREET_NUM'] . ' ' . $currententry['STREET_NAME']; ?></span>
						<?php if ( $currententry['UNIT_NUM'] != '' ) { ?><span
							class="unit"><?php echo $currententry['UNIT_NUM']; ?></span><?php } ?>
						<br><span class="city"><?php echo $currententry['CITY']; ?></span>, <span
							class="state"><?php echo $currententry['STATE']; ?></span>
					</div>

					<div class="tile-section price">
						<p><span class="price">$<?php echo number_format( $currententry['LIST_PRICE'] ); ?></span></p>
					</div>

					<div class="tile-section">
						<div class="row">
							<?php if ( $currententry['CLASS'] == 'RES' || strpos( $currententry['CLASS'], 'Residential' ) !== false ) { //RESIDENTIAL LISTINGS ?>
								<div class="col-3 text-center">
									<span class="icon"><img src="<?php echo getSvg( 'beds' ); ?>" alt="bedrooms"
									                        class="img-fluid lazy"></span>
									<span class="baths-num icon-data"><?php echo $beds; ?></span>
									<span class="icon-label">BEDS</span>
								</div>
								<div class="col-3 text-center">
									<span class="icon"><img src="<?php echo getSvg( 'baths' ); ?>" alt="bathrooms"
									                        class="img-fluid lazy"></span>
									<span class="baths-num icon-data"><?php echo $baths; ?></span>
									<span class="icon-label">BATHS</span>
								</div>
								<div class="col-3 text-xs-center">
									<span class="icon"><img src="<?php echo getSvg( 'sqft' ); ?>" alt="sqft"
									                        class="img-fluid lazy"></span>
									<span class="baths-num icon-data"><?php echo number_format( $sqft ); ?></span>
									<span class="icon-label">SQFT</span>
								</div>
								<?php if ( $currententry['PROP_TYPE'] == 'Condominiums' || $currententry['PROP_TYPE'] == 'Condominium' || strpos( $currententry['PROP_TYPE'], 'ASF' ) !== false || strpos( $currententry['PROP_TYPE'], 'CONDO' ) !== false ) { //CONDO OR MULTI ?>
									<div class="col-3 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'parking' ); ?>" alt="parking spaces" class="img-responsive lazy" ></span>
										<span
											class="baths-num icon-data"><?php echo number_format($currententry['PARKING_SPACES'], 0); ?></span>
										<span class="icon-label">PARKING</span>
									</div>
								<?php } else { //HOUSE ?>
									<div class="col-3 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'acres' ); ?>" alt="acres"
										                        class="img-fluid lazy"></span>
										<span class="baths-num icon-data"><?php echo $acreage; ?></span>
										<span class="icon-label">ACRES</span>
									</div>
								<?php } ?>
							<?php } elseif ( $currententry['CLASS'] == 'LND' || $currententry['CLASS'] == 'Residential Land' || $currententry['CLASS'] == 'Commercial Land' ) { //LOTS & LAND ?>
								<div class="col-6 text-center">
									<span class="icon"><img src="<?php echo getSvg( 'lotsize' ); ?>" alt="lot size"
									                        class="img-fluid lazy"></span>
									<span
										class="lot-dim-num icon-data"><?php echo str_replace( ' ', '', $currententry['LOT_DIMENSIONS'] ); ?></span>
									<span class="icon-label">LOT SIZE</span>
								</div>
								<div class="col-6 text-center">
									<span class="icon"><img src="<?php echo getSvg( 'acres' ); ?>" alt="acres"
									                        class="img-fluid lazy"></span>

									<span class="acres-num icon-data"><?php echo $acreage; ?></span>
									<span class="icon-label">ACRES</span>
								</div>
							<?php } else { //COMMERCIAL LISTINGS ?>
								<?php if ( $beds > 0 || $baths > 0 ) { //CHECK FOR BUILDING ?>
									<div class="col-3">
										<span class="icon"><img src="<?php echo getSvg( 'rooms' ); ?>" alt="rooms"
										                        class="img-fluid lazy"></span>
										<span class="beds-num icon-data"><?php echo $beds; ?></span>
										<span class="icon-label">ROOMS</span>
									</div>
									<div class="col-3 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'baths' ); ?>" alt="bathrooms"
										                        class="img-fluid lazy"></span>
										<span class="baths-num icon-data"><?php echo $baths; ?></span>
										<span class="icon-label">BATHS</span>
									</div>
									<div class="col-3 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'sqft' ); ?>" alt="sqft"
										                        class="img-fluid lazy"></span>
										<span class="sqft-num icon-data"><?php echo number_format( $sqft ); ?></span>
										<span class="icon-label">SQFT</span>
									</div>
									<div class="col-3 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'lotsize' ); ?>" alt="lot size"
										                        class="img-fluid lazy"></span>
										<span
											class="lot-dim-num icon-data"><?php echo $currententry['LOT_DIMENSIONS'] ?></span>
										<span class="icon-label">LOT SIZE</span>
									</div>
								<?php } else { //JUST LAND ?>
									<div class="col-6 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'lotsize' ); ?>" alt="lot size"
										                        class="img-responsive lazy"></span>
										<span
											class="lot-dim-num icon-data"><?php echo str_replace( ' ', '', $currententry['LOT_DIMENSIONS'] ); ?></span>
										<span class="icon-label">LOT SIZE</span>
									</div>
									<div class="col-6 text-center">
										<span class="icon"><img src="<?php echo getSvg( 'acres' ); ?>" alt="acres"
										                        class="img-responsive lazy"></span>
										<span class="acres-num icon-data"><?php echo $acreage; ?></span>
										<span class="icon-label">ACRES</span>
									</div>
								<?php } ?>
							<?php } ?>

						</div>
					</div>

					<div class="tile-section text-center">
						<span class="mlsnum">MLS# <?php echo $currententry['MLS_ACCT']; ?>
							/ ID# <?php echo $currententry['id']; ?></span>
					</div>

				</div>
		</div>
	</div>
<?php

?>