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
        $limit = isset($instance['limit']) ? esc_attr($instance['limit']) : 10;
    
        global $mi_current_ID;
        global $kernel;
        
        $params = array(
            'major' => get_post_meta($mi_current_ID, "major", true),
            'location' => get_post_meta($mi_current_ID, "location", true),
            'jobtype' => get_post_meta($mi_current_ID, "jobtype", true)
        );
        
        $canonicalizer = $kernel->getContainer()->get('mi_search.canonicalizer');
        
        if (empty($params['major'])) {
            $params['major'] = 'undeclared';
            $major = $params['major'];
        }
        else {
            $major = ucwords($params['major']);
            $params['major'] = htmlspecialchars($params['major'], ENT_QUOTES);
            $params['major'] = ucwords($params['major']);
            $params['major'] = $canonicalizer->dash($params['major']);
        }
        if (empty($params['location'])) {
            unset($params['location']);
        }
        else {
            $params['location'] = htmlspecialchars($params['location'], ENT_QUOTES);
            $params['location'] = $canonicalizer->formatLocation($params['location']);
            $params['location'] = $canonicalizer->dash($params['location']);
        }
        if (empty($params['jobtype'])) {
            unset($params['jobtype']);
        }
        
        $url = $kernel->getContainer()->get('router')->generate('mi_jobs_api_results', $params);
        $moreUrl = $kernel->getContainer()->get('router')->generate('mi_jobs_results', $params);
        
        ?>
        <div class="module">
            <h4><?php echo ($major === 'undeclared') ? "Latest Jobs" : $title; ?></h4>
            <div class="jobs-api-box" data-href="<?php echo $url; ?>" data-limit="<?php echo $limit; ?>">
                <div class="api-job"><a href="<?php echo $moreUrl; ?>" class="pull-right">See More <?php echo ($major === 'undeclared') ? "" : $major." " ?>Jobs &raquo;</a></div>
            </div>
        </div>
        <?php
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