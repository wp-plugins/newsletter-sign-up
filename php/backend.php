<?php
if(!class_exists('Newsletter_SignUp_Admin')) {

	require_once('dvk-plugin-admin.php');

	class Newsletter_SignUp_Admin extends DvK_Plugin_Admin{
		
		var $hook 		= 'newsletter-sign-up';
		var $longname	= 'Newsletter Sign-Up Configuration';
		var $shortname	= 'Newsletter Sign-Up';
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
			
			$this->add_hooks();
			
			// Only do stuff on Newsletter Sign-up admin page.
			if(isset($_GET['page']) && $_GET['page'] == $this->hook) {
		
				// Load settings, predefine some variables
				$this->options = get_option($this->optionname,$this->defaults);
			}
			
		}
		
		function add_hooks()
		{
			add_action( 'bp_include', array(&$this,'set_bp_active') );	
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
		function option_page()
		{
			$this->setup_admin_page("Newsletter Sign-Up Settings","Newsletter Sign-Up Configuration Settings");
		?>
				<p>Here you can configure the plugin. In order for the plugin to work properly you need to at least provide
				a form action and an e-mail identifier. For more information on what to fill in check out <a target="_blank" href="http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/">this post on my blog</a>.</p>
				</div>
			</div>
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
				<h3 class="hndle"><span>General settings - Newsletter Configuration</span></span></h3>
					<div class="inside">
					<form method="post" action="options.php" id="ns_settings_page">
				<?php 
					settings_fields('ns_options_group');
					
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
				?>
					<input type="hidden" name="ns_options[date_installed]" value="<?php if(isset($this->options['date_installed'])) echo $this->options['date_installed']; ?>" />
					<input type="hidden" name="ns_options[dontshowpopup]" value="<?php if(isset($this->options['dontshowpopup'])) echo $this->options['dontshowpopup']; ?>" />
					<input type="hidden" name="ns_options[load_widget_styles]" value="<?php if(isset($this->options['load_widget_styles'])) echo $this->options['load_widget_styles']; ?>" />
					<table class="form-table">			
						<tr valign="top">
							<th scope="row">Select your mailinglist provider: </th>
							<td>
								<select name="ns_options[email_service]" id="ns_mp_provider" onchange="document.location.href = 'options-general.php?page=<?php echo $this->hook; ?>&mp=' + this.value">
									<option value="other"<?php if($viewed_mp == NULL || $viewed_mp == 'other') echo ' SELECTED';?>>-- other / advanced</option>
									<option value="mailchimp"<?php if($viewed_mp == 'mailchimp') echo ' SELECTED';?> >MailChimp</option>
									<option value="ymlp"<?php if($viewed_mp == 'ymlp') echo ' SELECTED';?> >YMLP</option>
									<option value="icontact"<?php if($viewed_mp == 'icontact') echo ' SELECTED';?> >iContact</option>
									<option value="aweber"<?php if($viewed_mp == 'aweber') echo ' SELECTED';?> >Aweber</option>
									<option value="phplist"<?php if($viewed_mp == 'phplist') echo ' SELECTED';?> >PHPList</option>
								</select>
							</td>
						</tr>
						<?php $this->mailinglist_specific_rows($viewed_mp); ?>
						<tbody class="form_rows"<?php if(isset($viewed_mp) && in_array($viewed_mp,array('mailchimp','ymlp')) && isset($this->options['use_api']) && $this->options['use_api'] == 1) echo ' style="display:none" ';?>>
							<tr valign="top"><th scope="row">Newsletter form action</th>
								<td><input size="50%" type="text" id="ns_form_action" name="ns_options[form_action]" value="<?php if(isset($this->options['form_action'])) echo $this->options['form_action']; ?>" /></td>
							</tr>
							<tr valign="top"><th scope="row">E-mail identifier <span class="ns_small">name attribute of input field that holds the emailadress</span></th>
								<td><input size="50%" type="text" name="ns_options[email_id]" value="<?php if(isset($this->options['email_id'])) echo $this->options['email_id']; ?>"/></td>
							</tr>
						</tbody>
						<tbody>
							<tr valign="top"><th scope="row"><label for="subscribe_with_name">Subscribe with name?</label></th>
								<td><input type="checkbox" id="subscribe_with_name" name="ns_options[subscribe_with_name]" value="1"<?php if(isset($this->options['subscribe_with_name']) && $this->options['subscribe_with_name']=='1') { echo ' checked="checked"'; } ?> /></td>
							</tr>
							<tr id="email_id_row" valign="top"<?php if(!isset($this->options['subscribe_with_name']) || $this->options['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name identifier <span class="ns_small">name attribute of input field that holds the name</span></th>
								<td><input size="25%" id="ns_name_id" type="text" name="ns_options[name_id]" value="<?php if(isset($this->options['name_id'])) echo $this->options['name_id']; ?>" /></td>
							</tr>
						</tbody>
						</table>
						<p style="margin:10px;">
							For some newsletter services you need to specify some static data, like a list ID or your account name. You can specify the name / keys and values here and it will be sent along with the other required fields. Just empty the name field and hit save to delete a name / value pair.
						</p>
					<table class="form-table">
						<tr valign="top">
							<th scope="column" style="font-weight:bold;">Name</th>
							<th scope="column" style="font-weight:bold;">Value</th>
						</tr>
					<?php 
					$last_key = 0;
					
					if(isset($this->options['extra_data']) && is_array($this->options['extra_data'])) :
						foreach($this->options['extra_data'] as $key => $value) : ?>
							<tr valign="top">
								<td><input size="50%" type="text" name="ns_options[extra_data][<?php echo $key; ?>][name]" value="<?php echo $value['name']; ?>" /></td>
								<td><input size="50%" type="text" name="ns_options[extra_data][<?php echo $key; ?>][value]" value="<?php echo $value['value']; ?>" /></td>
							</tr>					
						<?php
						$last_key = $key + 1;
						endforeach; 
					endif; ?>
					<tr valign="top">
						<td><input size="50%" type="text" name="ns_options[extra_data][<?php echo $last_key; ?>][name]" value="" /></td>
						<td><input size="50%" type="text" name="ns_options[extra_data][<?php echo $last_key; ?>][value]" value="" /></td>
					</tr>
				</table>
					<p class="submit">
						<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
					</p>
					</div>
				</div>
				<div class="postbox">
					<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
					<h3 class="hndle"><span>Sign-up Checkbox settings</span></h3>
					<div class="inside">
					<table class="form-table">
					<tr valign="top"><th scope="row">Text to show after the checkbox</th>
						<td><input size="50%" type="text" name="ns_options[checkbox_text]" value="<?php if(isset($this->options['checkbox_text'])) echo $this->options['checkbox_text']; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row"><label for="ns_precheck_checkbox">Pre-check the checkbox?</label></th>
						<td><input type="checkbox" id="ns_precheck_checkbox" name="ns_options[precheck_checkbox]" value="1"<?php if(isset($this->options['precheck_checkbox']) && $this->options['precheck_checkbox']=='1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="do_css_reset">Do a CSS 'reset' on the checkbox.</label> <span class="ns_small">(check this if checkbox appears in a weird place)</span></th>
						<td><input type="checkbox" id="do_css_reset" name="ns_options[do_css_reset]" value="1"<?php if(isset($this->options['do_css_reset']) && $this->options['do_css_reset']=='1') { echo ' checked="checked"'; } ?>  /> </td>
					</tr>
					<tr valign="top"><th scope="row">Where to show the sign-up checkbox?</th>
						<td>
							<input type="checkbox" id="add_to_comment_form" name="ns_options[add_to_comment_form]" value="1"<?php if(isset($this->options['add_to_comment_form']) && $this->options['add_to_comment_form'] == '1') { echo ' checked="checked"'; } ?> /> <label for="add_to_comment_form">WordPress comment form</label><br />
							<input type="checkbox" id="add_to_reg_form" name="ns_options[add_to_reg_form]" value="1"<?php if(isset($this->options['add_to_reg_form']) && $this->options['add_to_reg_form'] == '1') { echo ' checked="checked"'; } ?> /> <label for="add_to_reg_form">WordPress registration form</label><br />
							<?php 
							if($this->bp_active == TRUE) { ?>
								<input type="checkbox" id="add_to_bp_form" name="ns_options[add_to_bp_form]" value="1"<?php if(isset($this->options['add_to_bp_form']) && $this->options['add_to_bp_form'] == '1') { echo ' checked="checked"'; } ?> /> <label for="add_to_bp_form">BuddyPress registration form</label><br />
							<?php 
							}
							if(defined('MULTISITE') && MULTISITE == TRUE) {
							?>
							<input type="checkbox" id="add_to_ms_form" name="ns_options[add_to_ms_form]" value="1"<?php if(isset($this->options['add_to_ms_form']) && $this->options['add_to_ms_form'] == '1') { echo ' checked="checked"'; } ?> /> <label for="add_to_ms_form">MultiSite registration form</label><br />
							<?php } ?>
						</td>
					</tr>
					<tr valign="top"><th scope="row"><label for="ns_cookie_hide">Hide the checkbox for user who used it to subscribe?</label><span class="ns_small">(uses a cookie)</span></th>
						<td><input type="checkbox" id="ns_cookie_hide" name="ns_options[cookie_hide]" value="1"<?php if(isset($this->options['cookie_hide']) && $this->options['cookie_hide'] == '1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					
				</table>
				
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
				</p>
				
				
				</div>
			</div>
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
				<h3 class="hndle" id="nsu-form-settings"><span>Sign-up form settings</span></h3>
					<div class="inside">
						<table class="form-table">
							<tr valign="top"><th scope="row">E-mail label</th>
								<td><input size="50%" type="text" name="ns_options[form][email_label]" value="<?php if(isset($this->options['form']['email_label'])) echo $this->options['form']['email_label']; ?>" /></td>
							</tr>
							<tr valign="top"><th scope="row">Name label <span class="ns_small">(if using subscribe with name)</span></th>
								<td><input size="50%" type="text" name="ns_options[form][name_label]" value="<?php if(isset($this->options['form']['name_label'])) echo $this->options['form']['name_label']; ?>" /></td>
							</tr>
							<tr valign="top"><th scope="row">Submit button value</th>
								<td><input size="50%" type="text" name="ns_options[form][submit_button]" value="<?php if(isset($this->options['form']['submit_button'])) echo $this->options['form']['submit_button']; ?>" /></td>
							</tr>
							<tr valign="top"><th scope="row">Text after sign-up</th>
								<td><textarea rows="5" cols="50" name="ns_options[form][text_after_signup]"><?php if(isset($this->options['form']['text_after_signup'])) echo $this->options['form']['text_after_signup']; ?></textarea></td>
							</tr>
							<tr valign="top"><th scope="row"><label for="ns_load_form_styles">Load some default CSS</label><span class="ns_small">(check this for some default styling of the labels and input fields)</span></th>
								<td><input type="checkbox" id="ns_load_form_styles" name="ns_options[form][load_form_css]" value="1" <?php if(isset($this->options['form']['load_form_css']) && $this->options['form']['load_form_css'] == 1) echo 'CHECKED'; ?> /></td>
							</tr>
						</table>
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
				</p>
					</form>
				
			<?php $this->close_admin_page();
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