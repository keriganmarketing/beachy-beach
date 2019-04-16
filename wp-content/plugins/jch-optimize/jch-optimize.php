<?php

/**
 * Plugin Name: JCH Optimize
 * Plugin URI: http://www.jch-optimize.net/
 * Description: This plugin aggregates and minifies CSS and Javascript files for optimized page download
 * Version: 2.4.2
 * Author: Samuel Marshall
 * License: GNU/GPLv3
 * Text Domain: jch-optimize
 * Domain Path: /languages
 * 
 */
/**
 * JCH Optimize - Plugin to aggregate and minify external resources for
 * optmized downloads
 *
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2014 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
$jch_backend = filter_input(INPUT_GET, 'jchbackend', FILTER_SANITIZE_STRING);
$jch_no_optimize = false;

define('_WP_EXEC', '1');

define('JCH_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JCH_PLUGIN_DIR', plugin_dir_path(__FILE__));


if (!defined('JCH_VERSION'))
{
        define('JCH_VERSION', '2.4.2');
}

require_once(JCH_PLUGIN_DIR . 'jchoptimize/loader.php');

//Handles activation routines
include_once JCH_PLUGIN_DIR. 'jchplugininstaller.php';
$JchPluginInstaller = new JchPluginInstaller();
register_activation_hook(__FILE__, array($JchPluginInstaller, 'activate'));

if (!file_exists(dirname(__FILE__) . '/dir.php'))
{
        $JchPluginInstaller->activate();
}

if (is_admin())
{
        require_once(JCH_PLUGIN_DIR . 'options.php');
}
else
{
        $params = JchPlatformPlugin::getPluginParams();
        $url_exclude = $params->get('url_exclude', array());
               
        if (defined('WP_USE_THEMES')
                && WP_USE_THEMES
                && $jch_backend != 1
                && version_compare(PHP_VERSION, '5.3.0', '>=')
                && !defined('DOING_AJAX')
                && !defined('DOING_CRON')
                && !defined('APP_REQUEST')
                && !defined('XMLRPC_REQUEST')
                && (!defined('SHORTINIT') || (defined('SHORTINIT') && !SHORTINIT))
                && !JchOptimizeHelper::findExcludes($url_exclude, JchPlatformUri::getInstance()->toString()))
        {
                add_action('init', 'jch_buffer_start', 0);
                add_action('template_redirect', 'jch_buffer_start', 0);
                add_action('shutdown', 'jch_buffer_end', -1);

		//Disable NextGen Resource Manager; incompatible with plugin
		//add_filter( 'run_ngg_resource_manager', '__return_false' );
        }
}

function jch_load_plugin_textdomain()
{
        load_plugin_textdomain('jch-optimize', FALSE, basename(dirname(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'jch_load_plugin_textdomain');

function jchoptimize($sHtml)
{
        global $jch_no_optimize;
        
        if($jch_no_optimize)
        {
                return $sHtml;
        }
        
        $params = JchPlatformPlugin::getPluginParams();
        
        try
        {
                $sOptimizedHtml = JchOptimize::optimize($params, $sHtml);
        }
        catch (Exception $e)
        {
                JchOptimizeLogger::log($e->getMessage(), $params);

                $sOptimizedHtml = $sHtml;
        }

        return $sOptimizedHtml;
}

function jch_buffer_start()
{
	JchOptimizePagecache::initialize();

        ob_start();
}

function jch_buffer_end()
{
	//Iterate through all active buffers
        while ($level = ob_get_level())
        {
		//Need to access the final buffer, apparently using ob_get_status is more reliable
		$buffer_status = ob_get_status();

		//If there's a valid HTML at the last buffer, optimize, cache and exit loop
		if ($buffer_status['level'] == '1' && JchOptimizeHelper::validateHtml(ob_get_contents()))
		{
			//Retrieve and erase the contents of buffer
			$sHtml = ob_get_clean();
			//Optimize and store HTML
			$sOptimizedHtml = jchoptimize($sHtml);
			JchOptimizePagecache::store($sOptimizedHtml);
			
			//Send optimized HTML to browser
			echo $sOptimizedHtml;

			//Exit loop
			break;
		}

		ob_end_flush();

                //buffer not turned off for some reason.
                if ($level == ob_get_level())
                {
                        break;
                }
        }
}

add_filter('plugin_action_links', 'jch_plugin_action_links', 10, 2);

function jch_plugin_action_links($links, $file)
{
        static $this_plugin;

        if (!$this_plugin)
        {
                $this_plugin = plugin_basename(__FILE__);
        }

        if ($file == $this_plugin)
        {
                $settings_link = '<a href="' . admin_url('options-general.php?page=jchoptimize-settings') . '">' . __('Settings') . '</a>';
                array_unshift($links, $settings_link);
        }

        return $links;
}

function jch_optimize_uninstall()
{
        delete_option('jch_options');

        JchPlatformCache::deleteCache();
	JchOptimizeAdmin::cleanHtaccess();
}

register_uninstall_hook(__FILE__, 'jch_optimize_uninstall');


