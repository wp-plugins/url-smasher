<?php
/*
	Plugin Name: The URL Smasher
	Plugin URI: http://cellarweb.com/wordpress-plugins/
	Description: Automatically shortens URLS in posts/pages/comments, using the goo.gl shortener API
	Text Domain: 
	Author: Rick Hellewell / CellarWeb.com
	Version: 1.01a 
	Author URI: http://CellarWeb.com
	License: GPLv2 or later
*/

/*
Copyright (c) 2015 by Rick Hellewell and CellarWeb.com
All Rights Reserved

		email: rhellewell@gmail.com  

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
	
Some code (PHP version checking) used via GPL license from 
	the plugin: https://wordpress.org/plugins/comment-form-inline-errors/
*/
// ----------------------------------------------------------------
// ----------------------------------------------------------------


	// Add settings link on plugin page
	 function url_smasher_settings_link($links) { 
	  $settings_link = '<a href="options-general.php?page=URL_Smasher_settings" title="URL Smasher">URL Smasher Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	$plugin = plugin_basename(__FILE__); 
	
	add_filter("plugin_action_links_$plugin", 'url_smasher_settings_link' );

//	build the class for all of this 
class URL_Smasher_Settings_Page
{

   	// Holds the values to be used in the fields callbacks
	private $options;

	// start your engines!
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'url_smasher_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'url_smasher_page_init' ) );
	}

	// add options page
	public function url_smasher_add_plugin_page()
	{
		// This page will be under "Settings"
		add_options_page(
			'URL Smasher Settings Admin', 
			'URL Smasher Settings', 
			'manage_options', 
			'URL_Smasher_settings', 
			array( $this, 'url_smasher_create_admin_page' )
		);
	}

   // options page callback
	public function url_smasher_create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'url_smasher_options' );
		?>

		<div class="wrap">
			<?php //url_smasher_screen_icon(); ?>
			<?php url_smasher_info_top(); ?>
			<form method="post" action="options.php">
				<?php
						// This prints out all hidden setting fields
						settings_fields( 'url_smasher_option_group' );   
						do_settings_sections( 'url_smasher_setting_admin' );
						submit_button(); 
					?>
			</form>
			<?php url_smasher_info_bottom();		// display bottom info stuff
					?>
		</div>
