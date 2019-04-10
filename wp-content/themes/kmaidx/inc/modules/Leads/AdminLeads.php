<?php
namespace Includes\Modules\Leads;

use Includes\Modules\MLS\BeachyBucket;
use Includes\Modules\Agents\Agents;
use Includes\Modules\Leads\RequestInfo;
use Includes\Modules\Leads\HomeValuation;

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
        $mlsLead   = new Leads();

        $leads = new RequestInfo();
        $infoRequests = $leads->getLeads([
            'meta_key'   => 'assigned_agent',
            'meta_value' => $agentName
        ]);

        $homeValuations = new HomeValuation();
        $valuations = $homeValuations->getLeads([
            'meta_key'   => 'assigned_agent',
            'meta_value' => $agentName
        ]);

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline" style="margin-bottom: .5rem;"><?php echo $agentName; ?>'s Lead Dashboard</h1>
            <h2 id="accounts" class="wp-heading-inline" style="margin-bottom: 1rem;">
                Go to: <strong>Client accounts (<?php echo count($userData); ?>)</strong> | <a href="#info-requests">Info requests (<?php echo count($infoRequests); ?>)</a> |  <a href="#home-valuations">Home valuations (<?php echo count($valuations); ?>)</a>
            </h2>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="accounts-title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="accounts-phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="accounts-email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="accounts-address" class="manage-column column-address"><span>Physical Address</span></th>
                    <th scope="col" id="accounts-date" class="manage-column column-date"><span>Last Logged In</span></th>
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
                        <td><?php echo date('j/n/y h:i a', strtotime($user['registered'])); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">
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

