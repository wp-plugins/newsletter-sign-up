=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: newsletter,sign-up,newsletter signup,checkbox,ymlp,email,subscribe,subscribers,mailchimp,aweber,phplist,icontact,mailinglist,widget,newsletter widget,subscribe widget
Requires at least: 2.0
Tested up to: 3.1.3
Stable tag: 1.4.2

Contains a newsletter sign-up checkbox to show at comment forms, a sign-up form widget and a shortcode to embed a sign-up form in your posts.

== Description ==

= Newsletter Sign-Up =

Boost your mailinglist size! Adds a checkbox to your comment or registration forms, a widget sign-up form to your widget areas and a shortcode to embed newsletter
sign-up forms in your posts and pages.

This plugin currently supports the following newsletter providers but is not limited to those: MailChimp, YMLP, Aweber, iContact, PHPList, Feedblitz.
You can practically use the plugin for EVERY newsletter provider that's around if you use the right configuration settings.

**Features:**

* Add a "sign-up to our newsletter" checkbox to your comment form or register form (including BuddyPress or MultiSite forms)
* Add a customizable newsletter sign-up form to your widget areas.
* Embed a customizable sign-up form in your posts or pages by using the shortcode `[newsletter-sign-up-form]`.
* Embed a sign-up form in your template files by calling `nsu_signup_form();`
* Use the MailChimp or YMLP API or mimic a normal form request
* Works with most major mailinglist services because of the form mimicing feature.

**More info:**

* [Newsletter Sign-Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/)
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress-plugins/) by the same author
* [Follow Danny on Twitter](http://twitter.com/DannyvanKooten) for lightning fast support and updates.

Got a great idea on how to improve this plugin, so you can get even more newsletter subscribers? Please, [let me know](http://dannyvankooten.com/contact/)!

== Installation ==

1. Upload the contents of newsletter-sign-up.zip to your plugins directory.
1. Activate the plugin
1. Specify your newsletter service settings. For more info head over to: [How to configure Newsletter Sign-Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/)
1. That's all. Watch your list grow!

== Frequently Asked Questions ==

= What does this plugin do? =

This plugins adds a checkbox to various forms troughout your WordPress blog where users have the option to fill in their emailadress. When checked (ie the user agrees
to sign-up to your newsletter) this plugin mimics a sign-up form POST request to your newsletter service so the user who commented / subscribed at your blog becomes
a newsletter subscriber.

= What is the shortcode to embed a sign-up form in my posts? =
Its `[newsletter-sign-up-form]`.

= Why does the checkbox not show up? =

You're theme probably does not support the comment hook this plugin uses to add the checkbox to your comment form. You can manually place the checkbox
by calling `<?php if(function_exists('ns_comment_checkbox')) ns_comment_checkbox(); ?>` inside the form tags of your comment form.

= Where can I get the form action of my sign-up form? =

Look at the source code of your sign-up form and check for `<form action="http://www.yourmailinglist.com/signup?a=asd123"`....
The action attribute is what you need here.

= Where can I get the email identifier of my sign-up form? =

Take a look at the source code of your sign-up form and look for the input field that holds the emailadress. You'll need the NAME attribute of this input field, eg: `<input type="text" name="emailid"....` (thus in this case emailid is what you need)

For more questions and answers go have a look at my website regarding [Newsletter Sign-Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/)

= Can I let my users subscribe with their name too? =

Yes, it's possible. Just provide your name identifier (finding it is much like the email identifier) and the plugin will try to submit the user's name along with the request.

= Can I also show a checkbox at the BuddyPress sign-up form? =

Yes, you can. This option was added in v1.0.1.

= Can I show a sign-up form by calling a function in my template files? =

Yes, use the following code snippet to embed a sign-up form in your sidebar for example: `if(function_exists('nsu_signup_form')) nsu_signup_form();`

== Screenshots ==

1. The configuration page of Newsletter Sign-Up in the WordPress admin panel.

== Changelog ==
= 1.4.2 =
* Improvement: Made the label at comment form and registration forms clickable so it checks the checkbox.
* Improvement: Made 'email' a required field when submitting the sign-up form.
* Improvement: Made 'name' an optionally required field when submitting the sign-up form.

= 1.4.1 =
* Added: the function `nsu_signup_form()` which you can call from your theme files to output a sign-up form, just like the shortcode.

= 1.4 =
* Improvement: Hide metaboxes in the NSU configuration screen
* Improvement: Edit all widget labels in NSU configuration screen instead of widget options. (You might have to reconfigure some of your settings, sorry!)
* Added: Ability to add a sign-up form to your posts or pages using the shortcode `[newsletter-sign-up-form]`
* Some more restructuring of the code.

= 1.3.3 =
* Improvement: Users can now edit the widget labels for the email and name input fields.
* Improvement: You can now use some common HTML-codes in the widget text's
* Improvement: Linebreaks (\n) are now converted to HTML linebreaks in frontend.
* Fixed: Widget typo in the label for the email input field.

= 1.3.2 =
* Fixed bug: not loading the widget's default CSS after submitting option page.
* Fixed bug: 404 error after submitting the widget using API and 'subscribe with name'.
* Improvement: Added id's to the input fields in the widget.

= 1.3.1 =
* Fixed: parse error, unexpected T_FUNCTION for older versions of PHP which do not support anonymous functions.

= 1.3 =
* Added a widget: adds a sign-up form to your widget areas

= 1.2 =
* Fixed critical bug causing all custom form requests to fail (iow no sign-up request was made). Sorry!
* Fixed bug in backend: empty aweber list id field

= 1.1.2 =
* Re-added the predefined form values for Aweber, iContact and MailChimp
* Fixed PHPList fatal error
* Added additional data support when using YMLP API

= 1.1.1 =
* Fixed small bug for YMLP or MailChimp API users

= 1.1 =
* Changed the backend for different newsletters
* Added YMLP API support
* Added MailChimp API support
* Now uses the WordPress HTTP API
* Removed the ReadOnly attribute of prefilled fields
* Now works with MultiSite registration forms too
* Fixed inline CSS, now uses optional stylesheet
* Better documentation

= 1.0.6 =
* Fixed a missing argument error.

= 1.0.5 =
* Fixed some undefined indexes notices in the frontend

= 1.0.4 =
* Small change in seconds before timeout when making the POST request.
* Fixed bug with addititional data not being properly saved.

= 1.0.3 =
* Changed the plugin's backend structure
* Added the <a href="http://dannyvankooten.com">DannyvanKooten.com</a> dashboard widget.

= 1.0.2 =
* Added option to send custom data along with the sign-up request. 

= 1.0.1 =
* Improved script and stylesheet loading - now only loads on NS options page.
* Added option to show checkbox at the BuddyPress register form

= 1.0 =
* Stable release
* Added CURL support
* Added option to show a checkbox at WP registration form
* Added option to subscribe commenters with their name

= 0.1 =
Beta release