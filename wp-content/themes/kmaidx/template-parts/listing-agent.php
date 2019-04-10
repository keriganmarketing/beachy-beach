<?php
$phone = (isset($agentData['cell_phone']) ? $agentData['cell_phone'] : (isset($agentData['office_phone']) ? $agentData['office_phone'] : ''));
?>
<div class="card">
    <img class="card-img-top" src="<?php echo $agentData['thumbnail']; ?>" alt="<?php echo $agentData['name']; ?>">
    <div class="card-block">
        <h4 class="card-title"><?php echo $agentData['name']; ?></h4>
        <ul class="contact-info">
            <?php if($agentData['email_address'] != ''){?><li class="email"><img src="<?php echo getSvg('email'); ?>" alt="Email <?php echo $agentData['name'];?>" > <a href="mailto:<?php echo $agentData['email_address']; ?>" ><?php echo $agentData['email_address']; ?></a></li><?php } ?>
            <?php if($phone != ''){?><li class="phone"><img src="<?php echo getSvg('phone'); ?>" alt="Call <?php echo $agentData['name'];?>" > <a href="tel:<?php echo $phone; ?>" ><?php echo $phone; ?></a></li><?php }else{ '<li></li>'; } ?>
        </ul>
        <a href="<?php echo $agentData['link']; ?>" class="btn btn-primary">view profile</a>
    </div>
</div>