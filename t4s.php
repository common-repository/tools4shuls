<?php

/*

Plugin Name: Tools 4 Shuls

Description: The Tools 4 Shuls Wordpress plugin makes it easier than ever to manage your Tools 4 Shuls Calendar and Donations modules and integrate them within your site.  To learn more about Tools 4 Shuls and create an account, visit http://www.tools4shuls.com.

Version: 1.2.0.3

*/





//VARIABLES

global $wpdb, $mysqli;

global $mysqli;



$t4s_plugin_url  = plugins_url( '/', dirname( __FILE__ ) );

$t4s_plugin_path = plugin_dir_path( dirname( __FILE__ ) );



$mysqli = new mysqli("localhost", DB_USER, DB_PASSWORD, DB_NAME);





//INCLUDES		

require( $t4s_plugin_path ."tools4shuls/includes/t4s_common.php");

require( $t4s_plugin_path ."tools4shuls/includes/t4s_payment_processing.php");

require( $t4s_plugin_path ."tools4shuls/includes/forms/t4s_export_forms.php");

require( $t4s_plugin_path ."tools4shuls/api/t4s_ppl_ipn.php");

require( $t4s_plugin_path ."tools4shuls/api/t4s_auth_ipn.php");

require( $t4s_plugin_path ."tools4shuls/includes/t4s_core_calls.php");

require( $t4s_plugin_path ."tools4shuls/includes/lib/Authorize/AuthorizeNet.php"); 





//WIDGETS

function register_T4S_widgets() {

    register_widget( 't4s_widget' );

	register_widget( 't4s_widget2' );

}

add_action( 'widgets_init', 'register_T4S_widgets' );

include("t4s_widgets.php");





//SHORTCODES

function register_t4s_shortcodes(){

   add_shortcode('showT4Sform', 'grab_T4Sform');

   add_shortcode( 't4s', 't4s_shortcode' );

}

add_action( 'init', 'register_t4s_shortcodes');



function t4s_shortcode( $atts ) {

	require_once("includes/t4s_common.php");

	generateT4SShortcode($atts);

	

}





//AJAX

add_action('wp_ajax_t4s_action', 't4s_action_callback');



function t4s_action_callback() {

	global $wpdb, $mysqli; 



	if ($_POST['task'] == 'don-cats') {	

		$camp = true;

		if ($_POST['val'] == 'full') $camp = false;

		

		$user = $_POST['user'];

		if (strlen($user) > 12) $user = "1";

		getClientDonationsCategories($user, $camp);

	}

	

	if ($_POST['task'] == 'don-funds') {	

		$user = $_POST['user'];

		$val = $_POST['val'];

		if (strlen($user) > 12) $user = "1";

		if (strlen($val) > 12) $val = "";

		getClientDonationsFunds($user, $val);

	}

	

	die(); 

}





// INSTALLATION



//ALL QUERIES HAVE NO INPUTS, SO NO NEED FOR PREPARE()

add_action('activate_tools4shuls/t4s.php', 't4s_install'); 



function t4s_install()

