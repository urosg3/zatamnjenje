<?php
/**
 * @package Berkmansiteblackout
 * @version 1.4
 */
/*
Plugin Name: Berkman Site Blackout
Plugin URI: http://blog.eagerterrier.co.uk/2012/01/stop-sopa-blackout-wp-plugin/
Description: Blacks out your website during a specific day.
Author: Toby Cox, DJCP
Version: 1.4
Author URI: http://cyber.law.harvard.edu/

A customized version of
http://blog.eagerterrier.co.uk/2012/01/stop-sopa-blackout-wp-plugin/

*/

$siteblackout_options = get_option('siteblackout_options'); 


function siteblackout_set_option($option_name, $option_value) {
	// first get the existing options in the database
	$siteblackout_ = get_option('siteblackout_options');
	// set the value
	$siteblackout_[$option_name] = $option_value;
	// write the new options to the database
	update_option('siteblackout_options', $siteblackout_);
}


function siteblackout_get_option($option_name) {

  // get options from the database
  $siteblackout_options = get_option('siteblackout_options'); 

  
  if (!$siteblackout_options || !array_key_exists($option_name, $siteblackout_options)) {
    // no options in database yet, or not this specific option 
    // create default options array

    
    $siteblackout_default_options=array();
    
    $siteblackout_default_options['test_mode']			= false;
    $siteblackout_default_options['show_blackout_to_logged_in_users'] = false;
    $siteblackout_default_options['message']			= 'Enter your custom message here.';
    
	$siteblackout_default_options['blackoutdate_year']	= '2012';
	$siteblackout_default_options['blackoutdate_month']	= '01';
	$siteblackout_default_options['blackoutdate_day']	= '18';
	$siteblackout_default_options['blackoutdate']		= '2012-01-18';
	$siteblackout_default_options['blackouttimestart']	= 8;
	$siteblackout_default_options['blackouttimeend']	= 20;
	$siteblackout_default_options['blackouttimezone']	= null;
    
    $siteblackout_default_options['page_title']			= 'Blacking out my site. . .';
    $siteblackout_default_options['page_link']			= 'http://example.com';

    // add default options to the database (if options already exist, 
    // add_option does nothing
    add_option('siteblackout_options', $siteblackout_default_options, 
               'Settings for the Site Blackout plugin');

    // return default option if option is not in the array in the database
    // this can happen if a new option was added to the array in an upgrade
    // and the options haven't been changed/saved to the database yet
    $result = $siteblackout_default_options[$option_name];

  } else {
    // option found in database
    $result = $siteblackout_options[$option_name];
  }
  

  return $result;
}

