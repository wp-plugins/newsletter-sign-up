<?php

/**
* Displays the comment checkbox, call this function if your theme does not use the 'comment_form' action in the comments.php template.
*/
function nsu_checkbox() {
    $NewsletterSignUp = NewsletterSignUp::instance();
    $NewsletterSignUp->output_checkbox();
}

/**
* Outputs a sign-up form, for usage in your theme files.
*/
function nsu_signup_form()
{
	$NewsletterSignUp = NewsletterSignUp::instance();
	$NewsletterSignUp->output_form(true);
}