<?php 
// SVN Revision keyword. SVN will auto-populate this with current revision text
$svn_revision_txt = '$Revision$';

/* Current revision of K2 */
$current = intval(substr($svn_revision_txt, 11, -2));

load_theme_textdomain('k2_domain');

/* Blast you red baron! Initialise the k2 system */

require(TEMPLATEPATH . '/options/app/archive.php');
require(TEMPLATEPATH . '/options/app/options.php');
require(TEMPLATEPATH . '/options/app/update.php');
require(TEMPLATEPATH . '/options/app/info.php');
require(TEMPLATEPATH . '/options/app/tools.php');

// If K2 isn't installed, install it.
// If it is installed but it is an older version, run the install event in case of new options
if (!get_option('k2installed') || get_option('k2installed') < $current) {
	installk2::installer();
}

// Let's add the options page.
add_action ('admin_menu', 'k2menu');

$k2loc = '../themes/'.basename(dirname($file)); 

function k2menu() {
	add_submenu_page('themes.php', __('K2 Options','k2_domain'), __('K2 Options','k2_domain'), 5, $k2loc . 'functions.php', 'menu');
}

function menu() {
	load_plugin_textdomain('k2options');
	//this begins the admin page

	include(TEMPLATEPATH . '/options/display/form.php');
}

// include Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk
require(TEMPLATEPATH . '/options/display/headers.php');

// Sidebar Modules for K2
// Only bootstrap if not activating a plugin & no other plugin is installed for handling sidebars
if(!function_exists('register_sidebar')	&& !(basename($_SERVER['SCRIPT_FILENAME']) == 'plugins.php' && $_GET['action'] == 'activate')) {
	require(TEMPLATEPATH . '/options/app/sbm.php');
	k2sbm::wp_bootstrap();
}

// Sidebar modules / WP Widgets init
if(function_exists('register_sidebar')) {
	register_sidebar(array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>'));
}

// this ends the admin page ?>
