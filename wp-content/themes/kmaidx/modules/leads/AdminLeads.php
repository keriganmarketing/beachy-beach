<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 10/5/2017
 * Time: 10:15 AM
 */

class AdminLeads
{
    public function __construct()
    {
        //listen for data save
        $this->save();

    }

    private function getUserRole($role){

        $userRole = get_role(strtolower(str_replace(' ', '_', $role)));
        //var_dump($userRole);

        return $userRole;
    }

    public function addUserRole($role, $capabilities = []){

        if ( ! $this->getUserRole($role)) { //role doesn't exist
            add_role(
                strtolower(str_replace(' ', '_', $role)),
                __($role), $capabilities
            );
        } else { //role exists. Just add capabilities
            $userRole = $this->getUserRole($role);
            foreach ($capabilities as $capability => $bool) {
                $userRole->add_cap($capability, $bool);
            }
//            echo '<pre>',print_r($userRole),'</pre>';
        }

    }

    public function createNavLabel()
    {

        add_action('admin_menu', function () {
            add_menu_page('My Beachy Buckets', 'My Beachy Buckets', 'edit_agent', 'bb-buckets', function () {
                $this->createMyBeachyBuckets();
            }, 'dashicons-palmtree', 6);
        });

        add_action('admin_menu', function () {
            add_menu_page('All Beachy Buckets', 'All Beachy Buckets', 'manage_options', 'bb-admin', function () {
                $this->createAllBeachyBuckets();
            }, 'dashicons-palmtree', 5);
        });

    }

    private function getAccessLevel($userId)
    {

        $userMeta     = get_user_meta($userId);
        $accessLevels = [unserialize($userMeta['wp_capabilities'][0])];

        echo '<pre>',print_r($accessLevels),'</pre>';

        $output = [];
        if (count($accessLevels[0]) > 0) {
            foreach ($accessLevels[0] as $level => $code) {
                array_push($output, $level);
            }
        }

        echo '<pre>',print_r($output),'</pre>';

        return $output;
    }

    private function getBuckets($agentName, $accessLevel = false)
    {
        $bb = new BeachyBucket();

        if ($accessLevel) {
            $userData = $bb->allBuckets($agentName);
        } else {
            $userData = $bb->clientBeachyBuckets($agentName);
        }

        return $userData;
    }

