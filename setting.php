<?php
/*
Plugin Name: BC API Checker
Plugin URI: shreedata.com
Description: This is used to get notifications of emails when the BC API connection has failed.
Author: Smrutiranjan mishra
Version: 1.7.2
Author URI: https://shreedata.com
*/
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}  
function BCAPICHECKER_register_settings() {  
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_access_token', 'BCAPICHECKER_callback' ); 
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_api_path', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_client_name', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_client_secret', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_client_id', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_email', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_body', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_frequency', 'BCAPICHECKER_callback' );
   register_setting( 'BCAPICHECKER_options_group', 'BCAPICHECKER_status');
} 
add_action( 'admin_init', 'BCAPICHECKER_register_settings' );
function BCAPICHECKER_callback( $data ) 
{		
	if ( empty( $data ) ) {
		add_settings_error(
			'requiredTextFieldEmpty',
			'empty',
			'Fields cannot be empty',
			'error'
		); 
	}
	else {
		return $data;
	}
}
function BCAPICHECKER_register_options_page() {
  add_options_page('BCAPI Control', 'BC Api Checker', 'manage_options', 'bcapichecker', 'BCAPICHECKER_options_page');
}
// Add settings link on plugin page
function BCAPICHECKER_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=bcapichecker">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'BCAPICHECKER_settings_link' );
add_action('admin_menu', 'BCAPICHECKER_register_options_page');
function BCAPICHECKER_notice__success() {
    if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
        echo sprintf( __( '<div class="notice notice-warning is-dismissible"><p><strong>The DISABLE_WP_CRON constant is set to true as of %s. WP-Cron is disabled and will not run on it\'s own. You need to set alternate Cron setting for BC API Checker be working.</strong></p></div>', 'bc4wpapichecker' ), current_time( 'm/d/Y g:i:s a' ) ) ;
    }
	 if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
        echo sprintf( __( '<div class="notice notice-warning is-dismissible"><p><strong>The ALTERNATE_WP_CRON constant is set to true as of %s.  This plugin cannot determine the status of your WP-Cron system.</strong></p></div>', 'bc4wpapichecker' ), current_time( 'm/d/Y g:i:s a' ) ) ;
    }
}
add_action( 'admin_notices', 'BCAPICHECKER_notice__success' );
function BCAPICHECKER_options_page()
{	
?>
  <div class="wrap">
  <?php screen_icon(); ?>
  <h3>BigCommerce Api Checker Control</h3>
  <form method="post" action="options.php">
  <?php  settings_fields( 'BCAPICHECKER_options_group' );
	do_settings_sections( 'BCAPICHECKER_options_group' );
    testconnection();BCAPICHECKER_activation();	
	?><style>.warning{background-color:yellow;color:#000;padding:5px;font-size:14px;width:100%;text-align:center;}.regular-text{width:80%;}.chkconnect{color:green;text-weight:bold;letter-spacing:1px;}.chkconnectfail{color:red;text-weight:bold;letter-spacing:1px;}.description{font-style:italic;font-size:11px;}</style> 
  <p>Please fill the following details to check api fails and get notifications for it.</p>
  <h2 class="title">API Credentials</h2>  
   <table class="form-table" role="presentation">
	<tbody>
	<tr>
	<th scope="row"><label>Connection Status</label></th>
	<td>
	<?php $status=get_option("BCAPICHECKER_status");if($status=="Connected"){ echo '<span class="chkconnect">Connected</span>&nbsp;&nbsp;&nbsp;';} else{ echo '<span class="chkconnectfail">Inactive</span>&nbsp;&nbsp;&nbsp;';}?> <input name="BCAPICHECKER_status" type="hidden" id="BCAPICHECKER_status" value="<?php echo get_option('BCAPICHECKER_status');?>" class="regular-text code"/>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="BCAPICHECKER_client_name">Channel Name</label></th>
	<td><input name="BCAPICHECKER_client_name" type="text" id="BCAPICHECKER_client_name" value="<?php echo get_option('BCAPICHECKER_client_name');?>" class="regular-text code"/></td>
	</tr>
	
	<tr>
	<th scope="row"><label for="BCAPICHECKER_api_path">Base API Path</label></th>
	<td><input name="BCAPICHECKER_api_path" type="text" id="BCAPICHECKER_api_path" value="<?php echo get_option('BCAPICHECKER_api_path');?>" class="regular-text code"/><p class="description">The API Path for your BigCommerce store. E.g., https://api.bigcommerce.com/stores/abc9defghi/v3/</p></td>
	</tr>
	
	<tr><th scope="row"><label for="BCAPICHECKER_access_token">Access Token</label></th>
	<td><input name="BCAPICHECKER_access_token" type="password" id="BCAPICHECKER_access_token" value="<?php echo get_option('BCAPICHECKER_access_token');?>" class="regular-text code"/><p class="description">The Access Token provided for your API account. E.g., ab24cdef68gh13ijkl5mn7opqrst9u2v</p></td>
	</tr>
	
	<tr><th scope="row"><label for="BCAPICHECKER_client_id">Client ID</label></th>
	<td><input name="BCAPICHECKER_client_id" type="password" id="BCAPICHECKER_client_id" value="<?php echo get_option('BCAPICHECKER_client_id');?>" class="regular-text code"/><p class="description">The Client ID provided for your API account. E.g., abcdefg24hijk6lmno8pqrs1tu3vwxyz</p></td>
	</tr>
	
	<tr><th scope="row"><label for="BCAPICHECKER_client_secret">Client Secret</label></th>
	<td><input name="BCAPICHECKER_client_secret" type="password" id="BCAPICHECKER_client_secret" value="<?php echo get_option('BCAPICHECKER_client_secret');?>" class="regular-text code"/><p class="description">The Client Secret provided for your API account. E.g., 0bcdefg24hijk6lmno8pqrs1tu3vw5x</p></td>
	</tr>
	</tbody></table>
<h2 class="title">Notification Setting</h2>
	<table class="form-table" role="presentation">
	<tbody>
	<tr>
	<th scope="row"><label for="BCAPICHECKER_email">Email Id</label></th>
	<td><input name="BCAPICHECKER_email" type="text" id="BCAPICHECKER_email" value="<?php echo get_option('BCAPICHECKER_email');?>" class="regular-text code" /><p class="description">You can add multiple email id by using , in between.like email1@domain.com,email2@domain.com</p></td>
	</tr>
	<tr>
	<th scope="row"><label for="BCAPICHECKER_body">Email Content</label></th>
	<td><textarea name="BCAPICHECKER_body" class="regular-text" style="height:150px;"><?php echo get_option('BCAPICHECKER_body');?></textarea></td>
	</tr>
	<tr>
	<th scope="row"><label for="BCAPICHECKER_frequency">Check Frequency</label></th>
	<td><select name="BCAPICHECKER_frequency">
	<?php 
	$options=array('daily'=>'Once Per Day','hourly'=>'Once Per Hour','twicedaily'=>'Twice Per Day','weekly '=>'Once Per Week');
	foreach($options as $key=>$val){
	  if($key==get_option('BCAPICHECKER_frequency')){
		  echo '<option value="'.$key.'" selected>'.$val.'</option>';
	  } else {echo '<option value="'.$key.'">'.$val.'</option>';}
	}?>
	</select><p class="description">You must be enabled wp cron to execute it automatically from wp by adding below line in wp-config.php file 
	define('DISABLE_WP_CRON', false);</p></td>
	</tr>
	<tr><td colspan="2"><strong>In case your wp has disabled the cron job. you can do it from below like in you cpanel or vps server..<br/><br/>
	wget -q -O - <?php echo site_url();?>/wp-cron.php?doing_wp_cron >/dev/null 2>&1 <br/><br/>
	The >/dev/null 2>&1 part of the command above disables email notifications.</strong><br/><br/>
	<strong>You can manage this cron event "BCAPICHECKER_event" from wp control plugin (i.e https://wordpress.org/plugins/wp-crontrol/)<br/><br/>
	You can add external cron job from cpanel as below like for once a day<br/><br/>
	0	0	*	*	*	curl -s <?php echo site_url();?>/wp-content/plugins/bc4wpapichecker/cron.php</strong>
	</td></tr>
	</tbody></table>
  <?php  submit_button('Save Your Settting'); ?>
  </form>
  </div>
<?php
}  
function testconnection(){
	$clientid=get_option("BCAPICHECKER_client_id");$access_token=get_option("BCAPICHECKER_access_token");
	$url=get_option("BCAPICHECKER_api_path")."/channels";	
	$channelname=get_option("BCAPICHECKER_client_name");
	$status='Inactive';
	if(!empty($url) && !empty($clientid) && !empty($access_token) && !empty($channelname))
	{	
		$url=str_replace("//channels","/channels",$url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept:application/json','X-Auth-Client:'.$clientid,'X-Auth-Token:'.$access_token
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,60);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_URL, $url); 
		$return = curl_exec($ch);
		curl_close($ch);	
		$res=json_decode($return); 
		update_option( 'BCAPICHECKER_status', 'Inactive' );
		foreach($res->data as $resval){		
			if($resval->name == $channelname && $resval->status == 'active')
			{
				$status='Connected';
				update_option( 'BCAPICHECKER_status', 'Connected' );			
			}
		}	
	}
	return $status;
}
register_deactivation_hook( __FILE__, 'BCAPICHECKER_deactivation' );
function BCAPICHECKER_activation() {    
	$frequency=get_option('BCAPICHECKER_frequency');
	if($frequency==""){
		wp_clear_scheduled_hook( 'BCAPICHECKER_event' );
		wp_schedule_event(time(), 'daily', 'BCAPICHECKER_event'); 
	} else {
		wp_clear_scheduled_hook( 'BCAPICHECKER_event' ); 
		wp_schedule_event(time(), $frequency, 'BCAPICHECKER_event'); 
	}   
}
function BCAPICHECKER_deactivation() {
	wp_clear_scheduled_hook( 'BCAPICHECKER_event' );
}
add_action('BCAPICHECKER_event', 'BCAPICHECKER_event_exe');
 
function BCAPICHECKER_event_exe() {
    $status=testconnection();
	if($status == "Inactive"){
		$subject='Bicommerce api connection failed at '.get_home_url();		
		$message ='';
		$email=get_option("BCAPICHECKER_email");
		if($email==""){
			$email=get_bloginfo('admin_email');
		} else {
			$emailarr=explode(",",$email);
			foreach($emailarr as $val){
				if($val!=""){
					$headers[] = 'Cc: '.$val.''."\r\n";
				}
			}
		}
		//echo "admin_email".get_bloginfo('admin_email')."<br/>";
		$headers[] = 'From: Webmaster <'.get_bloginfo('admin_email').'>'."\r\n"; 
		$message=get_option("BCAPICHECKER_body");
		if($message==""){
			$message .='<p>Hi Webmaster,</p>
			<p>Your BigCommerce Channel API for '.get_home_url().' has failed to connect.</p>
			<p>Thanks '.get_home_url().' Cron</p>';
		}
		$message .='<p align="left">'.date('l jS \of F Y h:i:s A').'</p>';
		
		add_filter( 'wp_mail_content_type', 'BCAPICHECKER_content_type' );
		//print_r($headers);
		//echo "<br/>".$subject."<br/>".$message;
		$status1=wp_mail(get_bloginfo('admin_email'),$subject,$message,$headers);
		remove_filter( 'wp_mail_content_type', 'BCAPICHECKER_content_type' );
		//var_dump($status1);
		if($status1 == false){ echo 'Email notifications has failed';}
		echo '<br/>Cron execute successfully';
	}
} 
function BCAPICHECKER_content_type(){return 'text/html';}