<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
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
defined('_WP_EXEC') or die('Restricted access');

class JchPlatformCache implements JchInterfaceCache
{

        protected static $wp_filesystem;

        /**
         * 
         * @param type $id
         * @return type
         */
        public static function getCache($id, $checkexpire=false)
        {
                $wp_filesystem = self::getWpFileSystem();
                
                if($wp_filesystem === false)
                {
                        return false;
                }
                
                $file = self::_getFileName($id);

                if (!$wp_filesystem->exists($file))
                {
                        return false;
                }

		if ($checkexpire && time() > $wp_filesystem->mtime($file) + self::getLifetime())
		{
			return false;
		}

                return self::_getCacheFile($file, $wp_filesystem);
        }

        /**
         * 
         * @param type $id
         * @param type $function
         * @param type $args
         * @return type
         */
        public static function getCallbackCache($id, $function, $args)
        {
                $wp_filesystem = self::getWpFileSystem();
                
                if($wp_filesystem === false)
                {
                        return false;
                }

                $file = self::_getFileName($id);

                if (!self::getCache($id, true))
                {
			$contents = call_user_func_array($function, $args);

			return self::saveCache($contents, $id);
                }

                return self::_getCacheFile($file, $wp_filesystem);
        }

        /**
         * 
         * @param type $type
         * @return type
         */
        public static function deleteCache($pagecacheonly=false)
        {
		//Purge LiteSpeed cache
		if (class_exists('LiteSpeed_Cache_API'))
		{
			LiteSpeed_Cache_API::purge_all();
		}

                $wp_filesystem = self::getWpFileSystem();

                if ($wp_filesystem === false || !$wp_filesystem->exists(JCH_CACHE_DIR))
                {
                        return false;
                }

		$cache_dir = dirname(JCH_CACHE_DIR);
		//Get list of all folders in the cache directory (.../wp-content/cache/)
		$cache_dir_list = $wp_filesystem->dirlist($cache_dir, false, false);

		foreach($cache_dir_list as $entry)
		{
			//Skip the jch-optimize cache if we're only deleting page cache
			if($entry['name'] == 'jch-optimize' && $pagecacheonly)
			{
				continue;
			}

			if($entry['type'] == 'd')
			{
				//Delete each cache folder, including JCH Optimize cache
				if(!$wp_filesystem->rmdir($cache_dir . DIRECTORY_SEPARATOR . $entry['name'], true))
				{
					return false;
				}
			}
		}

		return true;
        }


        /**
         * 
         * @param type $file
         * @return type
         */
        private static function _getCacheFile($file, $wp_filesystem)
        {
                $content = $wp_filesystem->get_contents($file);

                return unserialize(base64_decode($content));
        }

        /**
         * 
         */
        public static function initializeCache()
        {
                $wp_filesystem = self::getWpFileSystem();

                if ($wp_filesystem !== false && !$wp_filesystem->exists(JCH_CACHE_DIR))
                {
                        if (!$wp_filesystem->exists($wp_filesystem->wp_content_dir() . 'cache'))
                        {
                                $wp_filesystem->mkdir($wp_filesystem->wp_content_dir() . 'cache', FS_CHMOD_DIR);
                        }

                        $wp_filesystem->mkdir(JCH_CACHE_DIR, FS_CHMOD_DIR);

                        $index = JCH_CACHE_DIR . 'index.html';
                        $wp_filesystem->put_contents($index, '<html><body></body></html>', FS_CHMOD_FILE);
                }
        }

