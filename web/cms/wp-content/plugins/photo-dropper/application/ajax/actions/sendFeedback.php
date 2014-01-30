<?php
/**
 * Handle settings
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Ajax_Action_SendFeedback")){
	class Pdr_Ajax_Action_SendFeedback extends Pdr_AjaxAction{

		/**
		 * Handle Settings
		 */
		public function action()
		{
			global $PDR_UTIL;
			if (isset($this->p['msg'])) {
				if( !class_exists( 'WP_Http' ) ) {
					include_once( ABSPATH . WPINC. '/class-http.php' );
				}
				$request = new WP_Http;
				$url = 'http://app.photodropper.com/api/';
				$body = array('action' => 'add_feedback', 'domain' => site_url(), 'feedback' => urlencode($this->p['msg']));
				$result = $request->request( $url, array( 'method' => 'POST', 'body' => $body) );
			}
		}
	}
}