<?php
	}

	// Register and add the settings
	public function url_smasher_page_init()
	{		
		register_setting(
			'url_smasher_option_group', // Option group
			'url_smasher_options', // Option name
			array( $this, 'url_smasher_sanitize' ) // Sanitize
		);

		add_settings_section(
			'url_smasher_setting_section_id', // ID
			'', // Title
			array( $this, 'url_smasher_print_section_info' ), // Callback
			'url_smasher_setting_admin' // Page
		);  

		add_settings_field(
			'the_version', 
			'URL Smasher version', 
			array( $this, 'url_smasher_the_version_callback' ), 
			'url_smasher_setting_admin', 
			'url_smasher_setting_section_id', // Section		   
			array('fieldtype' => 'input', 'fieldsize' => '50', 'fieldmax' => '50')
		);	  

		add_settings_field(
			'google_api_key', 
			'Your Google API Key', 
			array( $this, 'url_smasher_google_api_key_callback' ), 
			'url_smasher_setting_admin', 
			'url_smasher_setting_section_id', // Section		   
			array('fieldtype' => 'input', 'fieldsize' => '50', 'fieldmax' => '50')
		);	  

		add_settings_field(
			'enable_content', 
			'Enable URL Smash for post/page content?', 
			array( $this, 'url_smasher_enable_content_callback' ), 
			'url_smasher_setting_admin', 
			'url_smasher_setting_section_id', // Section		   
			array('fieldtype' => 'checkbox', 'fieldsize' => null, 'fieldmax' => null )
		);	  

		add_settings_field(
			'enable_comments', 
			'Enable URL Smash for Comments?', 
			array( $this, 'url_smasher_enable_comments_callback' ), 
			'url_smasher_setting_admin', 
			'url_smasher_setting_section_id', // Section		   
			array('fieldtype' => 'checkbox', 'fieldsize' => null, 'fieldmax' => null )
		);	  

	}

	// sanitize the settings fields on submit
	// 	@param array $input Contains all settings fields as array keys
	public function url_smasher_sanitize( $input )
	{
		$new_input = array();
		if( isset( $input['the_version'] ) )
			$new_input['the_version'] =  $input['the_version'];

		if( isset( $input['google_api_key'] ) )
			$new_input['google_api_key'] = sanitize_text_field( $input['google_api_key'] );

		if( isset( $input['enable_comments'] ) ) 
			$new_input['enable_comments'] = "1";

		if( isset( $input['enable_content'] ) ) 
			$new_input['enable_content'] = "1";

		return $new_input;
	}

	// print the Section text
	public function url_smasher_print_section_info()
	{
		print '<h3><strong>Settings for URL Smasher from CellarWeb.com</strong></h3>';
		print '<p>Save your settings once after upgrading to the latest version.</p>';
	}

	// version number callback
	public function url_smasher_the_version_callback()
	{
	   printf(
			'<input type="text" type="hidden" id="the_version" name="url_smasher_options[the_version]" value="1.01a (22-Aug-2015)" readonly="readonly" width="5" maxlength="5" />',
			isset( $this->options['the_version'] ) ? esc_attr( $this->options['the_version']) : esc_attr( $this->options['the_version'])
		);
	}

	// api key callback
	public function url_smasher_google_api_key_callback()
	{
		printf(
			'<table><tr><td><input type="text" id="google_api_key" name="url_smasher_options[google_api_key]" size="50"  maxlength="50" value="%s" ></td><td valign="top">Enter your Google API Key. <em>An invalid key will result in URLs <strong>not</strong> being smashed</em>. <br>To get your own Google API Key, start <a href="https://developers.google.com/api-client-library/python/guide/aaa_apikeys" title="Get Your Own goo.gl API" target="_blank">here</a>. There is no validation of the entered key value.</td></tr></table>',
			isset( $this->options['google_api_key'] ) ? esc_attr( $this->options['google_api_key']) : ''
		);
	}
	
	// content checkbox callback
	public function url_smasher_enable_content_callback()
	{
		printf(
			"<table><tr><td><input type='checkbox' id='enable_content' name='url_smasher_options[enable_content]'   value='1' " . checked( '1', $this->options[enable_content] , false ) . " /></td><td valign='top'>Check if you want to smash URLs in page/post content. URLs are smashed only on content save/update.</td></tr></table> ",
			isset($this->options['enable_content'] ) ?  '1' : '0'
		);
	}
	
	// comment checkbox callback
	public function url_smasher_enable_comments_callback()
	{
		printf(
			"<table><tr><td><input type='checkbox' id='enable_comments' name='url_smasher_options[enable_comments]'   value='1' " . checked( '1', $this->options[enable_comments] , false ) . " /></td><td valign='top'>Check if you want to smash URLs in post comments. URLs in comments are only smashed when the comment is saved or updated.</td></tr></table> ",
			isset($this->options['enable_comments'] ) ?  '1' : '0'
		);
	}
	

}

if( is_admin() )
// end of the class stuff

	$my_settings_page = new URL_Smasher_Settings_Page();

