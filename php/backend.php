<?php
class Newsletter_SignUp_Admin {
	
	var $bp_active = false;
	
	function __construct()
	{
		define('NS_PLUGIN_URL',WP_CONTENT_URL . '/plugins/newsletter-sign-up/');
		define('NS_PLUGIN_DIR',WP_PLUGIN_DIR . '/newsletter-sign-up/');
		
		add_action('admin_init', array(&$this,'settings_init'));
		add_action('admin_menu', array(&$this,'add_option_page'));
		add_action('admin_print_styles-settings_page_ns-options',array(&$this,'add_admin_head'));
		
		add_filter("plugin_action_links_newsletter-sign-up/newsletter-sign-up.php", array(&$this,'add_settings_link'));
		
		// if buddypress is loaded, set buddypress_active to true
		add_action( 'bp_include', array(&$this,'set_bp_active') );
	}
	
	function set_bp_active()
	{
		$this->bp_active = true;
	}
	
	function add_settings_link($links) { 
		$settings_link = '<a href="options-general.php?page=ns-options.php">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}

	function settings_init()
	{
		register_setting('ns_options_group', 'ns_options');
	}
	
	function add_option_page()
	{
		add_options_page('Newsletter Sign-Up Options', 'Newsletter Sign-Up', 'manage_options', 'ns-options', array(&$this,'ns_option_page'));
	}
	
	function add_admin_head()
	{
		wp_enqueue_style('ns_admin_css', NS_PLUGIN_URL.'css/backend.css');
		wp_enqueue_script('jquery');
		wp_enqueue_script('ns_admin_js', NS_PLUGIN_URL.'js/backend.js');
	}
	
	function ns_option_page()
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
				</form>
		<?php
		$this->close_admin_page();
	}
	
	function setup_admin_page($title,$subtitle)
	{
		?>
		<div class="wrap">
		<h2><?php echo $title; ?></h2>
		<div class="postbox-container" style="width:70%;">
			<div class="metabox-holder">	
				<div class="meta-box-sortables">
					<div class="postbox">
						<h3 class="hndle"><span><?php echo $subtitle; ?></span></h3>
						<div class="inside">
		<?php
	}
	
	function close_admin_page()
	{
		?>
		</div></div></div></div></div></div>
			<?php include(NS_PLUGIN_DIR.'php/backend-right-sidebar.php'); ?>
		</div>
		<?php
	}
}