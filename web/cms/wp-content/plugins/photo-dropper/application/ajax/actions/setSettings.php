<?php
/**
 * Handle settings
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Ajax_Action_SetSettings")){
	class Pdr_Ajax_Action_SetSettings extends Pdr_AjaxAction{

		/**
		 * Handle Settings
		 */
		public function action()
		{
			global $PDR_UTIL;
			if (isset($this->p['commercial'])) {
				update_option('pdrp_commercial', $this->p['commercial'] * 1);
			}
			//$PDR_UTIL->setMark('welcome');
			if (isset($this->p['email'])) {
				if( !class_exists( 'WP_Http' ) ) {
					include_once( ABSPATH . WPINC. '/class-http.php' );
				}
				$request = new WP_Http;
				$url = 'http://app.photodropper.com/api/';
				$body = array('action' => 'welcome_signup', 'domain' => site_url(), 'email' => $this->p['email']);

				$result = $request->request( $url, array( 'method' => 'POST', 'body' => $body) );
			}
			$this->suc = 1;
		}
	}
}