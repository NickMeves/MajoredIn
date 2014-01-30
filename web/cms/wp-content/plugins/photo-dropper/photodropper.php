<?php    
/*
Plugin Name: PhotoDropper
Plugin URI: http://photodropper.com
Description: Find the perfect blog photo without ever leaving WordPress. Now with access to over 211M+ photos it is easy to find the right photo with PhotoDropper.
Version: 2.2
Author: PhotoDropper, LLC
Author URI: http://photodropper.com/team/
*/

if (substr_count(__FILE__, '/') > substr_count(__FILE__, '\\')) {
    define('PDR_DIR_SEP', '/');
}
else {
    define('PDR_DIR_SEP', '\\');
}

define('PDR_FOLDER', end(explode(PDR_DIR_SEP, str_replace(PDR_DIR_SEP . 'photodropper.php', '', __FILE__))));
define('PDR_DIR', WP_PLUGIN_DIR . '/' . PDR_FOLDER);
require_once(PDR_DIR . '/application/core/Bootstrap.php');
if(class_exists("Pdr_Bootstrap")){
    $Pdr_BOOT = Pdr_Bootstrap::getInstance();
}
