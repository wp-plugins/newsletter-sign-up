<?php

if(!class_exists('Newsletter_SignUp_Widget')) {

	class Newsletter_SignUp_Widget extends WP_Widget {
		
		function __construct() {
			parent::__construct(false, $name = 'Newsletter Sign-Up Widget');	
		}

		function widget($args, $instance) {	
			/* Get Newsletter Sign-up options */
			$options = get_option('ns_options');
			$additional_fields = '';
			
			/* Provide some defaults */
			$defaults = array( 'title' => 'Sign up for our newsletter!', 'text_after_signup' => 'Thanks for signing up to our newsletter!', 'text_before_form' => '', 'load_widget_styles' => 1);
			$instance = wp_parse_args( (array) $instance, $defaults );	
			
			extract( $args );
			extract($instance);
			$title = apply_filters('widget_title', $title);
			
			/* Set up form variables for API usage or normal form */
			if(isset($options['use_api']) && $options['use_api'] == 1) {
			
				/* Using API, send form request to widget-signup.php */
				$form_action = "";
				$email_id = 'ns_email';
				$name_id = 'ns_name';
				
			} else {
				
				/* Using normal form request, set-up using configuration settings */
				$form_action = $options['form_action'];
				$email_id = $options['email_id'];
				
				if(isset($options['name_id'])) {
					$name_id = $options['name_id'];
				}
				
			}
			
			/* Set up additional fields */
			if(isset($options['extra_data']) && is_array($options['extra_data'])) :
				foreach($options['extra_data'] as $ed) : 
					$additional_fields .= "<input type=\"hidden\" name=\"{$ed['name']}\" value=\"{$ed['value']}\" />";
				endforeach; 
			endif; 
			
			?>
				  <?php echo $before_widget; ?>
					  <?php echo $before_title . $title . $after_title; ?>
					  <?php if(!isset($_POST['ns_widget_submit'])) { //form has not been submitted yet ?>
						  <form class="nsu-widget-form" action="<?php echo $form_action; ?>" method="post">
						  
							<p>
								<?php echo $text_before_form; ?>
							</p>
							
							<?php if(isset($options['subscribe_with_name']) && $options['subscribe_with_name'] == 1) { ?>
								<p>
									<label for="ns-widget-name"><?php _e('Name'); ?></label>
									<input id="ns-widget-name" type="text" name="<?php echo $name_id; ?>" />
								</p>
							<?php } ?>
							
							<p>
								<label for="ns-widget-email"><?php _e('Emailadress'); ?></label>
								<input id="ns-widget-email" type="text" name="<?php echo $email_id; ?>" />
							</p>
							
							<?php echo $additional_fields; ?>
							<p>
								<input type="submit" name="ns_widget_submit" value="<?php _e('Sign up'); ?>" />
							</p>
						  </form>
					  <?php } else { // form has been submitted?>
							<p>
								<?php echo $text_after_signup; ?>
							</p>				  
					  <?php } ?>
				  <?php echo $after_widget; ?>
			<?php
		}

		function update($new_instance, $old_instance) {				
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['text_after_signup'] = strip_tags($new_instance['text_after_signup']);
			$instance['text_before_form'] = strip_tags($new_instance['text_before_form']);
			$instance['load_widget_styles'] = strip_tags($new_instance['load_widget_styles']);
			
			if(isset($instance['load_widget_styles']) && $instance['load_widget_styles'] == 1) {
				$ns_options = get_option('ns_options');
				$ns_options['load_widget_styles'] = 1;
				update_option('ns_options',$ns_options);
			}
			
			return $instance;
		}

		function form($instance) {	
			$defaults = array( 'title' => 'Sign up for our newsletter!', 'text_after_signup' => 'Thanks for signing up to our newsletter!', 'text_before_form' => '', 'load_widget_styles' => 1);
			$instance = wp_parse_args( (array) $instance, $defaults );		
			
			$title = esc_attr($instance['title']);
			$text_after_signup = esc_attr($instance['text_after_signup']);
			$text_before_form = esc_attr($instance['text_before_form']);
			$load_widget_styles = esc_attr($instance['load_widget_styles']);
			?>
			 <p>
			  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			 <p>
			  <label for="<?php echo $this->get_field_id('text_before_form'); ?>"><?php _e('Text to show before form:'); ?></label> 
			  <textarea class="widefat" id="<?php echo $this->get_field_id('text_before_form'); ?>" name="<?php echo $this->get_field_name('text_before_form'); ?>"><?php echo $text_before_form; ?></textarea>
			</p>
			 <p>
			  <label for="<?php echo $this->get_field_id('text_after_signup'); ?>"><?php _e('Text after sign-up:'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id('text_after_signup'); ?>" name="<?php echo $this->get_field_name('text_after_signup'); ?>" type="text" value="<?php echo $text_after_signup; ?>" />
			</p>
			 <p>
			  <label for="<?php echo $this->get_field_id('load_widget_styles'); ?>"><?php _e('Load some default CSS?'); ?></label> 
			  <input type="checkbox" id="<?php echo $this->get_field_id('load_widget_styles'); ?>" name="<?php echo $this->get_field_name('load_widget_styles'); ?>" value="1"<?php if(isset($load_widget_styles) && $load_widget_styles == 1) echo ' checked="checked" '; ?>/>
			</p>
			<?php 
		}

	}
}