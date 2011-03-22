<?php
class Newsletter_SignUp_Admin {
	
	function __construct()
	{
		define('NS_PLUGIN_URL',WP_CONTENT_URL . '/plugins/newsletter-sign-up/');
		define('NS_PLUGIN_DIR',WP_PLUGIN_DIR . '/newsletter-sign-up/');
		add_action('admin_init', array(&$this,'settings_init'));
		add_action('admin_menu', array(&$this,'add_option_page'));
		add_action('admin_print_styles',array(&$this,'add_admin_head'));
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
			<p style="margin:5px">Here you can set the options for your newsletter sign-up. To find the right settings check the source of your 
			newsletter sign up form, and find the 'action' of the form, and the 'name' of the e-mail input field.</p>
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
					<td><input size="25%" type="text" id="ns_form_action" name="ns_options[form_action]" value="<?php echo $options['form_action']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Email identifier (name)</th>
					<td><input size="25%" type="text" name="ns_options[email_id]" id="ns_email_id" value="<?php echo $options['email_id']; ?>" READONLY /></td>
				</tr>
				<tr valign="top" id="ns_aweber_options"<?php if($options['email_service'] != 'aweber') echo ' style="display:none" ';?> >
					<th scope="row">Aweber List name</th>
					<td><input size="25%" type="text" name="ns_options[aweber_list_name]" value="<?php echo $options['aweber_list_name']; ?>" /></td>
				</tr>
				<tr valign="top" id="ns_phplist_options"<?php if($options['email_service'] != 'phplist') echo ' style="display:none" ';?> >
					<th scope="row">PHPList list ID</th>
					<td><input size="2" type="text" name="ns_options[phplist_list_id]" value="<?php if(strlen($options['phplist_list_id']) > 0) { echo $options['phplist_list_id']; } else { echo 1; }; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Text to show after the checkbox</th>
					<td><input size="25%" type="text" name="ns_options[checkbox_text]" value="<?php echo $options['checkbox_text']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Pre-check the checkbox?</th>
					<td><input type="checkbox" name="ns_options[precheck_checkbox]" value="1"<?php if($options['precheck_checkbox']=='1') { echo ' checked="checked"'; } ?> /></td>
				</tr>
				<tr valign="top"><th scope="row">Hide the checkbox for user who used it to subscribe?<span class="ns_small">(uses a cookie)</span></th>
					<td><input type="checkbox" name="ns_options[cookie_hide]" value="1"<?php if($options['cookie_hide'] == '1') { echo ' checked="checked"'; } ?> /></td>
				</tr>
			</table>
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
				</p>
				
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