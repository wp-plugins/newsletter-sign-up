<div class="wrap" id="nsu-admin">

    <div id="nsu-main">

        <h2>Newsletter Sign-Up :: Form Settings</h2>

        <form method="post" action="options.php">
            <?php settings_fields('nsu_form_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <td colspan="2"><p>Customize your Sign-up form by providing your own values for the different labels, input fields and buttons of the sign-up form. </p></td>
                </tr>
                <tr valign="top">
                    <th scope="row">E-mail label</th>
                    <td><input class="widefat" type="text" name="nsu_form[email_label]" value="<?php echo esc_attr($opts['email_label']); ?>" /></td>
                </tr>
                <tr valign="top">
                   <th scope="row">E-mail default value</th>
                   <td><input class="widefat" type="text" name="nsu_form[email_default_value]" value="<?php echo esc_attr($opts['email_default_value']); ?>" /></td>
               </tr>
               <tr valign="top" class="name_dependent" <?php if($opts['mailinglist']['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name label <span class="ns_small">(if using subscribe with name)</span></th>
                <td>
                    <input class="widefat" type="text" name="nsu_form[name_label]" value="<?php echo esc_attr($opts['name_label']); ?>" /><br />
                    <input type="checkbox" id="name_required" name="nsu_form[name_required]" value="1"<?php if($opts['name_required'] == '1') { echo ' checked'; } ?> />
                    <label for="name_required">Name is a required field?</label>
                </td>

            </tr>
            <tr valign="top" class="name_dependent" <?php if($opts['mailinglist']['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>>
                <th scope="row">Name default value</th>
                <td><input class="widefat" type="text" name="nsu_form[name_default_value]" value="<?php echo esc_attr($opts['name_default_value']); ?>" /></td>

            </tr>
            <tr valign="top"><th scope="row">Submit button value</th>
                <td><input class="widefat" type="text" name="nsu_form[submit_button]" value="<?php echo esc_attr($opts['submit_button']); ?>" /></td>
            </tr>
            <tr valign="top"><th scope="row">Text to replace the form with after a successful sign-up</th>
                <td>
                    <textarea class="widefat" rows="5" cols="50" name="nsu_form[text_after_signup]"><?php echo esc_textarea($opts['text_after_signup']); ?></textarea>
                    <p><input id="nsu_form_wpautop" name="nsu_form[wpautop]" type="checkbox" value="1" <?php if($opts['wpautop'] == 1) echo 'checked'; ?> />&nbsp;<label for="nsu_form_wpautop"><?php _e('Automatically add paragraphs'); ?></label></p>
                </td>
            </tr>

            <?php if($opts['mailinglist']['use_api'] == 1) { ?>
            <tr valign="top"><th scope="row">Redirect to this url after signing up <small>(leave empty for no redirect)</small></th>
                <td><input class="widefat" type="text" name="nsu_form[redirect_to]" value="<?php echo $opts['redirect_to']; ?>" /></td>
            </tr>
            <?php } ?>

            <tr valign="top"><th scope="row"><label for="ns_load_form_styles">Load some default CSS</label> <small>(check this for some default styling of the labels and input fields)</small></th>
                <td><input type="checkbox" id="ns_load_form_styles" name="nsu_form[load_form_css]" value="1" <?php if($opts['load_form_css'] == 1) echo 'checked'; ?> /></td>
            </tr>
        </table>
        
        <?php submit_button(); ?>

    </form>
</div>

<?php require 'parts/sidebar.php'; ?>

</div>