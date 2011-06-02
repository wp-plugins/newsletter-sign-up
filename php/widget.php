<?php

if(!class_exists('Newsletter_SignUp_Widget')) {

	class Newsletter_SignUp_Widget extends WP_Widget {
		
		var $options;
		
		function __construct() {
			parent::__construct(false, $name = 'Newsletter Sign-Up Widget');
			$this->options = get_option('ns_options');
		}

		function widget($args, $instance) {	
			global $Newsletter_SignUp;
			/* Get Newsletter Sign-up options */
			$options = $this->options;
			
			/* Provide some defaults */
			$defaults = array( 'title' => 'Sign up for our newsletter!', 'text_before_form' => '', 'text_after_form' => '');
			$instance = wp_parse_args( (array) $instance, $defaults );	
			
			extract( $args );
			extract($instance);
			$title = apply_filters('widget_title', $title);
			$text_before_form = nl2br($text_before_form);
			$text_after_form = nl2br($text_after_form);
			
			echo $before_widget;
				echo $before_title . $title . $after_title;
					  
					if(!empty($text_before_form)) echo "<p>$text_before_form</p>";
					$Newsletter_SignUp->output_form(true);
					if(!empty($text_after_form)) echo "<p>$text_after_form</p>";
						
			echo $after_widget; 
		}

		function update($new_instance, $old_instance) {
			$instance = $new_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['text_before_form'] = strip_tags($instance['text_before_form'],'<a><b><strong><i><img><em><br>');
			$instance['text_after_form'] = strip_tags($instance['text_after_form'],'<a><b><strong><i><img><em><br>');
			
			return $instance;
		}

		function form($instance) {	
			$defaults = array( 'title' => 'Sign up for our newsletter!', 'text_before_form' => '', 'text_after_form' => '');
			$instance = wp_parse_args( (array) $instance, $defaults );		
			
			extract($instance);

			?>
			 <p>
			  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			 <p>
			  <label title="You can use the following HTML-codes:  &lt;a&gt;, &lt;strong&gt;, &lt;br /&gt;,&lt;em&gt; &lt;img ..&gt;" for="<?php echo $this->get_field_id('text_before_form'); ?>"><?php _e('Text to show before the form:'); ?></label> 
			  <textarea class="widefat" id="<?php echo $this->get_field_id('text_before_form'); ?>" name="<?php echo $this->get_field_name('text_before_form'); ?>"><?php echo $text_before_form; ?></textarea>
			</p>
			
			 <p>
			  <label title="You can use the following HTML-codes:  &lt;a&gt;, &lt;strong&gt;, &lt;br /&gt;,&lt;em&gt; &lt;img ..&gt;" for="<?php echo $this->get_field_id('text_after_form'); ?>"><?php _e('Text to show after the form:'); ?></label> 
			  <textarea class="widefat" id="<?php echo $this->get_field_id('text_after_form'); ?>" name="<?php echo $this->get_field_name('text_after_form'); ?>"><?php echo $text_after_form; ?></textarea>
			</p>
			
			<p>
				You can further configure the sign-up form at the <a href="options-general.php?page=newsletter-sign-up#nsu-form-settings">Newsletter Sign-Up configuration page</a>.
			</p>
			<?php 
		}

	}
}