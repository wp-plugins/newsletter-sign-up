<?php
if(!class_exists('NewsletterSignUpAdmin')) {

	class NewsletterSignUpAdmin {
		
		var $hook 		= 'newsletter-sign-up';
		var $longname	= 'Newsletter Sign-Up Configuration';
		var $shortname	= 'Newsletter Sign-Up';
		var $plugin_url = 'http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/';
		var $optionname = 'ns_options';
		var $filename	= 'newsletter-sign-up/newsletter-sign-up.php';
		var $accesslvl 	= 'manage_options';
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
			$this->icon_url = plugins_url('/backend/img/icon.png',dirname(__FILE__));
         
			add_filter("plugin_action_links_{$this->filename}", array(&$this,'add_settings_link'));
			add_action('admin_menu', array(&$this,'add_option_page'));
			add_action('admin_init', array(&$this,'settings_init'));
			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));	
			register_deactivation_hook($this->filename, array(&$this,'remove_options'));
			
			
			/* Only do stuff on admin page of this plugin */			
			if(isset($_GET['page']) && stripos($_GET['page'],$this->hook) !== FALSE) {
				add_action("admin_print_styles",array(&$this,'add_admin_styles'));
				add_action("admin_print_scripts",array(&$this,'add_admin_scripts'));
				
				$this->options = get_option($this->optionname);
				$this->check_usage_time();
				
			}
			
			
			add_action( 'bp_include', array(&$this,'set_bp_active') );	
						
		}
		
		/**
		* If buddypress is loaded, set buddypress_active to TRUE
		*/
		function set_bp_active()
		{
			$this->bp_active = TRUE;
		}
		
		function remove_options()
		{
			delete_option($this->optionname);
		}
		
		function add_admin_styles()
		{
			wp_enqueue_style( $this->hook . '_css', plugins_url('/backend/css/backend.css',dirname(__FILE__)));
		}
				
		function add_admin_scripts()
		{
			wp_enqueue_script(array('jquery','dashboard','postbox'));
			wp_enqueue_script('ns_admin_js', plugins_url('/backend/js/backend.js',dirname(__FILE__)));
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
		
		function donate_box()
		{
			$content = '<p>This plugin cost me countless hours of work. If you use it, please donate a token of your appreciation!</p>
					<center>
					<form id="dvk_donate" target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
						<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/nl_NL/i/scr/pixel.gif" width="1" height="1">
					</form>
				</center>';
			$this->postbox($this->hook.'-donatebox','Donate $10, $20 or $50!',$content);		
		}
		
		function latest_posts()
		{
			require_once(ABSPATH.WPINC.'/rss.php');  
			if ( $rss = fetch_rss( 'http://feeds.feedburner.com/dannyvankooten' ) ) {
				$content = '<ul>';
				$rss->items = array_slice( $rss->items, 0, 5 );
				
				foreach ( (array) $rss->items as $item ) {
					$content .= '<li class="dvk-rss-item">';
					$content .= '<a target="_blank" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. $item['title'] .'</a> ';
					$content .= '</li>';
				}
				$content .= '<li class="dvk-rss"><a href="http://dannyvankooten.com/feed/">Subscribe to my RSS feed</a></li>';
				$content .= '<li class="dvk-email"><a href="http://dannyvankooten.com/newsletter/">Subscribe by email</a></li>';
				$content .= '</ul><br style="clear:both;" />';
			} else {
				$content = '<p>No updates..</p>';
			}
			$this->postbox($this->hook.'-latestpostbox','Latest blog posts..',$content);
		}
		
		function likebox()
		{
			$content = '<p>Consider the following options, please:</p>
				<ul>
					<li><a href="http://DannyvanKooten.com/donate/" target="_blank">Buy me a beer</a></li>
					<li><a href="http://wordpress.org/extend/plugins/'.$this->hook.'/" target="_blank">Give it a good rating on WordPress.org.</a></li>
					<li>Tell others about this plugin.</li>
				</ul>';
			$this->postbox($this->hook.'-likebox','Like this plugin?',$content);
		
		}
		
		function support_box()
		{
			$content = '<p>Are you having trouble setting-up '.$this->shortname.', experiencing an error or got a great idea on how to improve it?</p><p>Please, post
				your question or tip in the <a target="_blank" href="http://wordpress.org/tags/'.$this->hook.'">Support forums</a> on WordPress.org</p>';
			$this->postbox($this->hook.'-support-box',"Looking for support?",$content);
		}
		
		function postbox($id,$title,$content)
		{
		?>
			<div id="<?php echo $id; ?>" class="postbox dvk-box">		
				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>			
				</div>
			</div>
		<?php			
		}
				
		function dashboard_widget() {
			$options = get_option('dvkdbwidget');
			if (isset($_POST['dvk_removedbwidget'])) {
				$options['dontshow'] = true;
				update_option('dvkdbwidget',$options);
			}		
			
			if (isset($options['dontshow']) && $options['dontshow']) {
				echo "If you reload, this widget will be gone and never appear again, unless you decide to delete the database option 'dvkdbwidget'.";
				return;
			}
			
			require_once(ABSPATH.WPINC.'/rss.php');
			if ( $rss = fetch_rss( 'http://feeds.feedburner.com/dannyvankooten' ) ) {
				echo '<div class="rss-widget">';
				echo '<a href="http://dannyvankooten.com/" title="Go to DannyvanKooten.com"><img src="http://static.dannyvankooten.com/images/dvk-64x64.png" class="alignright" alt="DannyvanKooten.com"/></a>';			
				echo '<ul>';
				$rss->items = array_slice( $rss->items, 0, 3 );
				foreach ( (array) $rss->items as $item ) {
					echo '<li>';
					echo '<a target="_blank" class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. $item['title'] .'</a> ';
					echo '<span class="rss-date">'. date('F j, Y', strtotime($item['pubdate'])) .'</span>';
					echo '<div class="rssSummary">'. $this->text_limit($item['summary'],250) .'</div>';
					echo '</li>';
				}
				echo '</ul>';
				echo '<div style="border-top: 1px solid #ddd; padding-top: 10px; text-align:center;">';
				echo '<a target="_blank" style="margin-right:10px;" href="http://feeds.feedburner.com/dannyvankooten"><img src="'.get_bloginfo('wpurl').'/wp-includes/images/rss.png" alt=""/> Subscribe by RSS</a>';
				echo '<a target="_blank" href="http://dannyvankooten.com/newsletter/"><img src="http://static.dannyvankooten.com/images/email-icon.png" alt=""/> Subscribe by email</a>';
				echo '<form class="alignright" method="post"><input type="hidden" name="dvk_removedbwidget" value="true"/><input title="Remove this widget" type="submit" value=" X "/></form>';
				echo '</div>';
				echo '</div>';
			}
		}

		function widget_setup() {
			$options = get_option('dvkdbwidget');
			if (!$options['dontshow'])
		    	wp_add_dashboard_widget( 'dvk_db_widget' , 'Latest posts on DannyvanKooten.com' , array(&$this, 'dashboard_widget'));
		}
		
		function text_limit( $text, $limit, $finish = '...') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}
		
		function add_option_page()
		{
         add_menu_page( $this->longname, "Newsl. Sign-up", $this->accesslvl, $this->hook, array(&$this,'options_page_default'), $this->icon_url );
         add_submenu_page( $this->hook, "Newsletter Sign-Up :: Configuration Settings", "Settings", $this->accesslvl, $this->hook, array($this,'options_page_default') );
         add_submenu_page( $this->hook, "Newsletter Sign-Up :: Configuration Extractor", "Config Extractor", $this->accesslvl, $this->hook .'/config-helper', array($this,'options_page_config_helper') );
		}
		
		function add_settings_link($links) { 
			$settings_link = '<a href="admin.php?page='.$this->hook.'">Settings</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}
		
		function check_usage_time()
		{
			if(isset($_GET['dontshowpopup']) && $_GET['dontshowpopup'] == 1) {
				$this->options['dontshowpopup'] = 1;
				update_option($this->optionname,$this->options);
			}			
			if(!isset($this->options['date_installed'])) {
				// set installed_time to now, so we can show pop-up in 30 days
				$this->options['date_installed'] = strtotime('now');
				update_option($this->optionname,$this->options);
				
			} elseif((!isset($this->options['dontshowpopup']) || $this->options['dontshowpopup'] != 1) && $this->options['date_installed'] < strtotime('-30 days')) {
				// plugin has been installed for over 30 days
				$this->actions['show_donate_box'] = true;
				wp_enqueue_style('dvk_donate', plugins_url('/backend/css/donate.css',dirname(__FILE__)));
				wp_enqueue_script('dvk_donate', plugins_url('/backend/js/donate.js',dirname(__FILE__)));
			}
		}
		
		function settings_init()
		{
			register_setting($this->optionname.'_group',$this->optionname,array(&$this,'validate_options'));
		}
		
		
		
	}
	
	
	
}