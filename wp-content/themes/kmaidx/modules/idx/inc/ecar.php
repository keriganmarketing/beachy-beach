<?php

/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 4/21/2017
 * Time: 9:15 AM
 */
class ecar_idx {
    public $request;
    public $response;
    public $debug = false;

    //DB INFO
    private $host = "104.218.13.63";
    private $database = "kmaserv_mls_ecar";
    private $dbuser = "kmaserv_mls";
    private $pass = "6L!J@uRwGR!A";

    //VARS
    public $variables;
    public $fieldTranslation;
    public $areaArray;
    public $cityArray;
    public $zipArray;
    public $typeArray;
    public $subdivisionArray;
    public $bedArray;
    public $bathArray;
    public $keyword;

    public function __construct() {

        try {
            $this->db = new wpdb($this->dbuser, $this->pass, $this->database, $this->host);
            $this->db->show_errors(); // Debug
        } catch (Exception $e) {    // Database Error
            echo $e->getMessage();
        }

        $this->set_var('fieldTranslation', array(
                'id'                => 'id',
                'ACREAGE'           => 'LIST_57',
                'AREA'              => 'LIST_29',
                'BATHS'             => 'LIST_67',
                'BATHS_FULL'        => 'LIST_68',
                'BATHS_HALF'        => 'LIST_69',
                'BEDROOMS'          => 'LIST_66',
                'CITY'              => 'LIST_39',
                'CO_LA_CODE'        => 'colisting_member_shortid',
                'CO_LO_CODE'        => 'colisting_office_shortid',
                'DATE_MODIFIED'     => 'LIST_87',
                'DIRECTIONS'        => 'LIST_82',
                'FTR_CONSTRC'       => 'GF20150204172056790876000000',
                'FTR_ENERGY'        => 'GF20150204172056617468000000',
                'FTR_EXTERIOR'      => 'GF20150204172056829043000000',
                'FTR_HOAINCL'       => 'LIST_151', //
                'MLS_APPROVED'      => 'LIST_4',
                'LA_CODE'           => 'listing_member_shortid',
                'LIST_DATE'         => 'LIST_10',
                'LIST_PRICE'        => 'LIST_22',
                'LOT_DIMENSIONS'    => 'LIST_56',
                'LO_CODE'           => 'listing_office_shortid',
                'MLS_ACCT'          => 'LIST_3',
                'LISTING_ID'        => 'LIST_105',
                'PARKING_FEATURES'  => 'GF20150204172057070880000000',
                'PARKING_SPACES'    => 'LIST_117',
                'PROP_TYPE'         => 'LIST_9',
                'REMARKS'           => 'LIST_78',
                'SA_CODE'           => 'LIST_62',
                'SO_NAME'           => 'LIST_61',
                'STATE'             => 'LIST_40',
                'STATUS'            => 'LIST_15',
                'STORIES'           => 'LIST_64',
                'STREET_NAME'       => 'LIST_34',
                'STREET_NUM'        => 'LIST_31',
                'SUBDIVISION'       => 'LIST_77',
                'SUB_AREA'          => 'LIST_94',
                'TOT_HEAT_SQFT'     => 'LIST_48',
                'UNIT_NUM'          => 'LIST_35',
                'WF_FEET'           => 'FEAT20150330173553939484000000',
                'YEAR_BUILT'        => 'LIST_53',
                'ZIP'               => 'LIST_43',
                'CLASS'             => 'CLASS',
                'listing_type'      => 'LIST_8',
                'NUM_UNITS'         => 'LIST_52',
                'LAT'               => 'LIST_46',
                'LNG'               => 'LIST_47',
            )
        );

        $areaList = array();
        $areas = $this->search_mls("SELECT DISTINCT LIST_29 from listings" );
        foreach($areas as $key ){
            $item = preg_replace('/\d+/u','', $key['LIST_29']);
            $item = str_replace('- ','', $item);
            $areaList[] = $item;
        }
        $subareas = $this->search_mls("SELECT DISTINCT LIST_94 from listings" );
        foreach($subareas as $key ){
            $item = preg_replace('/\d+/u','', $key['LIST_94']);
            $item = str_replace('- ','', $item);
            $areaList[] = $item;
        }
        $this->set_var('areaArray', $areaList);

        $zipList = array();
        $zips = $this->search_mls("SELECT DISTINCT LIST_43 from listings" );
        foreach($zips as $key ){
            $item = $key['LIST_43'];
            //$item = str_replace('- ','', $item);
            $zipList[] = $item;
        }
        $this->set_var('zipArray', $zipList);

        $subdivisionList = array();
        $subdivisions = $this->search_mls("SELECT DISTINCT LIST_77 from listings" );
        foreach($subdivisions as $key ){
            $item = $key['LIST_77'];
            //$item = str_replace('- ','', $item);
            $subdivisionList[] = $item;
        }
        $this->set_var('subdivisionArray', $subdivisionList);

        $cityList = array();
        $cities = $this->search_mls("SELECT DISTINCT LIST_39 from listings" );
        foreach($cities as $key ){
            $item = $key['LIST_39'];
            //$item = str_replace('- ','', $item);
            $cityList[] = $item;
        }
        $this->set_var('cityArray', $cityList);

        $typeList = array();
        $types = $this->search_mls("SELECT DISTINCT LIST_9 from listings" );
        foreach($types as $key ){
            $item = $key['LIST_9'];
            //$item = str_replace('- ','', $item);
            $typeList[] = $item;
        }
        $this->set_var('typeArray', $typeList);

        $this->set_var('priceArray', array(
                '50000'         =>'$50,000',
                '100000'        =>'$100,000',
                '150000'        =>'$150,000',
                '200000'        =>'$200,000',
                '250000'        =>'$250,000',
                '300000'        =>'$300,000',
                '350000'        =>'$350,000',
                '400000'        =>'$400,000',
                '450000'        =>'$450,000',
                '500000'        =>'$500,000',
                '550000'        =>'$550,000',
                '600000'        =>'$600,000',
                '650000'        =>'$650,000',
                '700000'        =>'$700,000',
                '750000'        =>'$750,000',
                '800000'        =>'$800,000',
                '850000'        =>'$850,000',
                '900000'        =>'$900,000',
                '1000000'       =>'$1,000,000',
                '1500000'       =>'$1,500,000',
                '2000000'       =>'$2,000,000',
                '2500000'       =>'$2,500,000',
                '3000000'       =>'$3,000,000',
                '3500000'       =>'$3,500,000',
                '4000000'       =>'$4,000,000',
                '5000000'       =>'$5,000,000'
            )
        );

        $this->set_var('bedArray', array(
                ''              => 'Any',
                '1'             => '1+',
                '2'             => '2+',
                '3'             => '3+',
                '4'             => '4+',
                '5'             => '5+'
            )
        );

        $this->set_var('bathArray', array(
                ''              => 'Any',
                '1'             => '1+',
                '2'             => '2+',
                '3'             => '3+',
                '4'             => '4+',
                '5'             => '5+'
            )
        );

        $this->set_var('statusArray', array(
                'Active'        =>'Active',
                'Sold'          =>'Sold',
                'Contingent'    =>'Contingent',
                'Pending'       =>'Pending'
            )
        );

        $this->set_var('acreageArray', array(
                '.5'            =>'1/2 or More Acres',
                '1'             =>'1 or More Acres',
                '2'             =>'2 or More Acres',
                '5'             =>'5 or More Acres',
                '10'            =>'10 or More Acres',
                '20'            =>'20 or More Acres',
                '40'            =>'40 or More Acres',
                '60'            =>'60 or More Acres',
                '80'            =>'80 or More Acres',
                '100'           =>'100 or More Acres',
                '120'           =>'120 or More Acres'
            )
        );

        $this->set_var('sqftArray', array(
                '600'           =>'600+',
                '800'           =>'800+',
                '1000'          =>'1,000+',
                '1200'          =>'1,200+',
                '1400'          =>'1,400+',
                '1600'          =>'1,600+',
                '1800'          =>'1,800+',
                '2000'          =>'2,000+',
                '2250'          =>'2,250+',
                '2250'          =>'2,200+',
                '2500'          =>'2,500+',
                '2750'          =>'2,750+',
                '3500'          =>'3,500+',
                '4000'          =>'4,000+'
            )
        );

        wp_enqueue_script( 'lazy-js' );
        wp_enqueue_script('select2-js');
        wp_enqueue_style('select2-styles');
        //add_action( 'wp_footer', 'idx_script_to_footer',100 );
        wp_enqueue_script( 'ecar-scripts' );

    }

