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

//flush_rewrite_rules(true);

//Cargamos las librerías de Composer
require __DIR__ . '/vendor/autoload.php';

//Cargamos la configuración básica
define("CLIENTIFY_API_URL", "https://api.clientify.net/v1");
define("CLIENTIFY_LOG_API_CALLS", false);
define('CLIENTIFY_API_KEY', get_option("_wp_pcc_clientify_api_key"));
define('WP_AED_ASOCIADAS_CACHE_FILE', plugin_dir_path(__FILE__).'cache/asociadas.json');
define('WP_AED_ASOCIADAS_SITEMAP_FILE', plugin_dir_path(__FILE__).'cache/sitemap_asociadas.xml');
define('WP_AED_ASOCIADAS_SITEMAP_URL', plugin_dir_url(__FILE__).'cache/sitemap_asociadas.xml');
define('WP_AED_HASH', get_option("_wp_pcc_clientify_hash"));
define('WP_AED_ASOCIADAS_TAGS', get_option("_wp_pcc_clientify_tag"));
define('WP_AED_NO_PHOTO', get_option("_wp_pcc_no_photo"));


define('WP_AED_ASOCIADA_PAGE_ID', get_option("_wp_pcc_asociada_page_id") /*696*/);
define('WP_AED_ASOCIADA_EDIT_PROFILE_ID', get_option("_wp_pcc_asociada_edit_profile_id") /*688*/);

$wp_cc_sectores = ["ADMINISTRACIÓN", "ASESORÍA DE EMPRESAS", "ASESORÍA JURÍDICA", "AUTOMOCIÓN", "COACHING", "COMERCIO, MODA, DISEÑO", "COMUNICACIÓN, MARKETING Y PUBLICIDAD", "CONSTRUCCIÓN", "CONSULTORÍA", "DEPORTE OCIO Y SALUD", "DISTRIBUCIÓN", "EDUCACIÓN", "FINANZAS", "FORMACIÓN", "HOSTELERÍA", "INDUSTRIA Y ENERGÍA", "INFORMÁTICA E INTERNET", "INMOBILIARIA", "MARÍTIMO PORTUARIO-COMBUSTIBLES SÓLIDOS-CONSTRUCCIÓN", "OUTPLACEMENT - RRHH", "SALUD Y ESTÉTICA", "SERVICIOS EMPRESARIALES", "SIDEROMETALURGIA", "TRANSPORTE", "SERVICIOS FAMILIARES", "TURISMO", "OTROS"];

function wp_pcc_asociada_po_edit() {
  return [__("ADMINISTRACIÓN", 'wp-perfil-contacto'), __("ASESORÍA DE EMPRESAS",'wp-perfil-contacto'), __("ASESORÍA JURÍDICA",'wp-perfil-contacto'),__("AUTOMOCIÓN",'wp-perfil-contacto'),__("COACHING", 'wp-perfil-contacto'), __("COMERCIO",'wp-perfil-contacto'),__("DEPORTE OCIO Y SALUD", 'wp-perfil-contacto'), __("EDUCACIÓN",'wp-perfil-contacto'),__("DISTRIBUCIÓN", 'wp-perfil-contacto'), __("FINANZAS",'wp-perfil-contacto'),__("CONSULTORÍA", 'wp-perfil-contacto'), __("CONSTRUCCIÓN",'wp-perfil-contacto'),__("MARKETING Y PUBLICIDAD", 'wp-perfil-contacto'), __("COMUNICACIÓN",'wp-perfil-contacto'),__("DISEÑO", 'wp-perfil-contacto'), __("MODA",'wp-perfil-contacto'),__("FORMACIÓN", 'wp-perfil-contacto'), __("HOSTELERÍA",'wp-perfil-contacto'),__("INDUSTRIA Y ENERGÍA", 'wp-perfil-contacto'), __("INFORMÁTICA E INTERNET",'wp-perfil-contacto'),__("OTROS", 'wp-perfil-contacto'), __("TURISMO",'wp-perfil-contacto'),__("SERVICIOS FAMILIARES", 'wp-perfil-contacto'), __("TRANSPORTE",'wp-perfil-contacto'),__("SIDEROMETALURGIA", 'wp-perfil-contacto'), __("SALUD Y ESTÉTICA",'wp-perfil-contacto'),__("SERVICIOS EMPRESARIALES", 'wp-perfil-contacto'), __("OUTPLACEMENT - RRHH",'wp-perfil-contacto'),__("MARÍTIMO PORTUARIO-COMBUSTIBLES SÓLIDOS-CONSTRUCCIÓN", 'wp-perfil-contacto'), __("INMOBILIARIA",'wp-perfil-contacto')];
}

//Cargamos resto de script
require __DIR__ . '/admin.php';
require __DIR__ . '/login.php';
require __DIR__ . '/editar-perfil.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/perfil.php';


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
    $temp = explode("-", get_query_var('asociada'));
    $asociada_id= end($temp); 
    $json = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE), true);
    if(isset($json[$asociada_id])) {
      $asociada = json_decode(json_encode($json[$asociada_id]));
      return 'Asociada: '.$asociada->first_name." ".$asociada->last_name;
    }
  }
  return $title;
}
//add_filter( 'the_title', 'wp_pcc_asociada_title', 10, 2 );

