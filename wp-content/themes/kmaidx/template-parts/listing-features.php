<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 5/22/2017
 * Time: 2:08 PM
 */
$extfeatures = explode(',',$listingInfo->exterior);
$amenities = explode(',',$listingInfo->amenities);
$intfeatures = explode(',',$listingInfo->interior);
$appliances = explode(',',$listingInfo->appliances);
$energy = explode(',',$listingInfo->energy_features);
$construction = explode(',',$listingInfo->construction);
$utilities = explode(',',$listingInfo->utilities);
?>
<div class="card-columns">
    <?php if (count($intfeatures) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Interior</strong>
            </div>
            <div class="card-block">
                <ul>
                <?php foreach ($intfeatures as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (count($appliances) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Appliances</strong>
            </div>
            <div class="card-block">
                <ul>
                <?php foreach ($appliances as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (count($extfeatures) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Exterior</strong>
            </div>
            <div class="card-block">
                <ul>
                <?php foreach ($extfeatures as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (count($amenities) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Amenities</strong>
            </div>
            <div class="card-block">
                <ul>
                <?php foreach ($amenities as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (count($energy) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Energy</strong>
            </div>
            <div class="card-block">
                <ul>
                <?php foreach ($energy as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (count($construction) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Construction</strong>
            </div>
            <div class="card-block">
            <ul>
                <?php foreach ($construction as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (count($utilities) > 1) { ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-header">
                <strong>Utilities</strong>
            </div>
            <div class="card-block">
            <ul>
                <?php foreach ($utilities as $feat) { ?>
                    <li><?php echo $feat; ?></li>
                <?php } ?>
            </ul>
            </div>
        </div>
    <?php } ?>
</div>