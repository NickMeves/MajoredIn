<?php	
/**
 * Class that provides general support for PD
 * @package PhotoDropper
 */
if(!class_exists("Pdr_Utility")){
	class Pdr_Core_Utility{

		/**
		 * Reference to singleton Pdr_Utilities class
		 * @var Pdr_Utilities Singleton's self-reference
		 */
		private static $instance;

		/**
		 * Constructor for utilities class
		 */
		private function __construct()
		{
		}

		/**
		 * Get instance of singletong Pdr_Utilities class
		 * @return Pdr_Utilities Object that references singleton class
		 */
		public function getInstance()
		{
			if(!isset(self::$instance)){
	            $c = __CLASS__;
	            self::$instance = new $c;
			}
			return self::$instance;
		}

		/***
		 * 
		 * Utilities
		 * 
		 ***/

		/**
		 * Return table with proper prefixes
		 * @global Wpdb $wpdb
		 * @param string $name
		 * @return string 
		 */
		public function t($name, $wpnative = false)
		{
			global $wpdb;
			return $wpdb->prefix . (!$wpnative ? "pdrp_" : '') . $name;
		}		

		/**
		 * Render a model using a particular view, optionally with passed in filler
		 * @param string $model The model being rendered
		 * @param string $view The name of the model's view to use
		 * @param array|object $filler The keys/values used by the template
		 * @return string Finished HTML
		 */
		public function tpl($model, $view, $filler = array()) {
			if (is_object($model)) {
				$className = str_replace('Pdr_Model_', '', get_class($model));
				$modelName = array();
				foreach (explode('_', $className) as $name) {
    				$modelName[] = lcfirst($name);
				}
				$modelName = implode('/', $modelName);
			}
			if (method_exists($model, 'toArray')) {
				foreach ($model->toArray() as $i => $v) {
					$filler[$i] = $v;
				}
			}
			foreach ($filler as $i => $v) {
    			${$i} = $v;
			}
			ob_start();
			include PDR_APP . '/models/views/' . $modelName . '/' . $view . '.html.php';
			return ob_get_clean();
		}

		/**
		 * Check a general mark (used for pointers)
		 * @param string $mark
		 * @return boolean|string
		 */
		public function isMarked($markStr)
		{
			$mark = 'pdrp_mark_' . $markStr;
			if (!isset($_COOKIE[$mark])) {
				if (!get_option($mark, 0)) {
					return false;
				}
				else {
					$this->setMark($markStr);
				}
			}
			return true;
		}

		/**
		 * Check a general mark (used for pointers)
		 * @param string $mark
		 * @return boolean
		 */
		public function get_option($name, $default = false)
		{
			$option = 'pdrp_' . str_replace('pdrp_', '', $name);
			$val = get_option($option, false); 
			return $val;
			if (!isset($_COOKIE['pdrp_options'])) {
				$options = new StdClass();
			}
			else {
				$options =  json_decode(base64_decode($_COOKIE['pdrp_options']));
			}
			if (isset($options->$option)) {
				return $options->$option;
			}
			else {
				if ($val) {
					$options->$option = $val;
					setcookie('pdrp_options', base64_encode(json_encode($options)), time() + 50000, '/', null, null, true); 
					return $val;
				}
				else {
					return $default;
				}
			}
			return true; 
		}

		public function setMark($mark)
		{
			$mark = 'pdrp_mark_' . $mark;
			$domain = current(explode('/', str_replace('http://', '', site_url())));
			update_option($mark, 1);
			setcookie($mark, '1', time() +10000000, '/', $domain, null, true);
		}

		/**
		 * Check if a value is set otherwise use second val
		 * @param mixed $var The value to check
		 * @param mixed $val THe value to use it var isn't set
		 * @return mixed
		 */
		function ifSet($var, $val) {
			return isset($var) ? $var : $val;
		}

		/**
		 *
		 * @param array $defaults An array of default values
		 * @param array $usertext An array of usertext
		 * @return array Defaults merged with usertext 
		 */
		public function applyUsertext($defaults = array(), $usertext = array())
		{
			foreach ($usertext as $i => $v) {
    			$defaults[$i] = $v;
			}
			return $defaults;
		}

		/**
		 * A scandir function that removes ./, ../ by default
		 * and allows a sort callback
		 * @param string $dir
		 * @param string|array $sort Sort callback
		 * @return array An array of file contents
		 */
		public function scandir($dir, $sort = false)
		{
			$contents = array();
			foreach (scandir($dir) as $file) {
				if ($file !== '.' && $file !== '..') {
					$contents[] = $file;
				}
			}
			if ($sort) {
				usort($contents, $sort);
			}
			return $contents;
		}

		/**
		 * Equivalent to file_get_contents but uses cURL if possible
		 * @param string $url
		 * @return string 
		 */
		public function get($url)
		{
			// Use CURL if possible - some hosts don't allow
			// file_get_contents
			if (function_exists('curl_init')) {
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_HEADER, 0); 
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
				$image = curl_exec($ch);
				curl_close($ch); 
			} else {
				if ((int)ini_get('allow_url_fopen')) {
					$image = file_get_contents($url);
				}
				else {
					$image = "Your webhost doesn't allow image downloading!";
				}
			}
			return $image;
		}	

		/**
		 * Get a token to reference this domain that should change very rarely
		 * and not rely on anything in the database
		 */
		public function getAuthId()
		{
			return sha1(SECURE_AUTH_KEY . $_SERVER['SERVER_ADDR']);
		}

		/**
		 * Take out all any non alnum characters
		 * @param string $unfiltered 
		 * @return string
		 */
		public function alnum($unfiltered)
		{
			return preg_replace('/[^a-zA-Z0-9\s]/', '', $unfiltered);
		}
	}
}

if (class_exists('Pdr_Core_Utility')) {
	global $PDR_UTIL;
	$PDR_UTIL = Pdr_Core_Utility::getInstance();
}
