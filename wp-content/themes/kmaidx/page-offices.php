<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package idx
 */

get_header();

date_default_timezone_set('America/Chicago');

require_once("helpers/MLS.php");

$mls = new MLS();

$mls->updateAllAgents();

get_footer();