{

    global $wpdb, $mysqli;

    $table = $wpdb->prefix."options";

	$query = array();

	

	//create a cookie record

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_cookie', '# Netscape HTTP Cookie File

# https://curl.haxx.se/rfc/cookie_spec.html

# This file was generated by libcurl! Edit at your own risk.



t4s.inspiredonline.com	FALSE	/	FALSE	0	PHPSESSID	".rand(111111,999999).strtotime(date('Y-m-d H:i:s'))."

#HttpOnly_.inspiredonline.com	TRUE	/	TRUE	".rand(111111,999999).strtotime(date('Y-m-d H:i:s'))."	_sauth	".rand(111111,999999).strtotime(date('Y-m-d H:i:s'))."')";



	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_hash', '')";

	

	//Create all needed records in the options table 

    $query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_login', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_password', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_clientID', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_apiKey', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_subscr_id', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_subscr_amount', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_subscr_date', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_payment_processor', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_auth_id', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_auth_key', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_paypal_email', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_auth_md5', '')";

	$query[] = "INSERT INTO ".$table." (option_name, option_value) VALUES ('t4s_cal_url', '')";

	

	$query[] = "CREATE TABLE IF NOT EXISTS `t4s_forms_options` (

				  `id` int(11) NOT NULL AUTO_INCREMENT,

				  `value` text NOT NULL,

				  `description` text NOT NULL,

				  `test_fields_id` int(11) NOT NULL,

				  `ordernum` int(11) NOT NULL,

				  PRIMARY KEY (`id`)

				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;";

				

	$query[] = "CREATE TABLE IF NOT EXISTS `t4s_forms` (

				  `id` int(11) NOT NULL AUTO_INCREMENT,

				  `name` text NOT NULL,

				  `content` text NOT NULL,

				  `active` int(11) NOT NULL,

				  `description` varchar(128),

				  `thank_you` varchar(128),

				  `price` varchar(12),

				  `pmt_type` varchar(24),

				  PRIMARY KEY (`id`)

				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;";

				

	$query[] = "CREATE TABLE IF NOT EXISTS `t4s_forms_fields` (

				  `id` int(11) NOT NULL AUTO_INCREMENT,

				  `label` text NOT NULL,

				  `name` text NOT NULL,

				  `content` text NOT NULL,

				  `t4s_forms_Id` int(11) DEFAULT NULL,

				  `ordernum` int(11) DEFAULT NULL,

				  PRIMARY KEY (`id`)

				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;";

				

	$query[] = "CREATE TABLE IF NOT EXISTS `t4s_paypal_payments` (

				  `id` int(11) NOT NULL AUTO_INCREMENT,

				  `date_submitted` varchar(24) NOT NULL,

				  `ip_address` varchar(16) NOT NULL,

				  `data` longtext NOT NULL,

				  `gateway` varchar(12) NOT NULL,

				  PRIMARY KEY (`id`)

				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				

	$query[] ="CREATE TABLE IF NOT EXISTS `t4s_authorize_payments` (

				  `id` int(11) NOT NULL AUTO_INCREMENT,

				  `description` text NOT NULL,

				  `amount` varchar(12) NOT NULL,

				  `form_id` int(11) NOT NULL,

				  `response` text,

				  `status` int(1),

				  PRIMARY KEY (`id`)

				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";



	$query[] = "CREATE TABLE IF NOT EXISTS `t4s_forms_submissions` (

				  `id` int(11) NOT NULL AUTO_INCREMENT,

				  `data` text NOT NULL,

				  `date_submitted` varchar(36) NOT NULL,

				  `ip` varchar(16) NOT NULL,

				  `form_id` int(11),

				  PRIMARY KEY (`id`),

				  UNIQUE KEY `id` (`id`)

				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

	

	try {

		

		foreach ($query as $sql) {	

			//THERE ARE NO INPUTS IN THESE QUERIES AND prepare() IS NOT NEEDED

			$wpdb->query($sql);	

		}

		

	} catch (exception $err) {

		echo "Setup encountered problems during plugin installation. The plugin might not function properly. Please try re-installing the plugin.";

	}

}





//UN-INSTALL

register_deactivation_hook( __FILE__, 't4s_uninstall' );



function t4s_uninstall() {



    global $wpdb, $mysqli;

    $table = $wpdb->prefix."options";

	

	//Delete associated records from the options table 

    $query = "DELETE FROM ".$table." WHERE option_name LIKE 't4s_%'";

	

	try {

		$wpdb->query($query);		

	} catch (exception $err) {

		

	}

}



//MENU PAGES

add_action('admin_menu', 't4s_admin_actions');



function t4s_admin_actions() {

    

	add_menu_page("T4S Plugin", "Tools 4 Shuls", 'manage_options', "t4s", "t4s_menu");

			

	add_submenu_page("t4s", "Tools 4 Shuls Settings", "T4S Settings", "manage_options", "t4s-settings", "t4s_settings_menu");

	add_submenu_page("t4s", 'T4S Form Creator', 'T4S Form Creator', "manage_options", 't4s-form-creator', 'admin_interface_function');

	add_submenu_page("t4s", 'T4S Form Payments', 'T4S Form Payments', "manage_options", 't4s-form-payments', 'admin_t4s_form_payments');

	add_submenu_page("t4s", 'Installation and Shortcodes', 'Installation and Shortcodes', "manage_options", 't4s-installation-and-shortcodes', 't4s_shortcodes_gen');

	add_submenu_page("t4s", 'Switch T4S Account', 'Switch T4S Account', "manage_options", 't4s-switch-accounts', 't4s_switch_accounts');

	

}



function t4s_menu()

{

    include('t4s_main.php');

}



function t4s_switch_accounts()

{

    global $wpdb, $mysqli;

    include('t4s_switch_account.php');

}



function t4s_settings_menu()

{

    global $wpdb, $mysqli;

   ?>

   <script type="text/javascript">

	window.location = '<?php echo admin_url( 'admin.php?page=t4s&t4spage=manage/settings');?>';

   </script>

   <?php

}