// ---------------------------------------------------------------------------- 
// supporting functions
// ---------------------------------------------------------------------------- 
//	display the top info part of the page	
// ---------------------------------------------------------------------------- 
function url_smasher_info_top() {

//$image = plugin_dir_url( __FILE__ ) . '/assets/url_smasher_logo.png';
$image = plugin_dir_url( __FILE__ ) . '/assets/icon-128x128.png';
	?>
<div class="wrap"> <img src="<?php echo $image; ?>" />
	<!--<h2>URL Smasher Settings</h2>-->
		<p>Enabling URL Smasher will automatically (without any effort on your part) convert all valid URLs to goo.gl short links. This will happen for pages, posts, and comments (depending on your settings below). Because you don't have to do anything special to smash a URL, this plugin is great for sites with lots of authors.</p>
		<p>URLs are smashed only when posts/pages/comments are saved/updated/submitted. Any prior content does not get URL Smashed unless you save that content.</p>
		<p>All you need is a valid Google API Key; start <a href='https://developers.google.com/api-client-library/python/guide/aaa_apikeys' title='Get Your Own goo.gl API' target='_blank'>here</a>. If you don't use a valid Google API Key, URLs will not be smashed, they will be saved as entered. Be aware of Google's limit on the number of daily requests on your account.</p>
		<p>If there is a problem with the URL Smasher process, those URLs will not be changed. Once you solve the problem with your Google account (which is almost always the cause of URL Smashing not woorking), the next Save/Update of your content will smash the URLs.</p>
		<p>Any URL Smashed into a goo.gl URL will not be re-smashed, and will stay the same.</p>
		<p>Save your settings once after upgrading to the latest version.</p>
	<hr>
	<!--<p><strong>These options are available:</strong></p>--> 
</div>
<?php 

}
// ---------------------------------------------------------------------------- 
// display the bottom info part of the page
// ---------------------------------------------------------------------------- 
function url_smasher_info_bottom() {
	// print copyright with current year, never needs updating
	$xstartyear = "2014";
	$xname = "Rick Hellewell";
	$xcompanylink1 = ' <a href="http://CellarWeb.com" title="CellarWeb" >CellarWeb/com</a>';
	// leave this empty if no company 2
	$xcompanylink2 = '';
	// output
	echo 'Copyright &copy; ' . $xstartyear . '  - ' . date("Y") . ' by ' . $xname . ' and ' . $xcompanylink1 ;
	if ($xcompanylink2) {
		echo ' and ';
		echo $xcompanylink2;
		}
	echo ' , All Rights Reserved. Released under GPL2 license.';
	return; 
}
// end  copyright ---------------------------------------------------------

// ---------------------------------------------------------------------------- 
// ``end of admin area
// ---------------------------------------------------------------------------- 

// ---------------------------------------------------------------------------- 
// start of operational area that changes the comments box stuff 		
// ---------------------------------------------------------------------------- 

$xoptions = get_option( 'url_smasher_options' );

// ---------------------------------------------------------------------------- 
// set the apikey global thing
global $apiKey;
global $commentdata;
$apiKey = $xoptions['google_api_key'];

// set up the filters to process things based on the options settings

// preprocess comment after submitted to smash urls if enabled
if ($xoptions['enable_comments']) {
	add_filter( 'preprocess_comment' , 'url_smash_comment' ); 
}

// preprocess posts/pages after submitted to smash urls if enabled
if ($xoptions['enable_content']) {
	add_filter( 'wp_insert_post_data' , 'url_smash_post' ); 
}
// ---------------------------------------------------------------------------- 

// ---------------------------------------------------------------------------- 
// end of add_actions and add_filters for posts/pages with comments open
// ---------------------------------------------------------------------------- 

// ---------------------------------------------------------------------------- 
// here's where we do the work!
// ---------------------------------------------------------------------------- 

 function url_smash_comment ($commentdata ) {	// smash urls in comments
	global $commentdata;
	unset( $commentdata['comment_author_url'] );
	$xtext = $commentdata['comment_content'];
	$xsmashed  = url_smasher_find_the_urls($xtext);
	$commentdata['comment_content']  = $xsmashed;
	return $commentdata; }
	
 function url_smash_post ($data, $postarr) {	// smash urls in posts and pages
	$xtext = $data['post_content'];
	$data['post_content']  = url_smasher_find_the_urls($xtext);
	return $data; }
	
