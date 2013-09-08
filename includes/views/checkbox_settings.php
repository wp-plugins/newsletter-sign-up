<div class="wrap" id="nsu-admin">

   <h2>Newsletter Sign-Up :: Checkbox Settings</h2>
  
  <div id="nsu-main">


    
    <form method="post" action="options.php" id="ns_settings_page">
      <?php settings_fields('nsu_checkbox_group'); ?>
      <table class="form-table">
        <tr valign="top"><th scope="row">Text to show after the checkbox</th>
          <td><input class="widefat" type="text" name="nsu_checkbox[text]" value="<?php echo esc_attr($opts['text']); ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Redirect to this url after signing up <small>(leave empty for no redirect)</small></th>
          <td><input class="widefat" type="text" name="nsu_checkbox[redirect_to]" value="<?php echo esc_attr($opts['redirect_to']); ?>" />

            <small>In general, I don't recommend setting a redirect url for the sign-up checkbox. This will cause some serious confusion, since
              users expect to be redirected to the post they commented on.</small>

            </td>
          </tr>
          <tr valign="top"><th scope="row"><label for="ns_precheck_checkbox">Pre-check the checkbox?</label></th>
            <td><input type="checkbox" id="ns_precheck_checkbox" name="nsu_checkbox[precheck]" value="1"<?php
            if ($opts['precheck'] == '1') {
             echo ' checked';
           }
           ?> /></td>
         </tr>
         <tr valign="top">
          <th scope="row"><label for="do_css_reset">Do a CSS 'reset' on the checkbox.</label> <small>(check this if checkbox appears in a weird place)</small></th>
          <td><input type="checkbox" id="do_css_reset" name="nsu_checkbox[css_reset]" value="1"<?php
          if ($opts['css_reset'] == '1') {
           echo ' checked="checked"';
         }
         ?>  /> </td>
       </tr>
       <tr valign="top"><th scope="row">Where to show the sign-up checkbox?</th>
        <td>
          <input type="checkbox" id="add_to_comment_form" name="nsu_checkbox[add_to_comment_form]" value="1"<?php
          if ($opts['add_to_comment_form'] == '1') {
           echo ' checked="checked"';
         }
         ?> /> <label for="add_to_comment_form">WordPress comment form</label><br />
         <input type="checkbox" id="add_to_reg_form" name="nsu_checkbox[add_to_registration_form]" value="1"<?php
         if ($opts['add_to_registration_form'] == '1') {
           echo ' checked="checked"';
         }
         ?> /> <label for="add_to_reg_form">WordPress registration form</label><br />
         <?php if ($this->bp_active) { ?>
         <input type="checkbox" id="add_to_bp_form" name="nsu_checkbox[add_to_buddypress_form]" value="1"<?php
         if ($opts['add_to_buddypress_form'] == '1') {
          echo ' checked="checked"';
        }
        ?> /> <label for="add_to_bp_form">BuddyPress registration form</label><br />
        <?php
      }
      if (defined('MULTISITE') && MULTISITE == TRUE) {
        ?>
        <input type="checkbox" id="add_to_ms_form" name="nsu_checkbox[add_to_multisite_form]" value="1"<?php
        if ($opts['add_to_multisite_form'] == '1') {
         echo ' checked="checked"';
       }
       ?> /> <label for="add_to_ms_form">MultiSite registration form</label><br />
       <?php } ?>
     </td>
   </tr>
   <tr valign="top"><th scope="row"><label for="ns_cookie_hide">Hide the checkbox for users who used it to subscribe before?</label><small>(uses a cookie)</small></th>
    <td><input type="checkbox" id="ns_cookie_hide" name="nsu_checkbox[cookie_hide]" value="1"<?php
    if ($opts['cookie_hide'] == '1') {
      echo ' checked="checked"';
    }
    ?> /></td>
  </tr>

</table>

<?php submit_button(); ?>

</form>
</div>

<?php require 'parts/sidebar.php'; ?>

</div>