function admin_t4s_form_payments() {

    global $wpdb, $mysqli;

    include('includes/forms/t4s_form_payments.php');

}



function t4s_shortcodes_gen()

{

    global $wpdb, $mysqli;

    include('t4s_shortcodes.php');

}



if (is_admin()) {

	include("includes/forms/t4s_form_creator.php");

}





//AJAX

add_action('wp_ajax_core_t4s_callback', 'core_t4s_callback');



function core_t4s_callback() {

	

	if (!$_GET && !$_POST) {

		echo "nonce";

		exit();

	}



	session_start();

				

	$tmp = getT4SAjaxArgs();

	

	sendT4sAjaxRequest($tmp['hash'], $tmp['function'], $tmp['path'], $tmp['file'], $tmp['args']);

	

	die(); 

}





//T4S SYSTEM AJAX SECTION 



function getT4SAjaxArgs() {

				

	$hash = "";

	$function = "";

	$path = "";

	$file = "";

	$args = array();

	

	if (isset($_POST['hash'])) {

		foreach ($_POST as $k => $v) {

			$v = strip_tags($v);

			if ($k == 'hash') {

				if (strlen($v) > 128) $v = "";

				$hash = $v;

			} else if ($k == 'actiont4s') {

				if (strlen($v) > 48) $v = "";

				$function = $v;

			} else if ($k == 'path') {

				if (strlen($v) > 256) $v = "";

				$path = $v;

			} else {

				$k = strip_tags($k);

				if (strlen($v) > 256) $v = "";

				if (strlen($k) > 256) $k = "";

				$args[$k] = $v;

			}

		}

	} else {	

		foreach ($_GET as $k => $v) {

			$v = strip_tags($v);

			if ($k == 'hash') {

				if (strlen($v) > 128) $v = "";

				$hash = $v;

			} else if ($k == 'actiont4s') {

				if (strlen($v) > 48) $v = "";

				$function = $v;

			} else if ($k == 'path') {

				if (strlen($v) > 256) $v = "";

				$path = $v;

			} else {

				$k = strip_tags($k);

				if (strlen($v) > 256) $v = "";

				if (strlen($k) > 256) $k = "";

				$args[$k] = $v;

			}

		}

	}

	

	$arr_set = array('update_org_web_details', 'reset_user_password', 'set_permission', 'update_admin', 'remove_admin', 'refresh_adminlist', 'create_admin', 'save_authorize_config', 'save_paypal_config', 'save_gateway_preferences', 'save_email_preferences', 'update_org_details');

	$arr_don = array('set_primary', 'update_option', 'refresh_optionslist', 'create_contact', 'get_campaign_donations_list', 'get_noncontact_options', 'update_contact', 'remove_contact', 'link_contact', 'refresh_contactlist', 'sort_donation_categories', 'sort_donation_funds', 'get_top_donations_by_fund', 'get_top_donations_by_campaign', 'get_top_donations_by_category', 'get_top_donations_by_fund', 'get_top_donations_by_campaign', 'get_top_donations_by_category', 'get_campaign_donations_list', 'get_donations_by_month');

	$arr_cal = array('update_rsvp_field', 'delete_rsvp_field', 'sort_rsvp_fields', 'setoption', 'newRSVPField', 'event-rsvps', 'addrsvppayment', 'delete_rsvp', 'rsvp_close', 'rsvp_open');



	if (in_array($function, $arr_cal)) {

		$file = "calendar";

	} elseif (in_array($function, $arr_don)) {

		$file = "donations";

	} elseif (in_array($function, $arr_set)) {

		$file = "settings";

	} elseif ($function == "delete_category") {

		$file = "categories";

	} elseif ($_POST['format'] == 'xls') {

		if (!isset($_POST['event_id'])) {

			$file = "donations";

			$c = strip_tags($_POST['c']);

			$f = strip_tags($_POST['f']);

			if (strlen($c) > 24) $c = 1;

			if (strlen($f) > 24) $f = 1;

			$args['c'] = $c;

			$args['f'] = $f;

			$args['r'] = "complete";

			$args['cb'] = "1372532648232";

		} else {

			$file = "calendar";

			$eid = intval($_POST['event_id']);

			$args['event_id'] = $eid;

			$args['format'] = 'xls';

		}

	} elseif (isset($_POST['option']) && isset($_POST['value'])) {

		$file = "calendar";

		$function = 'set_option';

	} elseif (isset($_POST['cat']) && isset($_POST['visibility'])) {

		$file = "calendar";

	}



	//CUSTOM CASES FOR FUNCTIONS

	if ($_POST['actiont4s'] == 'sort_rsvp_fields') {

		$i = 0;

		foreach ($_POST['rsvp-field'] as $g) {

			$g = strip_tags($g);

			if (strlen($g) > 256) $g = "";

			$args['rsvp-field['.$i.']'] = $g;

			$i++;

		}	

	}



	if ($_POST['actiont4s'] == 'sort_donation_fields') {

		$fund = intval($_POST['fund']);

		$url .= implode('&', array_map(function($key, $val) {

			return 'fund[' . urlencode($key) . ']=' . urlencode($val);

		  },

		  array_keys($fund), $fund)

		);	

		$tmp = explode("&", $url);

		foreach ($tmp as $k=>$v) {

			$args[$k] = $v;

		}	

		$args['temp'] = "";

	}

	

	if ($_POST['actiont4s'] == 'sort_donation_categories') {

		$i = 0;

		foreach ($_POST['cat'] as $g) {

			$g = strip_tags($g);

			if (strlen($g) > 256) $g = "";

			$args['cat['.$i.']'] = $g;

			$i++;

		}	

		$args['temp'] = '';

	}

	

	$output = array('hash' => $hash, 'function' => $function, 'path' => $path, 'file' => $file, 'args' => $args);

	

	return $output;



}