// ---------------------------------------------------------------------------- 
// smash the urls
function url_smasher_find_the_urls( $text ) {
	// get the urls_allowed value
	// regex to find all types of urls in the text
	$regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i" ;
	// put found urls in the $urls_array array
	preg_match_all($regex, $text, $urls_array) ;
	// extract the elements of the first element to get a one-dimension array
	$urls_array = $urls_array[0];
	// remove the first 'urls_allowed' count elements from the $results array
	// for each $urls_array, find the position in the text, then delete the url from that spot only
	foreach ($urls_array as $url_item) {
		if (!strpos($url_item,'goo.gl')) {
			if (strpos($text, $url_item)) {
				$xsmashed_url = url_smasher_smash_the_url($url_item);
				if (is_null($xsmashed_url)) {return $text; } 	// error, so leave it alone
				$text = str_replace(  $url_item,$xsmashed_url,$text);
			}
		}
	}
	return $text;	// excess urls stripped or replaced
}
// ---------------------------------------------------------------------------- 
/* shorten URL with my goo.gl API key
based on http://stackoverflow.com/questions/29214063/goo-gl-url-shortener-stopped-working-php-curl
plus answer to add the api key as query parameter in the request
*/
function url_smasher_smash_the_url($url)
    {
		global $apiKey;
        //This is the URL you want to shorten
        $longUrl = $url;
        //Get API key from : http://code.google.com/apis/console/

        $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
        $jsonData = json_encode($postData);

        $curlObj = curl_init();

        curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($curlObj);
        //change the response json string to object
        $json = json_decode($response);
        curl_close($curlObj);
		if (strpos($json->id,"goo.gl") == 0) {
			return $url;
		} else
		{ // put any additional error trapping here, got nothing here, since we already trapped basic URL Smasing request fails 
		}
		$newurl = $json->id ; 

        return $newurl;
}	

// ---------------------------------------------------------------------------- 
// check for WP/PHP versions, and add inline errors for bad form data
// ---------------------------------------------------------------------------- 
// based on the code from the plugin: https://wordpress.org/plugins/comment-form-inline-errors/
// 		which handles form errors (missing required fields, etc) 
//		plus checks WordPress and PHP versions
//		Code used via that plugin's GPL2, so is lawfully included (but props for saving me a lot of work!)


if (!defined('ABSPATH')) { exit; }

