<?php
if(!class_exists('Newsletter_SignUp_Admin')) {

	require_once('dvk-plugin-admin.php');

	class Newsletter_SignUp_Admin extends DvK_Plugin_Admin{
		
		var $hook 		= 'newsletter-sign-up';
		var $longname	= 'Newsletter Sign-Up Configuration';
		var $shortname	= 'Newsletter Sign-Up';
		var $filename	= 'newsletter-sign-up/newsletter-sign-up.php';
		var $bp_active = false;
		
		function __construct()
		{
			parent::__construct();			
			add_action('admin_init', array(&$this,'settings_init'));
			
			// if buddypress is loaded, set buddypress_active to true
			add_action( 'bp_include', array(&$this,'set_bp_active') );
		}
		
		function set_bp_active()
		{
			$this->bp_active = true;
		}

		function settings_init()
		{
			register_setting('ns_options_group', 'ns_options',array(&$this,'check_options'));
		}
				
		function add_admin_scripts()
		{
			parent::add_admin_scripts();
			wp_enqueue_script('ns_admin_js', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname($this->filename)).'/js/backend.js');
		}
		
		function check_options($options)
		{
			if(is_array($options['extra_data'])) :
				foreach($options['extra_data'] as $key => $value) :
					if(empty($value['name'])) unset($options['extra_data'][$key]);
				endforeach;		
			endif;
			
			return $options;
		}
		
		function option_page()
		{
			$this->setup_admin_page("Newsletter Sign-Up Settings","Newsletter Sign-Up Configuration Settings");
		?>
				<p style="margin:10px">Here you can configure the plugin. In order for the plugin to work properly you need to atleast provide
				a form action and an email identifier. For more information on what to fill in check out <a target="_blank" href="http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/">this post on my blog</a>.</p>
				<form method="post" id="ns_settings_page" action="options.php">
					<?php settings_fields('ns_options_group');  $options = get_option('ns_options'); ?>
					<table class="form-table">
					<tr valign="top"><th scope="row">Newsletter Service</th>
						<td>
							<select name="ns_options[email_service]" id="ns_email_service">
								<option value="mailchimp"<?php if($options['email_service'] == 'mailchimp') echo ' SELECTED';?> >MailChimp/FeedBlitz</option>
								<option value="icontact"<?php if($options['email_service'] == 'icontact') echo ' SELECTED';?> >iContact</option>
								<option value="aweber"<?php if($options['email_service'] == 'aweber') echo ' SELECTED';?> >Aweber</option>
								<option value="phplist"<?php if($options['email_service'] == 'phplist') echo ' SELECTED';?> >PHPList</option>
								<option value="ymlp"<?php if($options['email_service'] == 'ymlp') echo ' SELECTED';?> >YMLP</option>
								<option value="other"<?php if($options['email_service'] == 'other') echo ' SELECTED';?> >Other/Advanced</option>
							</select>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Newsletter form action</th>
						<td><input size="25%" type="text" id="ns_form_action" name="ns_options[form_action]" value="<?php if(isset($options['form_action'])) echo $options['form_action']; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row">Email identifier <span class="ns_small">name attribute of input field that holds the emailadress</span></th>
						<td><input size="25%" type="text" name="ns_options[email_id]" id="ns_email_id" value="<?php if(isset($options['email_id'])) echo $options['email_id']; ?>" READONLY /></td>
					</tr>

					<tr valign="top" id="ns_aweber_options"<?php if($options['email_service'] != 'aweber') echo ' style="display:none" ';?> >
						<th scope="row">Aweber List name</th>
						<td><input size="25%" type="text" name="ns_options[aweber_list_name]" value="<?php if(isset($options['aweber_list_name'])) echo $options['aweber_list_name']; ?>" /></td>
					</tr>
					<tr valign="top" id="ns_phplist_options"<?php if($options['email_service'] != 'phplist') echo ' style="display:none" ';?> >
						<th scope="row">PHPList list ID</th>
						<td><input size="2" type="text" name="ns_options[phplist_list_id]" value="<?php if(isset($options['phplist_list_id'])) { echo $options['phplist_list_id']; } else { echo 1; }; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row"><label for="ns_subscribe_with_name">Subscribe with name?</label></th>
						<td><input type="checkbox" id="ns_subscribe_with_name" name="ns_options[subscribe_with_name]" value="1"<?php if(isset($options['subscribe_with_name']) && $options['subscribe_with_name']=='1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					<tr id="ns_email_id_row" valign="top"<?php if($options['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name identifier <span class="ns_small">name attribute of input field that holds the name</span></th>
						<td><input size="25%" id="ns_name_id" type="text" name="ns_options[name_id]" value="<?php if(isset($options['name_id'])) echo $options['name_id']; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row">Text to show after the checkbox</th>
						<td><input size="25%" type="text" name="ns_options[checkbox_text]" value="<?php if(isset($options['checkbox_text'])) echo $options['checkbox_text']; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row"><label for="ns_precheck_checkbox">Pre-check the checkbox?</label></th>
						<td><input type="checkbox" id="ns_precheck_checkbox" name="ns_options[precheck_checkbox]" value="1"<?php if(isset($options['precheck_checkbox']) && $options['precheck_checkbox']=='1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					<tr valign="top"><th scope="row"><label for="ns_add_to_reg_form">Add the checkbox to registration form?</label></th>
						<td><input type="checkbox" id="ns_add_to_reg_form" name="ns_options[add_to_reg_form]" value="1"<?php if(isset($options['add_to_reg_form']) && $options['add_to_reg_form'] == '1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					<?php if($this->bp_active == true) { ?>
					<tr valign="top"><th scope="row"><label for="ns_add_to_reg_form">Add the checkbox to BuddyPress sign-up form?</label></th>
						<td><input type="checkbox" id="ns_add_to_bp_form" name="ns_options[add_to_bp_form]" value="1"<?php if(isset($options['add_to_bp_form']) && $options['add_to_bp_form'] == '1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					<?php } ?>
					<tr valign="top"><th scope="row"><label for="ns_cookie_hide">Hide the checkbox for user who used it to subscribe?</label><span class="ns_small">(uses a cookie)</span></th>
						<td><input type="checkbox" id="ns_cookie_hide" name="ns_options[cookie_hide]" value="1"<?php if(isset($options['cookie_hide']) && $options['cookie_hide'] == '1') { echo ' checked="checked"'; } ?> /></td>
					</tr>
					
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
				</p>
				
				
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><span>Additional data</span></h3>
					<div class="inside">
					<p style="margin:10px;">
						Want to send some additional data to your newsletter service? Specify the name / keys and values here and it will be sent along with the other required fields. Just empty the name field and hit save to delete a name / value pair.
					</p>
					<table class="form-table">
						<tr valign="top">
							<th scope="column">Name</th>
							<th scope="column">Value</th>
						</tr>
					<?php 
					$last_key = 0;
					
					if(isset($options['extra_data']) && is_array($options['extra_data'])) :
						foreach($options['extra_data'] as $key => $value) : ?>
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
					</form>
			<?php
			$this->close_admin_page();
		}
	}
}