<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/22/2017
 * Time: 2:08 PM
 */
$extfeatures = explode(',',$listingInfo->exterior);
$amenities = explode(',',$listingInfo->amenities);
?>
<div class="row">
	<div class="col-sm-6">
		<h3>Exterior</h3>
		<ul>
			<?php foreach($extfeatures as $feat){ ?>
				<li><?php echo $feat; ?></li>
			<?php } ?>
		</ul>
	</div>
	<div class="col-sm-6">
		<h3>Amenities</h3>
		<ul>
			<?php foreach($amenities as $feat){ ?>
				<li><?php echo $feat; ?></li>
			<?php } ?>
		</ul>
	</div>
</div>