<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/22/2017
 * Time: 1:56 PM
 */
?>
<div class="card" style="border-bottom:1px solid #ddd;">
<table class="table table-striped listing-data mb-0">
	<tbody>
	<tr><td class="title">MLS#</td><td class="data"><?php echo $listingInfo->mls_account; ?></td></tr>
    <tr><td class="title">Status</td><td class="data"><?php echo $listingInfo->status; ?></td></tr>
    <tr><td class="title">Property Type</td><td class="data"><?php echo $listingInfo->property_type; ?></td></tr>
	<tr><td class="title">H/C Sqft</td><td class="data"><?php echo number_format( intval($listingInfo->sq_ft), 0, '.', ',' ); ?></td></tr>
	<?php if(intval($listingInfo->acreage) > 0){ ?>
    <tr><td class="title">Acreage</td><td class="data">$<?php echo number_format( intval($listingInfo->acreage), 2,'.',','); ?> in <?php echo $listingInfo->last_tax_year; ?></td></tr>
	<?php } ?>
	<?php if(intval($listingInfo->lot_dimensions) > 0){ ?>
        <tr><td class="title">Lot Dimensions</td><td class="data"><?php echo $listingInfo->lot_dimensions; ?></td></tr>
	<?php } ?>
	<tr><td class="title">Year Built</td><td class="data"><?php echo $listingInfo->year_built; ?></td></tr>
	<tr><td class="title">Stories</td><td class="data"><?php echo floor( intval($listingInfo->stories) ); ?></td></tr>
    <?php if(intval($listingInfo->last_taxes) > 0){ ?>
	<tr><td class="title">Taxes</td><td class="data">$<?php echo number_format( intval($listingInfo->last_taxes), 2,'.',','); ?> in <?php echo $listingInfo->last_tax_year; ?></td></tr>
	<?php } ?>

	<?php if($listingInfo->hoa_amount != 0){ ?>
		<tr><td class="title">HOA Term</td><td class="data">$<?php echo number_format( intval($listingInfo->hoa_amount), 2,'.',','); ?></td></tr>
	<?php } ?>

	<?php if($listingInfo->hoa_frequency != ''){ ?>
		<tr><td class="title">HOA Term</td><td class="data"><?php echo $listingInfo->hoa_frequency; ?></td></tr>
	<?php } ?>

    </tbody>
</table>
</div>
