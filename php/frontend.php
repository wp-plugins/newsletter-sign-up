<?php
class Newsletter_SignUp {
	
	var $options = array();
	var $defaults = array(
		'form' => array(
			'email_label' => 'E-mail:',
			'name_label' => 'Name:',
			'submit_button' => 'Sign-Up'
		)
	);
	var $no_of_forms = 0;
	var $ns_checkbox = FALSE;
	
	public function __construct()
	{
		$this->options = get_option('ns_options',$this->defaults);
		$this->add_hooks();	
	}
	
	/* Register the widget */
	function add_widget()
	{
		return register_widget('Newsletter_SignUp_Widget');
	}
	
	/**
	* Add WP filters and actions according where necessary
	*/
	function add_hooks()
	{
		// widget initalization
		add_action('widgets_init',array(&$this,'add_widget'));
		add_action('init',array(&$this,'check_for_form_submit'));
		
		// register the shortcode which can be used to output sign-up form
		add_shortcode('newsletter-sign-up-form',array(&$this,'form_shortcode'));
		
		$stylesheet_opts = '?';
		// Load CSS to reset the checkbox' position?
		if(isset($this->options['do_css_reset']) && $this->options['do_css_reset'] == 1) {
			$stylesheet_opts .= 'checkbox_reset=1';
		} 
		
		// Load CSS to reset label and input fields for the sign-up form?
		if(isset($this->options['form']['load_form_css']) && $this->options['form']['load_form_css'] == 1) {
			$stylesheet_opts .= '&form_css=1';
		}
		
		wp_enqueue_style('ns_checkbox_style',plugins_url("/css/newsletter-sign-up.php$stylesheet_opts",dirname(__FILE__)));
		
		// Add to comment form? If so, add necessary actions. Try to add automatically.
		if(isset($this->options['add_to_comment_form']) && $this->options['add_to_comment_form'] == 1) {
			add_action('thesis_hook_after_comment_box',array(&$this,'add_checkbox'),20);
			add_action('comment_form',array(&$this,'add_checkbox'),20);
			add_action('comment_approved_',array(&$this,'grab_email_from_comment'),10,1);
			add_action('comment_post', array(&$this,'grab_email_from_comment'), 50, 2);
		}
		
		// If add_to_reg_form is ticked, add corresponding actions
		if(isset($this->options['add_to_reg_form']) && $this->options['add_to_reg_form'] == 1) {
			add_action('register_form',array(&$this,'add_checkbox'),20);
			add_action('register_post',array(&$this,'grab_email_from_wp_signup'), 50);
		}
		
		// If add_to_bp_form is ticked, add BuddyPress actions
		if(isset($this->options['add_to_bp_form']) && $this->options['add_to_bp_form'] == 1) {
			add_action('bp_before_registration_submit_buttons',array(&$this,'add_checkbox'),20);
			add_action('bp_complete_signup',array(&$this,'grab_email_from_wp_signup'),20);
		}
		
		// If running a MultiSite, add to registration form and add actions.
		if(isset($this->options['add_to_ms_form']) && $this->options['add_to_ms_form'] == 1) {
			add_action('signup_extra_fields',array(&$this,'add_checkbox'),20);
			add_action('signup_blogform',array(&$this,'add_hidden_checkbox'),20);
			add_filter('add_signup_meta',array(&$this,'add_checkbox_to_usermeta'));
			add_action('wpmu_activate_blog',array(&$this,'grab_email_from_ms_blog_signup'),20,5);
			add_action('wpmu_activate_user',array(&$this,'grab_email_from_ms_user_signup'),20,3);
		}
	}
	
