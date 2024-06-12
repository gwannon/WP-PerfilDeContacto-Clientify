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
use Gwannon\PHPClientifyAPI\contactClientify;

//Cargamos la configuración básica
define("CLIENTIFY_API_URL", "https://api.clientify.net/v1");
define("CLIENTIFY_LOG_API_CALLS", false);
define('CLIENTIFY_API_KEY', get_option("_wp_pcc_clientify_api_key"));

//Cargamos resto de script
require __DIR__ . '/admin.php';

//Cargamos el multi-idioma
function wp_pcc_plugins_loaded() {
  load_plugin_textdomain('wp-perfil-contacto', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}
add_action('plugins_loaded', 'wp_pcc_plugins_loaded', 0 );