function sendT4SAjaxRequest($hash, $function, $path, $file, $args = null) {

		

	if (!checkT4Shash($hash)) {

		echo "fail";

		exit();

	}



	$add = "";

	if ($args) {

		foreach ($args as $k => $v) {

			if ($k != 'fund') {

				$add .= "&".urlencode($k)."=".urlencode($v);

			} else {

				$ttl = "";

				if (is_array($v)) {

					foreach ($v as $o) {

						$ttl .= "&fund[]=".$o;

					}

				}

				$add .= $ttl;

			}

		}

	}



	if ($args['format'] != 'xls') {

		$url = "https://t4s.inspiredonline.com/_isuite/v/".getVersion()."/app_logic/manage/".$file."/ajax.php?action=".urlencode($function)."&path=".urlencode($path).$add;

	} else {

		if (!$args['event_id']) {

			$url = "https://t4s.inspiredonline.com/_isuite/v/".getVersion()."/app_logic/manage/donations/list.php?format=xls&start=".urlencode($args['start'])."&end=".urlencode($args['end'])."&c=&f=&r=complete&cb=1372532648232";

		} else {

			$url = "https://t4s.inspiredonline.com/manage/calendar/event-rsvps?format=xls&event_id=".$args['event_id'];

		}

	}

	

	//custom case for getting donations by month

	if (isset($_GET['fund'])) {

		$fund = intval($_GET['fund']);

		if ($function == 'get_donations_by_month' || $function == 'get_campaign_donations_list') $url .= "&fund=".$fund;

	}

	

	$settings = array('temp' => '');

			

	$url = str_replace("&action=core_t4s_callback", "", $url);		

			

	$ch = curl_init($url);



	curl_setopt($ch, CURLOPT_POST, true);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $settings);	

	

	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 

	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 

	curl_setopt ($ch, CURLOPT_TIMEOUT, 6); 	

	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 



	getT4Scookie();

	setT4SCookie($ch);

	

	curl_setopt ($ch, CURLOPT_REFERER, $url); 

	

	if ($args['format'] == 'xls') {

		$filename="report.xls";

		header("Content-Type: application/vnd.ms-excel");

		header("Content-Disposition: attachment; filename=$filename");

		header("Pragma: no-cache");

		header("Expires: 0");

		

		$function = "xls";



	}

	

	$response = curl_exec($ch);



	curl_close($ch);

	specialT4SresponseCases($function, $response);



}