    private function createMyBeachyBuckets()
    {
        $userId    = get_current_user_id();
        $userMeta  = get_user_meta($userId);
        $agentName = $userMeta['first_name'][0] . ' ' . $userMeta['last_name'][0];
        $userData  = $this->getBuckets($agentName);
        $mlsLead   = new kmaLeads();

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline" style="margin-bottom: 1rem;">Clients and Leads assigned
                to <?php echo $agentName; ?></h1>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="email" class="manage-column column-address"><span>Physical Address</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                add_thickbox();
                $thickboxes = '';
                foreach ($userData as $user) {
                    //echo '<pre>',print_r($user),'</pre>';
                    $user['id']            = isset($user['id']) ? $user['id'] : 0;
                    $user['zip'][0]        = isset($user['zip'][0]) ? $user['zip'][0] : '';
                    $user['city'][0]       = isset($user['city'][0]) ? $user['city'][0] : '';
                    $user['addr1'][0]      = isset($user['addr1'][0]) ? $user['addr1'][0] : '';
                    $user['addr2'][0]      = isset($user['addr2'][0]) ? $user['addr2'][0] : '';
                    $user['phone1'][0]     = isset($user['phone1'][0]) ? $user['phone1'][0] : '';
                    $user['thestate'][0]   = isset($user['thestate'][0]) ? $user['thestate'][0] : '';
                    $user['last_name'][0]  = isset($user['last_name'][0]) ? $user['last_name'][0] : '';
                    $user['first_name'][0] = isset($user['first_name'][0]) ? $user['first_name'][0] : '';

                    $emails    = $mlsLead->getLeads([
                        'meta_key'   => 'lead_info_email_address',
                        'meta_value' => $user['email']
                    ]);

                    $address = '';
                    if ($user['addr1'][0] != '') {
                        $address = $user['addr1'][0] . ($user['addr2'][0] != '' ? ', ' . $user['addr2'][0] : '') . '<br>' . $user['city'][0] . ', ' . $user['thestate'][0] . $user['zip'][0];
                    }
                    ?>
                    <tr>
                        <td><strong><?php echo $user['first_name'][0] . ' ' . $user['last_name'][0]; ?></strong></td>
                        <td><strong><a href="tel:<?php echo $user['phone1'][0]; ?>"><?php echo $user['phone1'][0]; ?></a></strong></td>
                        <td><strong><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></strong></td>
                        <td><strong><?php echo $address; ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <a target="_blank" href="/beachy-bucket/?users_bucket=<?php echo $user['id']; ?>"
                               role="button" class="button button-primary" style="float: right">View
                                All <?php echo count($user['buckets']); ?> Properties</a>
                            <p>Saved Properties: &nbsp;
                                <?php
                                foreach ($user['buckets'] as $mlsNumber) {
                                    echo '<a href="/listing/?mls=' . $mlsNumber . '" target="_blank">' . $mlsNumber . '</a>, ';
                                }
                                ?>
                            </p>

                            <?php if(count($emails) > 0) { ?>
                                <p>Recent Website Communications:</p>
                                <?php
                                foreach ($emails as $email) {
                                    //echo '<pre>', print_r($email), '</pre>';

                                    echo $email->lead_info_date;
                                    echo '<a href="#TB_inline?width=680&height=500&inlineId=viewlead-' . $email->ID . '" role="button" data-toggle="modal" class="button button-info thickbox" >View lead</a>';

                                    $thickboxes .= '<div id="viewlead-' . $email->ID . '" class="modal hide fade" style="display:none; ">
                                        <div>'
                                        . $email->lead_info_notification_preview .
                                        '</div>
                                    </div>';

//                                    'Name' 			        => 'locked',
//                                    'Date' 			        => 'locked',
//                                    'Phone Number'	        => 'locked',
//                                    'Email Address'	        => 'locked',
//                                    'Selected Agent'        => 'locked',
//                                    'MLS Number' 		    => 'locked',
//                                    'Address'               => 'locked',
//                                    'Property Type'         => 'locked',
//                                    'Message' 		        => 'locked',
//                                    'Notification Preview'  => 'locked'
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php

        echo $thickboxes;

    }

    private function createAllBeachyBuckets()
    {

        $userId    = get_current_user_id();
        $userMeta  = get_user_meta($userId);
        $agentName = $userMeta['first_name'][0] . ' ' . $userMeta['last_name'][0];
        $userData  = $this->getBuckets($agentName, true);

        //FOR AGENT DROPDOWN
        $agents     = new mlsTeam();
        $agentArray = $agents->getAgentNames();

        add_thickbox();
        $thickboxes = '';

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline" style="margin-bottom: 1rem;">All Clients and Leads</h1>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="address" class="manage-column column-address"><span>Physical Address</span></th>
                    <th scope="col" id="agent" class="manage-column column-agent"><span>Assigned Agent</span></th>
                    <th scope="col" id="properties" class="manage-column column-properties"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($userData as $user) {
                    $user['zip'][0]            = isset($user['zip'][0]) ? $user['zip'][0] : '';
                    $user['city'][0]           = isset($user['city'][0]) ? $user['city'][0] : '';
                    $user['addr1'][0]          = isset($user['addr1'][0]) ? $user['addr1'][0] : '';
                    $user['addr2'][0]          = isset($user['addr2'][0]) ? $user['addr2'][0] : '';
                    $user['phone1'][0]         = isset($user['phone1'][0]) ? $user['phone1'][0] : '';
                    $user['thestate'][0]       = isset($user['thestate'][0]) ? $user['thestate'][0] : '';
                    $user['last_name'][0]      = isset($user['last_name'][0]) ? $user['last_name'][0] : '';
                    $user['first_name'][0]     = isset($user['first_name'][0]) ? $user['first_name'][0] : '';
                    $user['selected_agent'][0] = isset($user['selected_agent'][0]) ? $user['selected_agent'][0] : '';

                    $address = '';
                    if ($user['addr1'][0] != '') {
                        $address = $user['addr1'][0] . ($user['addr2'][0] != '' ? ', ' . $user['addr2'][0] : '') . '<br>' . $user['city'][0] . ', ' . $user['thestate'][0] . $user['zip'][0];
                    }

                    $properties = '';
                    foreach ($user['buckets'] as $mlsNumber) {
                        $properties .= $mlsNumber . ', ';
                    }

                    //SELECT OPTIONS
                    $agentOptions = '';
                    foreach ($agentArray as $agent) {
                        $agentOptions .= '<label style="padding: .5rem 1rem; display: block;"><input type="radio" name="agentassignment" value="' . $agent . '" ' . ($user['selected_agent'][0] == $agent ? 'checked' : '') . ' /> ' . $agent . '</label>';
                    }

                    if ($user['selected_agent'][0] == 'First Available') {
                        $changeButton = '<a title="Select an Agent for ' . $user['first_name'][0] . ' ' . $user['last_name'][0] . '" href="#TB_inline?width=300&height=500&inlineId=assignagent' . $user['first_name'][0] . $user['last_name'][0] . '" role="button" data-toggle="modal" class="button button-secondary thickbox" style="float: right; color: #FFF; background-color: darkred; box-shadow: inset 0 -2px 0 rgba(0,0,0,.3); border-color: rgba(0,0,0,.3);" >Assign Agent</a>';
                    } else {
                        $changeButton = '<a title="Select an Agent for ' . $user['first_name'][0] . ' ' . $user['last_name'][0] . '" href="#TB_inline?width=300&height=500&inlineId=assignagent' . $user['first_name'][0] . $user['last_name'][0] . '" role="button" data-toggle="modal" class="button button-info thickbox" style="float: right" >Change Agent</a>';
                    }

                    $thickboxes .= '<div id="assignagent' . $user['first_name'][0] . $user['last_name'][0] . '" class="modal hide fade" style="display:none; ">
                        <div>
                            <form class="form" id="agentselect" method="post" action="' . $_SERVER['REQUEST_URI'] . '" >
                                <input type="hidden" name="formID" value="agentselect" >
                                <input type="hidden" name="cid" value="' . $user['id'] . '" >
                                <input type="hidden" name="cname" value="' . $user['first_name'][0] . ' ' . $user['last_name'][0] . '" >
                                <input type="hidden" name="cphone" value="' . $user['phone1'][0] . '" >
                                <input type="hidden" name="cemail" value="' . $user['email'] . '" >
                                <input type="hidden" name="caddress" value="' . $address . '" >
                                <input type="hidden" name="cbuckets" value="' . $properties . '" >
                                ' . $agentOptions . '
                                <div class="stuck" style="position: absolute; top: 50px; right: 30px;">
                                <button style="padding: .5rem 1rem; height: auto; font-size: 1.2em;" class="button button-primary" >SAVE</button>
                                </div>
                            </form>
                            </div>
                        </div>';
                    ?>

                    <tr id="<?php echo $user['first_name'][0] . $user['last_name'][0]; ?>-form">
                        <td><strong><?php echo $user['first_name'][0] . ' ' . $user['last_name'][0]; ?></strong></td>
                        <td>
                            <strong><a href="tel:<?php echo $user['phone1'][0]; ?>"><?php echo $user['phone1'][0]; ?></a></strong>
                        </td>
                        <td>
                            <strong><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></strong>
                        </td>
                        <td><strong><?php echo $address; ?></strong>
                        </td>
                        <td><strong><?php echo $user['selected_agent'][0]; ?></strong> <?php echo $changeButton; ?></td>
                        <td align="center"><a href="/beachy-bucket/?users_bucket=<?php echo $user['id']; ?>"
                                              role="button" class="button button-primary" style="float: right"
                                              target="_blank"><?php echo count($user['buckets']); ?> Properties</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>
        <?php

        echo $thickboxes;
    }

    private function save()
    {
        $formSubmitted = isset($_POST['formID']) ? $_POST['formID'] : null;
        if ($formSubmitted == 'agentselect') {

            $mlsLead       = new kmaLeads();
            $mls           = new MLS();
            $agent         = new mlsTeam();
            $selectedAgent = $_POST['agentassignment'];

            $agentInfo    = $agent->getSingleAgent($selectedAgent);
            $agentMLSInfo = $mls->getAgentByName($agentInfo['name']);
            $ADMIN_EMAIL  = ($agentMLSInfo != false ? $agentMLSInfo->email : 'info@beachybeach.com');
            $ADMIN_EMAIL  = ($agentInfo['email'] != '' ? $agentInfo['email'] : $ADMIN_EMAIL);
            $leadFor = $selectedAgent;

            //BEGIN EMAIL
            $sendadmin = array(
                'to'		=> $ADMIN_EMAIL,
                'from'		=> get_bloginfo().' <noreply@beachybeach.com>',
                'subject'	=> 'A lead has been assigned to you',
                'bcc'		=> 'support@kerigan.com',
                'cc'        => 'lacey@beachybeach.com',
                'replyto'   => 'info@beachybeach.com'
            );

            $emailvars = array(
                'Name'              => $_POST['cname'],
                'Email Address'     => $_POST['cemail'],
                'Phone Number'      => $_POST['cphone'],
                'Physical Address'  => $_POST['caddress'],
                'Properties saved'  => $_POST['cbuckets']
            );

            $fontstyle          = 'font-family: sans-serif;';
            $headlinestyle      = 'style="font-size:20px; '.$fontstyle.' color:#42BC7B;"';
            $copystyle          = 'style="font-size:16px; '.$fontstyle.' color:#333;"';
            $labelstyle         = 'style="padding:4px 8px; background:#F7F6F3; border:1px solid #FFFFFF; font-weight:bold; '.$fontstyle.' font-size:14px; color:#4D4B47; width:150px;"';
            $datastyle          = 'style="padding:4px 8px; background:#F7F6F3; border:1px solid #FFFFFF; '.$fontstyle.' font-size:14px;"';

            $headline           = '<h2 '.$headlinestyle.'>You have a new lead</h2>';
            $adminintrocopy     = '<p '.$copystyle.'>You have been assigned a new lead. Details are below:</p>';
            $dateofemail        = '<p '.$copystyle.'>Date Submitted: '.date('M j, Y').' @ '.date('g:i a').'</p>';

            $submittedData = '<table cellpadding="0" cellspacing="0" border="0" style="width:100%" ><tbody>';
            foreach($emailvars as $key => $var ){
                if(!is_array($var)){
                    $submittedData .= '<tr><td '.$labelstyle.' >'.$key.'</td><td '.$datastyle.'>'.$var.'</td></tr>';
                }else{
                    $submittedData .= '<tr><td '.$labelstyle.' >'.$key.'</td><td '.$datastyle.'>';
                    foreach($var as $k => $v){
                        $submittedData .= '<span style="display:block;width:100%;">'.$v.'</span><br>';
                    }
                    $submittedData .= '</ul></td></tr>';
                }
            }
            $submittedData .= '</tbody></table>';

            $adminContent = $adminintrocopy.$submittedData.$dateofemail;

            $emaildata = array(
                'headline'	=> $headline,
                'introcopy'	=> $adminContent,
            );

            $mlsLead->sendEmail( $sendadmin, $emaildata );

            update_user_meta($_POST['cid'], 'selected_agent', $selectedAgent);

        }
    }
}