add_filter('wpseo_title', 'wp_pcc_asociada_filter_wpseo_title');
function  wp_pcc_asociada_filter_wpseo_title($title) {
  if(is_page(WP_AED_ASOCIADA_PAGE_ID) ) {
    $temp = explode("-", get_query_var('asociada'));
    $asociada_id= end($temp); 
    $json = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE), true);
    if(isset($json[$asociada_id])) {
      $asociada = json_decode(json_encode($json[$asociada_id]));
      return str_replace('[ASOCIADA]', sprintf(__('Asociada %s %s', 'wp-perfil-contacto'), $asociada->first_name, $asociada->last_name), $title);
    }
  }
  return $title;
}



//Damos error 404 si la asociada no existe
add_filter( 'template_include', 'wp_pcc_asociada_404', 99 );
function wp_pcc_asociada_404( $template ) {
  if (is_page(WP_AED_ASOCIADA_PAGE_ID)  ) {
    //Si no existe la oferta error 404
    $temp = explode("-", get_query_var('asociada'));
    $asociada_id= end($temp); 
    
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
  if(isset($asociada->first_name)) return get_permalink(WP_AED_ASOCIADA_PAGE_ID).sanitize_title($asociada->first_name."-".$asociada->last_name."-".$asociada->id)."/";
  else return get_permalink(WP_AED_ASOCIADA_PAGE_ID).sanitize_title($asociada->getFirstName()."-".$asociada->getLastName()."-".$asociada->id)."/";
}

/* ----------- Cron job ------------ */
// /wp-admin/admin-ajax.php?action=asociadas
add_action( 'wp_ajax_asociadas', 'wp_pcc_asociada_cache' );
add_action( 'wp_ajax_nopriv_asociadas', 'wp_pcc_asociada_cache' );
function wp_pcc_asociada_cache() {
  header('Content-Type: application/json; charset=utf-8');
  if(!file_exists(WP_AED_ASOCIADAS_CACHE_FILE) || (time() - filemtime(WP_AED_ASOCIADAS_CACHE_FILE)) > /*(60*4)*/ 5) {
    $asociadas = [];
    $max = 100;
    $link = CLIENTIFY_API_URL."/contacts/?tag=".WP_AED_ASOCIADAS_TAGS."&page_size=".$max;
    while(1 == 1) {
      $headers = [];
      $curl = curl_init();
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
      foreach($json->results as $asociada) {
        $asociadas[$asociada->id] = $asociada;
      }
      if(isset($json->next) && $json->next != '') {
        $link = $json->next;
        sleep(1);
      } else break;
    }
    file_put_contents(WP_AED_ASOCIADAS_CACHE_FILE, json_encode($asociadas));
    wp_pcc_asociada_generate_sitemap($asociadas); //Generamos el sitemap
    echo json_encode($asociadas);
  } else {
    $json = file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE);
    echo $json;
  }
}

//Creamos el sitemap de asociadas
function wp_pcc_asociada_generate_sitemap($asociadas) {
  $sitemap = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="'.get_home_url().'/wp-content/plugins/wordpress-seo/css/main-sitemap.xsl"?>
  <urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
  foreach ($asociadas as $asociada) {
    $sitemap .= "\t\t".'<url><loc>'.wp_pcc_asociada_permalink($asociada).'</loc></url>'."\n";
  }  
  $sitemap .='</urlset>';
  file_put_contents(WP_AED_ASOCIADAS_SITEMAP_FILE, $sitemap);
}

//Metemos su propio sitemap
add_filter( 'wpseo_sitemap_index', 'wp_pcc_asociada_add_sitemap_custom_items' );
function  wp_pcc_asociada_add_sitemap_custom_items( $sitemap_custom_items ) {
  $sitemap_custom_items .= '
  <sitemap>
  <loc>'.WP_AED_ASOCIADAS_SITEMAP_URL.'</loc>
  <lastmod>'.date ("c", filemtime(WP_AED_ASOCIADAS_SITEMAP_FILE)).'</lastmod>
  </sitemap>';
  return $sitemap_custom_items;
}

//Quitamos el NOINDEX NOFOLLOW al eprfil de las asociadas

add_filter('wpseo_robots', 'wp_pcc_asociada_yoast_remove_noindex', 999);
function wp_pcc_asociada_yoast_remove_noindex($string= "") {
  if (is_page(WP_AED_ASOCIADA_PAGE_ID) && get_query_var('asociada') != '') {
    $string= "index,follow";
  }
  return $string;
}




function wp_pcc_asociadas_mapa_shortcode($params = array(), $content = null) {
  ob_start(); ?>
  <h2><?php _e("Asociadas", 'wp-perfil-contacto'); ?></h2>
  <ul>
    <?php $json = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE)); foreach ($json as $asociada) { ?>
      <li><a href="<?php echo wp_pcc_asociada_permalink($asociada); ?>"><?php printf(__("%s %s", 'wp-perfil-contacto'), $asociada->first_name, $asociada->last_name); ?></a></li>
    <?php } ?>
  </ul>
  <?php return ob_get_clean();
}
add_shortcode('asociadas-mapa', 'wp_pcc_asociadas_mapa_shortcode');
