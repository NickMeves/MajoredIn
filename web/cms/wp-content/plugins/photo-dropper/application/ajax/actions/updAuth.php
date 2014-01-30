<?php
/**
 * Is Authd
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Ajax_Action_UpdAuth")){
	class Pdr_Ajax_Action_UpdAuth extends Pdr_AjaxAction{
		public function action()
		{
			global $PDR_UTIL;
			$hash = $PDR_UTIL->alnum($_REQUEST['auth_hash']);
			$old_id = $PDR_UTIL->alnum($_REQUEST['auth_id']);
			if (strlen($hash) == 40 && strlen($old_id) == 32) {

				// Store the hash locally
				$auth_hash = update_option('pdrp_hash', $hash);

				// Get an ID that is unlikely to change but not from the DB
				// and pass back to PhotoDropper
				$auth_id = $PDR_UTIL->getAuthId();
				$rsp = $PDR_UTIL->get(
					'http://photodropper.com/api?action=updAuthId&old_id=' 
					. $old_id . '&auth_id=' . $auth_id . '&auth_hash=' . $hash
				);
			}
		}
	}
}