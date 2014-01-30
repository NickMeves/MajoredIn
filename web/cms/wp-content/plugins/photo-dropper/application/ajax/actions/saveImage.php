<?php
/**
 * Save an Image from PhotoDropper
 * @package PhotoDropper
 * @author Nicky Hajal
 */
 ini_set('display_errors', 1);
if(!class_exists("Pdr_Ajax_Action_SaveImage")){
	class Pdr_Ajax_Action_SaveImage extends Pdr_AjaxAction{

		/**
		 * Add an Image from Flickr
		 * @todo What are the required fields when posting?
		 */
		public function action()
		{
			global $PDR_UTIL;
			$p = (object)$this->p['photo'];
			$this->tries = 0;
			$this->errmsg = false;
			$this->prepare();
			$this->saveImage();
			$imageurl = $this->updir['url'] . '/' . $this->slug . '.' . $this->ext;

			if (!$this->errmsg) {
				$url = $imageurl;
				$size = getimagesize($this->imagefile);
				$type = image_type_to_mime_type($size[2]);
				$file = $this->imagefile;
				$content = '';

				// use image exif/iptc data for title and caption defaults if possible
				if ( $image_meta = @wp_read_image_metadata($file) ) {
					if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) )
						$this->title = $image_meta['title'];
					if ( trim( $image_meta['caption'] ) )
						$content = $image_meta['caption'];
				}

				$post_id = 0;
				$post_data = array();
				$content = '';

				// Construct the attachment array
				$attachment = array_merge( array(
					'post_mime_type' => $type,
					'guid' => $url,
					'post_parent' => $post_id,
					'post_title' => $this->title,
					'post_content' => $content,
				), $post_data );

				// This should never be set as it would then overwrite an existing attachment.
				if ( isset( $attachment['ID'] ) )
					unset( $attachment['ID'] );

				// Save the data
				$id = wp_insert_attachment($attachment, $file, $post_id);
				$attach_data = wp_generate_attachment_metadata( $id, $file );
				wp_update_attachment_metadata( $id,  $attach_data );
				if ( !is_wp_error($id) && $this->source_type == 'fk') {
					wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
					$meta = wp_get_attachment_metadata($id);
					$params = 'user_id=' . $p->owner . '&method=flickr.people.getInfo&format=json&api_key=' . PDR_FKEY;
					$request = 'http://api.flickr.com/services/rest?' . $params;
					$rsp = trim(str_replace('jsonFlickrApi', '', $PDR_UTIL->get($request)), '()');
					$owner = json_decode($rsp)->person;
					$meta['attributeTo'] = $owner->username->_content;
					$meta['attributeOwnerUrl'] = $owner->profileurl->_content;
					$meta['attributeUrl'] = $p->flickrUrl;
					wp_update_attachment_metadata($id, $meta);
				}
				$this->rsp = array("path" => $this->imagefile, 'mediaid' => $id, 'tries' => $this->tries, "suc" => 1);
			}

			if ($this->errmsg) {
				$this->rsp = array("msg" => $this->errmsg, "err" => 1);
			}
		}

		private function prepare() {
			$p = (object)$this->p['photo'];
			$this->url = $p->url;
			$hasTitle = true;
			if ($p->title) {
				$this->title = $p->title;
			}
			else {
				$hasTitle = false;
				$this->title = md5(uniqid());
			}
			$this->source_type = $p->type;
			if ($this->source_type == 'fk') {
				$this->ext = end(explode('.', str_replace('?zz=1', '', $this->url)));
			}
			else {
				$this->ext = 'jpg';
			}
			$this->updir = wp_upload_dir();
			$this->dir = $this->updir['path'];
			if (!file_exists($this->dir)) {
				mkdir($this->dir);
			}
			if (isset($_REQUEST['file_name'])) {
				$this->slug = str_replace(' ', '_', preg_replace('/[^_a-zA-Z0-9\s]/', '', strtolower(trim($_REQUEST['file_name']))));
			}
			else{
				$this->slug = str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(trim($this->title))));
			}
			if (!$hasTitle) {
				$this->title = '';
			}
		}
		private function saveImage() {
			global $PDR_UTIL;
			if ($this->tries <= 0) {
				$image = $PDR_UTIL->get($this->url);
				$this->imagefile = $this->dir . '/' . $this->slug . '.' . $this->ext;
				file_put_contents($this->imagefile, $image);
				@chmod($this->imagefile, 0777);
				$this->tries += 1;
				if (!file_exists($this->imagefile) || !is_array(getimagesize($this->imagefile)) || filesize($this->imagefile) < 10) {
					$this->saveImage();
				}
			}
			else {
				$this->errmsg = "Couldn't download image.";
			}
		}
	}
}