<?php
use Includes\Modules\MLS\Offices;

$offices = new Offices();
$locationlist = $offices->getAllOffices();

foreach($locationlist as $location){ ?>
	<div class="col-md-6 col-lg-4 col-xl-2">
		<div class="location text-center">
			<h3 class="location-title"><?php echo $location['name']; ?></h3>
			<div class="location-text">
				<p><strong><?php echo $location['address']; ?></strong><br>
					<em>office:</em>&nbsp;&nbsp; <?php echo $location['phone']; ?><br>
					<em>fax:</em>&nbsp;&nbsp; <?php echo $location['fax']; ?><br>
					<em>email:</em>&nbsp;&nbsp; <a href="mailto:<?php echo $location['email']; ?>"><?php echo $location['title']; ?></a></p>
			</div>
		</div>
	</div>
<?php } ?>