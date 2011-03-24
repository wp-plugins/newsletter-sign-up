=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: newsletter,sign-up,comment,subscribers,mailchimp,aweber,phplist,icontact,mailinglist
Requires at least: 2.0
Tested up to: 3.1
Stable tag: 1.0.1

Adds a checkbox to your comment form that allows people to subscribe to your newsletter. Turn your commenters into subscribers!

== Description ==

= Newsletter Sign-Up =

This is the continued and much better version of [Qoate Newsletter Sign Up](http://wordpress.org/extend/plugins/qoate-newsletter-sign-up/), offering much
cleaner code, better performance and much more.

**NOTE THAT THIS IS THE FIRST RELEASE, TESTERS FOR DIFFERENT NEWSLETTER SERVICES ARE MUCH APPRECIATED**

Want to turn your commenters into subscribers? This plugins makes it easy for your visitors to subscribe to your newsletter by adding a checkbox
to your comment form that allows them to automatically be added to your mailinglist of choice.

This plugin currently supports the following newsletter providers but is not limited to those: Aweber, Mailchimp, iContact, PHPList, Feedblitz.
You can practically use the plugin for EVERY newsletter provider that's around if you use the right configuration settings.

More info:

* [Newsletter Sign-Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/)
* Read more great [WordPress tips](http://dannyvankooten.com/) to get the most out of your website
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress/) by the same author

Got a great idea on how to improve this plugin, so you can get even more newsletter subscribers? Please, [let me know](http://dannyvankooten.com/contact/)!

== Installation ==

Follow the instruction on the [Qoate Newsletter Sign Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/) page.

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

== Changelog ==

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