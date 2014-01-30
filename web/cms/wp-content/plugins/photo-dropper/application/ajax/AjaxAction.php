<?php
if(!class_exists("Pdr_AjaxAction")){
	class Pdr_AjaxAction{

		/*
		 * Create an AjaxAction, parse $_REQUEST into parameters
		 */
		public function __construct()
		{
			foreach ($_REQUEST as $i => $v) {
				$this->p[$i] = $v;
			}
			$this->redirect = false;
			$this->hubAction = false;
			$this->forceRedirect = false;
		}

		/*
		 * Call the AjaxAction, normalize any required values and finish
		 */
		public function controller()
		{
			$this->action();
			if (!isset($this->msg)) {
				$this->msg = "";
			}
			$this->finish();
		}

		/*
		 * Merge response values together, encode as JSON and send
		 */
		public function finish()
		{
			if ($this->redirect && ($this->hubAction || $this->forceRedirect)) {
				header("Location: " . $this->redirect);
				exit;
			}
			$rsp = array('msg' => $this->msg);
			if(isset($this->rsp)){
				foreach ($this->rsp as $i => $v) {
					$rsp[$i] = $v;
				}
			}
			if (isset($this->suc) && !isset($this->err)) {
				$rsp['suc'] = $this->suc;
			}
			if (isset($this->err)) {
				$rsp['err'] = $this->err;
			}
			if (!isset($this->err) && !isset($this->suc)) {
				$this->suc = 1;
			}
			$rsp = json_encode($rsp);
			die($rsp);
		}												
	}
}