<!--                            --><?php //if(count($emails) > 0) { ?>
<!--                                <p>Recent Website Communications:</p>-->
<!--                                --><?php
//                                foreach ($emails as $email) {
//                                    //echo '<pre>', print_r($email), '</pre>';
//
//                                    echo $email['object']->lead_info_date;
//                                    echo '<a href="#TB_inline?width=680&height=500&inlineId=viewlead-' . $email['object']->ID . '" role="button" data-toggle="modal" class="button button-info thickbox" >View lead</a>';
//
//                                    $thickboxes .= '<div id="viewlead-' . $email['object']->ID . '" class="modal hide fade" style="display:none; ">
//                                        <div>'
//                                        . $email['object']->lead_info_notification_preview.
//                                        '</div>
//                                    </div>';
//
//                                }
//                            }
//                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <h2 id="info-requests" class="wp-heading-inline" style="margin-bottom: 1rem;">
                Go to: <a href="#accounts">Client accounts</a> | <strong>Info requests</strong> |  <a href="#home-valuations">Home valuations</a>
            </h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="info-title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="info-phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="info-email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="info-mlsnumber" class="manage-column column-email"><span>MLS Number</span></th>
                    <th scope="col" style="width:40%" id="info-agent" class="manage-column column-agent"><span>Message</span></th>
                    <th scope="col" id="info-date" class="manage-column column-date"><span>Date Submitted</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($infoRequests as $lead){ ?>
                    <tr>
                        <td><?php echo $lead['object']->post_title; ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_phone_number'][0]) ? $lead['meta']['lead_info_phone_number'][0] : ''); ?></td>
                        <td><?php echo $lead['meta']['lead_info_email_address'][0]; ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_mls_number'][0]) ? $lead['meta']['lead_info_mls_number'][0] : ''); ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_message'][0]) ? $lead['meta']['lead_info_message'][0] : ''); ?></td>
                        <td><?php echo (isset($lead['object']->post_date) ? date('j/n/y h:i a', strtotime($lead['object']->post_date)) : ''); ?></td>
                    </tr>
                <?php }
                ?>
                </tbody>
            </table>

            <h2 id="home-valuations" class="wp-heading-inline" style="margin-bottom: 1rem;">
                Go to: <a href="#accounts">Client accounts</a> | <a href="#info-requests">Info requests</a> |  <strong>Home valuations</strong>
            </h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="homeval-title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="homeval-phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="homeval-email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="homeval-type" class="manage-column column-email"><span>Property Type</span></th>
                    <th scope="col" id="homeval-address" class="manage-column column-email"><span>Property Address</span></th>
                    <th style="width:40%" scope="col" id="homeval-agent" class="manage-column column-details"><span>Property Details</span></th>
                    <th scope="col" id="homeval-date" class="manage-column column-date"><span>Date Submitted</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($valuations as $lead){ ?>
                    <tr>
                        <td><?php echo $lead['object']->post_title; ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_phone_number'][0]) ? $lead['meta']['lead_info_phone_number'][0] : ''); ?></td>
                        <td><?php echo $lead['meta']['lead_info_email_address'][0]; ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_listing_property_type'][0]) ? $lead['meta']['lead_info_listing_property_type'][0] : ''); ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_property_address'][0]) ? $lead['meta']['lead_info_property_address'][0] : ''); ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_property_details'][0]) ? $lead['meta']['lead_info_property_details'][0] : ''); ?></td>
                        <td><?php echo (isset($lead['object']->post_date) ? date('j/n/y h:i a', strtotime($lead['object']->post_date)) : ''); ?></td>
                    </tr>
                <?php }
                ?>
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
        $agents     = new Agents();
        $agentArray = $agents->getAgentNames();

        $leads = new RequestInfo();
        $infoRequests = $leads->getLeads();

        $homeValuations = new HomeValuation();
        $valuations = $homeValuations->getLeads();

        add_thickbox();
        $thickboxes = '';

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline" style="margin-bottom: .5rem;">Lead Dashboard</h1>
            <h2 id="accounts" class="wp-heading-inline" style="margin-bottom: 1rem;">
                Go to: <strong>Client accounts (<?php echo count($userData); ?>)</strong> | <a href="#info-requests">Info requests (<?php echo count($infoRequests); ?>)</a> |  <a href="#home-valuations">Home valuations (<?php echo count($valuations); ?>)</a>
            </h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="account-title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="account-phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="account-email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="account-address" class="manage-column column-address"><span>Physical Address</span></th>
                    <th scope="col" id="account-agent" class="manage-column column-agent" style="width:240px;"><span>Assigned Agent</span></th>
                    <th scope="col" id="account-properties" class="manage-column column-properties" style="width:110px;">Beachy Bucket</th>
                    <th scope="col" id="account-date" class="manage-column column-date"><span>Last Logged In</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($userData as $user) {
                    //echo '<pre>',print_r($user),'</pre>';
                    if($user['id']!=0){
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
                        <td><a href="/beachy-bucket/?users_bucket=<?php echo $user['id']; ?>"
                                              role="button" class="button button-primary"
                                              target="_blank"><?php echo count($user['buckets']); ?> Properties</a></td>
                        <td><?php echo date('j/n/y h:i a', strtotime($user['registered'])); ?></td>
                    </tr>
                <?php }} ?>
                </tbody>
            </table>

            <h2 id="info-requests" class="wp-heading-inline" style="margin-bottom: 1rem;">
                Go to: <a href="#accounts">Client accounts</a> | <strong>Info requests</strong> |  <a href="#home-valuations">Home valuations</a>
            </h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="info-title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="info-phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="info-email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="info-mlsnumber" class="manage-column column-email"><span>MLS Number</span></th>
                    <th scope="col" id="info-company" class="manage-column column-agent"><span>Contacted Agent/Company</span></th>
                    <th scope="col" id="info-agent" class="manage-column column-agent"><span>Assigned Agent</span></th>
                    <th scope="col" id="info-date" class="manage-column column-date"><span>Date Submitted</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach($infoRequests as $lead){
                        //echo '<pre>',print_r($lead),'</pre>';
                        $leadfor = ($lead['meta']['lead_info_selected_agent'][0]!='' ? $lead['meta']['lead_info_selected_agent'][0] :
                            (isset($lead['meta']['lead_info_lead_for'][0]) ? $lead['meta']['lead_info_lead_for'][0] : '' ));

                        $assignedAgent = (!isset($lead['meta']['assigned_agent'][0]) ? 'NONE' : $lead['meta']['assigned_agent'][0] );

                        if ($assignedAgent == 'NONE') {
                            $changeButton = '<a title="Select an Agent for ' . $lead['object']->post_title . '" href="#TB_inline?width=300&height=500&inlineId=assignagent' . $lead['object']->ID . '" role="button" data-toggle="modal" class="button button-secondary thickbox" style="float: right; color: #FFF; background-color: darkred; box-shadow: inset 0 -2px 0 rgba(0,0,0,.3); border-color: rgba(0,0,0,.3);" >Assign Agent</a>';
                            if($leadfor!='') {
                                update_post_meta($lead['object']->ID, 'assigned_agent', $leadfor);
                            }
                        } else {
                            $changeButton = '<a title="Select an Agent for ' . $lead['object']->post_title . '" href="#TB_inline?width=300&height=500&inlineId=assignagent' . $lead['object']->ID . '" role="button" data-toggle="modal" class="button button-info thickbox" style="float: right" >Change Agent</a>';
                        }

                        $agentOptions = '';
                        foreach ($agentArray as $agent) {
                            $agentOptions .= '<label style="padding: .5rem 1rem; display: block;"><input type="radio" name="agentassignment" value="' . $agent . '" ' . ($assignedAgent == $agent ? 'checked' : '') . ' /> ' . $agent . '</label>';
                        }

                        $thickboxes .= '<div id="assignagent' . $lead['object']->ID . '" class="modal hide fade" style="display:none; ">
                        <div>
                            <form class="form" id="agentselect" method="post" action="' . $_SERVER['REQUEST_URI'] . '" >
                                <input type="hidden" name="formID" value="agentselect" >
                                <input type="hidden" name="lead_id" value="' . $lead['object']->ID . '" >
                                <input type="hidden" name="cname" value="' . $lead['object']->post_title . '" >
                                <input type="hidden" name="cphone" value="' . (isset($lead['meta']['lead_info_phone_number'][0]) ? $lead['meta']['lead_info_phone_number'][0] : '') . '" >
                                <input type="hidden" name="cemail" value="' . $lead['meta']['lead_info_email_address'][0] . '" >
                                <input type="hidden" name="message" value="' . (isset($lead['meta']['lead_info_message'][0]) ? $lead['meta']['lead_info_message'][0] : '') . '" >
                                <input type="hidden" name="mls_number" value="' . (isset($lead['meta']['lead_info_mls_number'][0]) ? $lead['meta']['lead_info_mls_number'][0] : '') . '" >
                                ' . $agentOptions . '
                                <div class="stuck" style="position: absolute; top: 50px; right: 30px;">
                                <button style="padding: .5rem 1rem; height: auto; font-size: 1.2em;" class="button button-primary" >SAVE</button>
                                </div>
                            </form>
                            </div>
                        </div>';

                        ?>
                        <tr>
                            <td><?php echo $lead['object']->post_title; ?></td>
                            <td><?php echo (isset($lead['meta']['lead_info_phone_number'][0]) ? $lead['meta']['lead_info_phone_number'][0] : ''); ?></td>
                            <td><?php echo $lead['meta']['lead_info_email_address'][0]; ?></td>
                            <td><?php echo (isset($lead['meta']['lead_info_mls_number'][0]) ? $lead['meta']['lead_info_mls_number'][0] : ''); ?></td>
                            <td><?php echo $leadfor; ?></td>
                            <td><strong><?php echo $assignedAgent; ?></strong> <?php echo $changeButton; ?></td>
                            <td><?php echo (isset($lead['object']->post_date) ? date('j/n/y h:i a', strtotime($lead['object']->post_date)) : ''); ?></td>
                        </tr>
                    <?php }
                ?>
                </tbody>
            </table>

            <h2 id="home-valuations" class="wp-heading-inline" style="margin-bottom: 1rem;">
                Go to: <a href="#accounts">Client accounts</a> | <a href="#info-requests">Info requests</a> |  <strong>Home valuations</strong>
            </h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" id="homeval-title" class="manage-column column-name column-primary sortable desc"><a
                                href="?page=bb-admin&amp;orderby=name&amp;order=asc"><span>Name</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="homeval-phone" class="manage-column column-phone"><span>Phone Number</span></th>
                    <th scope="col" id="homeval-email" class="manage-column column-email"><span>Email Address</span></th>
                    <th scope="col" id="homeval-type" class="manage-column column-email"><span>Property Type</span></th>
                    <th scope="col" id="homeval-address" class="manage-column column-email"><span>Property Address</span></th>
                    <th scope="col" id="homeval-company" class="manage-column column-agent"><span>Contacted Agent/Company</span></th>
                    <th scope="col" id="homeval-agent" class="manage-column column-agent"><span>Assigned Agent</span></th>
                    <th scope="col" id="homeval-date" class="manage-column column-date"><span>Date Submitted</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($valuations as $lead){
                    //echo '<pre>',print_r($lead),'</pre>';
                    $leadfor = ($lead['meta']['lead_info_selected_agent'][0]!='' ? $lead['meta']['lead_info_selected_agent'][0] :
                        (isset($lead['meta']['lead_info_lead_for'][0]) ? $lead['meta']['lead_info_lead_for'][0] : '' ));

                    $assignedAgent = (!isset($lead['meta']['assigned_agent'][0]) ? 'NONE' : $lead['meta']['assigned_agent'][0] );

                    if ($assignedAgent == 'NONE') {
                        $changeButton = '<a title="Select an Agent for ' . $lead['object']->post_title . '" href="#TB_inline?width=300&height=500&inlineId=assignagent' . $lead['object']->ID . '" role="button" data-toggle="modal" class="button button-secondary thickbox" style="float: right; color: #FFF; background-color: darkred; box-shadow: inset 0 -2px 0 rgba(0,0,0,.3); border-color: rgba(0,0,0,.3);" >Assign Agent</a>';
                        if($leadfor!='') {
                            update_post_meta($lead['object']->ID, 'assigned_agent', $leadfor);
                        }
                    } else {
                        $changeButton = '<a title="Select an Agent for ' . $lead['object']->post_title . '" href="#TB_inline?width=300&height=500&inlineId=assignagent' . $lead['object']->ID . '" role="button" data-toggle="modal" class="button button-info thickbox" style="float: right" >Change Agent</a>';
                    }

                    $agentOptions = '';
                    foreach ($agentArray as $agent) {
                        $agentOptions .= '<label style="padding: .5rem 1rem; display: block;"><input type="radio" name="agentassignment" value="' . $agent . '" ' . ($assignedAgent == $agent ? 'checked' : '') . ' /> ' . $agent . '</label>';
                    }

                    $thickboxes .= '<div id="assignagent' . $lead['object']->ID . '" class="modal hide fade" style="display:none; ">
                        <div>
                            <form class="form" id="agentselect" method="post" action="' . $_SERVER['REQUEST_URI'] . '" >
                                <input type="hidden" name="formID" value="agentselect" >
                                <input type="hidden" name="valuation_id" value="' . $lead['object']->ID . '" >
                                <input type="hidden" name="cname" value="' . $lead['object']->post_title . '" >
                                <input type="hidden" name="cphone" value="' . (isset($lead['meta']['lead_info_phone_number'][0]) ? $lead['meta']['lead_info_phone_number'][0] : '') . '" >
                                <input type="hidden" name="cemail" value="' . $lead['meta']['lead_info_email_address'][0] . '" >
                                <input type="hidden" name="ptype" value="' . (isset($lead['meta']['lead_info_listing_property_type'][0]) ? $lead['meta']['lead_info_listing_property_type'][0] : '') . '" >
                                <input type="hidden" name="p_address" value="' . (isset($lead['meta']['lead_info_property_address'][0]) ? $lead['meta']['lead_info_property_address'][0] : '') . '" >
                                <input type="hidden" name="p_details" value="' . (isset($lead['meta']['lead_info_property_details'][0]) ? $lead['meta']['lead_info_property_details'][0] : '') . '" >
                                <input type="hidden" name="mls_number" value="' . (isset($lead['meta']['lead_info_mls_number'][0]) ? $lead['meta']['lead_info_mls_number'][0] : '') . '" >
                                ' . $agentOptions . '
                                <div class="stuck" style="position: absolute; top: 50px; right: 30px;">
                                <button style="padding: .5rem 1rem; height: auto; font-size: 1.2em;" class="button button-primary" >SAVE</button>
                                </div>
                            </form>
                            </div>
                        </div>';

                    ?>
                    <tr>
                        <td><?php echo $lead['object']->post_title; ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_phone_number'][0]) ? $lead['meta']['lead_info_phone_number'][0] : ''); ?></td>
                        <td><?php echo $lead['meta']['lead_info_email_address'][0]; ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_listing_property_type'][0]) ? $lead['meta']['lead_info_listing_property_type'][0] : ''); ?></td>
                        <td><?php echo (isset($lead['meta']['lead_info_property_address'][0]) ? $lead['meta']['lead_info_property_address'][0] : ''); ?></td>
                        <td><?php echo $leadfor; ?></td>
                        <td><strong><?php echo $assignedAgent; ?></strong> <?php echo $changeButton; ?></td>
                        <td><?php echo (isset($lead['object']->post_date) ? date('j/n/y h:i a', strtotime($lead['object']->post_date)) : ''); ?></td>
                    </tr>
                <?php }
                ?>
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

            $mlsLead       = new Leads();
            $agent         = new Agents();
            $selectedAgent = $_POST['agentassignment'];

            $agentInfo    = $agent->assembleAgentData($selectedAgent);
            $ADMIN_EMAIL  = ($agentInfo['email_address']!='' ? $agentInfo['email_address'] : 'info@beachybeach.com');

            if(isset($_POST['lead_id'])){ //info request assignment

                //BEGIN EMAIL
                $emailvars = array(
                    'Name'              => $_POST['cname'],
                    'Email Address'     => $_POST['cemail'],
                    'Phone Number'      => $_POST['cphone'],
                    'Lead Type'         => 'Info Request'
                );

                if(isset($_POST['mls_number'])){
                    $emailvars['MLS Number'] = $_POST['mls_number'];
                }
                if(isset($_POST['message'])){
                    $emailvars['Message'] = $_POST['message'];
                }

                $tableData = '';
                foreach ($emailvars as $key => $var) {
                    if($var != '') {
                        $tableData .= '<tr><td class="label"><strong>' . $key . '</strong></td><td>' . htmlentities(stripslashes($var)) . '</td>';
                    }
                }

                $mlsLead->sendEmail(
                    [
                        'to'        => $ADMIN_EMAIL,
                        'from'      => get_bloginfo() . ' <noreply@' . $mlsLead->domain . '>',
                        'subject'   => 'A lead has been assigned to you',
                        'cc'        => $mlsLead->ccEmail,
                        'bcc'       => $mlsLead->bccEmail,
                        'headline'  => 'You have a new lead',
                        'introcopy' => 'You have been assigned a new lead. Details are below:',
                        'leadData'  => $tableData
                    ]
                );

                update_post_meta( $_POST['lead_id'], 'assigned_agent', $selectedAgent );
            }

            if(isset($_POST['valuation_id'])){ //home valuation assignment

                $emailvars = array(
                    'Name'              => $_POST['cname'],
                    'Email Address'     => $_POST['cemail'],
                    'Phone Number'      => $_POST['cphone'],
                    'Lead Type'         => 'Home Valuation'
                );

                if(isset($_POST['p_type'])){
                    $emailvars['Property Type'] = $_POST['p_type'];
                }
                if(isset($_POST['p_address'])){
                    $emailvars['Property Address'] = $_POST['p_address'];
                }
                if(isset($_POST['p_details'])){
                    $emailvars['Property Details'] = $_POST['p_details'];
                }

                $tableData = '';
                foreach ($emailvars as $key => $var) {
                    if($var != '') {
                        $tableData .= '<tr><td class="label"><strong>' . $key . '</strong></td><td>' . htmlentities(stripslashes($var)) . '</td>';
                    }
                }

                $mlsLead->sendEmail(
                    [
                        'to'        => $ADMIN_EMAIL,
                        'from'      => get_bloginfo() . ' <noreply@' . $mlsLead->domain . '>',
                        'subject'   => 'A lead has been assigned to you',
                        'cc'        => $mlsLead->ccEmail,
                        'bcc'       => $mlsLead->bccEmail,
                        'headline'  => 'You have a new home valuation lead',
                        'introcopy' => 'You have been assigned a home valuation lead. Details are below:',
                        'leadData'  => $tableData
                    ]
                );

                update_post_meta( $_POST['valuation_id'], 'assigned_agent', $selectedAgent );

            }

            if(isset($_POST['cid'])){ //leads with accounts assignment

                //BEGIN EMAIL
                $emailvars = array(
                    'Name'              => $_POST['cname'],
                    'Email Address'     => $_POST['cemail'],
                    'Phone Number'      => $_POST['cphone'],
                    'Physical Address'  => $_POST['caddress'],
                    'Properties saved'  => $_POST['cbuckets']
                );

                $tableData = '';
                foreach ($emailvars as $key => $var) {
                    if($var != '') {
                        $tableData .= '<tr><td class="label"><strong>' . $key . '</strong></td><td>' . htmlentities(stripslashes($var)) . '</td>';
                    }
                }

                $mlsLead->sendEmail(
                    [
                        'to'        => $ADMIN_EMAIL,
                        'from'      => get_bloginfo() . ' <noreply@' . $mlsLead->domain . '>',
                        'subject'   => 'A client account has been assigned to you',
                        'cc'        => $mlsLead->ccEmail,
                        'bcc'       => $mlsLead->bccEmail,
                        'headline'  => 'You have a new client account',
                        'introcopy' => 'You have been assigned a new client account. Details are below:',
                        'leadData'  => $tableData
                    ]
                );

                update_user_meta($_POST['cid'], 'selected_agent', $selectedAgent);
            }


        }
    }
}
