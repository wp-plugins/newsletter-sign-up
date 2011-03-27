<?php

/**
* Backend for all DannyvanKooten.com plugins
* Version 0.1
* Credits to Yoast.com for most of this code
*/

if(!class_exists('DvK_Plugin_Admin')) {
	
	class DvK_Plugin_Admin {
	
		var $hook 		= '';
		var $filename	= '';
		var $longname	= '';
		var $shortname	= '';
		var $optionname = '';
		var $homepage	= '';
		var $accesslvl	= 'manage_options';
		
		function __construct()
		{
			add_filter("plugin_action_links_{$this->filename}", array(&$this,'add_settings_link'));
			add_action('admin_menu', array(&$this,'add_option_page'));
			add_action("admin_print_styles-settings_page_{$this->hook}",array(&$this,'add_admin_styles'));
			add_action("admin_print_scripts-settings_page_{$this->hook}",array(&$this,'add_admin_scripts'));
			
			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));	
		}
		
		function add_admin_styles()
		{
			wp_enqueue_style('dvk_plugin_admin_css', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname($this->filename)). '/css/backend.css');
		}
		
		function add_admin_scripts()
		{
			wp_enqueue_script('postbox');
			wp_enqueue_script('dashboard');
			wp_enqueue_script('jquery');
		}
		
		function add_option_page()
		{
			add_options_page($this->longname, $this->shortname, $this->accesslvl, $this->hook, array(&$this,'option_page'));
		}
		
		function add_settings_link($links) { 
			$settings_link = '<a href="options-general.php?page='.$this->hook.'">Settings</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}
		
		function option_page()
		{
		 /* Override in plugin file backend.php */
		}
		
		function donate_box()
		{
			$content = '<p>Glad to hear. I\'ve spent alot of hours developing this plugin before releasing it for <b>free</b>. Consider showing me a token of your appreciation by buying me a beer. Drop me a line if you do, I most certainly appreciate!</p>
					<center>
					<form id="dvk_donate" target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
						<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/nl_NL/i/scr/pixel.gif" width="1" height="1">
					</form>
				</center>';
			$this->postbox($this->hook.'-donatebox','Happy with '.$this->shortname.'?',$content);		
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
					<li>Tell others about this plugin.</li>
					<li><a href="http://wordpress.org/extend/plugins/'.$this->hook.'/" target="_blank">Give a good rating on WordPress.org.</a></li>
					<li><a href="http://DannyvanKooten.com/donate/" target="_blank">Buy me a beer</a></li>
				</ul>';
			$this->postbox($this->hook.'-likebox','Like this plugin?',$content);
		
		}
		
		function support_box()
		{
			$content = '<p>Are you having trouble setting-up '.$this->shortname.', experiencing an error or got a great idea on how to improve it? Please, post
				your question or tip in the <a target="_blank" href="http://wordpress.org/tags/'.$this->hook.'">Support forums</a> on WordPress.org</p>';
			$this->postbox($this->hook.'-support-box',"Looking for support?",$content);
		}
		
		function postbox($id,$title,$content)
		{
		?>
			<div id="<?php echo $id; ?>" class="postbox dvk-box">				
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>			
				</div>
			</div>
		<?php			
		}
		
		function setup_admin_page($title,$subtitle)
		{
			?>
			<div class="wrap">
			<h2><a href="http://dannyvankooten.com/" target="_blank"><span id="dvk-avatar"></span></a><?php echo $title; ?></h2>
			<div class="postbox-container" style="width:65%;">
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
		<?php
		}
		
		function dashboard_widget() {
			$options = get_option('dvkdbwidget');
			if (isset($_POST['dvk_removedbwidget'])) {
				$options['dontshow'] = true;
				update_option('dvkdbwidget',$options);
			}		
			
			if ($options['dontshow']) {
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
		
	}

}