if (!class_exists('url_smasher_wpCommentFormInlineErrors')){
	class url_smasher_wpCommentFormInlineErrors
	{
		/* minimum required wp version */
		public $url_smasher_wpVer = "4.0";
		/* minimum required php version */
		public $url_smasher_phpVer = "5.3";
		// add the action to call the init function
		public function __construct() { add_action('init', array($this, 'url_smasher_init')); }

		// initialize some stuff
		public function url_smasher_init()
		{
			if(!$this->url_smasher_checkRequirements()){ return; }
			/* all these hooks are in wp since version 3.0, that's where we aim. */
			add_filter('wp_die_handler', array($this, 'url_smasher_getWpDieHandler'));
		}

		// check the WordPress version, and display notices as needed
		private function url_smasher_checkRequirements()
		{
			global $wp_version;
			if (!version_compare($wp_version, $this->wpVer, '>=')){
				$this->url_smasher_pluginDeactivate();
				add_action('admin_notices', array($this, 'url_smasher_displayVersionNotice'));
				return FALSE;
			} elseif (!version_compare(PHP_VERSION, $this->phpVer, '>=')){
				$this->url_smasher_pluginDeactivate();
				add_action('admin_notices', array($this, 'url_smasher_displayPHPNotice'));
				return FALSE;
			}
			return TRUE;
		}

		// Deactivates our plugin if anything goes wrong. Also, removes the
		//		"Plugin activated" message, if we don't pass requriments check.

		private function url_smasher_pluginDeactivate()
		{
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			deactivate_plugins(plugin_basename(__FILE__));
			unset($_GET['activate']);
		}

		// hey you!  get your WordPress upgraded! 
		public function url_smasher_displayVersionNotice()
		{
			global $url_smasher_wp_version;
			$this->url_smasher_displayAdminError(
				'<p>Your version of WordPress is not current, so you shouldn\'t use this plugin. You should really upgrade to the latest WordPress. Do it now!</p><p>Until you upgrade, this plugin won\'t work, and you are leaving your site open to all sorts of hackers. (This plugin requires at least WordPress varsion ' . $this->url_smasher_wpVer . ' or higher.)</p> <p>You are currently using ' . $url_smasher_wp_version . '. Please upgrade your WordPress version now!</p><p>The URL Smasher plugin has been disabled until you update your WordPress version.</p>');
		}

		// hey you! updatae your PHP version!
		public function url_smasher_displayPHPNotice()
		{
			$this->url_smasher_displayAdminError(
				'<p>You need PHP version at least '. $this->url_smasher_phpVer .' to run this plugin. You should really update your PHP version to protect your site against hackers.</p><p>You are currently using PHP version ' . PHP_VERSION . '.</p><p>The URL Smasher plugin has been disabled until you update your PHP version.</p>');
		}

		// display the error all pretty
		private function url_smasher_displayAdminError($error) { echo '<div id="message" class="error"><p><strong>' . $error . '</strong></p></div>';  }

		// overwrite WordPress error handling
		function url_smasher_getWpDieHandler($handler){ return array($this, 'url_smasher_handleWpError'); }

		// display the error messages inside the array, make it pretty
		function url_smasher_handleWpError($message, $title='', $args=array())
		{
			// this is simple, if it's not admin error, and we simply continue
			// and sort it our way. Meaning, send errors to form itself and display them thru $_SESSION.
			// and yes, we test if comment id is present, not sure how else to test if commenting featured is being used :)
			if(!is_admin() && !empty($_POST['comment_post_ID']) && is_numeric($_POST['comment_post_ID'])){
				$_SESSION['formError'] = $message;
				// let's save those form fields in session as well hey? bit annoying
				// filling everything again and again. might work
				$denied = array('submit', 'comment_post_ID', 'comment_parent');
				foreach($_POST as $key => $value){
					if(!in_array($key, $denied)){
						$_SESSION['formFields'][$key] = stripslashes($value);
					}
				}
				// write, redirect, go
				session_write_close();
				wp_safe_redirect(get_permalink($_POST['comment_post_ID']) . '#formError', 302);
				exit;
			} else {
				_default_wp_die_handler($message, $title, $args);   // this is for the other errors
			}
		}

		// display the error on the form inline nice and fancy
		public function url_smasher_displayFormError()
		{
			$formError = $_SESSION['formError'];
			unset($_SESSION['formError']);
			if(!empty($formError)){
				echo '<div id="formError" class="formError" style="color:red;">';
				echo '<p>'. $formError .'</p>';
				echo '</div><div class="clear clearfix"></div>';
			}
		}

		// put the form data back in so it doesn't need to be filled out again
		function url_smasher_formDefaults($fields)
		{
			$formFields = $_SESSION['formFields'];
			foreach($fields as $key => $field){
				if($this->stringContains('input', $field)){
					if($this->stringContains('type="text"', $field)){
						$fields[$key] = str_replace('value=""', 'value="'. stripslashes($formFields[$key]) .'"', $field);
					}
				} elseif ($this->stringContains('</textarea>', $field)){
					$fields[$key] = str_replace('</textarea>', stripslashes($formFields[$key]) .'</textarea>', $field);
				}
			}
			return $fields;
		}

		// a bit of special handling for the comment field
		function url_smasher_formCommentDefault($comment_field)
		{
			$formFields = $_SESSION['formFields'];
			unset($_SESSION['formFields']);
			return str_replace('</textarea>', $formFields['comment'] . '</textarea>', $comment_field);
		}

		// a little help with filling out the form
		public function url_smasher_stringContains($needle, $haystack){ return strpos($haystack, $needle) !== FALSE; }

	}

}

new url_smasher_wpCommentFormInlineErrors();

// ---------------------------------------------------------------------------- 
// all done!
// ---------------------------------------------------------------------------- 


