<?php
/**
 * Class kmaLeads
 * Author: Bryan Baird
 * Date: 2/3/2017
 * Time: 1:21 PM
 */

class kmaLeads {

    /**
     * Leads constructor.
     */
    public function __construct() {

	    date_default_timezone_set ( 'America/Chicago' );

    }

    /**
     * @return null
     */
    public function createPostType() {

        //CREATE LEAD MGMT SYS
        $leads = new Custom_Post_Type(
            'Lead',
            array(
                'supports'			 => array( 'title' ),
                'menu_icon'			 => 'dashicons-star-empty',
                'has_archive' 		 => false,
                'menu_position'      => null,
                'public'             => false,
                'publicly_queryable' => false,
                'capability_type'    => array('lead','leads'),
            )
        );

        $leads->add_meta_box(
            'Lead Info',
            array(
                'Name' 			        => 'locked',
                'Date' 			        => 'locked',
                'Phone Number'	        => 'locked',
                'Email Address'	        => 'locked',
                'Selected Agent'        => 'locked',
                'MLS Number' 		    => 'locked',
                'Address'               => 'locked',
                'Property Type'         => 'locked',
                'Message' 		        => 'locked',
                'Notification Preview'  => 'locked'
            )
        );

        $leads->add_taxonomy( 'Type' );

    }

    public function createAdminColumns(){

        add_filter('manage_lead_posts_columns', 'columns_head_lead', 0);
        add_action('manage_lead_posts_custom_column', 'columns_content_lead', 0, 2);

        function columns_head_lead($defaults) {
            $defaults['lead_type'] = 'Lead Type';
            $defaults['mls_number'] = 'MLS Number';
            $defaults['email_address'] = 'Email Address';
            $defaults['phone_number'] = 'Phone Number';
            return $defaults;
        }
        function columns_content_lead($column_name, $post_ID) {
            switch ( $column_name ) {
                case 'lead_type':
                    $term = wp_get_object_terms( $post_ID, 'type' );
                    echo (isset($term[0]->name) ? $term[0]->name : null );
                    break;

                case 'mls_number':
                    $mls_number = get_post_meta( $post_ID, 'lead_info_mls_number', TRUE );
                    echo (isset($mls_number) ? $mls_number : null );
                    break;

                case 'email_address':
                    $email_address = get_post_meta( $post_ID, 'lead_info_email_address', TRUE );
                    echo (isset($email_address) ? '<a href="mailto:'.$email_address.'" >'.$email_address.'</a>' : null );
                    break;

                case 'phone_number':
                    $phone_number = get_post_meta( $post_ID, 'lead_info_phone_number', TRUE );
                    echo (isset($phone_number) ? $phone_number : null );
                    break;
            }
        }

    }

    public function getLeads($args = []){
        $request = [
            'posts_per_page' => - 1,
            'offset'         => 0,
            'post_type'      => 'lead',
            'post_status'    => 'publish',
        ];

        $args = array_merge( $request, $args );
        $results = get_posts( $args );

        $resultArray = [];
        foreach ( $results as $item ){

            array_push( $resultArray, $item );

        }

        return $resultArray;

    }

    public function sendEmail(

        $sendadmin = array(
            'to'		=> 'support@kerigan.com',
            'from'		=> 'Website <noreply@kerigan.com>',
            'subject'	=> 'Email from website'
        ),
        $emaildata = array(
            'headline'	=> 'This is an email from the website!',
            'introcopy'	=> 'If we weren\'t testing, there would be stuff here.',
            'filedata' => '',
            'fileinfo' => ''
        ),
        $emailTemplate = ''
        
    ){

        $eol = "\r\n";

        //search for directory in active WP template
        if(file_exists(wp_normalize_path(get_template_directory().'/modules/leads/emailtemplate.php' ))){
            $emailTemplate = file_get_contents(wp_normalize_path(get_template_directory().'/modules/leads/emailtemplate.php' ));
        }else{

            $emailTemplate = '<!doctype html>
                <html>
                    <head>
                        <meta charset="utf-8">
                    </head>
                    <body bgcolor="#EAEAEA" style="background-color:#EAEAEA;">
                        <table cellpadding="0" cellspacing="0" border="0" align="center" style="width:650px; background-color:#FFFFFF; margin:30px auto;" bgcolor="#FFFFFF" >
                            <tbody>
                                <tr>
                                    <td style="padding:20px; border-top:10px solid #333333; border-bottom: #333333 solid 2px;" >
                                    <!--[content]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                </html>';
        }

        $split = strrpos($emailTemplate, '<!--[content]-->');
        $templatebot = substr($emailTemplate, $split);
        $templatetop = substr($emailTemplate, 0, $split);

        $bottomsplit = strrpos($templatebot, '<!--[date]-->');
        $bottombot = substr($templatebot, $bottomsplit);
        $bottomtop = substr($templatebot, 0, $bottomsplit);
        $senddate = date('M j, Y').' @ '.date('g:i a');

        //build headers
        $headers  = 'From: ' . $sendadmin['from'] . $eol;
        $headers .= (isset($sendadmin['cc']) ? 'Cc: ' . $sendadmin['cc'] . $eol : '');
	    $headers .= (isset($sendadmin['bcc']) ? 'Bcc: ' . $sendadmin['bcc'] . $eol : '');
        $headers .= 'MIME-Version: 1.0' . $eol;

        //noreply pass: raw9z.kvc@b*
        //start building the email (if attachment)
	    $hasFile = ( isset($emaildata['fileinfo']) && isset($emaildata['filedata']) ? TRUE : FALSE );
        if( $hasFile ){

            //file info
            $mime_boundary = md5(time());
            $name = $emaildata['fileinfo']['filename'];
            $type = $emaildata['fileinfo']['filetype'];
            $data = $emaildata['filedata'];

            //mixed content type
            $headers .= "Content-Type: multipart/mixed;boundary=\"" . $mime_boundary . "\"". $eol;

            //add attachment
            $emailcontent  = "--".$mime_boundary . $eol;
            $emailcontent .= "Content-Type: ".$type."; name=\"".$name."\"" . $eol;
            $emailcontent .= "Content-Transfer-Encoding: base64".$eol;
            $emailcontent .= "Content-Disposition: attachment".$eol.$eol;
            $emailcontent .= $data . $eol;
            $emailcontent .= "--".$mime_boundary . $eol; //transition to new content type

            //add html email content type
            $emailcontent .= "Content-Type: text/html; charset=\"utf-8\"" . $eol;
            $emailcontent .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;

            // fancy html part
            $emailcontent .= $templatetop . $eol . $eol;
            $emailcontent .= $emaildata['headline'];
            $emailcontent .= $emaildata['introcopy'];
            $emailcontent .= $bottomtop;
            $emailcontent .= $senddate;
            $emailcontent .= $bottombot . $eol . $eol;

            $emailcontent .= "--".$mime_boundary."--" . $eol . $eol; //close text/html part


        }else{ //no attachment
            $headers .= 'Content-type: text/html; charset=utf-8' . $eol;
            $emailcontent  = $templatetop . $eol . $eol;
            $emailcontent .= '<h2>'.$emaildata['headline'].'</h2>';
            $emailcontent .= '<p>'.$emaildata['introcopy'].'</p>';
            $emailcontent .= $templatebot . $eol . $eol;
        }

        mail( $sendadmin['to'], $sendadmin['subject'], $emailcontent, $headers );

    }

}