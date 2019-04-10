<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 4/21/2017
 * Time: 9:03 AM
 */

class URIInfo {
	public $info;
	public $header;
	private $url;

	public function __construct($url) {
		$this->url = $url;
		$this->setData();
	}

	public function setData() {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_FILETIME, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		$this->header = curl_exec($curl);
		$this->info = curl_getinfo($curl);
		curl_close($curl);
	}

	public function getFiletime() {
		return $this->info['filetime'];
	}

	// Other functions can be added to retrieve other information.
}

/// CORE FUNCTIONS
function getMlsTemplateFile($file = '', $include = TRUE){

	if($file!=''){

		$activeTemplateDir = get_template_directory_uri().'/modules/idx/templates/';
		$templatefileRequested = $file.'.php';

		$relativeLink = wp_make_link_relative($activeTemplateDir.$templatefileRequested);

		if($include){
			include($relativeLink);
		}else{
			return file_get_contents($relativeLink);
		}

	}

}

function getSvg($file = ''){

	if($file!=''){

		$activeTemplateDir = get_template_directory_uri().'/modules/idx/assets/';
		$templatefileRequested = $file.'.svg';

		return $activeTemplateDir.$templatefileRequested;

	}

}

include ('inc/rafgc.php');
include ('inc/bcar.php');
include ('inc/ecar.php');

//create virtual page
include('inc/virtual_page.php');

$vp =  new virtual_page();
$vp->add('/listing/', 'listing_func');

// Example of content generating function
// Must set $this->body even if empty string
function listing_func($v, $url) {
	// extract an id from the URL
	$id = 'none';
	if (preg_match('(\d{6})', $_SERVER['PHP_SELF'], $m))
	    print('got here');
	    $id = $m[0];

	// could wp_die() if id not extracted successfully...
	$v->title = "My Virtual Page Title";
	$v->body = "Some body content for my virtual page test - id $id\n";
	$v->template = 'page'; // optional
	$v->subtemplate = 'test'; // optional

}
