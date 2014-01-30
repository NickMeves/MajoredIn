<?php
/**
 * Is Authd
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Ajax_Action_Logout")){
	class Pdr_Ajax_Action_Logout extends Pdr_AjaxAction{
		public function action()
		{
			global $PDR_UTIL;
			delete_option('pdrp_hash');
			$this->suc = 1;
		}
	}
}