	/**
	* Has a newsletter sign-up form been submitted? 
	*/
	function check_for_form_submit()
	{
		if(isset($_POST['nsu_submit']))
		{
			$email = $_POST['nsu_email'];
			$naam = (isset($_POST['nsu_name'])) ? $_POST['nsu_name'] : null;
			
			if(6 > strlen($email) || (isset($this->options['subscribe_with_name']) && $this->options['subscribe_with_name'] == 1 && isset($this->options['form']['name_required']) && $this->options['form']['name_required'] == 1 && empty($naam))) {
				wp_die( __('Error: please fill the required fields (name, email).') );
			}
			
			if(!is_email($email)) {
				 wp_die( __('Error: please enter a valid email address.') );
			}
			
			$this->send_post_data($email,$naam);
		}
		return;
	}
	
	
	/**
	*	Output the checkbox, if not already done (manually)
	*/
	public function add_checkbox() 
	{ 	
		global $ns_checkbox;

		if(isset($this->options['cookie_hide']) && $this->options['cookie_hide'] == 1 && isset($_COOKIE['ns_subscriber'])) $ns_checkbox = TRUE;
		
		if(!$ns_checkbox) {
		?>
		<p id="ns-checkbox">
			<input value="1" id="nsu_checkbox" type="checkbox" name="newsletter-signup-do" <?php if(isset($this->options['precheck_checkbox']) && $this->options['precheck_checkbox'] == 1) echo 'checked="checked" '; ?>/>
			<label for="nsu_checkbox">
				<?php if(!empty($this->options['checkbox_text'])) { echo $this->options['checkbox_text']; } else { echo "Sign me up for the newsletter!"; } ?>
			</label>
		</p>
		<?php 
		}
		$ns_checkbox = TRUE;
	}
	
	/**
	* Adds a hidden checkbox to the second page of the MultiSite sign-up form (the blog sign-up form) containing the checkbox value of the previous screen
	*/
	function add_hidden_checkbox()
	{
		?>
		<input type="hidden" name="newsletter-signup-do" value="<?php echo (isset($_POST['newsletter-signup-do'])) ? 1 : 0; ?>" />
		<?php
	}
	
	/**
	* Save the value of the checkbox to MultiSite sign-ups table
	*/
	function add_checkbox_to_usermeta($meta)
	{
		$meta['newsletter-signup-do'] = (isset($_POST['newsletter-signup-do'])) ? 1 : 0;
		return $meta;
	}
	
