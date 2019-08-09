<div class="row">
    <div class="col-md-6 flex-order-md-second">
        <div id="req-info-btn" class="text-center text-md-right">
            <form class="form form-inline" action="/contact/" method="get" style="display:inline-block;" >
                <input type="hidden" name="reason" value="Property inquiry" />
                <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>" />
                <input type="hidden" name="mls_number" value="<?php echo $listingInfo->mls_account; ?>" />
                <input type="hidden" name="selected_agent" value="<?php echo ($isOurs ? $agentData['name'] : ''); ?>" />
                <button type="submit" class="btn btn-primary mb-2" >Request Info</button>
            </form>
			<?php if(is_user_logged_in()){?>
                <form class="form form-inline" method="post" style="display:inline-block;" >
                    <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>" />
                    <input type="hidden" name="mls_account" value="<?php echo $listingInfo->mls_account; ?>" />
                    <button type="submit" class="btn btn-primary mb-2" ><img src="<?php echo getSvg( 'star' ); ?>" alt="save to favorites" style="width: 16px; vertical-align: sub; margin: 0 3px 0 0;"> <?php echo $buttonText; ?></button>
                </form>
			<?php }else{ ?>
                <a href="/beachy-bucket/user-login/" class="btn btn-primary mb-2" >Log in to save</a>
            <?php } ?>
            <button type="button" class="btn btn-primary hidden-md-up mb-2" data-toggle="modal" data-target="#lightbox" >View more photos</button>

            <div class="social-sharing" style="padding: .5rem 0;">
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
    <div class="col-md-6 flex-order-md-first text-center text-md-left">
        <h1 class="listing-page-location mt-2 mt-md-0"><?php echo $listingInfo->street_number.' '.$listingInfo->street_name. ' '. $listingInfo->street_suffix; ?></h1>
		<h2 class="listing-page-area"><?php echo $listingInfo->city; ?>, FL</h2>
		<h3 class="listing-page-price">$<?php echo number_format($listingInfo->price); ?> 
            <a class="btn btnprimary" href="/mortgage-calculator/?balance=<?php echo $listingInfo->price; ?>" >calculate monthly payment</a></h3>
	</div>
</div>
<div class="listing-details">
	<p><?php echo $listingInfo->description; ?></p>
</div>