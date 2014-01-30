<?php
/**
 * Mark a setting in the DB
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Ajax_Action_Mark")){
	class Pdr_Ajax_Action_Mark extends Pdr_AjaxAction{

		/**
		 * Mark a setting in the DB
		 */
		public function action()
		{
			global $PDR_UTIL;
			$PDR_UTIL->setMark($this->p['mark']);
			$this->rsp = array("marked" => true);
		}
	}
}