	/**
	* Send the post data to the newsletter service, mimic form request
	*/
	function send_post_data($email,$naam = '')
	{	
		// when not using api and no form action has been given, abandon.
		if(empty($this->options['use_api']) && empty($this->options['form_action'])) return;
		
		$post_data = array();
		
		/* Are we using API? */
		if(isset($this->options['use_api']) && $this->options['use_api'] == 1) {
			
			switch($this->options['email_service']) {
				
				/* Send data using the YMLP API */
				case 'ymlp':
					$request_uri = "http://www.ymlp.com/api/Contacts.Add?";
					$request_uri .= "Key=" . $this->options['ymlp_api_key'];
					$request_uri .= "&Username=" . $this->options['ymlp_username'];
					$request_uri .= "&Email=" . $email;
					$request_uri .= "&GroupID=" . $this->options['ymlp_groupid'];
					$request_uri .= $this->add_additional_data(array('format' => 'query_string', 'api' => 'ymlp'));
					$result = wp_remote_get($request_uri);
				break;
				
				/* Send data using the MailChimp API */
				case 'mailchimp':
					$request   = array(
					  'apikey' => $this->options['api_key'],
					  'id' => $this->options['list_id'],
					  'email_address' => $email,
					  'double_optin' => TRUE,
					  'merge_vars' => array(
							'OPTIN_TIME' => date('Y-M-D H:i:s')
					  )
					);
					
					/* Subscribe with name? If so, add name to merge_vars array */
					if(isset($this->options['subscribe_with_name']) && $this->options['subscribe_with_name'] == 1) {
						$request['merge_vars'][$this->options['name_id']] = $naam;
					}
					
					// Add any set additional data to merge_vars array
					$request['merge_vars'] = array_merge($request['merge_vars'],$this->add_additional_data());
					
					$result = wp_remote_post(
						'http://'.substr($this->options['api_key'],-3).'.api.mailchimp.com/1.3/?output=php&method=listSubscribe', 
						array( 'body' => json_encode($request))
					);		
					
				break;
			
			}
			
		} else {
		/* We are not using API, mimic a normal form request */
			
			$post_data = array(
				$this->options['email_id'] => $email,
			);
		
			// Subscribe with name? Add to $post_data array.
			if(isset($this->options['subscribe_with_name']) && $this->options['subscribe_with_name'] == 1) $post_data[$this->options['name_id']] = $naam;
			
			// Add list specific data
			switch($this->options['email_service']) {
				
				case 'aweber':
					$post_data['listname'] = $this->options['aweber_list_name'];
					$post_data['redirect'] = get_bloginfo('wpurl');
					$post_data['meta_message'] = '1';
					$post_data['meta_required'] = 'email';
				break;
				
				case 'phplist':
					$post_data['list['.$this->options['phplist_list_id'].']'] = 'signup';
					$post_data['subscribe'] = "Subscribe";
					$post_data["htmlemail"] = "1"; 
					$post_data['emailconfirm'] = $emailadres;
					$post_data['makeconfirmed']='0';
				break;
			
			}
			
			$post_data = array_merge($post_data,$this->add_additional_data($post_data));

			$result = wp_remote_post($this->options['form_action'],
				array( 'body' => $post_data ) 
			);	
			
		}
		
		// store a cookie, if preferred by site owner
		if(isset($this->options['cookie_hide']) && $this->options['cookie_hide'] == 1) @setcookie('ns_subscriber',TRUE,time()+9999999);	
	
	}
	
