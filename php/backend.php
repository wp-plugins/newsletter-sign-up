<?php
if(!class_exists('Newsletter_SignUp_Admin')) {

	require_once('backend-abstract.php');

	class Newsletter_SignUp_Admin extends NSU_Plugin_Admin_Abstract{
		
		var $hook 		= 'newsletter-sign-up';
		var $longname	= 'Newsletter Sign-Up Configuration';
		var $shortname	= 'Newsl. Sign-Up';
		var $plugin_url = 'http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/';
		var $optionname = 'ns_options';
		var $filename	= 'newsletter-sign-up/newsletter-sign-up.php';
		var $icon_url 	= '';
		var $bp_active = FALSE;
		var $options = array();
		var $defaults = array(
			'email_service' => '',
			'add_to_comment_form' => 1,
			'checkbox_text' => 'Sign me up to your newsletter!',
			'do_css_reset' => 1,
			'form' => array(
				'email_label' => 'E-mail:',
				'name_label' => 'Name:',
				'submit_button' => 'Sign-Up'
			)
		);
		var $actions;
		
		function __construct()
		{
			parent::__construct();
			
			add_action( 'bp_include', array(&$this,'set_bp_active') );	
			
			// Only do stuff on Newsletter Sign-up admin page.
			if(isset($_GET['page']) && $_GET['page'] == $this->hook) {
		
				// Load settings, predefine some variables
				$this->options = get_option($this->optionname,$this->defaults);
			}
			
		}
		
		/**
		* If buddypress is loaded, set buddypress_active to TRUE
		*/
		function set_bp_active()
		{
			$this->bp_active = TRUE;
		}
				
		function add_admin_scripts()
		{
			parent::add_admin_scripts();
			wp_enqueue_script('ns_admin_js', plugins_url('/js/backend.js',dirname(__FILE__)));
		}
		
		/**
		* Validate the submitted options
		*/
		function validate_options($options)
		{

			if(is_array($options['extra_data'])) :
				foreach($options['extra_data'] as $key => $value) :
					if(empty($value['name'])) unset($options['extra_data'][$key]);
				endforeach;		
			endif;
			
			$options['form']['text_after_signup'] = strip_tags($options['form']['text_after_signup'],'<a><b><strong><i><img><em><br>');
			
			return $options;
		}
		
		/**
		* Create the option page
		*/
		function options_page_default()
		{
         					
					$viewed_mp = NULL;
					if(!empty($_GET['mp'])) $viewed_mp = $_GET['mp'];
					elseif(empty($_GET['mp']) && isset($this->options['email_service'])) $viewed_mp = $this->options['email_service'];
					if(!in_array($viewed_mp,array('mailchimp','icontact','aweber','phplist','ymlp','other'))) $viewed_mp = NULL;
					
					// Fill in some predefined values if options not set or set for other newsletter service
					if(!isset($this->options['email_service']) || $this->options['email_service'] != $viewed_mp) {
						switch($viewed_mp) {
						
							case 'mailchimp': 
								if(empty($this->options['email_id'])) $this->options['email_id'] = 'EMAIL';
								if(empty($this->options['name_id'])) $this->options['name_id'] = 'NAME';
							break;
							
							case 'ymlp': 
								if(empty($this->options['email_id'])) $this->options['email_id'] = 'YMP0'; 
							break;
							
							case 'aweber':
								if(empty($this->options['form_action'])) $this->options['form_action'] = 'http://www.aweber.com/scripts/addlead.pl';
								if(empty($this->options['email_id'])) $this->options['email_id'] = 'email'; 
								if(empty($this->options['name_id'])) $this->options['name_id'] = 'name';
							break;
							
							case 'icontact':
								if(empty($this->options['email_id'])) $this->options['email_id'] = 'fields_email'; 
							break;
						}
					}
      
               require 'views/options_page.html.php';
					 
		}
      
      function options_page_config_helper()
      {
      
         if(isset($_POST['form'])) {
            $error = true;
            
            $form = $_POST['form'];
            
            // strip unneccessary tags
            $form = strip_tags($form,'<form><input><button>');
            
            
            preg_match_all("'<(.*?)>'si",$form,$matches);
            
            if(is_array($matches) && isset($matches[0])) {
               $matches = $matches[0];
               $html = stripslashes(join('',$matches));
               
               $clean_form = htmlspecialchars(str_replace(array('><','<input'),array(">\n<","\t<input"),$html),ENT_NOQUOTES);
               
                $doc = new DOMDocument();
                $doc->strictErrorChecking = FALSE;
                $doc->loadHTML($html);
                $xml = simplexml_import_dom($doc);
                
                if($xml) {
                  $result = true;
                  $form = $xml->body->form;
                  
                  if($form) {
                        unset($error);
                       $form_action = (isset($form['action'])) ? $form['action'] : 'Can\'t help you on this one..';
                       
                        if($form->input) {
                  
                           $additional_data = array();
                           
                           /* Loop trough input fields */
                           foreach($form->input as $input) {
                           
                               // Check if this is a hidden field
                              if($input['type'] == 'hidden') {
                                $additional_data[] = array($input['name'],$input['value']);
                              // Check if this is the input field that is supposed to hold the EMAIL data
                              } elseif(stripos($input['id'],'email') !== FALSE || stripos($input['name'],'email') !== FALSE) {
                                 $email_identifier = $input['name'];
                              
                              // Check if this is the input field that is supposed to hold the NAME data
                              } elseif(stripos($input['id'],'name') !== FALSE || stripos($input['name'],'name') !== FALSE) {
                                 $name_identifier = $input['name'];
                              }
                              
                           }
                           
                        }
                  }
                  
                 
                  
                  // Correct value's
                  if(!isset($email_identifier)) $email_identifier = 'Can\'t help you on this one..';
                  if(!isset($name_identifier)) $name_identifier = 'Can\'t help you on this one. Not using name data?';

                  
                }
               
            }

         }
         
         require 'views/options_page_config_helper.html.php';
         
      }
		
		/**
		* Show the rows that are unique for some mailinglist providers (i.e. MC API or YMLP API)
		* @param mailinglist: the mailinglist provider that is being viewed
		*/
		function mailinglist_specific_rows($mailinglist)
		{

			switch($mailinglist) {
			
				case 'mailchimp': ?>
					<tr valign="top">
						<th scope="row"><label for="use_api">Use MailChimp API? <span class="ns_small">(recommended)</span></label></th>
						<td><input type="checkbox" id="use_api" name="ns_options[use_api]" value="1"<?php if(isset($this->options['use_api']) && $this->options['use_api']=='1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					<tbody class="api_rows" <?php if(!isset($this->options['use_api']) || $this->options['use_api'] != 1) echo ' style="display:none" ';?>>
						<tr valign="top"><th scope="row">MailChimp API Key <a target="_blank" href="http://admin.mailchimp.com/account/api">(?)</a></th>
							<td><input size="50%" type="text" id="api_key" name="ns_options[api_key]" value="<?php if(isset($this->options['api_key'])) echo $this->options['api_key']; ?>" /></td>
						</tr>
						<tr valign="top"><th scope="row">MailChimp List ID <a href="http://www.mailchimp.com/kb/article/how-can-i-find-my-list-id" target="_blank">(?)</a></th>
							<td><input size="50%" type="text" name="ns_options[list_id]" value="<?php if(isset($this->options['list_id'])) echo $this->options['list_id']; ?>" /></td>
						</tr>
					</tbody>
				<?php
				break;
				
				case 'ymlp': ?>
					<tr valign="top"><th scope="row"><label for="use_api">Use the YMLP API? <span class="ns_small">(recommended)</span></label></th>
						<td><input type="checkbox" id="use_api" name="ns_options[use_api]" value="1"<?php if(isset($this->options['use_api']) && $this->options['use_api']=='1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					
					<tbody class="api_rows"<?php if(!isset($this->options['use_api']) || $this->options['use_api'] != 1) echo ' style="display:none" ';?>>
						<tr valign="top"><th scope="row">YMLP API Key <a target="_blank" href="http://www.ymlp.com/app/api.php">(?)</a></th>
							<td><input size="50%" type="text" id="ymlp_api_key" name="ns_options[ymlp_api_key]" value="<?php if(isset($this->options['ymlp_api_key'])) echo $this->options['ymlp_api_key']; ?>" /></td>
						</tr>
						<tr valign="top"><th scope="row">YMLP Username</th>
							<td><input size="50%" type="text" id="ymlp_username" name="ns_options[ymlp_username]" value="<?php if(isset($this->options['ymlp_username'])) echo $this->options['ymlp_username']; ?>" /></td>
						</tr>
						<tr valign="top"><th scope="row">YMLP GroupID<span class="ns_small">(starts at 1, check URL when 'viewing all contacts' in certain group)</span></th>
							<td><input size="50%" type="text" id="ymlp_groupid" name="ns_options[ymlp_groupid]" value="<?php if(isset($this->options['ymlp_groupid'])) echo $this->options['ymlp_groupid']; ?>" /></td>
						</tr>
					</tbody>
				<?php				
				break;
				
				case 'phplist': ?>
					<tr valign="top">
						<th scope="row">PHPList list ID</th>
						<td><input size="2" type="text" name="ns_options[phplist_list_id]" value="<?php if(isset($this->options['phplist_list_id'])) { echo $this->options['phplist_list_id']; } else { echo 1; }; ?>" /></td>
					</tr>
				<?php
				break;
				
				case 'aweber': ?>
					<tr valign="top">
						<th scope="row">Aweber list name</th>
						<td><input size="25%" type="text" name="ns_options[aweber_list_name]" value="<?php if(isset($this->options['aweber_list_name'])) echo $this->options['aweber_list_name']; ?>" /></td>
					</tr>
				<?php
				break;
				
				
			}	
		
		}
		
		
	}
	
	
	
}