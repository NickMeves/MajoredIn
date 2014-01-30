<?php
/**
 * Is Authd
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Ajax_Action_GetAuth")){
	class Pdr_Ajax_Action_GetAuth extends Pdr_AjaxAction{
		public function action()
		{
			global $PDR_UTIL;
			$auth_hash = get_option('pdrp_hash', false);
			if ($auth_hash) {
				$query = $_SERVER['QUERY_STRING'];
				$auth_id = $PDR_UTIL->getAuthId();
				$query .= '&auth_id=' . $auth_id . '&auth_hash=' . $auth_hash;
				$rsp = $PDR_UTIL->get('http://photodropper.com/api?action=getAuth&' . $query);
				$rsp = substr($rsp, strpos($rsp, '(') + 1);
				$rsp = json_decode(substr($rsp, 0, strlen($rsp) - 1));
				if (isset($rsp->hash)) {
					$this->rsp = array("hash" => $rsp->hash);
				}
			}
			$this->suc = 1;
		}
	}
}