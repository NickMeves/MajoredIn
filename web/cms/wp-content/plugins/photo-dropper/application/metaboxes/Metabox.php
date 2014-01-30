<?php
/**
 * Base Class that a metabox should extend
 */
if(!class_exists("Pdr_Metabox")){
	class Pdr_Metabox{

		/*
		 * Create shortcode
		 */
		public function __construct($opts)
		{
			$this->init($opts);
			add_action('add_meta_boxes', array(&$this, 'hook'));
		}

		/**
		 * Use this to set defaults (over-written by child)
		 */
		private function init($opts)
		{
			$this->autosave = false;
			foreach ($opts as $key => $val) {
				$this->$key = $val;
			}

			// Normalize any permutation of type input
			// to an array of types
			if (!isset($this->types)) {
				$this->types = array();
			}
			if (!is_array($this->types)) {
				$this->types = array($this->types);
			}
			if (isset($this->type)) {
				$this->types[] = $this->type;
			}
			add_action('save_post', array(&$this, 'startSave'));
			$this->nonce_action = md5($this->title);
			$this->nonce_name = $this->id . '_meta_nonce';
		}

		/**
		 * When ready, add the meta box
		 */
		public function hook()
		{
			foreach ($this->types as $type) {
				add_meta_box(
					$this->id,
					$this->title, 
					array(&$this, 'startRender'), 
					$type, 
					$this->context,
					$this->priority
				); 
			}
		}

		/*
		 * Setup a nonce, then call render()
		 */
		public function startRender($post)
		{
			wp_nonce_field( $this->nonce_action, $this->nonce_name ); 
			$this->render($post);
		}

		/*
		 * Render the metabox (should be over-written by child)
		 */
		public function render($post)
		{
			return '';
		}

		/**
		 * Save appropriately during autosave
		 * and if nonce is valid
		 */
		public function startSave($post_id)
		{
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && !$this->autosave) {
				return false;
			}

			if( !current_user_can( 'edit_posts' ) ) {
				return false;  
			}
			
			if (!isset($_POST[$this->nonce_name]) || !wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
				return false;
			}
			$this->save($post_id);
		}

		/**
		 * Save function (should be over-written by child)
		 */
		public function save($post_id)
		{
			// Override
		}
	}
}


