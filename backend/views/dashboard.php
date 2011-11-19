<div class="wrap" id="<?php echo $this->hook; ?>">
    <h2><a href="http://dannyvankooten.com/" target="_blank"><span id="dvk-avatar"></span></a>Newsletter Sign-Up :: Mailinglist Settings</h2>
    <div class="postbox-container" style="width:65%;">
        <div class="metabox-holder">	
            <div class="meta-box-sortables">
                <div class="postbox">
                    <div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
                    <h3 class="hndle"><span>Mailinglist Settings</span></span></h3>
                    <div class="inside">
                        <form method="post" action="options.php" id="ns_settings_page">
                            <?php settings_fields('nsu_mailinglist_group'); ?>
    
                            <table class="form-table">	
                                <tr valign="top">
                                    <td colspan="2"><p>These settings are the most important since without these Newsletter Sign-Up can't do it's job. Having trouble finding
                                        the right configuration settings? Have a look at <a href="http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/">this post on my blog</a> or try the <a href="admin.php?page=newsletter-sign-up/config-helper">configuration extractor</a>.</p></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">Select your mailinglist provider: </th>
                                    <td>
                                        <select name="nsu_mailinglist[provider]" id="ns_mp_provider" onchange="document.location.href = 'admin.php?page=<?php echo $this->hook; ?>&mp=' + this.value">
                                            <option value="other"<?php if ($viewed_mp == NULL || $viewed_mp == 'other')
                                echo ' SELECTED'; ?>>-- other / advanced</option>
                                            <option value="mailchimp"<?php if ($viewed_mp == 'mailchimp')
                                echo ' SELECTED'; ?> >MailChimp</option>
                                            <option value="ymlp"<?php if ($viewed_mp == 'ymlp')
                                echo ' SELECTED'; ?> >YMLP</option>
                                            <option value="icontact"<?php if ($viewed_mp == 'icontact')
                                echo ' SELECTED'; ?> >iContact</option>
                                            <option value="aweber"<?php if ($viewed_mp == 'aweber')
                                echo ' SELECTED'; ?> >Aweber</option>
                                            <option value="phplist"<?php if ($viewed_mp == 'phplist')
                                echo ' SELECTED'; ?> >PHPList</option>
                                        </select>
                                    </td>
                                </tr>
<?php $this->mailinglist_specific_rows($viewed_mp); ?>
                                <tbody class="form_rows"<?php if (isset($viewed_mp) && in_array($viewed_mp, array('mailchimp', 'ymlp')) && isset($opts['use_api']) && $opts['use_api'] == 1)
    echo ' style="display:none" '; ?>>
                                    <tr valign="top"><th scope="row">Newsletter form action</th>
                                        <td><input size="50%" type="text" id="ns_form_action" name="nsu_mailinglist[form_action]" value="<?php if (isset($opts['form_action']))
    echo $opts['form_action']; ?>" /></td>
                                    </tr>
                                    <tr valign="top"><th scope="row">E-mail identifier <span class="ns_small">name attribute of input field that holds the emailadress</span></th>
                                        <td><input size="50%" type="text" name="nsu_mailinglist[email_id]" value="<?php if (isset($opts['email_id']))
    echo $opts['email_id']; ?>"/></td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr valign="top"><th scope="row"><label for="subscribe_with_name">Subscribe with name?</label></th>
                                        <td><input type="checkbox" id="subscribe_with_name" name="nsu_mailinglist[subscribe_with_name]" value="1"<?php if (isset($opts['subscribe_with_name']) && $opts['subscribe_with_name'] == '1') {
    echo ' checked="checked"';
} ?> /></td>
                                    </tr>
                                    <tr class="name_dependent" valign="top"<?php if (!isset($opts['subscribe_with_name']) || $opts['subscribe_with_name'] != 1)
    echo 'style="display:none;"'; ?>><th scope="row">Name identifier <span class="ns_small">name attribute of input field that holds the name</span></th>
                                        <td><input size="25%" id="ns_name_id" type="text" name="nsu_mailinglist[name_id]" value="<?php if (isset($opts['name_id']))
                                    echo $opts['name_id']; ?>" /></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="margin:10px;">
                                For some newsletter services you need to specify some additional static data, like a list ID or your account name. These fields are usually found as hidden fields in your sign-up form's HTML code.
                                You can specify these additional fields here using name / value pairs so they will be sent along with every sign-up request.
                            </p>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="column" style="font-weight:bold;">Name</th>
                                    <th scope="column" style="font-weight:bold;">Value</th>
                                </tr>
<?php
$last_key = 0;

if (isset($opts['extra_data']) && is_array($opts['extra_data'])) :
    foreach ($opts['extra_data'] as $key => $value) :
        ?>
                                        <tr valign="top">
                                            <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $key; ?>][name]" value="<?php echo $value['name']; ?>" /></td>
                                            <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $key; ?>][value]" value="<?php echo $value['value']; ?>" /></td>
                                        </tr>					
        <?php
        $last_key = $key + 1;
    endforeach;
endif;
?>
                                <tr valign="top">
                                    <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $last_key; ?>][name]" value="" /></td>
                                    <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $last_key; ?>][value]" value="" /></td>
                                </tr>
                            </table>
                            <p class="submit">
                                <input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
                            </p>
                            <p class="nsu-tip">
                                Having trouble finding the right configuration settings? Try the <a href="admin.php?page=newsletter-sign-up/config-helper">configuration extractor</a>, it's there to help you!
                            </p>
                    </div>
                </div></div></div></div></div>
<div class="postbox-container" style="width:30%;">
    <div class="metabox-holder">	
        <div class="meta-box-sortables">						
<?php
$this->likebox();
$this->donate_box();
$this->latest_posts();
$this->support_box();
?>				
        </div>
    </div>
</div>
</div>
<?php if (isset($this->actions['show_donate_box']) && $this->actions['show_donate_box']) { ?>
    <div id="dvk-donate-box">
        <div id="dvk-donate-box-content">
            <img width="16" height="16" class="dvk-close" src="<?php echo plugins_url('/backend/img/close.png', dirname(__FILE__)); ?>" alt="X">
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
                        <a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $this->plugin_url; ?>" data-text="Showing my appreciation to @DannyvanKooten for his awsome #WordPress plugin: <?php echo $this->shortname; ?>" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                    </td>
                </tr>
            </table>
            <a class="dvk-dontshow" href="options-general.php?page=<?php echo $this->hook ?>&dontshowpopup=1">(do not show me this pop-up again)</a>
        </div>
    </div>
    <?php } ?>