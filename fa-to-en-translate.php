<?php
/*
Plugin Name: Permalink Persian to english
Plugin URI: http://novinweb.ir/fa-to-en
Description: WordPress Permalink automatic translation from Persian to English
Version: 1.0
Author: navid shayeste
Author URI: http://forum.shoptalk.ir/user/2-navid/
*/

define("CLIENTID",get_option('wp_novinweb_translate_clientid'));
define("CLIENTSECRET",get_option('wp_novinweb_translate_clientsecret'));
define("SOURCE","fa");
define("TARGET","en");


class fatoensettingsHttpRequest
{
	function curlRequest($url, $header = array(), $postData = ''){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if(!empty($header)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if(!empty($postData)){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
		}
		$curlResponse = curl_exec($ch);
		curl_close($ch);
		return $curlResponse;
	}
}

class fatoensettingsBingTranslator extends fatoensettingsHttpRequest
{
	private $_clientID = CLIENTID;
	private $_clientSecret = CLIENTSECRET;
	private $_fromLanguage = SOURCE;
	private $_toLanguage = TARGET;

	private $_grantType = "client_credentials";
	private $_scopeUrl = "http://api.microsofttranslator.com";
	private $_authUrl = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";

	private function _getTokens(){
		try{
			$header = array('User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:6.0.1) Gecko/20100101 Firefox/6.0.1');
			$postData = array(
				'grant_type' => $this->_grantType,
				'scope' => $this->_scopeUrl,
				'client_id' => $this->_clientID,
				'client_secret' => $this->_clientSecret
			);
			$response = $this->curlRequest($this->_authUrl, $header, $postData);
			$jsonObj = json_decode($response);
			if(!empty($jsonObj->access_token)){
				return $jsonObj->access_token;
			}
		}
		catch(Exception $e){
			//echo "Exception-" . $e->getMessage();
		}
	}

	function translate($inputStr){
		$params = "text=" . rawurlencode($inputStr) . "&from=" . $this->_fromLanguage . "&to=" . $this->_toLanguage;
		$translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/Translate?$params";
		$accessToken = $this->_getTokens();
		$authHeader = "Authorization: Bearer " . $accessToken;
		$header = array($authHeader, "Content-Type: text/xml");
		$curlResponse = $this->curlRequest($translateUrl, $header);
		
		$xmlObj = simplexml_load_string($curlResponse);
		$translatedStr = '';
		foreach((array)$xmlObj[0] as $val){
			$translatedStr = $val;
		}

		return $translatedStr;
	}

}

if(get_option("wp_novinweb_translate_hamahangi")!='yes'){

function wp_novinweb_translate($postid){
	global $wpdb;
	$sql = "SELECT post_title,post_name FROM $wpdb->posts WHERE ID = '$postid'";
	$results = $wpdb->get_results($sql);	
	$post_title = $results[0]->post_title;
	$post_name = $results[0]->post_name;

	if( !substr_count($post_name,'%') && !is_numeric($post_name) ){
		if(substr_count($post_name,'_')){
			$fatoensettings_post_name = str_replace('_','-',$post_name);
			$sql = "UPDATE $wpdb->posts SET post_name = '$fatoensettings_post_name' WHERE ID = '$postid'";
			$wpdb->query($sql);
		}
		return true;
	}

	$post_title = str_replace(array('_','/'),array(' ',' '),$post_title);
	$fatoensettings_bing= new fatoensettingsBingTranslator();
	$fatoensettings_title = sanitize_title( $fatoensettings_bing->translate($post_title) );
	if( strlen($fatoensettings_title) < 2 ){
		$fatoensettings_title = $postid;
	}
		
	$sql = "UPDATE $wpdb->posts SET post_name = '$fatoensettings_title' WHERE ID = '$postid'";		
	$wpdb->query($sql);
}
//add_action('publish_post', 'wp_novinweb_translate', 1);
//add_action('edit_post', 'wp_novinweb_translate', 1);
add_action('save_post', 'wp_novinweb_translate', 1);

}else{

function wp_novinweb_translate($postname){
	$post_name = $postname;
	$post_title = $_POST['post_title'];
	
	if( !empty($post_name) && !is_numeric($post_name) ) return str_replace('_','-',$post_name);

	$post_title = str_replace(array('_','/'),array(' ',' '),$post_title);
	$fatoensettings_bing= new fatoensettingsBingTranslator();
	$fatoensettings_title = sanitize_title( $fatoensettings_bing->translate($post_title) );
	
	return $fatoensettings_title;
}
add_filter('name_save_pre', 'wp_novinweb_translate', 1);

}

function wp_novinweb_translate_activate(){
	add_option('wp_novinweb_translate_clientid','wp-fa-to-en');
	add_option('wp_novinweb_translate_clientsecret','pK2JdEwF/Janzz2O36Lgkq0QcDkc4Fuw0HqJvWVIFLQ=');
	add_option('wp_novinweb_translate_language','fa');
	add_option('wp_novinweb_translate_hamahangi','');
	add_option('wp_novinweb_translate_deactivate','');
}
register_activation_hook( __FILE__, 'wp_novinweb_translate_activate' );

if(get_option("wp_novinweb_translate_deactivate")=='yes'){
	function wp_novinweb_translate_deactivate(){
		delete_option('wp_novinweb_translate_clientid');
		delete_option('wp_novinweb_translate_clientsecret');
		delete_option('wp_novinweb_translate_language');
		delete_option('wp_novinweb_translate_hamahangi');
		delete_option('wp_novinweb_translate_deactivate');
	}
	register_deactivation_hook( __FILE__, 'wp_novinweb_translate_deactivate' );
}

if(is_admin()){require_once('fa-to-en-translate-admin.php');}

?>