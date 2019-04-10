<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/22/2017
 * Time: 2:07 PM
 */
$intfeatures = explode(',',$listingInfo->interior);
$appliances = explode(',',$listingInfo->appliances);
?>
<div class="row">
	<div class="col-sm-6">
		<h3>Interior</h3>
		<ul>
			<?php foreach($intfeatures as $feat){ ?>
				<li><?php echo $feat; ?></li>
			<?php } ?>
		</ul>
	</div>
	<div class="col-sm-6">
		<h3>Appliances</h3>
		<ul>
			<?php foreach($appliances as $feat){ ?>
				<li><?php echo $feat; ?></li>
			<?php } ?>
		</ul>
	</div>
</div>