    function set_var($var = 'request',$new_val) {
        $this->{$var} = $new_val;
    }

    function get_var($var = 'response') {
        return $this->{$var};
    }

    // Get MLS Results
    public function num_search_mls($query) {
        $result = $this->db->get_var($query);
        return $result;
    }

    public function search_mls($query) {
        $result = $this->db->get_results($query, 'ARRAY_A');
        return $result;
    }

    public function getVars(){

        foreach($this->fieldTranslation as $key => $var ){
            $this->variables[$key] = (isset($_GET[$key]) ? $_GET[$key] : '');
        }
        $this->keyword = (isset($_GET['keyword']) ? $_GET['keyword'] : '');
        return $this->variables;

    }

    public function translateRow($row){

        $newRow = array();
        foreach($this->fieldTranslation as $key => $var ){
            $newRow[$key] = $row[$var];
        }
        return $newRow;

    }

    public function untranslateRow($row){

        $newRow = array();
        foreach($this->fieldTranslation as $key => $var ){
            $newRow[$var] = $row[$key];
        }
        return $newRow;

    }

    public function translateSingle($value){
        return $this->fieldTranslation[$value];
    }

    public function untranslateSingle($value){
        return array_search($this->fieldTranslation[$value]);
    }

    public function getMedia($mlsnum,$type,$limit = ''){

        $query = "SELECT * FROM media WHERE MEDIA_TYPE='".$type."' AND MLS_ACCT='".$mlsnum."' ";
        if($type == 'Virtual Tour'){
            $query .= "ORDER BY object_id DESC LIMIT 1";
        }else{
            $query .= "ORDER BY preferred DESC, object_id ASC ";
            if($limit!=''){
                $query .= "LIMIT ".$limit;
            }
        }

        $mediaquery = $this->db->get_results($query, 'ARRAY_A');
        $mediagroup = array();

        foreach($mediaquery as $mediaresult){
            array_push($mediagroup,$mediaresult);
        }

        return $mediagroup;

    }

