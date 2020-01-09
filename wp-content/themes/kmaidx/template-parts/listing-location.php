<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/22/2017
 * Time: 2:08 PM
 */

$address = $listingInfo->street_number.' '.$listingInfo->street_name. ' '. $listingInfo->street_suffix . ', ' . $listingInfo->city . ', FL';
?>
<div class="col mb-2" >
    <div class="card" style="border-bottom:1px solid #ddd;">
	<table role="presentation" class="table table-striped listing-data mb-0">
		<tbody>
		<?php if( isset($listingInfo->lot_description) ){ ?>
            <tr><td class="title">Area</td><td class="data">$<?php echo $listingInfo->lot_description; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->waterfront) && $listingInfo->waterfront != null){ ?>
        <tr><td class="title">Waterfront</td><td class="data"><?php echo $listingInfo->waterfront; ?></td></tr>
		<?php }else{ echo '<tr><td class="title">Waterfront</td><td class="data">No</td></tr>'; } ?>
		<?php if( isset($listingInfo->waterview_description) && $listingInfo->waterview_description != null ){ ?>
        <tr><td class="title">Waterfront</td><td class="data"><?php echo $listingInfo->waterview_description; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->elementary_school) ){ ?>
            <tr><td class="title">Elementary School</td><td class="data"><?php echo $listingInfo->elementary_school; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->middle_school) ){ ?>
            <tr><td class="title">Middle School</td><td class="data"><?php echo $listingInfo->middle_school; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->high_school) ){ ?>
            <tr><td class="title">High School</td><td class="data"><?php echo $listingInfo->high_school; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->county) ){ ?>
		<tr><td class="title">County</td><td class="data"><?php echo $listingInfo->county; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->zip) ){ ?>
		<tr><td class="title">Zip Code</td><td class="data"><?php echo $listingInfo->zip; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->sub_area) ){ ?>
		<tr><td class="title">Sub-area</td><td class="data"><?php echo $listingInfo->sub_area; ?></td></tr>
		<?php } ?>
		<?php if( isset($listingInfo->subdivision) ){ ?>
		<tr><td class="title">Subdivision</td><td class="data"><?php echo $listingInfo->subdivision; ?></td></tr>
		<?php } ?>
		</tbody>
	</table>
</div>
</div>
<div class="col-md-7">
	<div id="listing-map">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe
				title="map location" 
                class="embed-responsive-item"  
				style="overflow= hidden; border: none; margin: 0;" 
                src="https://maps.google.com/maps?q=<?php echo urlencode($address); ?>&hl=es;z=14&amp;output=embed"
            ></iframe>
        </div>
    </div>
</div>
