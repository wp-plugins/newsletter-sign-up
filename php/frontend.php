<?php
class Newsletter_SignUp {
	
	var $options;
	var $ns_checkbox = false;
	
	public function __construct()
	{
		add_action('comment_form',array(&$this,'add_checkbox'),20);
		add_action('comment_post', array(&$this,'do_signup'), 50);
		
		$this->options = get_option('ns_options');
	}
	
	public function add_checkbox() 
	{ 	
		global $ns_checkbox;

		if($this->options['cookie_hide'] == 1 && isset($_COOKIE['ns_subscriber'])) $ns_checkbox = true;
		
		if(!$ns_checkbox) {
		?>
		<p style="clear:both; display:block;">
			<input style="margin:0 5px 0 0; display:inline-block; width:13px; height:13px; " value="1" type="checkbox" name="newsletter-signup-do" <?php if($this->options['precheck_checkbox'] == 1) echo 'checked="checked" '; ?>/>
			<?php if(strlen($this->options['checkbox_text']) > 0) { echo $this->options['checkbox_text']; } else { echo "Sign me up for the newsletter!"; } ?>
		</p>
		<?php 
		}
		$ns_checkbox = true;
	}
	
	function do_signup($comment_id)
	{
		if($_POST['newsletter-signup-do'] != 1 || empty($this->options['form_action'])) return;
		
		global $comment_author_email, $comment_author, $user_email;
		
		$emailadres = (strlen($comment_author_email) > 0) ? $comment_author_email : $user_email;
		$variables = array(
			
			$this->options['email_id'] => $emailadres,
			
		);
		
		if($this->options['email_service']=='aweber') {
			$variables['listname'] = $this->options['aweber_list_name'];
			$variables['redirect'] = get_bloginfo('wpurl');
			$variables['meta_message'] = '1';
			$variables['meta_required'] = 'email';
		} elseif($this->options['email_service']=='phplist') {
			$variables['list['.$$this->options['phplist_list_id'].']'] = 'signup';
			$variables['subscribe'] = "Subscribe";
			$variables["htmlemail"] = "1"; 
			$variables['emailconfirm'] = $emailadres;
			$variables['makeconfirmed']='0';
		}
		
		$encodedVariables = array_map ( 'rawurlencode_callback', $variables, array_keys($variables) );
		$postContent = join('&', $encodedVariables);
		$postContentLen = strlen($postContent);
		$streamCtx = stream_context_create (
			array (
				'http' => array (
					'method' => 'POST',
					'content' => $postContent,
					'header'  => "Content-Type: application/x-www-form-urlencoded\r\nContent-Length: $postContentLen\r\n"
				)
			)
		);
		$fp = @fopen($this->options['form_action'], 'r', FALSE, $streamCtx);		
		@setcookie('ns_subscriber',true,time()+9999999);	
	}

}