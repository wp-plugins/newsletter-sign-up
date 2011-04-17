=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: newsletter,sign-up,comment,subscribers,mailchimp,aweber,phplist,icontact,mailinglist
Requires at least: 2.0
Tested up to: 3.1
Stable tag: 1.1.1

Adds a checkbox to your comment form that allows people to subscribe to your newsletter. Turn your commenters into subscribers!

== Description ==

= Newsletter Sign-Up =

Want to turn your commenters into subscribers? This plugins makes it easy for your visitors to subscribe to your newsletter by adding a checkbox
to your comment form that allows them to automatically be added to your mailinglist of choice.

This plugin currently supports the following newsletter providers but is not limited to those: YMLP, Aweber, [Mailchimp](http://eepurl.com/c78PM), iContact, PHPList, Feedblitz.
You can practically use the plugin for EVERY newsletter provider that's around if you use the right configuration settings.

**Features:**

* Add a "sign-up to our newsletter" checkbox to your comment form, register form (including BuddyPress or MultiSite forms)
* Use the MailChimp or YMLP API
* Specify additional data to be sent to your newsletter service along with the sign-up request
* Subscribe the commenter with their name
* Hide the checkbox for users who used it to sign-up to your newsletter before

**More info:**

* [Newsletter Sign-Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/)
* Read more great [WordPress tips](http://dannyvankooten.com/) to get the most out of your website
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress/) by the same author

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

== Screenshots ==

1. The configuration page of Newsletter Sign-Up in the WordPress admin panel.

== Changelog ==
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