    public function getValues($field){
        return $this->search_mls("SELECT DISTINCT ".$field." from listings");
    }

}

add_action('wp_ajax_loadEcarIdx', 'loadEcarIdx');
add_action('wp_ajax_nopriv_loadEcarIdx', 'loadEcarIdx');
function loadEcarIdx() {

    if(isset($_SESSION['smartselect'])){

        $result = $_SESSION['smartselect'];

    } else {

        $mls = new ecar_idx();

        $result['typeArray'] = array();
        foreach ( $mls->typeArray as $areaname => $value ) {
            $result['typeArray'][] = array(
                'id'        => $value,
                'text'      => $value,
                'class'     => 'option',
            );
        }

        $result['areaArray'] = array();
        $result['areaArray'][0] = array(
            'text'          => 'AREAS',
            'children'      => array()
        );
        foreach ( $mls->areaArray as $areaname => $value ) {
            $result['areaArray'][0]['children'][] = array(
                'id'        => $value,
                'text'      => $value,
                'class'     => 'option',
            );
        }

        $result['areaArray'][1] = array(
            'text'          => 'CITIES',
            'children'      => array()
        );
        foreach ( $mls->cityArray as $areaname => $value ) {
            $result['areaArray'][1]['children'][] = array(
                'id'        => $value,
                'text'      => $value,
                'class'     => 'option',
            );
        }

        $result['areaArray'][2] = array(
            'text'          => 'SUBDIVISIONS',
            'children'      => array()
        );
        foreach ( $mls->subdivisionArray as $areaname => $value ) {
            $result['areaArray'][2]['children'][] = array(
                'id'        => $value,
                'text'      => $value,
                'class'     => 'option',
            );
        }

        $result['areaArray'][3] = array(
            'text'          => 'Zip Code',
            'children'      => array()
        );
        foreach ( $mls->zipArray as $areaname => $value ) {
            $result['areaArray'][3]['children'][] = array(
                'id'        => $value,
                'text'      => $value,
                'class'     => 'option',
            );
        }

        $_SESSION['smartselect']        = json_encode( $result );
        $result                         = json_encode( $result );

    }

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo $result;
    }

    wp_die();

}

wp_register_script( "ecar-scripts", get_template_directory_uri() . '/modules/idx/scripts/ecar.ajax.js', array('jquery'), '0.0.0' , true );
wp_localize_script( 'ecar-scripts', 'wpAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

