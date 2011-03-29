<?php
class Newsletter_SignUp {
	
	var $options;
	var $ns_checkbox = false;
	
	public function __construct()
	{
		$this->options = get_option('ns_options');
		
		add_action('thesis_hook_after_comment_box',array(&$this,'add_checkbox'),20);
		add_action('comment_form',array(&$this,'add_checkbox'),20);
		add_action('comment_approved_',array(&$this,'do_signup'),10,1);
		add_action('comment_post', array(&$this,'do_signup'), 50, 2);
		
		if(isset($this->options['add_to_reg_form']) && $this->options['add_to_reg_form'] == 1) {
			add_action('register_form',array(&$this,'add_checkbox'),20);
			add_action('register_post',array(&$this,'do_signup'), 50);
		}
		if(isset($this->options['add_to_bp_form']) && $this->options['add_to_bp_form'] == 1) {
			add_action('bp_before_registration_submit_buttons',array(&$this,'add_checkbox'),20);
			add_action('bp_complete_signup',array(&$this,'do_signup'),20);
		}
		
	}
	
	public function add_checkbox() 
	{ 	
		global $ns_checkbox;

		if(isset($this->options['cookie_hide']) && $this->options['cookie_hide'] == 1 && isset($_COOKIE['ns_subscriber'])) $ns_checkbox = true;
		
		if(!$ns_checkbox) {
		?>
		<p style="clear:both; display:block;">
			<input id="ns_checkbox" style="margin:0 5px 0 0; display:inline-block; width:13px; height:13px; " value="1" type="checkbox" name="newsletter-signup-do" <?php if($this->options['precheck_checkbox'] == 1) echo 'checked="checked" '; ?>/>
			<label for="ns_checkbox" style="display:inline-block;"><?php if(strlen($this->options['checkbox_text']) > 0) { echo $this->options['checkbox_text']; } else { echo "Sign me up for the newsletter!"; } ?></label>
		</p>
		<?php 
		}
		$ns_checkbox = true;
	}
	
	function do_signup($cid,$comment)
	{
		if($_POST['newsletter-signup-do'] != 1 || empty($this->options['form_action'])) return;
		
		$cid = (int) $cid;		

		if(isset($_POST['user_email'])) {
			
			// gather emailadress from user who WordPress registered
			$emailadres = $_POST['user_email'];
			$naam = $_POST['user_login'];
		
		} elseif(isset($_POST['signup_email'])) {
		
			// gather emailadress from user who BuddyPress registered
			$emailadres = $_POST['signup_email'];
			$naam = $_POST['signup_username'];

		} else {
			
			// gather emailadress from commenter
			if(!is_object($comment)) $comment = get_comment($cid);

			if($comment->comment_karma != 0) { 
				return;
			}
		
			$emailadres = $comment->comment_author_email;
			$naam = $comment->comment_author;
		}

		// Setup variables array
		$variables = array(
			
			$this->options['email_id'] => $emailadres,
			
		);
		
		// Subscribe with name? Add to $variables array.
		if($this->options['subscribe_with_name'] == 1) $variables[$this->options['name_id']] = $naam;
		
		// Add list specific variables
		switch($this->options['email_service']) {
			
			case 'aweber':
				$variables['listname'] = $this->options['aweber_list_name'];
				$variables['redirect'] = get_bloginfo('wpurl');
				$variables['meta_message'] = '1';
				$variables['meta_required'] = 'email';
			break;
			
			case 'phplist':
				$variables['list['.$$this->options['phplist_list_id'].']'] = 'signup';
				$variables['subscribe'] = "Subscribe";
				$variables["htmlemail"] = "1"; 
				$variables['emailconfirm'] = $emailadres;
				$variables['makeconfirmed']='0';
			break;
		
		}
		
		// Add additional data
		if(isset($this->options['extra_data']) && is_array($this->options['extra_data'])) {
			foreach($this->options['extra_data'] as $key => $value) {
				$variables[$value['name']] = $value['value'];
			}		
		}
		
		// Setup data string
		foreach($variables as $key=>$value) { $variables_string .= $key.'='.$value.'&'; }
		rtrim($variables_string,'&');
					
		if(function_exists('curl_init')) {
		
			//open connection
			$streamCtx = curl_init();
			
			//set the url, number of vars and data
			curl_setopt($streamCtx,CURLOPT_URL,$this->options['form_action']);
			curl_setopt($streamCtx,CURLOPT_POST,count($variables));
			curl_setopt($streamCtx,CURLOPT_POSTFIELDS,$variables_string);
			curl_setopt($streamCtx,CURLOPT_USERAGENT, 'Mozilla/5.0');
			curl_setopt($streamCtx,CURLOPT_HEADER, false);
			curl_setopt($streamCtx,CURLOPT_CONNECTTIMEOUT,3);
			curl_setopt($streamCtx,CURLOPT_TIMEOUT,8);
			curl_setopt($streamCtx,CURLOPT_RETURNTRANSFER,1);
			
			// execute post request
			curl_exec($streamCtx);
			curl_close($streamCtx);

		} else {
		
			// Make the post request (no CURL)			
			$postContentLen = strlen($variables_string);
			$streamCtx = stream_context_create (
				array (
					'http' => array (
						'method' => 'POST',
						'content' => $variables_string,
						'header'  => "Content-Type: application/x-www-form-urlencoded\r\nContent-Length: $postContentLen\r\n"
					)
				)
			);
			$fp = @fopen($this->options['form_action'], 'r', FALSE, $streamCtx);
			
		}
		// set the cookie if preferred
		if(isset($this->options['cookie_hide']) && $this->options['cookie_hide'] == 1) @setcookie('ns_subscriber',true,time()+9999999);	
	}

}