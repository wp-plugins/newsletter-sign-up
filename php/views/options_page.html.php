<div class="wrap" id="<?php echo $this->hook; ?>">
			<h2><a href="http://dannyvankooten.com/" target="_blank"><span id="dvk-avatar"></span></a>Newsletter Sign-Up Settings</h2>
			<div class="postbox-container" style="width:65%;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">
						<div class="postbox">
							<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
							<h3 class="hndle"><span>Newsletter Sign-Up Configuration Settings</span></h3>
							<div class="inside">
                        <p>Here you can configure the plugin. In order for the plugin to work properly you need to at least provide
                        a form action and an e-mail identifier. For more information on what to fill in check out <a target="_blank" href="http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/">this post on my blog</a> or use the <a href="admin.php?page=newsletter-sign-up/config-helper">configuration extractor tool.</a></p>
                     </div>
                 </div>
                 <div class="postbox">
                        <div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
                        <h3 class="hndle"><span>General settings - Newsletter Configuration</span></span></h3>
                           <div class="inside">
                           <form method="post" action="options.php" id="ns_settings_page">
                           <?php settings_fields('ns_options_group'); ?>
                           <input type="hidden" name="ns_options[date_installed]" value="<?php if(isset($this->options['date_installed'])) echo $this->options['date_installed']; ?>" />
					<input type="hidden" name="ns_options[dontshowpopup]" value="<?php if(isset($this->options['dontshowpopup'])) echo $this->options['dontshowpopup']; ?>" />
					<input type="hidden" name="ns_options[load_widget_styles]" value="<?php if(isset($this->options['load_widget_styles'])) echo $this->options['load_widget_styles']; ?>" />
					<table class="form-table">			
						<tr valign="top">
							<th scope="row">Select your mailinglist provider: </th>
							<td>
								<select name="ns_options[email_service]" id="ns_mp_provider" onchange="document.location.href = 'admin.php?page=<?php echo $this->hook; ?>&mp=' + this.value">
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
							<tr class="name_dependent" valign="top"<?php if(!isset($this->options['subscribe_with_name']) || $this->options['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name identifier <span class="ns_small">name attribute of input field that holds the name</span></th>
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
                  <?php $last_key = 0;
					
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
							<tr valign="top" class="name_dependent" <?php if(!isset($this->options['subscribe_with_name']) || $this->options['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name label <span class="ns_small">(if using subscribe with name)</span></th>
								<td>
									<input size="50%" type="text" name="ns_options[form][name_label]" value="<?php if(isset($this->options['form']['name_label'])) echo $this->options['form']['name_label']; ?>" /><br />
									<input type="checkbox" id="name_required" name="ns_options[form][name_required]" value="1"<?php if(isset($this->options['form']['name_required']) && $this->options['form']['name_required'] == '1') { echo ' checked="checked"'; } ?> />
									<label for="name_required">Name is a required field?</label>
								</td>
							
							</tr>
							<tr valign="top"><th scope="row">Submit button value</th>
								<td><input size="50%" type="text" name="ns_options[form][submit_button]" value="<?php if(isset($this->options['form']['submit_button'])) echo $this->options['form']['submit_button']; ?>" /></td>
							</tr>
							<tr valign="top"><th scope="row">Text after submitting the sign-up form</th>
								<td><textarea rows="5" cols="50" name="ns_options[form][text_after_signup]"><?php if(isset($this->options['form']['text_after_signup'])) echo $this->options['form']['text_after_signup']; ?></textarea></td>
							</tr>
							<tr valign="top"><th scope="row"><label for="ns_load_form_styles">Load some default CSS</label><span class="ns_small">(check this for some default styling of the labels and input fields)</span></th>
								<td><input type="checkbox" id="ns_load_form_styles" name="ns_options[form][load_form_css]" value="1" <?php if(isset($this->options['form']['load_form_css']) && $this->options['form']['load_form_css'] == 1) echo 'CHECKED'; ?> /></td>
							</tr>
						</table>
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
				</p>
				<p class="nsu-tip">
					Did you know that you can easily edit your widget's sign-up form text by installing the <a href="http://dannyvankooten.com/wordpress-plugins/wysiwyg-widgets/" target="_blank">WYSIWYG Widgets plugin</a> alongside this plugin?
				</p>
					</form>
				<br style="clear:both;" />
            </div></div></div></div></div></div>
		<div class="postbox-container" style="width:30%;">
			<div class="metabox-holder">	
				<div class="meta-box-sortables">						
					<?php
						$this->likebox();
						$this->donate_box();
						$this->latest_posts();
						$this->support_box();
						$content = '<p>Looking for more neat plugins or random tips on how to improve your WordPress website? Look around
							on my blog: <a href="http://DannyvanKooten.com" target="_blank">DannyvanKooten.com</a>.</p>';
						$this->postbox($this->hook.'-bloglink-box',"Looking for more tools and tips?",$content);
					?>				
				</div>
			</div>
		</div>
	</div>
   <?php if(isset($this->actions['show_donate_box']) && $this->actions['show_donate_box']) { ?>
				<div id="dvk-donate-box">
					<div id="dvk-donate-box-content">
						<img width="16" height="16" class="dvk-close" src="<?php echo plugins_url('/img/close.png',dirname(__FILE__)); ?>" alt="X">
						<h3>Support me</h3>
						<p>I noticed you've been using <?php echo $this->shortname; ?> for at least 30 days, would you like to show me a token of your appreciation by buying me a beer or tweet about <?php echo $this->shortname; ?>?</p>
						
						<table>
							<tr>
								<td>
								<form id="dvk_donate" target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
									<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/nl_NL/i/scr/pixel.gif" width="1" height="1">
								</form>
								</td>
								<td>
									<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $this->plugin_url; ?>" data-text="Showing my appreciation to @DannyVKI for his awsome #WordPress plugin: <?php echo $this->shortname; ?>" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
								</td>
							</tr>
						</table>
						<a class="dvk-dontshow" href="options-general.php?page=<?php echo $this->hook ?>&dontshowpopup=1">(do not show me this pop-up again)</a>
					</div>
				</div>
			<?php 
			} ?>