function siteblackout_options() {


	if (isset($_POST['info_update'])) {

		?><div class="updated"><p><strong><?php 
		
		// process submitted form
		$siteblackout_options = get_option('siteblackout_options');
		$siteblackout_options['page_title']									= $_POST['page_title'];
		$siteblackout_options['page_link']									= $_POST['page_link'];
		$siteblackout_options['message']									= $_POST['message'];
		$siteblackout_options['blackoutdate_year']							= $_POST['blackoutdate_year'];
		$siteblackout_options['blackoutdate_month']							= $_POST['blackoutdate_month'];
		$siteblackout_options['blackoutdate_day']							= $_POST['blackoutdate_day'];
		$siteblackout_options['blackouttimestart']							= $_POST['blackouttimestart'];
		$siteblackout_options['blackouttimeend']							= $_POST['blackouttimeend'];
		$siteblackout_options['blackouttimezone']							= $_POST['blackouttimezone'];
		$siteblackout_options['message']									= $_POST['message'];
		$siteblackout_options['test_mode']									= ($_POST['test_mode']=="true"			? true : false);
		$siteblackout_options['show_blackout_to_logged_in_users']			= ($_POST['show_blackout_to_logged_in_users']=="true"			? true : false);
		update_option('siteblackout_options', $siteblackout_options);

		_e('Options saved', 'mtli')
		?></strong></p></div><?php
	} 
	


	?>
	<div class="wrap">
		<form method="post">
			<h2>Site Blackout Plugin</h2> 
				
			<div class="whitebg">
			<fieldset class="options" name="general">
				<legend><?php _e('General settings', 'siteblackout') ?></legend>
				<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
					<tr>
						<td>Page Title</td>
					</tr>
					<tr>
						<td><input type="text" name="page_title" id="page_title" value="<?php echo siteblackout_get_option('page_title');?>" /> </td>
					</tr>
					<tr>
						<td>Page Link</td>
					</tr>
					<tr>
						<td><input type="text" name="page_link" id="page_link" value="<?php echo siteblackout_get_option('page_link');?>" /> </td>
					</tr>
					<tr>
						<td>Message</td>
					</tr>
					<tr>
						<td><?php if(function_exists('wp_editor')){ wp_editor( stripslashes(siteblackout_get_option('message')), 'message' ); } else { ?><textarea name="message" id="message" style="width:800px; height:400px;"><?php echo stripslashes(siteblackout_get_option('message'));?></textarea><?php } ?></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="options" name="general">
				<legend><?php _e('Blackout Day', 'siteblackout') ?></legend>
				<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
					<tr>
						<td>You can change the times of the Blackout Day here if you wish</td>
					</tr>
					<tr>
						<td>
							<?php $selecteddateday = sprintf('%02d',siteblackout_get_option('blackoutdate_day'));
								$selecteddatemonth = sprintf('%02d',siteblackout_get_option('blackoutdate_month'));
								$selecteddateyear = siteblackout_get_option('blackoutdate_year');?>
							<select name="blackoutdate_day" id="blackoutdate_day">
								<?php for($i=1;$i<32; $i++){ ?><option value="<?php echo sprintf('%02d',$i);?>"<?php if($selecteddateday==$i) echo ' selected';?>><?php echo $i;?></option><?php } ?>
							</select>
							<select name="blackoutdate_month" id="blackoutdate_month">
								<?php for($i=1;$i<13; $i++){ ?><option value="<?php echo sprintf('%02d',$i);?>"<?php if($selecteddatemonth==$i) echo ' selected';?>><?php echo date('F',strtotime('2012-'.$i.'-01'));?></option><?php } ?>
							</select>
							<select name="blackoutdate_year" id="blackoutdate_year">
								<?php for($i=2012;$i<(date('Y')+5); $i++){ ?><option value="<?php echo $i;?>"<?php if($selecteddateyear==$i) echo ' selected';?>><?php echo $i;?></option><?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Start Time<td>
					</tr>
					<tr>
						<td>
							<select id="blackouttimestart" name="blackouttimestart">
								<?php $currenttimestart = siteblackout_get_option('blackouttimestart');?>
								<?php for($i=0; $i<25; $i++){ ?>
									<option value="<?php echo $i;?>"<?php if($currenttimestart==$i) echo ' selected';?>><?php echo sprintf('%02d', $i);?>:00:00</option>
								<?php } ?>
							</select>	
						</td>
					</tr>
					<tr>
						<td>End Time<td>
					</tr>
					<tr>
						<td>
							<select id="blackouttimeend" name="blackouttimeend">
								<?php $currenttimeend = siteblackout_get_option('blackouttimeend');?>
								<?php for($i=0; $i<25; $i++){ ?>
									<option value="<?php echo $i;?>"<?php if($currenttimeend==$i) echo ' selected';?>><?php echo sprintf('%02d', $i);?>:00:00</option>
								<?php } ?>
							</select>	
						</td>
					</tr>
					<tr>
						<td>Time Zone</td>
					</tr>
					<tr>
						<td>
							<select name="blackouttimezone" id="blackouttimezone">
								<option value="">Default - <?php echo date_default_timezone_get();?></option>
								<option value="America/New_York"<?php if(siteblackout_get_option('blackouttimezone')=='America/New_York') echo ' selected';?>>Eastern Time (New York)</option>
								<option value="America/Los_Angeles"<?php if(siteblackout_get_option('blackouttimezone')=='America/Los_Angeles') echo ' selected';?>>Pacific Time (Los Angeles)</option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="options" name="general">
				<legend><?php _e('Enable test mode?', 'siteblackout') ?></legend>
				<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
					<tr>
						<td>By checking this box you will turn on the site blackout plugin <b>RIGHT NOW</b></td>
					</tr>
					<tr>
						<td><input type="checkbox" name="test_mode" id="test_mode" value="true" <?php if (siteblackout_get_option('test_mode')) echo "checked"; ?> /> </td>
					</tr>
					<tr>
						<td>By default, logged in users will see your site's full content. If you would like logged in users to get the site blackout page instead, click here</td>
					</tr>
					<tr>
						<td><input type="checkbox" name="show_blackout_to_logged_in_users" id="show_blackout_to_logged_in_users" value="true" <?php if (siteblackout_get_option('show_blackout_to_logged_in_users')) echo "checked"; ?> /> </td>
					</tr>
				</table>
			</fieldset>
			<div class="submit">
				<input type="submit" name="info_update" value="<?php _e('Update options', 'siteblackout') ?>" /> 
			</div>
			</div>
		</form>
	</div>


<?php
}


// Hook function for init action to do some initialization
function siteblackout_init() {
	// load texts for localization
	load_plugin_textdomain('siteblackout');
}



if(!function_exists('siteblackout_header')):
function siteblackout_header($status_header, $header, $text, $protocol) {
	if ( siteblackout_showtologgedinusers() ) {
		if( defined( 'WPCACHEHOME' ) ) {
			// Solves issue of white page output with Super Cache plugin version 0.9.9.6.
			// Did not occur when removing <html> and </html> tag in splash page source, so weird problem.
			ob_end_clean();
		}
		nocache_headers();
		return "$protocol 503 Service Unavailable";
	}
}
endif;

if(!function_exists('siteblackout_content')){
function siteblackout_content() {
	if ( siteblackout_showtologgedinusers() && !strstr(htmlspecialchars($_SERVER['REQUEST_URI']), '/wp-admin/') ) {
		if( strstr($_SERVER['PHP_SELF'],    'wp-login.php') 
				|| strstr($_SERVER['PHP_SELF'], 'async-upload.php') // Otherwise media uploader does not work 
				|| strstr(htmlspecialchars($_SERVER['REQUEST_URI']), '/plugins/') 		// So that currently enabled plugins work while in maintenance mode.
				|| strstr($_SERVER['PHP_SELF'], 'upgrade.php')
			){ 
				return; 
		} else {
		$siteblackout_page_title = siteblackout_get_option('page_title');
		$page = dirname(__FILE__) . '/siteblackout_display_custom.php';
		include($page);
		exit();
	}
	}
}
}

if(!function_exists('siteblackout_feed')){
	function siteblackout_feed() {
		if ( !is_user_logged_in() ) {
			die('<?xml version="1.0" encoding="UTF-8"?>'.
				'<status>Service unavailable</status>');
		}
	}
}

if(!function_exists('siteblackout_add_feed_actions')){
	function siteblackout_add_feed_actions() {
		$feeds = array ('rdf', 'rss', 'rss2', 'atom');
		foreach ($feeds as $feed) {
			add_action('do_feed_'.$feed, 'siteblackout_feed', 1, 1);
		}
	}
}

function siteblackout_checkdate(){
	if(siteblackout_get_option('blackouttimezone')){
		date_default_timezone_set(siteblackout_get_option('blackouttimezone'));
	}
	$toreturn = false;
	if(date('Y-m-d')==siteblackout_get_option('blackoutdate_year').'-'.sprintf('%02d',siteblackout_get_option('blackoutdate_month')).'-'.sprintf('%02d',siteblackout_get_option('blackoutdate_day'))){
		if(date('H')>=siteblackout_get_option('blackouttimestart') && date('H')<siteblackout_get_option('blackouttimeend')){
			$toreturn = true;
		}
	}
	return $toreturn;
}

function siteblackout_testmode(){
	return siteblackout_get_option('test_mode');
}

function siteblackout_showtologgedinusers(){
	return ((!is_user_logged_in() && !is_admin()) || siteblackout_get_option('show_blackout_to_logged_in_users'));
}

if (function_exists('add_filter') && (siteblackout_checkdate() || siteblackout_testmode()) ){
	add_filter('status_header', 'siteblackout_header', 10, 4);
	add_action('template_redirect', 'siteblackout_content');
	siteblackout_add_feed_actions();
} 




/*  ADMIN FUNCTIONS AND HOOKS GO BELOW */


function siteblackout_admin() {

  if (function_exists('add_options_page')) {

    add_options_page('Site Blackout' /* page title */, 
                     'Site Blackout' /* menu title */, 
                     8 /* min. user level */, 
                     basename(__FILE__) /* php file */ , 
                     'siteblackout_options' /* function for subpanel */);
  }

}


// Adding Admin CSS
function siteblackout_admin_css() {
	echo "
	<style type='text/css'>
	.form-table				{ margin-bottom: 0 !important; }
	.form-table th			{ font-size: 11px; min-width: 200px; }
	.form-table .largetext	{ font-size: 12px; }
	.form-table td			{ max-width: 500px; }
	.form-table tr:last-child	{ border-bottom: 1px solid #DEDEDE; }
	.form-table tr:last-child td { padding-bottom: 20px; }
	.form-table select		{ width: 275px; }
	.form-table input[type='text'] {width:800px;}
	</style>
	";
}

add_filter('admin_head', 'siteblackout_admin_css');
add_filter('admin_menu', 'siteblackout_admin');
add_filter('init', 'siteblackout_init');

?>
