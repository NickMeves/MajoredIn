<?php
if(!class_exists("Pdr_Ajax")){
	class Pdr_Ajax{
		public function __construct()
		{
			$ajax_dir = PDR_APP . '/ajax';
			require_once($ajax_dir . '/AjaxAction.php');
			$this->action = str_replace('pdr_', '', $_REQUEST['action']);
			if (file_exists($ajax_dir . '/actions/' . $this->action . '.php')) {
				require_once($ajax_dir . '/actions/' . $this->action . '.php');
				$actionClass = 'Pdr_Ajax_Action_' . ucfirst($this->action);
				$action = new $actionClass($_REQUEST);
				add_action('wp_ajax_' . $_REQUEST['action'], array(&$action, 'controller'));
				if (isset($action->public) && $action->public) {
					add_action('wp_ajax_nopriv_' . $_REQUEST['action'], array(&$action, 'controller'));
				}
			}
			else {
				return false;
			}
		}
	}
}
$pdr = new Pdr_Ajax();