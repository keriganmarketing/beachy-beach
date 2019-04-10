<?php
namespace Includes\Modules\Notifications;

use GuzzleHttp\Client;
use Includes\Modules\Leads\Leads;
use Includes\Modules\MLS\FullListing;

class ListingUpdated
{
    public function notify()
    {
        $users = $this->getUsersWithSavedProperties();

        foreach ($users as $user) {
            $changed = $this->userHasUpdatedFavorites($user->user_id);
            if (count($changed) > 0) {
                $this->notifyUserOfChanges($user->user_id, $changed);
            }
        }
    }

    private function userHasUpdatedFavorites($userId)
    {
        $favorites       = $this->flattenListings($this->favoritedListings($userId));
        $updatedListings = $this->flattenListings($this->fetchUpdatedListings());
        $changed         = [];

        foreach ($favorites as $favorite) {
            if (in_array($favorite, $updatedListings)) {
                $changed[] = $favorite;
            }
        }
        return $changed;
    }

    private function getUsersWithSavedProperties()
    {
        global $wpdb;

        $query   = "SELECT DISTINCT user_id from wp_beachy_buckets";
        $results = $wpdb->get_results($query);

        return $results;
    }

    private function fetchUpdatedListings()
    {
        $client = new Client(['base_uri' => 'https://mothership.kerigan.com/api/v1/']);
        $raw    = $client->request(
            'GET',
            'updatedListings'
        );

        $updatedListings = json_decode($raw->getBody());

        return $updatedListings;
    }

    private function favoritedListings($userId)
    {
        global $wpdb;

        $query   = "SELECT DISTINCT mls_account FROM wp_beachy_buckets WHERE user_id = {$userId}";
        $results = $wpdb->get_results($query);

        return $results;
    }

    private function flattenListings($listings)
    {
        $mlsNumberArray = [];

        foreach ($listings as $listing) {
            array_push($mlsNumberArray, $listing->mls_account);
        }

        return $mlsNumberArray;
    }

    private function notifyUserOfChanges($userId, $mlsIds)
    {

        $user = get_userdata($userId);

        $to   = $user->user_nicename . ' <' . $user->user_email . '>';

        $tableData = '';
        foreach($mlsIds as $mlsId){
            $fullListing = new FullListing($mlsId);
            $listingInfo = $fullListing->create();

            $tableData .= '<tr><td width="50%"><img src="' . $listingInfo->preferred_image . '" width="100%" ></td>
            <td><table>
                <tr><td>
                <p>' . $listingInfo->street_number.' '.$listingInfo->street_name .' '.$listingInfo->street_suffix .  '<br>
                ' . $listingInfo->city . ', FL</p>
                <p><strong>$' . number_format($listingInfo->price) . '</strong></p></td></tr>
                <tr><td><a style="display: block; line-height: 20px;" href="https://beachybeach.com/listing/?mls=' . $mlsId . '" >View property</a></td></tr>
            </table>
            </td></tr><tr><td>&nbsp;</td></tr>';
        }

        $tableData .= '<tr><td colspan="2" align="center"><a style="display: block; line-height: 20px;" href="https://beachtimerealty.com/my-account/" >View all saved properties</a></td></tr>';

        $email = new Leads();

        $email->sendEmail(
            [
                'to'        => $to,
                'from'      => get_bloginfo() . ' <noreply@' . $email->domain . '>',
                'subject'   => 'Updated Property Alert',
                'cc'        => '',
                'bcc'       => $email->bccEmail,
                'replyto'   => '',
                'headline'  => 'Updated Property Alert',
                'introcopy' => 'One or more properties in your Beachy Bucket has been updated. Details are below:',
                'leadData'  => $tableData
            ]
        );

    }
}
