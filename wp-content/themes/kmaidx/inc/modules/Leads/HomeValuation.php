<?php

namespace Includes\Modules\Leads;

use Includes\Modules\Agents\Agents;

class HomeValuation extends Leads
{
    public function __construct ()
    {
        parent::__construct ();
        parent::set('postType','Home Valuation');
        parent::assembleLeadData(
            [
                'phone_number'     => 'Phone Number',
                'selected_agent'   => 'Selected Agent',
                'property_address' => 'Property Address',
                'property_type'    => 'Property Type',
                'property_details' => 'Property Details',
                'lead_for'         => 'Lead for',
                'heard'            => 'How you heard about us'
            ]
        );
    }

    public function handleLead ($dataSubmitted = [])
    {
        $dataSubmitted['full_name'] = (isset($dataSubmitted['full_name']) ? $dataSubmitted['full_name'] :
            (isset($dataSubmitted['first_name']) ? $dataSubmitted['first_name'] . ' ' . $dataSubmitted['last_name'] : '')
        );

        $dataSubmitted['property_address'] = parent::toFullAddress(
            $dataSubmitted['listing_address'], $dataSubmitted['listing_address_2'],
            $dataSubmitted['listing_city'], $dataSubmitted['listing_state'], $dataSubmitted['listing_zip']
        );

        if($dataSubmitted['lead_for'] == 'specific'){

            $agent = new Agents();
            $agentInfo = $agent->assembleAgentData($dataSubmitted['selected_agent']);
            parent::set('adminEmail',($agentInfo['email_address'] != '' ? $agentInfo['email_address'] : 'info@beachybeach.com'));
            parent::set('ccEmail','info@beachybeach.com');
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

        // parent::set($this->adminEmail,'bryan@kerigan.com'); //temp

        if(!parent::validateSubmission($dataSubmitted)){
            echo '<div class="alert alert-danger" role="alert">
            <strong>Errors were found. Please correct the indicated fields below.</strong>
            </div>';
            return;
        }
        
        echo '<div class="alert alert-success" role="alert">
        <strong>Your request has been received. We will review your submission and get back with you soon.</strong>
        </div>';

        parent::addToDashboard($dataSubmitted);
        parent::sendNotifications($dataSubmitted);
        
    }

    public function checkSpam($dataSubmitted)
    {
        $client = new \Gothick\AkismetClient\Client(
            site_url(),           // Your website's URL (this becomes Akismet's "blog" parameter)
            "KMA Spam Checker",   // Your website or app's name (Used in the User-Agent: header when talking to Akismet)
            "1.0",                // Your website or app's software version (Used in the User-Agent: header when talking to Akismet)
            akismet_get_key()     
        );

        $result = $client->commentCheck([
            'user_ip'              => $dataSubmitted['ip_address'],
            'user_agent'           => $dataSubmitted['user_agent'],
            'referrer'             => $dataSubmitted['referrer'],
            'comment_author'       => $dataSubmitted['full_name'],
            'comment_author_email' => $dataSubmitted['email_address'],
            'comment_content'      => $dataSubmitted['property_details']
        ], $_SERVER);

        $spam = $result->isSpam();
        //echo '<pre>',print_r($result),'</pre>';

        return $spam; // Boolean 
    }

}