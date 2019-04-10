<?php

namespace Includes\Modules\Leads;

use Includes\Modules\Agents\Agents;
use Includes\Modules\MLS\FullListing;

class RequestInfo extends Leads
{
    public function __construct ()
    {
        parent::__construct ();
        parent::set('ccEmail','lacey@beachybeach.com');
        parent::assembleLeadData(
            [
                'phone_number'       => 'Phone Number',
                'reason_for_contact' => 'Reason for Contact',
                'selected_agent'     => 'Selected Agent',
                'mls_number'         => 'MLS Number',
                'message'            => 'Message',
                'lead_for'           => 'Lead for',
            ]
        );
    }

    public function handleLead ($dataSubmitted = [])
    {
        $fullName = (isset($dataSubmitted['full_name']) ? $dataSubmitted['full_name'] : null);
        $dataSubmitted['full_name'] = (isset($dataSubmitted['first_name']) && isset($dataSubmitted['last_name']) ? $dataSubmitted['first_name'] . ' ' . $dataSubmitted['last_name'] : $fullName);

        if($dataSubmitted['lead_for'] == 'specific'){

            $agent = new Agents();
            $agentInfo = $agent->assembleAgentData($dataSubmitted['selected_agent']);
            parent::set('adminEmail', (isset($agentInfo['email_address']) && $agentInfo['email_address'] != '' ? $agentInfo['email_address'] : $this->adminEmail));
            $dataSubmitted['lead_for'] = '';

        }elseif($dataSubmitted['lead_for'] == 'pcb'){

            parent::set('adminEmail','info@beachybeach.com');
            $dataSubmitted['selected_agent'] = '';
            $dataSubmitted['lead_for'] = 'Beachy Beach Real Estate';

        }elseif($dataSubmitted['lead_for'] == '30a'){

            parent::set('adminEmail','30a@beachybeach.com');
            $dataSubmitted['selected_agent'] = '';
            $dataSubmitted['lead_for'] = 'Beachy Beach 30A';

        }

        //parent::set($this->adminEmail,'bbaird85@gmail.com'); //temp
        parent::addToDashboard($dataSubmitted);
        if(parent::validateSubmission($dataSubmitted)){
            echo '<div class="alert alert-success" role="alert">
            <strong>Your request has been received. We will review your submission and get back with you soon.</strong>
            </div>';
        }else{
            echo '<div class="alert alert-danger" role="alert">
            <strong>Errors were found. Please correct the indicated fields below.</strong>
            </div>';
            return;
        }
        $this->sendNotifications($dataSubmitted);
    }

    protected function sendNotifications ($leadInfo)
    {
        $emailAddress = (isset($leadInfo['email_address']) ? $leadInfo['email_address'] : null);
        $fullName     = (isset($leadInfo['full_name']) ? $leadInfo['full_name'] : null);

        $tableData = '';
        foreach ($this->additionalFields as $key => $var) {
            if($leadInfo[$key]!='') {
                $tableData .= '<tr><td class="label"><strong>' . $var . '</strong></td><td>' . htmlentities(stripslashes($leadInfo[$key])) . '</td>';
            }
        }

        if($leadInfo['mls_number']!=''){

            $fullListing = new FullListing($leadInfo['mls_number']);
            $listingInfo = $fullListing->create();

            $tableData .= '<tr><td width="50%"><img src="' . $listingInfo->preferred_image . '" width="100%" ></td>
            <td><table>
                <tr><td>
                <p>' . $listingInfo->street_number.' '.$listingInfo->street_name .' '.$listingInfo->street_suffix . '<br>
                ' . $listingInfo->city . ', FL</p>
                <p><strong>$' . number_format($listingInfo->price) . '</strong></p></td></tr>
                <tr><td><a style="display: block; line-height: 20px;" href="https://beachybeach.com/listing/?mls=' . $leadInfo['mls_number'] . '" >View property</a></td></tr>
            </table>
            </td></tr><tr><td>&nbsp;</td></tr>';
        }

        parent::sendEmail(
            [
                'to'        => $this->adminEmail,
                'from'      => $this->siteName . ' <noreply@' . $this->domain . '>',
                'subject'   => $this->postType . ' from website',
                'cc'        => $this->ccEmail,
                'bcc'       => $this->bccEmail,
                'replyto'   => $fullName . '<' . $emailAddress . '>',
                'headline'  => 'You have a new ' . strtolower($this->postType),
                'introcopy' => 'A ' . strtolower($this->postType) . ' was received from the website. Details are below:',
                'leadData'  => $tableData
            ]
        );

        parent::sendEmail(
            [
                'to'        => $fullName . '<' . $emailAddress . '>',
                'from'      => $this->siteName . ' <noreply@' . $this->domain . '>',
                'subject'   => 'Your website submission has been received',
                'bcc'       => $this->bccEmail,
                'headline'  => 'Thank you',
                'introcopy' => 'We\'ll review the information you\'ve provided and get back with you as soon as we can.',
                'leadData'  => $tableData
            ]
        );

    }

}