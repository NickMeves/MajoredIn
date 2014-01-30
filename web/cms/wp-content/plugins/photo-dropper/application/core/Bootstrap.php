<?php	
/**
 * Main Plugin Class
 * @package PhotoDropper
 * @author Nicky Hajal
 */
if(!class_exists("Pdr_Bootstrap")){
	class Pdr_Bootstrap{
		/**
		 * Reference to singleton ::nc_Main class
		 * @var Pdr_Main Singleton's self-reference
		 */
		private static $instance;

		/**
		 * Constructor for main plugin class
		 */
		private function __construct()
		{
			$this->initGlobals();
			$this->initAjax();
			if (is_admin()) {
				$this->initPointers();
			}
			$this->addHooks();
		}

		/**
		 * Get instance of singleton Pdr_Main classs
		 * @return Pdr_Main Object that references singleton class
		 */
		public function getInstance()
		{
			if(!isset(self::$instance)){
	            $c = __CLASS__;
	            self::$instance = new $c;
			}
			return self::$instance;
		}

		/**
		 * Initialize Global Variables and include {@link Pdr_Core_Utility}
		 */
		private function initGlobals()
		{
			define('PDR_APPLICATION_ENV', getenv('APPLICATION_ENV'));
			define('PDR_ENV', strpos($_SERVER['HTTP_HOST'], '.tumblen') !== false ? 'development' : 'production');

			define('PDR_URL', site_url() . '/wp-content/plugins/' . PDR_FOLDER);

			// APP(lication) is a directory because we only ever want to include from here
			define('PDR_APP', WP_PLUGIN_DIR . '/' . PDR_FOLDER . '/application');

			// INT(erface) is a url because we only ever want to link to resources from here
			define('PDR_INT', PDR_URL . '/interface');
			define('PDR_ADM', get_bloginfo('wpurl') . '/wp-admin/admin.php?page=photodropper');

			// Use the correct, safe directories for writing
			$pdr_upload_dir = wp_upload_dir();
			define('PDR_TEMP', $pdr_upload_dir['basedir'] . '/.pdr_temp');
			define('PDR_CACHE', $pdr_upload_dir['basedir'] . '/.pdr_cache');
			define('PDR_FKEY', 'd087381c85f6827a767dfb49d3aaa79d');
			define('PDR_THEAPP', '//app.photodropper.com');

			// Some external globals
			require_once(PDR_DIR . '/application/core/Utility.php');
		}

		/**
		 * Initialize Ajax if Needed
		 */
		private function initAjax()
		{
			global $pagenow;
			if($pagenow == 'admin-ajax.php'){
				require_once(PDR_APP . '/ajax/Ajax.php');
			}  
		}
		
		/**
		 * Initialize WordPress Menus
		 */
		public function initMenus()
		{
			/*
			$router = Pdr_Core_Router::getInstance();
			$route =  array(&$router, 'defaultRoute');
			$perms = 'edit_pages';

			add_menu_page( 'Sales', 'Sales', $perms, 'sales', $route, PDR_URL . '/images/sales_icon.png',  null);             
			add_submenu_page('sales', 'All Transactions', 'All Transactions', $perms, 'sales/list', $route);
			*/
		}

		public function initMetaboxes()
		{
			require_once(PDR_APP . '/metaboxes/Metabox.php');
			require_once(PDR_APP . '/metaboxes/attributionLocation.php');
		}

		/**
		 * Add actions and filters for Unc to WordPress runtime
		 * @uses Pdr_Core_Bootstrap::initMenus
		 * @uses Pdr_Core_Bootstrap::enqueue
		 * @uses Pdr_Core_Bootstrap::loadGlobals
		 */
		private function addHooks()
		{
			add_action('init', array(&$this, 'enqueue'));
			add_action('admin_init', array(&$this, 'initMetaboxes'));
			add_action('admin_menu',  array(&$this, 'initMenus'));
			add_action('admin_footer',  array(&$this, 'printPointers'));
			add_action('media_buttons', array(&$this, 'mediabutton'), 20);
			add_filter('the_content', array(&$this, 'applyAttribution'));
		}

		/**
		 * Figure out which pointers need to be shown
		 * @global type $PDR_UTIL 
		 */
		public function initPointers()
		{
			global $PDR_UTIL;
			$this->pointers = array();
			if (!$PDR_UTIL->isMarked('install')) {
				$p = new stdClass();
				$p->el = '.wp-submenu-wrap a.wp-first-item:visible';
				$p->content = '<h3>PhotoDropper is Installed!</h3><p>You can now access it when you edit any post or page.</p>';
				$p->buttons = 'function( event, t ) {
					button1 = jQuery("<a id=\"pointer-close\" style=\"margin-right:10px\" class=\"button-primary\">Try it Out!</a>");
					button2 = jQuery("<a id=\"pointer-close\" class=\"button-secondary\">Close</a>");
					button1.bind("click.pointer", function() {
						location.href = ajaxurl.replace("admin-ajax.php", "post-new.php");
					});
					button2.bind( "click.pointer", function() {
						t.element.pointer("close");
					});
					return jQuery("<div/>").append(button2).append(button1);
				}';
				$p->position = '{
				my: "left top",
				at: "middle bottom", 
				edge: "top",
				offset: "-40px 0"
				}';
				$p->close = 'function(){}';
				$p->mark = 'install';
				$this->pointers[] = $p;
			}
			if (((isset($_GET['action']) && $_GET['action'] == 'edit') || strpos($_SERVER['REQUEST_URI'], 'post-new.php') !== false) && !$PDR_UTIL->isMarked('mediaButtonNotify')) {
				$p = new stdClass();
				$p->el = '#wp-content-media-buttons a.pdrp_open';
				$p->content = '<h3>The Right Photo is Right Here</h3><p>Click the PhotoDropper button anytime you need to find a great image for your posts!</p>';
				$p->buttons = 'function( event, t ) {
					button1 = jQuery("<a id=\"pointer-close\" style=\"margin-right:10px\" class=\"pdrp_open button-primary\">Open PhotoDropper</a>");
					button2 = jQuery("<a id=\"pointer-close\" class=\"button-secondary\">Close</a>");
					button1.bind("click.pointer", function() {
						t.element.pointer("close");
					});
					button2.bind( "click.pointer", function() {
						t.element.pointer("close");
					});
					return jQuery("<div/>").append(button2).append(button1);
				}';
				$p->position = '{
				my: "left middle",
				at: "left middle", 
				offset: "25px 0",
				edge: "left"
				}';
				$p->close = 'function(){}';
				$p->mark = 'mediaButtonNotify';
				$this->pointers[] = $p;
			}
		}

		/**
		 * Print the scripts to show any pointers needed 
		 */
		public function printPointers()
		{
			foreach($this->pointers as $p) {
				echo '
					<script type="text/javascript">
						jQuery(function(){
							jQuery("' . $p->el . '").pointer({ 
								content: "' . $p->content . '",
								buttons:  ' . $p->buttons . ',
								position: ' . $p->position . ',
								close: ' . $p->close . '
							}).pointer("open");
							jQuery.post(ajaxurl+"?action=pdr_mark", {mark: "' . $p->mark . '"}, function(){});
						});
					</script>
				';
			}
		}

		/**
		 * Enqueue any necessary scripts/styles based on context
		 */
		public function enqueue()
		{
			global $PDR_UTIL;
			$uri = $_SERVER['REQUEST_URI'];
			if (
				end(explode('/', trim($uri, '/'))) == 'wp-admin' ||
				strstr($uri, 'wp-admin/index.php') || 
				strstr($uri, 'wp-admin/post-new.php') || 
				strstr($uri, 'wp-admin/post.php') 
			) {
				$current_user = wp_get_current_user();
				wp_enqueue_script('jquery');
				wp_enqueue_style( 'wp-pointer' ); 
				wp_enqueue_script( 'wp-pointer' ); 
				wp_enqueue_script('photodropper', PDR_URL . '/interface/js/photodropper.js'); 
				wp_localize_script('photodropper', 'PDRP', array(
					"ID" => "?in=wordpress&domain=" . site_url(),
					"APP" => PDR_THEAPP,
					"APPURL" => $this->appUrl(),
					"FKEY" => PDR_FKEY,
					"WELCOMED" => $PDR_UTIL->isMarked('welcome') ? 1 : 0,
					"COMMERCIAL" => $PDR_UTIL->get_option('pdrp_commercial', 1),
					"USEREMAIL" => $current_user->user_email,
					"USERFNAME" => get_user_meta($current_user->ID, 'first_name'),
					"USERLNAME" => get_user_meta($current_user->ID, 'last_name'),
					"ADDCARD" => PDR_URL . '/application/core/SecureRedirect.php',
				));
			}
			else {
				wp_enqueue_style( 'pdrp_styles', PDR_URL . '/interface/css/public.css' ); 
			}
		}

		/**
		 * Get the correct url to the App
		 * @param boolean|string $file
		 * @return string
		 */
		public function appUrl($release = 'market')
		{
			if (isset($_REQUEST['pdrp_rel'])) {
				$release = $_REQUEST['pdrp_rel'];
			}
			return PDR_THEAPP . '?release=' . $release . '&in=wordpress&domain=' . site_url();
		}

		/**
		 * Add PhotoDropper mediabutton 
		 */
		public function mediabutton()
		{
			$link_markup = '
				<a href="#" title="Find the Right Photo with PhotoDropper" class="pdrp_open" title="Drop in a Flickr Image">
					<img src="'.get_option('siteurl').'/wp-content/plugins/' . PDR_FOLDER . '/interface/images/photodropper_icon16.png" alt="Drop in a Flickr Image" />
				</a>
			';
			echo $link_markup;
		}


		/**
		 * Apply an attribution to a post correctly
		 * @global type $post
		 * @param type $content
		 * @return string 
		 */
		public function applyAttribution($content)
		{
			// Get IDs
			global $post, $pdrp_attr_ids;
			$pdr_post = new Pdr_Post($content);
			$ids = $pdr_post->getAttrIds();
			if (count($ids)) {
				$where = get_post_meta($post->ID, 'pdrp_attributionLocation');
				$extended = get_post_meta($post->ID, 'pdrp_attributionExtended');
				$where = count($where) ? $where[0] : false;
				$extended = count($extended) ? $extended[0] : true;
				if ($where == 'tag') {
					$pdrp_attr_ids = $ids;
					return $content;
				}
				if($where == 'caption') {
					foreach ($ids as $id => $pht) {
						// Attribution HTML
						$attrStr = ' 
							<span class=\'pdrp_captionAttribution %captionType%\'>
								' . pdrp_pl($extended, 1) . ':
								<a href=\'' . $pht['attributeUrl'] . '\' target=\'_blank\' class=\'pdrp_link pdrp_attributionLink\'>
									' . $pht['attributeTo'] . '</a>
							</span>
						';

						// Apply to caption if it exists
						$captStart = strpos($content, '[caption id="attachment_' . $id . '"');
						if ($captStart !== false) {
							$captEnd = strpos($content, ']', $captStart);
							$captCode = substr($content, $captStart, $captEnd-$captStart);
							$captMatch = '';
							$captM = preg_match("/caption=\"(.+)\"/", $captCode, $captMatch);
							$captDef = rtrim($captMatch[0], '"');
							$newCaptDef = $captDef . ' ' . str_replace('%captionType%', 'pdrp_sharedCaption', $attrStr);
							$newCaptCode = str_replace("\n", "", str_replace($captDef, $newCaptDef, $captCode));
							$content = str_replace($captCode, $newCaptCode, $content);
							unset($ids[$id]);
						}

						// Or create a caption if it doesn't exist
						else {
							$imgClass = strpos($content, 'wp-image-' . $id);
							if ($imgClass !== false) {
								$str = '';
								$imgEnd = strpos($content, '/>', $imgClass);
								$ancEnd = strpos($content, '</a>', $imgClass);
								if ($ancEnd && $ancEnd - $imgEnd < 10) {
									$startStr = '<a ';
									$imgEnd = $ancEnd;
								}
								else {
									$startStr = '<img ';
								}
								$imgStart = strrpos(substr($content, 0, $imgClass), $startStr);
								$widthMatch = '';
								$widthM = preg_match("/width=\"(.+)\"/", substr($content, $imgStart, $imgEnd - $imgStart), $widthMatch);
								$width = rtrim($widthMatch[0], '"');
								$str = substr($content, 0, $imgStart);
								$imgStr = substr($content, $imgStart, $imgEnd - $imgStart);

								// Handle the alignment of a caption based on WP setting
								$align = 'alignnone';
								if (strpos($imgStr, 'alignleft') !== false) {
									$align = 'alignleft';
								}
								else if (strpos($imgStr, 'aligncenter') !== false) {
									$align = 'aligncenter';
								}
								else if (strpos($imgStr, 'alignright') !== false) {
									$align = 'alignright';
								}

								// Add caption
								$str .= '[caption align="' . $align . '" ' . $width . '" id="attachment_' . $id . '" caption="' . str_replace('%captionType%', 'pdrp_emptyCaption', $attrStr) . '"]';
								$str .= $imgStr;
								$str .= '</a>[/caption]' . substr($content, $imgEnd+4);
								$content = $str;
								unset($ids[$id]);
							}

						}
					}
				}
				if (count($ids) && (!$where || $where = 'end')) {
					$photos = count($ids) > 1 ? 'photos' : 'photo';
					$attrStr = '';
					foreach ($ids as $pht) {
						$attrStr .= '
							<a href="' . $pht['attributeUrl'] . '" target="_blank" class="pdrp_link pdrp_attributionLink">
								' . $pht['attributeTo'] . '</a>,';
					}
					$attrStr = preg_replace('~(.*)' . preg_quote(',', '~') . '~', '$1' . ' & ', trim($attrStr, ','), 1);
					$content = $content . '
						<div id="pdrp_endAttribution">
						' . pdrp_pl($extended, count($ids)) . ' by: 
						 ' . $attrStr . '
						</div>
					';
				}
			}
			return $content;
		}
	}
}
global $pdrp_attr_id;