        /**
         * 
         * @global type $wp_filesystem
         * @return type
         */
        public static function getWpFileSystem()
        {
                if (!isset(self::$wp_filesystem))
                {
                        global $wp_filesystem;
                        
                        $wp_filesystem_cache = $wp_filesystem;

                        if (!function_exists('request_filesystem_credentials'))
                        {
                                include_once JchPlatformPaths::rootPath() . 'wp-admin/includes/file.php';
                        }

                        add_filter('request_filesystem_credentials', array('JchPlatformCache', 'requestFilesystemCredentials'), 10, 7);
                        
                        if (false === ($creds = request_filesystem_credentials(admin_url('options-general.php?page=jchoptimize-settings'), '', false,
                                                                                         WP_CONTENT_DIR, null, true)))
                        {
                                $message = 'The plugin needs to access the filesystem via FTP. Please define your FTP credentials in your wp_config.php file like this: <br /><br /><textarea disabled="disabled" rows="3" cols="40" style="background-color: ghostwhite; color: #444; resize: none;">define(\'FTP_HOST\', \'[Your FTP host]\'); &#13;&#10;define(\'FTP_USER\', \'[Your FTP Username]\'); &#13;&#10;define(\'FTP_PASS\', \'[Your FTP password]\'); </textarea>';

                                self::$wp_filesystem = false;
                                
                                if (function_exists('jch_add_notices'))
                                {
                                        jch_add_notices('error', __($message, 'jch-optimize'));
                                        
                                        return false;
                                }
                                else
                                {
                                        throw new Exception(__($message, 'jch-optimize'));
                                }
                        }

                        if (false === WP_Filesystem($creds, WP_CONTENT_DIR, true))
                        {
                                $message = 'Could not connect to the filesystem. Please check your FTP credentials in wp_config.php file';

                                if (function_exists('jch_add_notices'))
                                {
                                        jch_add_notices('error', __($message, 'jch-optimize'));
                                        
                                        return false;
                                }
                                else
                                {
                                        throw new Exception(__($message));
                                }
                        }
                        
                        self::$wp_filesystem = $wp_filesystem;
                       
                        if(!defined('JCH_CACHE_DIR'))
                        {
                                define('JCH_CACHE_DIR', $wp_filesystem->wp_content_dir() . 'cache/jch-optimize/');
                        }
                        
                        $wp_filesystem = $wp_filesystem_cache;
                }
                
                return self::$wp_filesystem;
        }

        /**
         * 
         */
        public static function requestFilesystemCredentials($value, $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership)
        {
                $method = get_filesystem_method(array(), $context, $allow_relaxed_file_ownership);

                if ($method == 'direct')
                {
                        return true;
                }

		if(!function_exists('wp_generate_password'))
		{
			include_once JchPlatformPaths::rootPath() . 'wp-includes/pluggable.php';
		}

                $credentials = get_option('ftp_credentials', array('hostname' => '', 'username' => ''));

                $credentials['hostname'] = defined('FTP_HOST') ? FTP_HOST : $credentials['hostname'];
                $credentials['username'] = defined('FTP_USER') ? FTP_USER : $credentials['username'];
                $credentials['password'] = defined('FTP_PASS') ? FTP_PASS : '';

                if ($method == 'ssh2')
                {
                        // Check to see if we are setting the public/private keys for ssh
                        $credentials['public_key']  = defined('FTP_PUBKEY') ? FTP_PUBKEY : '';
                        $credentials['private_key'] = defined('FTP_PRIKEY') ? FTP_PRIKEY : '';
                }

                if (in_array('', $credentials))
                {
                        return false;
                }

                return $credentials;
        }

        /**
         * 
         * @param type $id
         * @return type
         */
        private static function _getFileName($id)
        {
                return JCH_CACHE_DIR . md5(NONCE_SALT . $id);
        }

        /**
         * 
         */
        public static function gc()
        {
                $wp_filesystem = self::getWpFileSystem();
                
                if($wp_filesystem === false)
                {
                        return false;
                }

                $result = true;

                $files = JchPlatformUtility::lsFiles(rtrim(JCH_CACHE_DIR, '/\\'), '.', TRUE);
                $now   = time();

                foreach ($files as $file)
                {
                        $time = $wp_filesystem->mtime($file);

                        if (($time + self::getLifetime()) < $now || empty($time))
                        {
                                $result |= $wp_filesystem->delete($file);
                        }
                }

		//Delete any page cache
		$result |= self::deleteCache(true);


                return $result;
        }

	/**
	 *
	 *
	 */
	public static function saveCache($content, $id)
	{
                $wp_filesystem = self::getWpFileSystem();
		$filecontents = base64_encode(serialize($content));
		$file = self::_getFileName($id);

		self::initializeCache();

		if ($wp_filesystem->put_contents($file, $filecontents, FS_CHMOD_FILE))
		{
			return $content;
		}
		else
		{
			throw new Exception(__('Error writing files to cache'));
		}
	}

	protected static function getLifetime()
	{
		static $lifetime;

		if(!$lifetime)
		{
			$params = JchPlatformPlugin::getPluginParams();

			$lifetime = $params->get('cache_lifetime', '900');
		}

		return (int) $lifetime;
	}
}