	/** 
	* Returns array with additional data names as key, values as value. 
	* @param data, the normal form data (name, email, list variables)
	*/
	function add_additional_data($args = array())
	{
		$defaults = array(
			'format' => 'array',
			'api' => NULL
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		if($args['format'] == 'query_string') {
		
			$add_data = "";
			if(isset($this->options['extra_data']) && is_array($this->options['extra_data'])) {
				foreach($this->options['extra_data'] as $key => $value) {
					if($args['api'] == 'ymlp') $value['name'] = str_replace('YMP','Field',$value['name']);
					$add_data .= "&".$value['name']."=".$value['value'];
				}		
			}
			return $add_data;
		} 
		
		$add_data = array();
		if(isset($this->options['extra_data']) && is_array($this->options['extra_data'])) {
			foreach($this->options['extra_data'] as $key => $value) {
				$add_data[$value['name']] = $value['value'];
			}		
		}
			
		return $add_data;
	}
	
	/**
	* Perform the sign-up for users that registered trough a MultiSite register form
	* This function differs because of the need to grab the emailadress from the user using get_userdata
	* @param user_id : the ID of the new user
	* @param password : the password, we don't actually use this
	* @param meta : the meta values that belong to this user, holds the value of our 'newsletter-sign-up' checkbox.
	*/
	function grab_email_from_ms_user_signup($user_id, $password = NULL,$meta = NULL){
		if(!isset($meta['newsletter-signup-do']) || $meta['newsletter-signup-do'] != 1) return;
		$user_info = get_userdata($user_id);
		
		$email = $user_info->user_email;
		$naam = $user_info->first_name;
		
		$this->send_post_data($email,$naam);
	}
	
	/**
	* Perform the sign-up for users that registered trough a MultiSite register form
	* This function differs because of the need to grab the emailadress from the user using get_userdata
	* @param user_id : the ID of the new user
	* @param password : the password, we don't actually use this
	* @param meta : the meta values that belong to this user, holds the value of our 'newsletter-sign-up' checkbox.
	*/
	function grab_email_from_ms_blog_signup($blog_id, $user_id, $a, $b ,$meta){
		
		if(!isset($meta['newsletter-signup-do']) || $meta['newsletter-signup-do'] != 1) return;
		$user_info = get_userdata($user_id);
		
		$email = $user_info->user_email;
		$naam = $user_info->first_name;
		
		$this->send_post_data($email,$naam);
	}
	
	/**
	* Grab the emailadress (and name) from a regular WP or BuddyPress sign-up and then send this to mailinglist.
	*/
	function grab_email_from_wp_signup()
	{
		if($_POST['newsletter-signup-do'] != 1) return;
		
		if(isset($_POST['user_email'])) {
			
			// gather emailadress from user who WordPress registered
			$email = $_POST['user_email'];
			$naam = $_POST['user_login'];
		
		} elseif(isset($_POST['signup_email'])) {
		
			// gather emailadress from user who BuddyPress registered
			$email = $_POST['signup_email'];
			$naam = $_POST['signup_username'];

		} else { return; }
		
		$this->send_post_data($email,$naam);
	}
	
	/**
	* Grab the emailadress and name from comment and then send it to mailinglist.
	* @param cid : the ID of the comment
	* @param comment : the comment object, optionally
	*/
	function grab_email_from_comment($cid,$comment = NULL)
	{
		if($_POST['newsletter-signup-do'] != 1) return;
		
		$cid = (int) $cid;
		
		// get comment data
		if(!is_object($comment)) $comment = get_comment($cid);

		// if spam, abandon function
		if($comment->comment_karma != 0) return;
		
		$email = $comment->comment_author_email;
		$naam = $comment->comment_author;
		
		$this->send_post_data($email,$naam);
	}
	
	function form_shortcode($atts = null,$content = null)
	{
		$form = $this->output_form(false);
		return $form;
	}
	
	public function output_form($echo = true)
	{
		$options = $this->options;
		$additional_fields = '';
		$output = '';
		
		$this->no_of_forms++;
		$formno = $this->no_of_forms;
		
		/* Set up form variables for API usage or normal form */
		if(isset($options['use_api']) && $options['use_api'] == 1) {
			
			/* Using API, send form request to widget-signup.php */
			$form_action = "";
			$email_id = 'nsu_email';
			$name_id = 'nsu_name';
				
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
		
		$email_label = (!empty($options['form']['email_label'])) ? $options['form']['email_label'] : 'E-mail:';
		$name_label = (!empty($options['form']['name_label'])) ? $options['form']['name_label'] : 'Name:';
		$submit_button = (!empty($options['form']['submit_button'])) ? $options['form']['submit_button'] : __('Sign-Up');
		$text_after_signup = nl2br($options['form']['text_after_signup']);
		
		 if(!isset($_POST['nsu_submit'])) { //form has not been submitted yet 
			
			$output .= "<form class=\"nsu-form\" id=\"nsu-form-$formno\" action=\"$form_action\" method=\"post\">";
				
			if(isset($options['subscribe_with_name']) && $options['subscribe_with_name'] == 1) {	
				$output .= "<p><label for=\"nsu-name-$formno\">$name_label</label><input class=\"nsu-field\" id=\"nsu-name-$formno\" type=\"text\" name=\"$name_id\" /></p>";		
			} 
							
			$output .= "<p><label for=\"nsu-email-$formno\">$email_label</label><input class=\"nsu-field\" id=\"nsu-email-$formno\" type=\"text\" name=\"$email_id\" /></p>";
			$output .= $additional_fields;
			$output .= "<p><input type=\"submit\" id=\"nsu-submit-$formno\" class=\"nsu-submit\" name=\"nsu_submit\" value=\"$submit_button\" /></p>";
			$output .= "</form>";
				
		} else { // form has been submitted
		
			$output = "<p id=\"nsu-signed-up-$formno\">$text_after_signup</p>";		
				
		 }
		 
		 if($echo) {
			echo $output;
		 } else {
			return $output;
		 }
	}

}