<?php


/**
 *                    BCAR AND ECAR LIST REFERENCE
 *
 * LIST_3   Searchable Field        List Number Main        Int             11
 * LIST_8   Searchable Field        Property Type           Character        2
 * LIST_9   Searchable Field        Sub-Type                Character      100
 * LIST_15  Searchable Field        Status                  Character      100
 * LIST_22  Searchable Field        List Price              Decimal         15
 * LIST_29  Searchable Field        Area                    Character      100
 * LIST_31  Searchable Field        Street #                Character       10
 * LIST_34  Searchable Field        Street Name             Character       30
 * LIST_35  Searchable Field        Unit #                  Character       30
 * LIST_39  Searchable Field        City                    Character      100
 * LIST_40  Searchable Field        State                   Character      100
 * LIST_43  Searchable Field        Zip Code                Character      100
 * LIST_46  Searchable Field        Latitude                Decimal         11
 * LIST_47  Searchable Field        Longitude               Decimal         11
 * LIST_48  Searchable Field        Apx SqFt(Htd/Cool)      Decimal         10
 * LIST_57  Searchable Field        Acreage                 Decimal         10
 * LIST_66  Searchable Field        Bedrooms                Int              4
 * LIST_67  Searchable Field        Total Baths             Decimal          7
 * LIST_77  Searchable Field        Subdivision             Character       100
 * LIST_87  Searchable Field        Timestamp(modified)     DateTime        25
 * LIST_94  Searchable Field        Sub Area                Character       100
 * LIST_192 Searchable Field        Waterfront              Character       100
 *
 */

require("vendor/autoload.php");


/**
 * @property array ecarOptions
 * @property array bcarOptions
 */
class MLS
{

    /**
     * MLS constructor.
     */
    function __construct()
    {
        $this->getEcarOptions();
        $this->getBcarOptions();
    }

    /**
     * @param $loginUrl
     * @param $username
     * @param $password
     * @return \PHRETS\Session
     */
    private function connectToMLS($loginUrl, $username, $password)
    {
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl($loginUrl)
            ->setUsername($username)
            ->setPassword($password)
            ->setRetsVersion('1.7.2')
            ->setOption("compression_enabled", true)
            ->setOption("offset_support", true);


        $rets = new \PHRETS\Session($config);

        return $rets;
    }

    /**
     * @return string
     */
    private function getECARLogin()
    {
        return 'http://retsgw.flexmls.com/rets2_2/Login';
    }

    /**
     * @return string
     */
    private function getECARUserName()
    {
        return 'ecn.rets.e9649';
    }

    /**
     * @return string
     */
    private function getECARPassword()
    {
        return 'mafic-biotic29';
    }

    /**
     * @return string
     */
    private function getBCARLogin()
    {
        return 'http://retsgw.flexmls.com:80/rets2_3/Login';
    }

    /**
     * @return string
     */
    private function getBCARUserName()
    {
        return 'bc.rets.kerigan';
    }

    /**
     * @return string
     */
    private function getBCARPassword()
    {
        return 'moths-phobe10';
    }

    /**
     * Starts the process for updating BCAR Database. Photos are updated as well
     */
    public function updateBCAR()
    {
        $loginUrl = $this->getBCARLogin();
        $username = $this->getBCARUserName();
        $password = $this->getBCARPassword();

        $this->updateDatabase($loginUrl, $username, $password, 'wp_bcar');
    }

    /**
     * Starts the process for updating ECAR Database. Photos are updated as well
     */
    public function updateECAR()
    {
        $loginUrl = $this->getECARLogin();
        $username = $this->getECARUserName();
        $password = $this->getECARPassword();

        $this->updateDatabase($loginUrl, $username, $password, 'wp_ecar');
    }

    /**
     * @param $loginUrl
     * @param $username
     * @param $password
     * @param $table
     */
    private function updateDatabase($loginUrl, $username, $password, $table)
    {
        global $wpdb;
        $wpdb->query("TRUNCATE table " . $table);
        $classArray = [];

        $rets = $this->connectToMLS($loginUrl, $username, $password);

        $rets->Login();

        $classes = $this->getClasses($rets, 'Property');

        foreach ($classes as $class) {
            if ($class['ClassName'] != 'F') {
                array_push($classArray, $class['ClassName']);
            }
        }

        foreach ($classArray as $class) {

            $results = ($table == 'wp_ecar') ?
                $this->get_ECAR_data($class, $rets) :
                $this->get_BCAR_data($class, $rets);


            foreach ($results as $result) {
                $this->updateTable($table, $wpdb, $result, $class);
            }
        }
        if ($table == 'wp_bcar') {
            $this->updateBCARPhotos($rets);
            $this->addPreferredImages($table, $wpdb, 'wp_bcar_photos');
        }
        $rets->Disconnect();

    }


    /**
     * @param $rets
     * @param $type
     * @return mixed
     * @internal param $resources
     */
    private function getClasses($rets, $type)
    {
        $letterClasses = $rets->GetClassesMetadata($type);

        return $letterClasses;
    }

    /**
     * @param $class
     * @param $rets
     * @return mixed
     * @internal param $table
     * @internal param $waterfront
     */
    private function get_ECAR_data($class, $rets)
    {

        $results = $rets->Search('Property', $class, '*', $this->ecarOptions[$class]);

        $count = 0;
        foreach ($results as $result) {
            echo '<pre>', print_r($result['LIST_3']), '</pre>';
            $count++;
        }
        echo $count;

        return $results;
    }

    /**
     * @param $class
     * @param $rets
     * @return mixed
     */
    private function get_BCAR_data($class, $rets)
    {

        $results = $rets->Search('Property', $class, '(LIST_87=1950-01-01T00:00:00+)', ['Limit' => 1000, 'Select' => 'LIST_3']);

        echo '<p>' . $results->getTotalResultsCount() . '</p>';
        echo '<p>' . $results->isMaxRowsReached() . '</p>';
        foreach ($results as $result) {
            echo '<pre>', print_r($result['LIST_3']), '</pre>';
        }


        return $results;
    }

    /**
     * @param $table
     * @param $wpdb
     * @param $result
     * @param $class
     */
    private function updateTable($table, $wpdb, $result, $class)
    {
        $waterfront = ($table == 'wp_bcar') ? $result['LIST_192'] : $result['GF20131203222329624962000000'];

        $this->listingTableUpdater($table, $wpdb, $result, $waterfront, $class);
    }

