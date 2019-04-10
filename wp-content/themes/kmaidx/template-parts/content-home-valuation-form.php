<?php

use Includes\Modules\Agents\Agents;
use Includes\Modules\Leads\HomeValuation;

//DEFAULT FORM VARS
$yourname            = (isset($_GET['full_name']) ? $_GET['full_name'] : '');
$youremail           = (isset($_GET['email_address']) ? $_GET['email_address'] : '');
$phone               = (isset($_GET['phone_number']) ? $_GET['phone_number'] : '');
$reason              = (isset($_GET['reason_for_contact']) ? $_GET['reason_for_contact'] : '');
$mlsnumber           = (isset($_GET['mls_number']) ? $_GET['mls_number'] : '');
$agentOptions        = '';
$listing_state       = '';

$sessionAgent = (isset($_SESSION['agent_override']) ? $_SESSION['agent_override'] : null);
$overrideFields = (isset($sessionAgent) && $sessionAgent != '' ? true : false);

//IS USER LOGGED IN?
$currentUser     = get_user_meta(get_current_user_id());
$currentUserInfo = get_userdata(get_current_user_id());
$yourname        = ($currentUser['first_name'][0] != '' ? $currentUser['first_name'][0] : $yourname);
$yourname        = ($currentUser['last_name'][0] != '' ? $yourname . ' ' . $currentUser['last_name'][0] : $yourname);
$youremail       = (isset($currentUserInfo->user_email) ? $currentUserInfo->user_email : $youremail);
$phone           = (isset($currentUser['phone1'][0]) ? $currentUser['phone1'][0] : $phone);

$selectedAgent = (isset($currentUser['selected_agent'][0]) ? $currentUser['selected_agent'][0] : null); //get agent from user data.
$selectedAgent = (isset($_GET['selected_agent']) ? $_GET['selected_agent'] : $selectedAgent ); //IF GET, then override.
$selectedAgent = (isset($sessionAgent) && $sessionAgent != '' ? $sessionAgent : $selectedAgent);
$selectedAgent = (isset($_GET['selected_agent']) && isset($_GET['reason']) && $_GET['reason'] == 'Just reaching out' ? $_GET['selected_agent'] : $selectedAgent ); //IF GET and from team, then override.

//SELECT OPTIONS
$agents     = new Agents();
$agentArray = $agents->getAgentNames();
foreach($agentArray as $agent){
    $agentOptions .= '<option value="'.$agent.'" '.($selectedAgent == $agent ? 'selected' : '').' '.($overrideFields && $selectedAgent != $agent ? 'disabled' : '') .'>'.$agent.'</option>';
}

$formID                 = (isset($_POST['formID']) ? $_POST['formID'] : '');
$securityFlag           = (isset($_POST['secu']) ? $_POST['secu'] : '');
$formSubmitted          = ($formID == 'homevaluation' && $securityFlag == '' ? TRUE : FALSE);

