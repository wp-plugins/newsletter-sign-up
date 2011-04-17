<?php
/*
Plugin Name: Newsletter Sign-Up
Plugin URI: http://DannyvanKooten.com/wordpress-plugins/newsletter-sign-up/
Description: Adds a checkbox to your comment form to turn your commenters into subscribers
Version: 1.1.2
Author: Danny van Kooten
Author URI: http://DannyvanKooten.com
License: GPL2
*/

/*  Copyright 2010  Danny van Kooten  (email : danny@vkimedia.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* 
	TO DO
	
	* Implement ConstantContact API
	* Implement Widget
	* Add %%NAME%% to use in additional data
	* Add %%IP%% to use in additional data
	* Add additional data for YMLP API
	
*/



require_once('php/frontend.php');
$Newsletter_SignUp = new Newsletter_SignUp();

if(is_admin()) {
	require_once('php/backend.php');
	$Newsletter_SignUp_Admin = new Newsletter_SignUp_Admin();
}

function ns_comment_checkbox(){
	global $Newsletter_SignUp;
	
	$Newsletter_SignUp->add_checkbox();
}

