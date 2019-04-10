<?php
if(count($openHouses)>0){
?>
<h2>Upcoming Open Houses</h2>
<div class="card-columns">
    <?php foreach($openHouses as $openHouse){
        $addressString = urlencode($openHouse->street_address.' '.$openHouse->city.', '.$openHouse->state); ?>
        <div class="card" style="border-bottom:1px solid #ddd;">
            <div class="card-block">
                <p class="card-text"><strong>Date:</strong> <?php echo date('M j, Y', strtotime($openHouse->event_start)); ?><br>
                <strong>Time:</strong> <?php echo date('h:s a', strtotime($openHouse->event_start)); ?> - <?php echo date('h:s a', strtotime($openHouse->event_end)); ?></p>
                <a target="_blank" href="https://www.google.com/maps/place/<?php echo $addressString; ?>" class="btn btn-default btn-sm card-link">Get Directions</a>
            </div>
        </div>
    <?php } ?>
</div>
<?php } ?>