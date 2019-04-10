<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

header("Content-type: text/css");

if (file_exists(wp_normalize_path('social/social.css'))){
	echo file_get_contents(wp_normalize_path('social/social.css'));
}
if (file_exists(wp_normalize_path('slider/slider.css'))){
	echo file_get_contents(wp_normalize_path('slider/slider.css'));
}
if (file_exists(wp_normalize_path('idx/idx.css'))){
	echo file_get_contents(wp_normalize_path('idx/idx.css'));
}
if (file_exists(wp_normalize_path('../css/typography.css'))){
	echo file_get_contents(wp_normalize_path('../css/typography.css'));
}

