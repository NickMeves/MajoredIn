<?php
/*
Plugin Name: Related Jobs
Version:     1.0
Plugin URI:  http://www.majoredin.com
Description: Show jobs related to the current page based on the major, location & internship custom fields.
Author:      Nick Meves
Author URI:  http://www.majoredin.com
*/

class RelatedJobs extends WP_Widget
{
    /**
     * Constructor
     */
    function RelatedJobs()
    {
        parent::WP_Widget(false, $name = 'Related Jobs');
    }
    
    /**
     * Prints widget to blog pages
     */
    function widget($args, $instance)
    {
        extract($args);
    
        // these are our widget options
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $limit = isset($instance['limit']) ? esc_attr($instance['count']) : 10;
    
        //TODO: RENDER
        global $_current_ID;
        echo '<div>'.$_current_ID.'</div>';
    }
    
    /**
     * Allows params to be saved
     */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['limit'] = strip_tags($new_instance['limit']);
        
        return $instance;
    }
    
    /**
     * Widget Form rendering
     */
    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $limit = isset($instance['limit']) ? esc_attr($instance['limit']) : '';
        ?>
    		<p>
    			<label for="<?php echo $this->get_field_id('title'); ?>">
    			Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /> 
    			</label>
    		</p>
    		<p>
    			<label for="<?php echo $this->get_field_id('limit'); ?>">
    			No. of Jobs: <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" /> 
    			</label>
    		</p>
        <?php
	}
}

function mi_related_jobs_register_widget() {

    if (function_exists('register_widget')) {
        register_widget('RelatedJobs');
    }
}
add_action('widgets_init', 'mi_related_jobs_register_widget', 1);