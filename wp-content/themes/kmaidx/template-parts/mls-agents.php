<?php

foreach($team as $agent){

    $agentData = $wpTeam->assembleAgentData( $agent['mls_name'] );
    //$wpTeam->updateAgent($agentData);

    $phone = (isset($agentData['cell_phone']) ? $agentData['cell_phone'] : (isset($agentData['office_phone']) ? $agentData['office_phone'] : ''));

    $agentCategories = '';
	$is30a = false;
    foreach($agentData['categories'] as $category){
        if($category['category-slug'] == '30a-office' || $category['category-slug'] == 'seacrest-office' || $category['category-slug'] == 'destin-office'){
            $is30a = true;
        }
        $agentCategories .= ' '.$category['category-slug'].'-filter';
    }


    $company = ( $is30a ? 'Beachy Beach 30a Real Estate' : 'Beachy Beach Real Estate' );

    if($agentData['name'] == 'Karen Smith'){
	    $company = 'Beachy Beach Real Estate <br> Beachy Beach 30a Real Estate';
    }

    ?>
    <div class="agent-card col-sm-6 col-lg-4 col-xl-3 mb-5<?php echo $agentCategories; ?> all-filter" >
        <div class="card" >
            <div class="card-image">
                <img class="card-img-top" src="<?php echo ($agentData['thumbnail'] != '' ? $agentData['thumbnail'] : get_template_directory_uri().'/img/beachybeach-placeholder.jpg' ); ?>" alt="<?php echo $agentData['name']; ?>" >
            </div>
            <div class="card-block">
                <div class="agent-info">
                    <h4 class="card-title"><?php echo $agentData['name']; ?></h4>
                    <h5 class="card-subtitle"><?php echo ($agentData['title'] != '' ? $agentData['title'] : 'Realtor' ); ?></h5>
                    <h5 class="card-subtitle company"><?php echo $company; ?></h5>
                    <ul class="contact-info">
                        <?php if($agentData['email_address'] != ''){?><li class="email"><img src="<?php echo getSvg('email'); ?>" alt="Email <?php echo $agentData['name'];?>" > <a href="mailto:<?php echo $agentData['email_address']; ?>" ><?php echo $agentData['email_address']; ?></a></li><?php } ?>
                        <?php if($phone != ''){?><li class="phone"><img src="<?php echo getSvg('phone'); ?>" alt="Call <?php echo $agentData['name'];?>" > <a href="tel:<?php echo $phone; ?>" ><?php echo $phone; ?></a></li><?php }else{ '<li></li>'; } ?>
                    </ul>
                </div>
                <div class="agent-actions">
                    <form class="form form-inline" action="/contact/" method="get" style="display:inline-block;" >
                        <input type="hidden" name="reason" value="Just reaching out" />
                        <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>" />
                        <input type="hidden" name="selected_agent" value="<?php echo $agentData['name']; ?>" />
                        <button type="submit" class="btn btn-primary" >Contact Me</button>
                    </form>
                    <a href="<?php echo $agentData['link']; ?>" class="btn btn-primary">view profile</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>