    /**
     * @internal param $waterfront
     */
    private function getEcarOptions()
    {
        $waterfront = 'GF20131203222329624962000000'; //mother of god

        $this->ecarOptions = [
            'A' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_58,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131203203513863218000000,GF20131203203523234694000000,GF20131203203501805928000000,GF20131203185526796706000000,GF20131203203446527084000000,GF20131203185458688530000000,GF20131203222306734642000000,GF20131203222538613490000000,LIST_88,LIST_89,LIST_90,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'B' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131230164914843719000000,GF20131230164912246391000000,GF20131230164914069211000000,GF20131230164913550188000000,GF20131230164913256545000000,GF20131230164915907956000000,GF20131230164916157466000000,GF20131230164916093183000000,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'C' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_56,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'E' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
            'F' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_90,LIST_56,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'G' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',
            ],
            'H' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',

            ],
            'I' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_146,LIST_53,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
        ];
    }

    /**
     * @internal param $waterfront
     */
    private function getBcarOptions()
    {
        $waterfront = 'LIST_192';

        $this->bcarOptions = [
            'A' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150204172056869833000000,GF20150204172056907082000000,GF20150204172057113687000000,GF20150204172056829043000000,GF20150204172057197731000000,GF20150204172056617468000000,GF20150204172056790876000000,GF20150204172056580165000000,GF20150204172056948623000000,GF20150204172057026304000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'B' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'C' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_56,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'E' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_78,LIST_80,LIST_82',
            ],
            'F' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'G' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',
            ],
            'H' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',

            ],
            'I' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_146,LIST_53,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
            'J' => [
                'Limit'  => 99999,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
        ];
    }

    /**
     * @param $getVars
     * @return string
     * @internal param $searchTerm
     */
    public function buildQuery($getVars = null)
    {

        $status                 = isset($getVars['status']) ? $getVars['status'] : array('Active');
        $sq_ft                  = isset($getVars['sq_ft']) ? $getVars['sq_ft'] : null;
        $maxPrice               = isset($getVars['max_price']) ? (int)$getVars['max_price'] : null;
        $pool                   = isset($getVars['pool']) ? $getVars['pool'] : null;
        $waterfront             = isset($getVars['waterfront']) ? $getVars['waterfront'] : null;
        $minPrice               = isset($getVars['min_price']) ? (int)$getVars['min_price'] : null;
        $bedrooms               = isset($getVars['bedrooms']) ? $getVars['bedrooms'] : null;
        $bathrooms              = isset($getVars['bathrooms']) ? $getVars['bathrooms'] : null;
        $cityArrayTmp           = isset($getVars['city']) ? $getVars['city'] : array();
        $class                  = isset($getVars['class']) ? urldecode($getVars['class']) : null;
        $classArray             = $class != null ? $this->getPropertyTypes($class) : null;
        $cityArrayCount         = is_array($cityArrayTmp) ? count($cityArrayTmp) : 0;
        $classArrayCount        = is_array($classArray) ? count($classArray) : 0;
        $propertyTypeArray      = isset($getVars['property_type']) ? $getVars['property_type'] : array();
        $propertyTypeArrayCount = is_array($propertyTypeArray) ? count($propertyTypeArray) : 0;
        $cityArray              = array();
        foreach ($cityArrayTmp as $city) {
            if ($city != 'Edgewater Beach') {
                $city = '%' . $city;
                $city .= '%';
            }
            array_push($cityArray, $city);
        }

        $query = "SELECT b.mls_account, b.id, b.latitude, b.longitude, b.lot_dimensions, b.property_type, b.status, b.state, b.preferred_image, b.price, b.area, b.sub_area, b.subdivision, b.city, b.street_number, b.street_name, b.unit_number, b.zip, b.bedrooms, b.bathrooms, b.sq_ft, b.acreage, b.class, b.waterfront, b.date_modified FROM wp_bcar b WHERE 1=1 AND";

        for ($i = 0; $i < count($status); $i++) {
            $query .= ' b.status LIKE "' . $status[$i] . '"';
            if ($i != count($status) - 1) {
                $query .= ' OR';
            }
        }
        if ($cityArrayCount > 0) {
            $query .= ' AND(';
            for ($i = 0; $i < $cityArrayCount; $i++) {
                $query .= ' b.city LIKE "' . stripslashes($cityArray[$i]) . '" OR b.zip LIKE "' . stripslashes($cityArray[$i]) . '" OR b.subdivision LIKE "' . stripslashes($cityArray[$i]) . '" OR b.area LIKE "' . stripslashes($cityArray[$i]) . '"';

                if ($i != $cityArrayCount - 1) {
                    $query .= ' OR';
                }
            }
            $query .= ')';
        }
        if ($minPrice) {
            $query .= ' AND b.price > ' . $minPrice;
        }
        if ($maxPrice) {
            $query .= ' AND b.price < ' . $maxPrice;
        }
        if ($bedrooms) {
            $query .= ' AND b.bedrooms >= ' . $bedrooms;
        }
        if ($bathrooms) {
            $query .= ' AND b.bathrooms >= ' . $bathrooms;
        }
        if ($pool) {
            $query .= ' AND b.pool = 1 ';
        }
        if ($waterfront) {
            $query .= ' AND b.waterfront = "Yes" ';
        }
        if ($sq_ft) {
            $query .= ' AND b.sq_ft > ' . $sq_ft;
        }
        if ($classArray[0] != '' && $classArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $classArrayCount; $i++) {
                $query .= ' b.class LIKE "' . $classArray[$i] . '"';

                if ($i != $classArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }
        if ($propertyTypeArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $propertyTypeArrayCount; $i++) {
                $query .= ' b.property_type LIKE "' . $propertyTypeArray[$i] . '"';

                if ($i != $propertyTypeArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }

        $query .= " UNION ";

        $query .= "SELECT e.mls_account, e.id, e.latitude, e.longitude, e.lot_dimensions, e.property_type, e.status, e.state, e.preferred_image, e.price, e.area, e.sub_area, e.subdivision, e.city, e.street_number, e.street_name, e.unit_number, e.zip, e.bedrooms, e.bathrooms, e.sq_ft, e.acreage, e.class, e.waterfront, e.date_modified FROM wp_ecar e WHERE 1=1 AND";

        for ($i = 0; $i < count($status); $i++) {
            $query .= ' e.status LIKE "' . $status[$i] . '"';
            if ($i != count($status) - 1) {
                $query .= ' OR';
            }
        }
        if ($cityArrayCount > 0) {
            $query .= ' AND(';
            for ($i = 0; $i < $cityArrayCount; $i++) {
                $query .= ' e.city LIKE "' . stripslashes($cityArray[$i]) . '" OR e.zip LIKE "' . stripslashes($cityArray[$i]) . '" OR e.subdivision LIKE "' . stripslashes($cityArray[$i]) . '" OR e.area LIKE "' . stripslashes($cityArray[$i]) . '"';

                if ($i != $cityArrayCount - 1) {
                    $query .= ' OR';
                }
            }
            $query .= ')';
        }
        if ($minPrice) {
            $query .= ' AND e.price > ' . $minPrice;
        }
        if ($maxPrice) {
            $query .= ' AND e.price < ' . $maxPrice;
        }
        if ($bedrooms) {
            $query .= ' AND e.bedrooms >= ' . $bedrooms;
        }
        if ($bathrooms) {
            $query .= ' AND e.bathrooms >= ' . $bathrooms;
        }
        if ($pool) {
            $query .= ' AND e.pool = 1 ';
        }
        if ($waterfront) {
            $query .= ' AND e.waterfront = "Yes" ';
        }
        if ($sq_ft) {
            $query .= ' AND e.sq_ft > ' . $sq_ft;
        }
        if ($classArrayCount > 0 && $classArray[0] != '') {
            $query .= " AND(";
            for ($i = 0; $i < $classArrayCount; $i++) {
                $query .= ' e.class LIKE "' . $classArray[$i] . '"';

                if ($i != $classArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }
        if ($propertyTypeArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $propertyTypeArrayCount; $i++) {
                $query .= ' e.property_type LIKE "' . $propertyTypeArray[$i] . '"';

                if ($i != $propertyTypeArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }


        return 'SELECT DISTINCT Z.mls_account, Z.id, Z.latitude, Z.longitude, Z.lot_dimensions, Z.property_type, Z.status, Z.state, Z.preferred_image, Z.price, Z.area, Z.sub_area, Z.subdivision, Z.city, Z.street_number, Z.street_name, Z.unit_number, Z.zip, Z.bedrooms, Z.bathrooms, Z.sq_ft, Z.acreage, Z.class, Z.waterfront, Z.date_modified FROM (' . $query . ') Z GROUP BY Z.mls_account';
    }

    /**
     * @param $getVars
     * @return string
     * @internal param $searchTerm
     */
    public function mapQuery($getVars = null)
    {

        $sq_ft                  = isset($getVars['sq_ft']) ? $getVars['sq_ft'] : null;
        $maxPrice               = isset($getVars['max_price']) ? (int)$getVars['max_price'] : null;
        $minPrice               = isset($getVars['min_price']) ? (int)$getVars['min_price'] : null;
        $bedrooms               = isset($getVars['bedrooms']) ? $getVars['bedrooms'] : null;
        $bathrooms              = isset($getVars['bathrooms']) ? $getVars['bathrooms'] : null;
        $cityArray              = isset($getVars['city']) ? $getVars['city'] : array();
        $classArray             = isset($getVars['class']) ? $getVars['class'] : array();
        $cityArrayCount         = is_array($cityArray) ? count($cityArray) : 0;
        $classArrayCount        = is_array($classArray) ? count($classArray) : 0;
        $propertyTypeArray      = isset($getVars['property_type']) ? $getVars['property_type'] : array();
        $propertyTypeArrayCount = is_array($propertyTypeArray) ? count($propertyTypeArray) : 0;

        $query = "SELECT b.mls_account, b.id, b.latitude, b.longitude, b.lot_dimensions, b.property_type, b.status, b.state, b.preferred_image, b.price, b.area, b.sub_area, b.subdivision, b.city, b.street_number, b.street_name, b.unit_number, b.zip, b.bedrooms, b.bathrooms, b.sq_ft, b.acreage, b.class, b.waterfront, b.date_modified FROM wp_bcar b WHERE b.status like 'Active' ";

        if ($cityArrayCount > 0) {
            $query .= ' AND(';
            for ($i = 0; $i < $cityArrayCount; $i++) {
                $query .= ' b.city LIKE "' . $cityArray[$i] . '" OR b.zip LIKE "' . $cityArray[$i] . '" OR b.subdivision LIKE "' . $cityArray[$i] . '" OR b.area LIKE "' . $cityArray[$i] . '"';

                if ($i != $cityArrayCount - 1) {
                    $query .= ' OR';
                }
            }
            $query .= ')';
        }
        if ($minPrice) {
            $query .= ' AND b.price > ' . $minPrice;
        }
        if ($maxPrice) {
            $query .= ' AND b.price < ' . $maxPrice;
        }
        if ($bedrooms) {
            $query .= ' AND b.bedrooms >= ' . $bedrooms;
        }
        if ($bathrooms) {
            $query .= ' AND b.bathrooms >= ' . $bathrooms;
        }
        if ($sq_ft) {
            $query .= ' AND b.sq_ft > ' . $sq_ft;
        }
        if ($classArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $classArrayCount; $i++) {
                $query .= ' b.class LIKE "' . $classArray[$i] . '"';

                if ($i != $classArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }
        if ($propertyTypeArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $propertyTypeArrayCount; $i++) {
                $query .= ' b.property_type LIKE "' . $propertyTypeArray[$i] . '"';

                if ($i != $propertyTypeArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }

        $query .= " UNION ";

        $query .= "SELECT e.mls_account, e.id, e.latitude, e.longitude, e.lot_dimensions, e.property_type, e.status, e.state, e.preferred_image, e.price, e.area, e.sub_area, e.subdivision, e.city, e.street_number, e.street_name, e.unit_number, e.zip, e.bedrooms, e.bathrooms, e.sq_ft, e.acreage, e.class, e.waterfront, e.date_modified FROM wp_ecar e WHERE e.status like 'Active' ";

        if ($cityArrayCount > 0) {
            $query .= ' AND(';
            for ($i = 0; $i < $cityArrayCount; $i++) {
                $query .= ' e.city LIKE "' . $cityArray[$i] . '" OR e.zip LIKE "' . $cityArray[$i] . '" OR e.subdivision LIKE "' . $cityArray[$i] . '" OR e.area LIKE "' . $cityArray[$i] . '"';

                if ($i != $cityArrayCount - 1) {
                    $query .= ' OR';
                }
            }
            $query .= ')';
        }
        if ($minPrice) {
            $query .= ' AND e.price > ' . $minPrice;
        }
        if ($maxPrice) {
            $query .= ' AND e.price < ' . $maxPrice;
        }
        if ($bedrooms) {
            $query .= ' AND e.bedrooms >= ' . $bedrooms;
        }
        if ($bathrooms) {
            $query .= ' AND e.bathrooms >= ' . $bathrooms;
        }
        if ($sq_ft) {
            $query .= ' AND e.sq_ft > ' . $sq_ft;
        }
        if ($classArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $classArrayCount; $i++) {
                $query .= ' e.class LIKE "' . $classArray[$i] . '"';

                if ($i != $classArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }
        if ($propertyTypeArrayCount > 0) {
            $query .= " AND(";
            for ($i = 0; $i < $propertyTypeArrayCount; $i++) {
                $query .= ' e.property_type LIKE "' . $propertyTypeArray[$i] . '"';

                if ($i != $propertyTypeArrayCount - 1) {
                    $query .= " OR";
                }
            }
            $query .= ")";
        }


        return 'SELECT DISTINCT Z.mls_account, Z.id, Z.latitude, Z.longitude, Z.property_type, Z.status, Z.preferred_image FROM (' . $query . ') Z GROUP BY Z.mls_account';
    }

    /**
     * @param $column
     * @return array|null|object
     */
    public
    function getDistinctColumn($column)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            "Select * FROM
            ((SELECT DISTINCT $column from wp_bcar as b)
            UNION
            (SELECT DISTINCT $column from wp_ecar as e)) as Q
            WHERE Q.$column IS NOT NULL
            AND
            Q.$column <> ''
            ORDER BY Q.$column;");

        return $results;
    }

    /**
     * @param $query
     * @return string
     */
    public
    function getTotalQuery($query)
    {
        $total_query = "SELECT COUNT(preferred_image) FROM (" . $query . ") as Q ";

        return $total_query;
    }

    /**
     * @return int|number
     */
    function determinePagination()
    {
        $page = isset($_GET['pg']) ? abs((int)$_GET['pg']) : 1;

        return $page;
    }

    /**
     * @param $page
     * @param $listingsPerPage
     * @return mixed
     */
    function determineOffset($page, $listingsPerPage)
    {
        if ($page > 1) {
            $offset = $page * $listingsPerPage - $listingsPerPage;
        } else {
            $offset = $page - 1;
        }

        return $offset;
    }

    /**
     * @param $rets
     */
    public function updateBCARPhotos($rets)
    {
        global $wpdb;
        $listings = $wpdb->get_results("SELECT DISTINCT mls_account FROM wp_bcar");

        foreach ($listings as $listing) {
            $listingsArray[] = $listing->mls_account;
        }

        $chunkedArray = array_chunk($listingsArray, 250);

        foreach ($chunkedArray as $chunk => $mls) {
            $newArray[$chunk] = implode(",", $mls);
        }

        $this->truncateTable($wpdb, 'wp_bcar_photos');

        foreach ($newArray as $mls_batch) {
            $photos = $rets->GetObject('Property', 'Photo', urldecode($mls_batch), '1', 1);

            $this->updatePhotosTable($photos, $wpdb, 'wp_bcar_photos');

        }

    }

    /**
     * Handles the photo updates for ECAR.
     */
    public function updateECARPhotos()
    {
        global $wpdb;
        $loginUrl      = $this->getECARLogin();
        $username      = $this->getECARUserName();
        $password      = $this->getECARPassword();
        $listingsArray = array();

        $rets = $this->connectToMLS($loginUrl, $username, $password);

        $rets->Login();

        $listings = $wpdb->get_results("SELECT DISTINCT mls_account FROM wp_ecar");

        foreach ($listings as $listing) {
            $listingsArray[] = $listing->mls_account;
        }

        $chunkedArray = array_chunk($listingsArray, 250);

        foreach ($chunkedArray as $chunk => $mls) {
            $newArray[$chunk] = implode(",", $mls);
        }

        $this->truncateTable($wpdb, 'wp_ecar_photos');

        foreach ($newArray as $mls_batch) {

            $photos = $rets->GetObject('Property', 'Photo', urldecode($mls_batch), '1', 1);


            $this->updatePhotosTable($photos, $wpdb, 'wp_ecar_photos');


        }

        $rets->Disconnect();

        $this->addPreferredImages('wp_ecar', $wpdb, 'wp_ecar_photos');

    }

    /**
     * @param $photos
     * @param $wpdb
     * @param $photosTable
     */
    private
    function updatePhotosTable($photos, $wpdb, $photosTable)
    {
        foreach ($photos as $photo) {
            if ($photo->isError()) {
                $mls_account = $photo->getContentId();

                //TODO: get a new URL for the placeholder
                $photo_url = 'http://pcb.beachybeach.com/wp-content/uploads/2015/05/beachybeach-placeholder.png';

                $wpdb->insert($photosTable,
                    array(
                        'mls_account' => $mls_account,
                        'photo_url'   => $photo_url,
                    ),
                    array(
                        '%s',
                        '%s',
                    )
                );
            } else {
                $mls_account = $photo->getContentId();
                $photo_url   = $photo->getLocation();
                echo '<p>' . $photo_url . '</p>';
                $wpdb->insert($photosTable,
                    array(
                        'mls_account' => $mls_account,
                        'photo_url'   => $photo_url,
                    ),
                    array(
                        '%s',
                        '%s',
                    )
                );
            }

        }
    }

    /**
     * @param $wpdb
     * @param $table
     */
    public function truncateTable($wpdb, $table)
    {
        $wpdb->query("TRUNCATE table " . $table);
    }

    public function addPreferredImages($table, $wpdb, $photosTable)
    {
        $photos = $wpdb->get_results("SELECT * FROM " . $photosTable);

        foreach ($photos as $photo) {
            $wpdb->query("UPDATE " . $table . " SET preferred_image='" . $photo->photo_url . "' WHERE id='" . $photo->id . "'");
        }

    }

    /**
     * @param $name
     * @return array|null|object
     */
    public function hotCommunities($name)
    {
        global $wpdb;
        $matchName = addslashes(strtolower($name));

        $community = $wpdb->get_results($this->getHotCommunitiesQuery($matchName));

        return $community;

    }

    /**
     * @param string $sortBy
     * @param string $orderBy
     * @return array|null|object
     */
    public function getCommercialProperties($sortBy = 'price', $orderBy = 'ASC')
    {
        global $wpdb;

        $query = $this->getCommercialPropertiesQuery($sortBy, $orderBy);

        $results = $wpdb->get_results($query);

        return $results;
    }

    /**
     * Updates all agents assigned to BeachyBeach in the WP database
     */
    public function updateAllAgents()
    {
        global $wpdb;

        $wpdb->query("TRUNCATE table wp_agents");

        $this->updateAgents('bcar');
        $this->updateAgents('ecar');
        $this->populateAgentsPostType();

    }

    /**
     * @return array|null|object
     */
    public function getAllAgents()
    {
        global $wpdb;

        $mls_numbers = $wpdb->get_results(
            "SELECT DISTINCT short_id
             FROM wp_agents
             WHERE short_id NOT LIKE '%.%'");

        return $mls_numbers;
    }

    protected function populateAgentsPostType()
    {
        global $wpdb;

        $agents = $wpdb->get_results(
            "SELECT DISTINCT full_name
             FROM wp_agents
             WHERE short_id NOT LIKE '%.%'");

        foreach ($agents as $agent) {
            $fullName = $agent->full_name;
            if ($this->agentNotInWordpress($fullName)) {
                wp_insert_post(array(
                    'post_type'   => 'agent',
                    'post_status' => 'draft',
                    'post_title'  => $agent->full_name
                ));
            }
        }
    }

    protected function agentNotInWordPress($agentName)
    {
        global $wpdb;

        $return = $wpdb->get_row("
                    SELECT ID FROM wp_posts
                    WHERE post_title LIKE '{$agentName}'
                    AND post_type LIKE 'agent'");

        if (empty($return)) {
            return true;
        }

        return false;
    }


    /**
     * @param $agency
     * @return array
     */
    public function updateAgents($agency)
    {
        global $wpdb;
        $loginUrl = $agency == 'bcar' ? $this->getBCARLogin() : $this->getECARLogin();
        $username = $agency == 'bcar' ? $this->getBCARUserName() : $this->getECARUserName();
        $password = $agency == 'bcar' ? $this->getBCARPassword() : $this->getECARPassword();

        $rets = $this->connectToMLS($loginUrl, $username, $password);

        $rets->Login();

        $results = $rets->Search('ActiveAgent', 'Agent', '(MEMBER_1=20150408194257838828000000,20150408194307689490000000,20150408194320410962000000,20150408194321318828000000,20150602223531097668000000,20150604205855393719000000,20150604205858831143000000,20150604205900724360000000,20150604205902175093000000,20150604205907714447000000,20160322171505617327000000,20150609202601941446000000,20140121012113297213000000,20140121012119338938000000,20140121012125834424000000,20140121012126557078000000,20140212094525216859000000,20140318175700090139000000,20140318175833790221000000,20140318180032104932000000,20140318180039219905000000,20140321233133870979000000,20141015192001778707000000,20160322173009103378000000),(ACTIVE=1),(STATUS=1),(MLS_STATUS=1)',
            ['Limit' => -1, 'Select' =>
                'MEMBER_0,MEMBER_1,MEMBER_2,MEMBER_3,MEMBER_4,MEMBER_5,MEMBER_6,MEMBER_7,MEMBER_8,MEMBER_9,MEMBER_10,MEMBER_11,MEMBER_12,MEMBER_13,MEMBER_14,MEMBER_15,MEMBER_16,MEMBER_17,MEMBER_19,ACTIVE']);

        $agents = [];
        foreach ($results as $result) {
            $name = ucwords($result['MEMBER_3'] . ' ' . $result['MEMBER_4']);
            $wpdb->insert('wp_agents',
                array(
                    'full_name'    => $name,
                    'agent_id'     => $result['MEMBER_0'],
                    'is_active'    => $result['ACTIVE'],
                    'office_id'    => $result['MEMBER_1'],
                    'first_name'   => $result['MEMBER_3'],
                    'last_name'    => $result['MEMBER_4'],
                    'office_phone' => $result['MEMBER_5'],
                    'cell_phone'   => $result['MEMBER_6'],
                    'home_phone'   => $result['MEMBER_7'],
                    'fax'          => $result['MEMBER_8'],
                    'pager'        => $result['MEMBER_9'],
                    'email'        => $result['MEMBER_10'],
                    'url'          => $result['MEMBER_11'],
                    'street_1'     => $result['MEMBER_12'],
                    'street_2'     => $result['MEMBER_13'],
                    'city'         => $result['MEMBER_14'],
                    'state'        => $result['MEMBER_15'],
                    'zip'          => $result['MEMBER_16'],
                    'short_id'     => $result['MEMBER_17'],
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                )
            );
        }

        return $agents;
    }

    /**
     *
     */

    /**
     * @param $fullName
     * @return array|null|object
     */
    public function getAgentByName($fullName)
    {
        global $wpdb;

        $query = "SELECT * FROM wp_agents
                  WHERE full_name LIKE '{$fullName}'
                  AND short_id NOT LIKE '%.%' LIMIT 2";

        $agentObjects = $wpdb->get_results($query);

        if (!empty($agentObjects)) {
            $agent = array();
            foreach ($agentObjects as $key => $value) {

                $agent[$key] = $value;

            }
            for ($i = 0; $i < count($agentObjects); $i++) {
                $agent[0]->short_ids[$i] = $agentObjects[$i]->short_id;
            }

            unset($agent[0]->short_id);

            return $agent[0];
        }

        return false;

    }

    /**
     * @param $mlsId
     * @return array|null|object
     */
    public function getAgentByMLSId($mlsId)
    {
        global $wpdb;

        $query = "SELECT * FROM wp_agents
                  WHERE short_id LIKE '{$mlsId}' LIMIT 1";

        $agent = $wpdb->get_results($query);

        return $agent[0];
    }

    public function getAgentListings($short_ids, $orderBy = 'price', $sortBy = 'DESC')
    {
        global $wpdb;

        $query = $this->getAgentListingsQuery($short_ids);

        $results = $wpdb->get_results($query . " ORDER BY " . $orderBy . " $sortBy");

        return $results;
    }

    /**
     * @return array
     */
    public function getWeightedClassList()
    {
        // Returns a list of classes from the database sorted by most popular.
        // Unless you know a better way to perform this query, DO NOT TOUCH!
        global $wpdb;
        $query   = $this->getWeightedClassListQuery();
        $results = $wpdb->get_results($query);

        $totalResults = array();

        foreach ($results as $result) {
            if (!isset($totalResults[$result->class])) {
                $totalResults[$result->class] = $result->mycount;
            } else {
                $totalResults[$result->class] += $result->mycount;
            }
        }

        return $totalResults;

    }

    public function quickListing($mlsNumber)
    {
        global $wpdb;
        $query = "SELECT * FROM wp_bcar WHERE mls_account LIKE '{$mlsNumber}'
                  UNION
                  SELECT * FROM wp_ecar WHERE mls_account LIKE '{$mlsNumber}'
                  LIMIT 1";

        $result = $wpdb->get_results($query);

        return $result[0];

    }

    /**
     * @param $mls_number
     * @return array
     */
    public function getListing($mls_number)
    {
        global $wpdb;

        $result = $wpdb->get_results("SELECT * FROM (SELECT * FROM wp_bcar b WHERE mls_account LIKE '{$mls_number}'
                          UNION
                          SELECT * FROM wp_ecar  e WHERE mls_account LIKE '{$mls_number}') Q LIMIT 1");

        $listing = $result[0];

        return $listing;

    }

    /**
     * @param string $file
     * @return string
     */
    public function getSvg($file = '')
    {
        if ($file != '') {

            $activeTemplateDir     = get_template_directory_uri() . '/helpers/assets/';
            $templatefileRequested = $file . '.svg';

            return $activeTemplateDir . $templatefileRequested;

        }

    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     *  BELOW THIS LINE THERE BE DRAGONS! ONLY SCROLL DOWN IF YOU HATE YOUR LIFE!
     *
     *  PLEASE DON'T TOUCH UNLESS YOU HAVE BEEN ASKED BY DARON AND/OR JESUS CHRIST
     */
    /////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @param $sortBy
     * @param $orderBy
     * @return string
     */
    private function getCommercialPropertiesQuery($sortBy, $orderBy)
    {
        $query = "SELECT * FROM
                  ((SELECT b.id, b.property_type, b.status, b.state, b.preferred_image,
                  b.mls_account, b.price, b.area, b.sub_area, b.subdivision,
                  b.city, b.street_number, b.street_name, b.unit_number, b.zip,
                  b.bedrooms, b.bathrooms, b.sq_ft, b.acreage, b.class,
                  b.waterfront, b.date_modified
                  FROM wp_bcar b
                  WHERE b.property_type LIKE 'E' OR b.property_type LIKE 'F')
                  UNION
                  (SELECT e.id, e.property_type, e.status, e.state, e.preferred_image,
                  e.mls_account, e.price, e.area, e.sub_area, e.subdivision,
                  e.city, e.street_number, e.street_name, e.unit_number, e.zip,
                  e.bedrooms, e.bathrooms, e.sq_ft, e.acreage, e.class,
                  e.waterfront, e.date_modified
                  FROM wp_ecar e
                  WHERE e.status like 'Active'
                  AND e.property_type LIKE 'E' OR e.property_type LIKE 'F')) Q
                  ORDER BY Q." . $sortBy . " " . $orderBy;

        return $query;
    }

    /**
     * @return string
     */
    private function getWeightedClassListQuery()
    {
        $query = 'SELECT Q.class, Q.mycount
                    FROM (

                    SELECT t.class, tsum.mycount
                    FROM wp_bcar t
                    JOIN (

                    SELECT id, class, COUNT( class ) AS mycount
                    FROM wp_bcar
                    GROUP BY class
                    ORDER BY mycount DESC
                    )tsum ON t.id = tsum.id
                    UNION
                    SELECT e.class, esum.mycount
                    FROM wp_ecar e
                    JOIN (

                    SELECT id, class, COUNT( class ) AS mycount
                    FROM wp_ecar
                    GROUP BY class
                    ORDER BY mycount DESC
                    )esum ON e.id = esum.id

                    )Q

                     WHERE Q.class IS NOT NULL AND Q.class <> "" ORDER BY  Q.mycount DESC';

        return $query;
    }

    /**
     * @param $matchName
     * @return string
     */
    private function getHotCommunitiesQuery($matchName)
    {
        return "SELECT Q.id, Q.latitude, Q.longitude FROM
                ((SELECT e.id, e.latitude, e.longitude
                FROM wp_ecar e
                WHERE  LOWER(`city`)
                REGEXP '^" . $matchName . "$'
                OR LOWER(`sub_area`) REGEXP '^" . $matchName . "$'
                OR LOWER(`subdivision`) REGEXP '^" . $matchName . "$'
                OR LOWER(`area`) REGEXP '^" . $matchName . "$')
                UNION ALL
                (SELECT b.id, b.latitude, b.longitude
                FROM wp_bcar b
                WHERE  LOWER(`city`)
                REGEXP '^" . $matchName . "$'
                OR LOWER(`sub_area`) REGEXP '^" . $matchName . "$'
                OR LOWER(`subdivision`) REGEXP '^" . $matchName . "$'
                OR LOWER(`area`) REGEXP '^" . $matchName . "$')) Q LIMIT 1";
    }

    /**
     * @param $table
     * @param $wpdb
     * @param $result
     * @param $waterfront
     */
    private function listingTableUpdater($table, $wpdb, $result, $waterfront, $class)
    {
        if ($table == 'wp_ecar') {

            if ($class == 'A') {
                $interior              = $result['GF20131203203513863218000000'];
                $appliances            = $result['GF20131203203523234694000000'];
                $exterior              = $result['GF20131203203501805928000000'];
                $energy_features       = $result['GF20131203185526796706000000'];
                $construction          = $result['GF20131203203446527084000000'];
                $utilities             = $result['GF20131203185458688530000000'];
                $zoning                = $result['GF20131203222306734642000000'];
                $waterview_description = $result['GF20131203222538613490000000'];
                $sqft_source           = $result['LIST_58'];
            }
            if ($class == 'B') {
                $interior              = $result['GF20131230164914843719000000'];
                $appliances            = $result['GF20131230164912246391000000'];
                $exterior              = $result['GF20131230164914069211000000'];
                $energy_features       = $result['GF20131230164913550188000000'];
                $construction          = $result['GF20131230164913256545000000'];
                $utilities             = $result['GF20131230164915907956000000'];
                $zoning                = $result['GF20131230164916157466000000'];
                $waterview_description = $result['GF20131230164916093183000000'];
                $sqft_source           = $result['LIST_58'];

            }
            if ($class == 'C') {
                $interior              = null;
                $appliances            = null;
                $exterior              = null;
                $energy_features       = null;
                $construction          = $result['GF20131231201806058732000000'];
                $utilities             = $result['GF20131231131427101593000000'];
                $zoning                = $result['GF20131231131427333528000000'];
                $waterview_description = $result['GF20131231131427271202000000'];
                $sqft_source           = null;

            }
            if ($class == 'E' || $class == 'F') {
                $interior              = null;
                $appliances            = null;
                $exterior              = null;
                $energy_features       = null;
                $construction          = null;
                $utilities             = null;
                $zoning                = $result['LIST_74'];
                $waterview_description = $result['GF20140106175333111396000000'];
                $sqft_source           = null;

            }
            if ($class == 'G') {
                $interior              = $result['GF20131230211344214865000000'];
                $appliances            = $result['GF20131230211343236208000000'];
                $exterior              = $result['GF20131230211343842573000000'];
                $energy_features       = $result['GF20131230211343605075000000'];
                $construction          = $result['GF20131230211343419650000000'];
                $utilities             = null;
                $zoning                = $result['GF20131230211345452659000000'];
                $waterview_description = $result['GF20131230211345387488000000'];
                $sqft_source           = null;

            }
            if ($class == 'H') {
                $interior              = null;
                $appliances            = null;
                $exterior              = null;
                $energy_features       = null;
                $construction          = null;
                $utilities             = null;
                $zoning                = null;
                $waterview_description = $result['GF20140122222400891202000000'];
                $sqft_source           = $result['LIST_58'];

            }
            if ($class == 'I') {
                $interior              = null;
                $appliances            = null;
                $exterior              = null;
                $energy_features       = null;
                $construction          = null;
                $utilities             = null;
                $zoning                = null;
                $waterview_description = null;
                $sqft_source           = null;

            }
        } else {
            $interior              = $result['GF20131203203513863218000000'];
            $appliances            = $result['GF20131203203523234694000000'];
            $exterior              = $result['GF20131203203501805928000000'];
            $energy_features       = $result['GF20131203185526796706000000'];
            $construction          = $result['GF20131203203446527084000000'];
            $utilities             = $result['GF20131203185458688530000000'];
            $zoning                = $result['GF20131203222306734642000000'];
            $waterview_description = $result['GF20131203222538613490000000'];
            $sqft_source           = $result['LIST_58'];

        }

        $wpdb->insert($table,
            array(
                'mls_account'              => $result['LIST_3'],
                'property_type'            => $result['LIST_8'],
                'class'                    => $result['LIST_9'],
                'status'                   => $result['LIST_15'],
                'price'                    => $result['LIST_22'],
                'area'                     => preg_replace('/^[0-9]* ?- ?/', '', $result['LIST_29']),
                'street_number'            => $result['LIST_31'],
                'street_name'              => ucwords(strtolower($result['LIST_34'])),
                'unit_number'              => $result['LIST_35'],
                'city'                     => $result['LIST_39'],
                'state'                    => $result['LIST_40'],
                'zip'                      => $result['LIST_43'],
                'latitude'                 => $result['LIST_46'],
                'longitude'                => $result['LIST_47'],
                'sq_ft'                    => $result['LIST_48'],
                'acreage'                  => $result['LIST_57'],
                'bedrooms'                 => $result['LIST_66'],
                'bathrooms'                => $result['LIST_67'],
                'subdivision'              => $result['LIST_77'],
                'date_modified'            => $result['LIST_87'],
                'sub_area'                 => preg_replace('/^[0-9]* ?- ?/', '', $result['LIST_94']),
                'waterfront'               => $waterfront,
                'agent_id'                 => $result['LIST_5'],
                'colist_agent_id'          => $result['LIST_6'],
                'office_id'                => $result['LIST_106'],
                'colist_office_id'         => $result['LIST_165'],
                'list_date'                => $result['LIST_10'],
                'sold_date'                => $result['LIST_12'],
                'sold_price'               => $result['LIST_23'],
                'listing_member_shortid'   => $result['listing_member_shortid'],
                'colisting_member_shortid' => $result['colisting_member_shortid'],
                'interior'                 => $interior,
                'appliances'               => $appliances,
                'amenities'                => null,
                'exterior'                 => $exterior,
                'lot_description'          => null,
                'energy_features'          => $energy_features,
                'construction'             => $construction,
                'utilities'                => $utilities,
                'zoning'                   => $zoning,
                'waterview_description'    => $waterview_description,
                'elementary_school'        => $result['LIST_88'],
                'middle_school'            => $result['LIST_89'],
                'high_school'              => $result['LIST_90'],
                'sqft_source'              => $sqft_source,
                'year_built'               => $result['LIST_53'], // int 4
                'lot_dimensions'           => $result['LIST_56'], //
                'stories'                  => $result['LIST_64'], // decimal 6
                'full_baths'               => $result['LIST_68'], // int 6
                'half_baths'               => $result['LIST_69'], // int 6
                'last_taxes'               => $result['LIST_75'], // decimal 11
                'last_tax_year'            => $result['LIST_76'], // int 4
                'description'              => $result['LIST_78'],
                'apn'                      => $result['LIST_80'],
                'directions'               => $result['LIST_82'],

            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%f',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%f',
                '%f',
                '%f',
                '%f',
                '%d',
                '%f',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%f',
                '%s',
                '%s',
                //start adding here
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%f',
                '%d',
                '%d',
                '%f',
                '%d',
                '%s',
                '%s',
                '%s',

            )
        );
    }

    /**
     * @param $mls_number
     * @param $result
     * @param $listing
     * @param $waterfront
     * @param $rets
     * @return mixed
     */
    private function createListingObject($mls_number, $result, $listing, $waterfront, $rets)
    {
        $listing['mls_account']              = $result[0]['LIST_3'];//
        $listing['property_type']            = $result[0]['LIST_8'];//
        $listing['class']                    = $result[0]['LIST_9'];//
        $listing['status']                   = $result[0]['LIST_15'];//
        $listing['price']                    = $result[0]['LIST_22'];//
        $listing['area']                     = preg_replace('/^[0-9]* ?- ?/', '', $result[0]['LIST_29']);//
        $listing['street_number']            = $result[0]['LIST_31'];//
        $listing['street_name']              = ucwords(strtolower($result[0]['LIST_34']));//
        $listing['unit_number']              = $result[0]['LIST_35'];//
        $listing['city']                     = $result[0]['LIST_39'];//
        $listing['state']                    = $result[0]['LIST_40'];//
        $listing['county']                   = $result[0]['LIST_41'];//
        $listing['zip']                      = $result[0]['LIST_43'];//
        $listing['latitude']                 = $result[0]['LIST_46'];//
        $listing['longitude']                = $result[0]['LIST_47'];//
        $listing['sq_ft']                    = $result[0]['LIST_48'];//
        $listing['listing_member_shortid']   = $result[0]['listing_member_shortid'];//
        $listing['colisting_member_shortid'] = $result[0]['colisting_member_shortid'];//
        $listing['acreage']                  = $result[0]['LIST_57'];//
        $listing['bedrooms']                 = $result[0]['LIST_66'];//
        $listing['bathrooms']                = $result[0]['LIST_67'];//
        $listing['subdivision']              = $result[0]['LIST_77'];//
        $listing['date_modified']            = $result[0]['LIST_87'];//
        $listing['sub_area']                 = preg_replace('/^[0-9]* ?- ?/', '', $result[0]['LIST_94']);//
        $listing['waterfront']               = $waterfront;//
        $listing['agent_id']                 = $result[0]['LIST_5'];//
        $listing['colist_agent_id']          = $result[0]['LIST_6'];//
        $listing['office_id']                = $result[0]['LIST_106'];//
        $listing['colist_office_id']         = $result[0]['LIST_165'];//
        $listing['list_date']                = $result[0]['LIST_10'];//
        $listing['sold_date']                = $result[0]['LIST_12'];//
        $listing['sold_price']               = $result[0]['LIST_23'];//
        $listing['interior']                 = $result[0]['GF20150204172056869833000000'];
        $listing['appliances']               = $result[0]['GF20150204172056907082000000'];
        $listing['amenities']                = $result[0]['GF20150204172057113687000000'];
        $listing['exterior']                 = $result[0]['GF20150204172056829043000000'];
        $listing['lot_description']          = $result[0]['GF20150204172057197731000000'];
        $listing['energy_features']          = $result[0]['GF20150204172056617468000000'];
        $listing['construction']             = $result[0]['GF20150204172056790876000000'];
        $listing['utilities']                = $result[0]['GF20150204172056580165000000'];
        $listing['zoning']                   = $result[0]['GF20150204172056948623000000'];
        $listing['waterview_description']    = $result[0]['GF20150204172057026304000000'];
        $listing['elementary_school']        = $result[0]['LIST_88'];
        $listing['middle_school']            = $result[0]['LIST_89'];
        $listing['high_school']              = $result[0]['LIST_90'];
        $listing['sqft_source']              = $result[0]['LIST_146'];
        $listing['year_built']               = $result[0]['LIST_53'];
        $listing['lot_dimensions']           = $result[0]['LIST_56'];
        $listing['stories']                  = $result[0]['LIST_64'];
        $listing['full_baths']               = $result[0]['LIST_68'];
        $listing['half_baths']               = $result[0]['LIST_69'];
        $listing['last_taxes']               = $result[0]['LIST_75'];
        $listing['last_tax_year']            = $result[0]['LIST_76'];
        $listing['description']              = $result[0]['LIST_78'];
        $listing['apn']                      = $result[0]['LIST_80'];
        $listing['directions']               = $result[0]['LIST_82'];

        $photos    = $rets->GetObject('Property', 'HiRes', $mls_number, '*', 1);
        $documents = $rets->GetObject('Property', 'PDF', $mls_number, '*', 1);
        $videos    = $rets->GetObject('Property', 'UnbrandedVideo', $mls_number, '*', 1);

        foreach ($photos as $photo) {
            $listing['photos'][] = array(
                'photo_url'         => $photo->getLocation(),
                'photo_description' => $photo->getContentDescription()
            );
        }

        foreach ($documents as $document) {
            if ($document->isError()) {
                break;
            }
            $listing['documents'][] = array(
                'document_url' => $document->getLocation(),
            );
        }

        foreach ($videos as $video) {
            if ($video->isError()) {
                break;
            }
            $listing['videos'][] = array(
                'video_url' => $video->getLocation(),
            );
        }

        return (object)$listing;
    }

    /**
     * @param $short_ids
     * @return string
     */
    private function getAgentListingsQuery($short_ids)
    {
        $query = "SELECT * FROM wp_bcar WHERE 1=1";

        $query .= " AND ";
        for ($i = 0; $i < count($short_ids); $i++) {
            $shortId = (string) $short_ids[$i];

            $query .= "(listing_member_shortid LIKE '{$shortId}' OR colisting_member_shortid LIKE '{$shortId}'
            OR office_id LIKE '{$shortId}' OR colist_office_id LIKE '{$shortId}')";

            if ($i < count($short_ids) - 1) {
                $query .= ' OR ';
            }
        }

        $query .= " UNION ";

        $query .= "SELECT * FROM wp_ecar WHERE 1=1";

        $query .= " AND ";
        for ($i = 0; $i < count($short_ids); $i++) {

            $shortId = (string) $short_ids[$i];

            $query .= "( listing_member_shortid LIKE '{$shortId}' OR colisting_member_shortid LIKE '{$shortId}'
            OR office_id LIKE '{$shortId}' OR colist_office_id LIKE '{$shortId}' )";

            if ($i < count($short_ids) - 1) {
                $query .= ' OR ';
            }
        }

        return "SELECT * FROM (" . $query . ") Q GROUP BY Q.mls_account";
    }

    public function getPropertyTypes($class = null)
    {
        $typeArray = [
            'Single Family Home'   => array('Detached Single Family'),
            'Condo / Townhome'     => array('Condominium', 'Townhouse', 'Townhomes'),
            'Commercial'           => array('Office', 'Retail', 'Industrial', 'Income Producing', 'Unimproved Commercial', 'Business Only', 'Auto Repair', 'Improved Commercial', 'Hotel/Motel'),
            'Lots / Land'          => array('Vacant Land', 'Residential Lots', 'Land', 'Land/Acres', 'Lots/Land'),
            'Multi-Family Home'    => array('Duplex Multi-Units', 'Triplex Multi-Units'),
            'Rental'               => array('Apartment', 'House', 'Duplex', 'Triplex', 'Quadruplex', 'Apartments/Multi-family'),
            'Manufactured'         => array('Mobile Home', 'Mobile/Manufactured'),
            'Farms / Agricultural' => array('Farm', 'Agricultural', 'Farm/Ranch', 'Farm/Timberland'),
            'Other'                => array('Attached Single Unit', 'Attached Single Family', 'Dock/Wet Slip', 'Dry Storage', 'Mobile/Trailer Park', 'Mobile Home Park', 'Residential Income', 'Parking Space', 'RV/Mobile Park')
        ];

        if ($class != null) {
            return $typeArray[$class];
        }

        return $typeArray;
    }

}