if( $formSubmitted ){ //FORM WAS SUBMITTED

    $leads = new HomeValuation();
    $leads->handleLead($_POST);

}
?>
<a id="homeval" class="pad-anchor"></a>
<form class="form homevalform" enctype="multipart/form-data" method="post" action="#homeval" id="homeval">
	<input type="hidden" name="formID" value="homevaluation" >
    <h3>Contact Information</h3>
    <div class="row" style="width:100%">
        <div class="col-md-6 col-lg-4">
            <label for="full_name" class="sr-only">Name *</label>
            <div class="input-group mb-2">
                <input name="full_name" type="text" id="your_name" class="textbox form-control <?php echo ( $yourname && $formSubmitted ? 'has-error' : ''); ?>" value="<?php echo ($yourname != '' ? $yourname : ''); ?>" required placeholder="Name *">
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <label for="email_address" class="sr-only">Email Address *</label>
            <div class="input-group mb-2">
                <input name="email_address" type="text" id="your_email" class="textbox form-control <?php echo( $youremail=='' && $formSubmitted ? 'has-error' : ''); ?>" value="<?php echo ($youremail != '' ? $youremail : ''); ?>" required placeholder="Email Address *">
            </div>
        </div>
        <div class="col-lg-4">
            <label for="phone" class="sr-only">Phone Number *</label>
            <div class="input-group mb-2">
                <input name="phone_number" type="text" id="phone" class="textbox form-control <?php echo ( $phone && $formSubmitted ? 'has-error' : ''); ?>" value="<?php echo ($phone != '' ? $phone : ''); ?>" placeholder="Phone Number *">
            </div>
        </div>
        <div class="col-12 mt-4">
            <label for="who" >Select an area office or specific agent.</label>
        </div>
        <div class="custom-controls-inline col-12">

            <label class="custom-control custom-radio mt-2 mb-2">
                <input id="radioStacked1" name="lead_for" type="radio" class="custom-control-input" onclick="toggleSelect();" value="pcb" <?= ($overrideFields ? 'disabled' : ''); ?>>
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Beachy Beach Real Estate</span>
            </label>
            <label class="custom-control custom-radio">
                <input id="radioStacked2" name="lead_for" type="radio" class="custom-control-input" onclick="toggleSelect();" value="30a" <?= ($overrideFields ? 'disabled' : ''); ?>>
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Beachy Beach 30A Real Estate</span>
            </label>
            <label class="custom-control custom-radio">
                <input id="select-an-agent" name="lead_for" type="radio" class="custom-control-input" onclick="toggleSelect();" value="specific" <?php echo ($selectedAgent!='' ? 'checked' : ''); ?> >
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Select an agent</span>
            </label>

            <div class="form-group <?php echo ( $selectedAgent=='' && $formSubmitted ? 'has-error' : ''); ?>" id="agent-select-dd" style="display: none; margin:0;">
                <label for="selected_agent" class="sr-only">Your Agent</label>
                <select class="form-control" name="selected_agent" required>
			        <?php echo $agentOptions; ?>
                </select>
            </div>

        </div>

    </div>
    <div class="spacer"></div>
    <h3>Property Information</h3>
    <div class="row" style="width:100%">
        <div class="col-md-8">
            <label for="listing_address" class="sr-only">Listing Address *</label>
            <div class="input-group mb-2">
                <input name="listing_address" type="text" id="listing_address" value="" class="textbox form-control" required placeholder="Listing Address *">
            </div>
        </div>
        <div class="col-md-4">
            <label for="listing_address_2" class="sr-only">Apt/Suite *</label>
            <div class="input-group mb-2">
                <input name="listing_address_2" type="text" id="listing_address_2" class="textbox form-control" value="" placeholder="Apt/Suite">
            </div>
        </div>
        <div class="col-md-5">
            <label for="listing_city" class="sr-only">City *</label>
            <div class="input-group mb-2">
                <input name="listing_city" type="text" id="listing_city" class="textbox form-control" value="" required placeholder="City *">
            </div>
        </div>
        <div class="col-md-4">
            <label for="listing_state" class="sr-only">State *</label>
            <div class="input-group mb-2">
                <select class="form-control" required name="listing_state">
                    <option value="AL" <?php if($listing_state == 'AL'){ echo 'selected'; } ?> >Alabama</option>
                    <option value="AK" <?php if($listing_state == 'AK'){ echo 'selected'; } ?> >Alaska</option>
                    <option value="AZ" <?php if($listing_state == 'AZ'){ echo 'selected'; } ?> >Arizona</option>
                    <option value="AR" <?php if($listing_state == 'AR'){ echo 'selected'; } ?> >Arkansas</option>
                    <option value="CA" <?php if($listing_state == 'CA'){ echo 'selected'; } ?> >California</option>
                    <option value="CO" <?php if($listing_state == 'CO'){ echo 'selected'; } ?> >Colorado</option>
                    <option value="CT" <?php if($listing_state == 'CT'){ echo 'selected'; } ?> >Connecticut</option>
                    <option value="DE" <?php if($listing_state == 'DE'){ echo 'selected'; } ?> >Delaware</option>
                    <option value="FL" <?php if($listing_state == 'FL' || $listing_state == ''){ echo 'selected'; } ?> >Florida</option>
                    <option value="GA" <?php if($listing_state == 'GA'){ echo 'selected'; } ?> >Georgia</option>
                    <option value="HI" <?php if($listing_state == 'HI'){ echo 'selected'; } ?> >Hawaii</option>
                    <option value="ID" <?php if($listing_state == 'ID'){ echo 'selected'; } ?> >Idaho</option>
                    <option value="IL" <?php if($listing_state == 'IL'){ echo 'selected'; } ?> >Illinois</option>
                    <option value="IN" <?php if($listing_state == 'IN'){ echo 'selected'; } ?> >Indiana</option>
                    <option value="IA" <?php if($listing_state == 'IA'){ echo 'selected'; } ?> >Iowa</option>
                    <option value="KS" <?php if($listing_state == 'KS'){ echo 'selected'; } ?> >Kansas</option>
                    <option value="KY" <?php if($listing_state == 'KY'){ echo 'selected'; } ?> >Kentucky</option>
                    <option value="LA" <?php if($listing_state == 'LA'){ echo 'selected'; } ?> >Louisiana</option>
                    <option value="ME" <?php if($listing_state == 'ME'){ echo 'selected'; } ?> >Maine</option>
                    <option value="MD" <?php if($listing_state == 'MD'){ echo 'selected'; } ?> >Maryland</option>
                    <option value="MA" <?php if($listing_state == 'MA'){ echo 'selected'; } ?> >Massachusetts</option>
                    <option value="MI" <?php if($listing_state == 'MI'){ echo 'selected'; } ?> >Michigan</option>
                    <option value="MN" <?php if($listing_state == 'MN'){ echo 'selected'; } ?> >Minnesota</option>
                    <option value="MS" <?php if($listing_state == 'MS'){ echo 'selected'; } ?> >Mississippi</option>
                    <option value="MO" <?php if($listing_state == 'MO'){ echo 'selected'; } ?> >Missouri</option>
                    <option value="MT" <?php if($listing_state == 'MT'){ echo 'selected'; } ?> >Montana</option>
                    <option value="NE" <?php if($listing_state == 'NE'){ echo 'selected'; } ?> >Nebraska</option>
                    <option value="NV" <?php if($listing_state == 'NV'){ echo 'selected'; } ?> >Nevada</option>
                    <option value="NH" <?php if($listing_state == 'NH'){ echo 'selected'; } ?> >New Hampshire</option>
                    <option value="NJ" <?php if($listing_state == 'NJ'){ echo 'selected'; } ?> >New Jersey</option>
                    <option value="NM" <?php if($listing_state == 'NM'){ echo 'selected'; } ?> >New Mexico</option>
                    <option value="NY" <?php if($listing_state == 'NY'){ echo 'selected'; } ?> >New York</option>
                    <option value="NC" <?php if($listing_state == 'NC'){ echo 'selected'; } ?> >North Carolina</option>
                    <option value="ND" <?php if($listing_state == 'ND'){ echo 'selected'; } ?> >North Dakota</option>
                    <option value="OH" <?php if($listing_state == 'OH'){ echo 'selected'; } ?> >Ohio</option>
                    <option value="OK" <?php if($listing_state == 'OK'){ echo 'selected'; } ?> >Oklahoma</option>
                    <option value="OR" <?php if($listing_state == 'OR'){ echo 'selected'; } ?> >Oregon</option>
                    <option value="PA" <?php if($listing_state == 'PA'){ echo 'selected'; } ?> >Pennsylvania</option>
                    <option value="RI" <?php if($listing_state == 'RI'){ echo 'selected'; } ?> >Rhode Island</option>
                    <option value="SC" <?php if($listing_state == 'SC'){ echo 'selected'; } ?> >South Carolina</option>
                    <option value="SD" <?php if($listing_state == 'SD'){ echo 'selected'; } ?> >South Dakota</option>
                    <option value="TN" <?php if($listing_state == 'TN'){ echo 'selected'; } ?> >Tennessee</option>
                    <option value="TX" <?php if($listing_state == 'TX'){ echo 'selected'; } ?> >Texas</option>
                    <option value="UT" <?php if($listing_state == 'UT'){ echo 'selected'; } ?> >Utah</option>
                    <option value="VT" <?php if($listing_state == 'VT'){ echo 'selected'; } ?> >Vermont</option>
                    <option value="VA" <?php if($listing_state == 'VA'){ echo 'selected'; } ?> >Virginia</option>
                    <option value="WA" <?php if($listing_state == 'WA'){ echo 'selected'; } ?> >Washington</option>
                    <option value="WV" <?php if($listing_state == 'WV'){ echo 'selected'; } ?> >West Virginia</option>
                    <option value="WI" <?php if($listing_state == 'WI'){ echo 'selected'; } ?> >Wisconsin</option>
                    <option value="WY" <?php if($listing_state == 'WY'){ echo 'selected'; } ?> >Wyoming</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <label for="listing_zip" class="sr-only">Zip *</label>
            <div class="input-group mb-2">
                <input name="listing_zip" type="text" id="listing_zip" class="textbox form-control" value="" required placeholder="Zip *">
            </div>
        </div>
        <div class="col-12">
            <label for="listing_property_type" class="sr-only">Property Type *</label>
            <div class="input-group form-check form-check-inline mt-2 mb-2">
                <label class="custom-control custom-radio">
                    <input type="radio" name="property_type" value="Single Family Home" class="custom-control-input" >
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Single Family Home</span>
                </label>
                <label class="custom-control custom-radio">
                    <input type="radio" name="property_type" value="Commercial" class="custom-control-input" >
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Commercial</span>
                </label>
                <label class="custom-control custom-radio">
                    <input type="radio" name="property_type" value="Condo/Townhome" class="custom-control-input" >
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Condo/Townhome</span>
                </label>
                <label class="custom-control custom-radio">
                    <input type="radio" name="property_type" value="Lot/Land" class="custom-control-input" >
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Lot/Land</span>
                </label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label for="property_details" class="sr-only">Property Details</label>
                <textarea name="property_details" rows="4" class="form-control" placeholder="Property Details" style="height: 130px;"></textarea>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="secu" style="position: absolute; height: 1px; top: -50px; left: -50px; width: 1px; padding: 0; margin: 0; visibility: hidden;" >
                <button type="submit" class="btn btn-primary btn-md mb-2" >Submit Valuation Request</button>
            </div>
        </div>
    </div>
</form>