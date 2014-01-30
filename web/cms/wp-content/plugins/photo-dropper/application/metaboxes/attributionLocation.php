<?php
/**
 * Base Class that a metabox should extend
 */
if(!class_exists("Pdr_Metabox_AttributionLocation")){
	class Pdr_Metabox_AttributionLocation extends Pdr_Metabox{

		/*
		 * Render the metabox (should be over-written by child)
		 */
		public function render($post)
		{
			$vals = get_post_custom($post->ID);
			$attrVal = isset($vals['pdrp_attributionLocation']) ? $vals['pdrp_attributionLocation'][0] : get_option('pdrp_defaultAttributionLocation', 'end');
			$extVal = isset($vals['pdrp_attributionExtended']) ? $vals['pdrp_attributionExtended'][0] : get_option('pdrp_defaultAttributionExtended', '1');
			echo '
				<label class="unc_mbox_label" style="display:block; margin-top:12px;">
					Where should PhotoDropper put photo attributions in this post?
				</label>
				<select id="pdrp_attributionLocation" name="pdrp_attributionLocation" style="margin-top:9px; width:200px;">
					<option value="caption" ' . selected($attrVal, 'caption', 0) . '>In Photo Captions</option>
					<option value="end" ' . selected($attrVal, 'end', 0) . '>At the End of the Post</option>
					<option value="tag" ' . selected($attrVal, 'tag', 0) . '>Use PhotoDropper PHP Tag</option>
				</select>
				<a href="http://www.photodropper.com/wordpress-plugin#php" style="display:none" target="_blank" id="pdrp_phpTagHelp">Click for help using the PHP Tag</a>
				' ; 
			?>
			
			<?php
		}

		/**
		 * Save function (should be over-written by child)
		 */
		public function save($post_id)
		{
			update_option('pdrp_defaultAttributionLocation', esc_attr($_POST['pdrp_attributionLocation']));
			update_post_meta($post_id, 'pdrp_attributionLocation', esc_attr($_POST['pdrp_attributionLocation']));
		}
	}
}

$pdr_mbox_AttributionLocation = new Pdr_Metabox_AttributionLocation(
	array(
		"id" => "unc_mbox-attributionLocation",
		"title" => "Photo Attribution Location",
		"context" => "side",
		"priority" => "default",
		"types" => array("post", "page")
	)
);

