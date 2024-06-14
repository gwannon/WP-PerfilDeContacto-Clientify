<?php

/**
 * Plugin Name: WP Perfil de contacto Clientify
 * Plugin URI:  https://github.com/gwannon/WP-PerfilDeContacto-Clientify
 * Description: Plugins de Wordpress que genera un panel donde los contactos de Clientify pueda modificar sus datos
 * Version:     1.0
 * Author:      Gwannon
 * Author URI:  https://github.com/gwannon/
 * License:     GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-perfil-contacto
 *
 * PHP 7.3
 * WordPress 6.1.1
 */

//Cargamos las librerías de Composer
require __DIR__ . '/vendor/autoload.php';


//Cargamos la configuración básica
define("CLIENTIFY_API_URL", "https://api.clientify.net/v1");
define("CLIENTIFY_LOG_API_CALLS", false);
define('CLIENTIFY_API_KEY', get_option("_wp_pcc_clientify_api_key"));
define('WP_AED_ASOCIADAS_CACHE_FILE', plugin_dir_path(__FILE__).'cache/asociadas.json');
define('WP_AED_HASH', "EVAMARIASEFUE");
define('WP_AED_ASOCIADAS_TAGS', "asociadas");


define('WP_AED_ASOCIADA_PAGE_ID', 696);

//Cargamos resto de script
require __DIR__ . '/admin.php';
require __DIR__ . '/login.php';
require __DIR__ . '/shortcodes.php';

//Cargamos el multi-idioma
function wp_pcc_plugins_loaded() {
  load_plugin_textdomain('wp-perfil-contacto', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}
add_action('plugins_loaded', 'wp_pcc_plugins_loaded', 0 );

/* ----------- Rewrite Rules ------- */
add_action( 'init', 'wp_pcc_rewrite_rules' );
function wp_pcc_rewrite_rules(){
  add_rewrite_rule('^asociada/([^/]*)/?','index.php?page_id='.WP_AED_ASOCIADA_PAGE_ID.'&asociada=$matches[1]','top');
  add_rewrite_tag('%asociada%','([^&]+)');
}

/* ----------- Filters ------------- */
function wp_pcc_asociada_title( $title, $id = null ) {
  if ( is_page(WP_AED_ASOCIADA_PAGE_ID) && in_the_loop()) {
    $asociada_id = end(explode("-", get_query_var('asociada')));
    $json = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE), true);
    if(isset($json[$asociada_id])) {
      $asociada = json_decode(json_encode($json[$asociada_id]));
      return 'Asociada: '.$asociada->first_name." ".$asociada->last_name;
    }
  }
  return $title;
}
add_filter( 'the_title', 'wp_pcc_asociada_title', 10, 2 );

//Damos error 404 si la asociada no existe
add_filter( 'template_include', 'wp_pcc_asociada_404', 99 );
function wp_pcc_asociada_404( $template ) {
  if (is_page(WP_AED_ASOCIADA_PAGE_ID)  ) {
    //Si no existe la oferta error 404
    $asociada_id = end(explode("-", get_query_var('asociada')));
    
    $json = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE), true);
    if(!isset($json[$asociada_id])) {
      status_header( 404 );
      nocache_headers();
      include( get_query_template( '404' ) );
      die();
    }

    //Si la oferta existe pero la URL es diferente 
    $asociada = json_decode(json_encode($json[$asociada_id]));
    $currentlink = (is_ssl() ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $link = wp_pcc_asociada_permalink($asociada);
    if($link != $currentlink) {
      wp_redirect($link, 301);
      exit;
    }

  }
  return $template;
}

function wp_pcc_asociada_permalink($asociada) {
  return get_permalink(WP_AED_ASOCIADA_PAGE_ID).sanitize_title($asociada->first_name."-".$asociada->last_name."-".$asociada->id)."/";
}

/* ----------- Cron job ------------ */
// /wp-admin/admin-ajax.php?action=asociadas
add_action( 'wp_ajax_asociadas', 'wp_pcc_asociada_cache' );
add_action( 'wp_ajax_nopriv_asociadas', ' wp_pcc_asociada_cache' );
function wp_pcc_asociada_cache() {
  if(!file_exists(WP_AED_ASOCIADAS_CACHE_FILE) || (time() - filemtime(WP_AED_ASOCIADAS_CACHE_FILE)) > /*(60*4)*/ 5) {
    $asociadas = [];
    $max = 100;
    $link = CLIENTIFY_API_URL."/contacts/?tag=".WP_AED_ASOCIADAS_TAGS."&page_size=".$max;
    while(1 == 1) {
      $curl = curl_init();
      echo $link."<br>";
      $headers[] = 'Authorization: Token '.CLIENTIFY_API_KEY;
      curl_setopt($curl, CURLOPT_URL, $link);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
      curl_setopt($curl, CURLOPT_ENCODING, '');
      curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
      curl_setopt($curl, CURLOPT_TIMEOUT, 0);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($curl);
      $json = json_decode($response);
      echo "<hr>";
      echo "<pre>";
      print_r($response);
      echo "</pre>";
      foreach($json->results as $asociada) {
        $asociadas[$asociada->id] = $asociada;
      }
      if(isset($json->next) && $json->next != '') {
        $link = $json->next;
        sleep(25);
      } else break;
      //break;
    }
    file_put_contents(WP_AED_ASOCIADAS_CACHE_FILE, json_encode($asociadas));
    echo json_encode($asociadas);
  } else {
    $json = file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE);
    echo $json;
  }
}
