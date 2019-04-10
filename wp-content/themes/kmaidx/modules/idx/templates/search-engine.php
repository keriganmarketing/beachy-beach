<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 4/28/2017
 * Time: 4:33 PM
 */
//CONTROL SEARCH & SESSION
if($_POST['cmd'] == 'search') {

	//GET VARS FROM SEARCH
	$area         = ( isset( $_REQUEST['AREA'] )            ? $_REQUEST['AREA'] : null );
	$propertytype = ( isset( $_REQUEST['PROP_TYPE'] )       ? $_REQUEST['PROP_TYPE'] : null );
	$minprice     = ( isset( $_REQUEST['PRICE_MIN'] )       ? $_REQUEST['PRICE_MIN'] : null );
	$maxprice     = ( isset( $_REQUEST['PRICE_MAX'] )       ? $_REQUEST['PRICE_MAX'] : null );
	$beds         = ( isset( $_REQUEST['BEDROOMS'] )        ? $_REQUEST['BEDROOMS'] : null );
	$baths        = ( isset( $_REQUEST['BATHS'] )           ? $_REQUEST['BATHS'] : null );
	$sqft         = ( isset( $_REQUEST['TOT_HEAT_SQFT'] )   ? $_REQUEST['TOT_HEAT_SQFT'] : null );
	$acreage      = ( isset( $_REQUEST['ACREAGE'] )         ? $_REQUEST['ACREAGE'] : null );

	//SAVE NEW INFO TO SESSION
	$_SESSION['AREA']           = $area;
	$_SESSION['PROP_TYPE']      = $propertytype;
	$_SESSION['PRICE_MIN']      = $minprice;
	$_SESSION['PRICE_MAX']      = $maxprice;
	$_SESSION['BEDROOMS']       = $beds;
	$_SESSION['BATHS']          = $baths;
	$_SESSION['TOT_HEAT_SQFT']  = $sqft;
	$_SESSION['ACREAGE']        = $acreage;

	//print('reading search form.');

} else {

	//GET VARS FROM SESSION
	$area         = $_SESSION['AREA'];
	$propertytype = $_SESSION['PROP_TYPE'];
	$minprice     = $_SESSION['PRICE_MIN'];
	$maxprice     = $_SESSION['PRICE_MAX'];
	$beds         = $_SESSION['BEDROOMS'];
	$baths        = $_SESSION['BATHS'];
	$sqft         = $_SESSION['TOT_HEAT_SQFT'];
	$acreage      = $_SESSION['ACREAGE'];

	//print( 'reading session.' );

}

if($_GET['remove']!=''){
	$_SESSION[$_GET['remove']] = '';

	$currentUrl = $_SERVER['SELF'];
	//header("HTTP/1.1 303 See Other");
	//header("Location: $currentUrl");

}
//print_r($_SESSION);

//GENERATE QUERY
$query = " from listings WHERE 1=1 ";

if(isset($area) && $area!= '') {

    $query .= "AND ( ";
    if(is_array($area)){
        for($i=0;$i<count($area);$i++){
            $query .= " " . $mls->translateSingle('AREA') .        " LIKE '%" . $area[$i] . "%' 
                     OR " . $mls->translateSingle('SUB_AREA') .    " LIKE '%" . $area[$i] . "%' 
                     OR " . $mls->translateSingle('SUBDIVISION') . " LIKE '%" . $area[$i] . "%' 
                     OR " . $mls->translateSingle('CITY') .        " LIKE '%" . $area[$i] . "%' 
                     OR " . $mls->translateSingle('ZIP') .         " LIKE '%" . $area[$i] . "%' ";

            if($i != count($area) - 1){ $query .= " OR "; }
        }
    }
    $query .= " ) ";

}

if(isset($propertytype) && $propertytype != '') {

    $query .= "AND ( ";
    if(is_array($propertytype)){
        for($i=0;$i<count($propertytype);$i++){
            $query .= " " . $mls->translateSingle('PROP_TYPE') . " LIKE '%" . $propertytype[$i] . "%' ";

            if($i != count($propertytype) - 1){ $query .= " OR "; }
        }
    }
    $query .= " ) ";

}

if(isset($minprice) && $minprice != '') {   $query .= "AND ( " . $mls->translateSingle('LIST_PRICE') .    " > " . $minprice . " ) "; }
if(isset($maxprice) && $maxprice != '') {   $query .= "AND ( " . $mls->translateSingle('LIST_PRICE') .    " < " . $maxprice . " ) "; }
if(isset($beds) && $beds != '') {           $query .= "AND ( " . $mls->translateSingle('BEDROOMS') .      " > " . $beds . " ) "; }
if(isset($baths) && $baths != '') {         $query .= "AND ( " . $mls->translateSingle('BATHS') .         " > " . $baths . " ) "; }
if(isset($sqft) && $sqft != '') {           $query .= "AND ( " . $mls->translateSingle('TOT_HEAT_SQFT') . " > " . $sqft . " ) "; }
if(isset($acreage) && $acreage != '') {     $query .= "AND ( " . $mls->translateSingle('ACREAGE') .       " > " . $acreage . " ) "; }


$numQuery = "SELECT COUNT(id) ".$query;
$realQuery = "SELECT * ".$query;

$numresults = $mls->num_search_mls($numQuery);

$numrows = (int)$numresults;
$resultsperpage = 36;
$numpages = ceil($numrows/$resultsperpage);

if(!isset($pageNum)){ $pageNum = 1; }
if(!isset($sortby)){ $sortby = $mls->translateSingle('DATE_MODIFIED'); }

$realQuery .= " ORDER BY " . $sortby;
$realQuery .= " LIMIT " . (($pageNum - 1) * 36) . ", 36";