if (!function_exists('photodropper_attribution')) {
	function photodropper_attribution($extended = true) {
		rewind_posts();
		$ids = array();
		while (have_posts()) : the_post(); 
			$pdr_post = new Pdr_Post(get_the_content());
			$attr_ids = $pdr_post->getAttrIds();
			$ids = array_merge($ids, $attr_ids);
		endwhile;
		rewind_posts();
		if (count($ids)) {
			$photos = count($ids) > 1 ? 'photos' : 'photo';
			$attrStr = '';
			foreach ($ids as $pht) {
				$attrStr .= '
					<a href="' . $pht['attributeUrl'] . '" target="_blank" class="pdrp_link pdrp_attributionLink">
						' . $pht['attributeTo'] . '</a>,';
			}
			$attrStr = preg_replace('~(.*)' . preg_quote(',', '~') . '~', '$1' . ' & ', trim($attrStr, ','), 1);
			echo '
				<div id="pdrp_tagAttribution">
					' . pdrp_pl($extended, count($ids)) . ' by : 
				 ' . $attrStr . '
				</div>
			';
		}
	}
}
function pdrp_pl ($type = false, $count = 0) 
{
	$out = '';
	$photos = $count > 1 ? 'photos' : 'photo';
	if ($type) {
			$out = $photos;
	}
	else {
		$out = $photos;
	}
	return $out;
}
if(!class_exists("Pdr_Post")){
	class Pdr_Post{
		public function __construct($content) {
			$this->content = $content;
		}
		public function getAttrIds() {
			$bits = preg_split("/[^a-zA-Z0-9]/", $this->content);
			$ids = array();
			foreach($bits as $i => $bit) {
				if ($bit == 'wp' && $bits[$i+1] == 'image') {
					$id = (int)$bits[$i+2];
					$meta = wp_get_attachment_metadata($id);
					if (isset($meta['attributeUrl'])) {
						$ids[$id] = $meta;
					}
				}
			}

			if (function_exists('get_post_thumbnail_id')) {
				#hat-tip to roi_davidsword for the following
				$id = get_post_thumbnail_id( get_the_ID() );
				if($id) {
				  $meta = get_post_meta($id,'_wp_attachment_metadata',true);
				  if (isset($meta['attributeUrl']))
					$ids[$id] = $meta;
				}
			}
			return $ids;
		}
	}
}