function specialT4SresponseCases($function, $response) {



	$t4s_hash = getT4Shash();	

			

	if (strncmp($function, 'get_top_donations_by_', 21)==0)  {

		$url = admin_url( 'admin.php?page=t4s&t4spage=donations/'); 

		

		$response = str_replace("campaign-summary?fund=", $url.urlencode("campaign-summary?fund="), $response);

		$response = str_replace("fund-summary?fund=", $url.urlencode("fund-summary?fund="), $response);

		$response = str_replace("./list", $url."list", $response);

		

	}



	if ($function == "refresh_contactlist" || $function == 'remove_contact') {

		$response = str_replace("RemoveContact(", "Plugin_RemoveContact('".$t4s_hash."', ", $response);

		$response = str_replace("SaveContact(", "Plugin_SaveContact('".$t4s_hash."', ", $response);

		$response = str_replace("EditContact(", "Plugin_EditContact('".$t4s_hash."', ", $response);			

		$response = str_replace("CloseContactUpdate(", "Plugin_CloseContactUpdate('".$t4s_hash."', ", $response);

		$response = str_replace("SetContactPrimary(", "Plugin_SetContactPrimary('".$t4s_hash."', ", $response);

		$response = str_replace("SetPermission(", "Plugin_SetPermission('".$t4s_hash."', ", $response);

		

		echo $response;

	}



	if ($function == 'link_contact') {

		$response = str_replace("RemoveContact(", "Plugin_RemoveContact('".$t4s_hash."', ", $response);

		$response = str_replace("SaveContact(", "Plugin_SaveContact('".$t4s_hash."', ", $response);

		$response = str_replace("EditContact(", "Plugin_EditContact('".$t4s_hash."', ", $response);			

		$response = str_replace("CloseContactUpdate(", "Plugin_CloseContactUpdate('".$t4s_hash."', ", $response);

		$response = str_replace("SetPermission(", "Plugin_SetPermission('".$t4s_hash."', ", $response);

		

	}

	

	if ($function == 'get_campaign_donations_list') {

		$regexp = "/https:\/\/t4s.inspiredonline.com\/manage\/donations\/list\?p=1\&d=(.*)'/";

		$matches = array();

		$result = preg_match_all($regexp, $response, $matches);		

		$response = str_replace('https://t4s.', '?page=t4s&t4spage=https://t4s.', $response);

			

		foreach ($matches[0] as $m) {

			$m = str_replace("'", "", $m);

			$response = str_replace($m, urlencode($m), $response);

		}

		

		$response = str_replace('https%3A%2F%2Ft4s.inspiredonline.com%2Fmanage%2Fdonations%2Flist%3Fp%3D', 'donations%2Flist%3Fp%3D', $response);		

		echo $response;

	}

	

	if ($function == 'create_contact') {

		echo $response;

	}

	

	if ($function == 'refresh_optionslist') {

		$response = str_replace("UpdateDonationOption(", "Plugin_UpdateDonationOption('".$t4s_hash."', ", $response);

		echo $response;

	}



	if ($function == 'refresh_adminlist') {	

		

		$response = str_replace("CreateNewAdmin(", "Plugin_CreateNewAdmin('".$t4s_hash."', ", $response);

		$response = str_replace("CancelNewAdmin(", "Plugin_CancelNewAdmin('".$t4s_hash."' ", $response);

		$response = str_replace("RemoveAdmin(", "Plugin_RemoveAdmin('".$t4s_hash."', ", $response);

		$response = str_replace("SaveAdmin(", "Plugin_SaveAdmin('".$t4s_hash."', ", $response);

		$response = str_replace("CloseAdminUpdate(", "Plugin_CloseAdminUpdate('".$t4s_hash."', ", $response);

		$response = str_replace("EditAdmin(", "Plugin_EditAdmin('".$t4s_hash."', ", $response);

		$response = str_replace("SetPermission(", "Plugin_SetPermission('".$t4s_hash."', ", $response);

		

		echo $response;

	}

	

	$response = str_replace("api/t4s_plugin_ajax.php?action=sort_rsvp_fields", "api/t4s_plugin_ajax.php?action=sort_rsvp_fields&hash=".$t4s_hash, $response);

	

	$response = str_replace("DonationsRefreshTopDonations(", "Plugin_DonationsRefreshTopDonations('".$t4s_hash."', ", $response);

	

	if ($function == 'update_option' || $function == 'update_org_details' || $function == 'save_email_preferences' || $function == 'save_gateway_preferences' || $function == 'save_paypal_config' || $function == 'save_authorize_config' || $function == 'create_admin' || $function == 'remove_admin' || $function == 'update_admin' || $function == 'set_permission' || $function == 'reset_user_password' || $function == 'update_org_web_details' || $function == 'get_noncontact_options' || (substr($function,0,21) == 'get_top_donations_by_') || $function == "xls" || $function == 'get_donations_by_month' || $function == 'addrsvppayment' || $function == 'delete_rsvp' || $function == 'rsvp_close' || $function == 'rsvp_open') {		

		echo $response;

	}

	

	if ($function == 'newRSVPField') echo $response;

}



?>