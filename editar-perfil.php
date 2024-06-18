<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Creamos la cookie
function wp_pcc_login_cookies() {
  if(!isset($_COOKIE['wp_pcc'])) {
    if(isset($_GET['wp-pcc-hash']) && $_GET['wp-pcc-hash'] != '' && isset($_GET['wp-pcc-id']) && is_numeric($_GET['wp-pcc-id'])) {
      $asociada = new contactClientify($_GET['wp-pcc-id']); //Chequeamos los datos
      if(wp_pcc_user_hash($asociada) == $_GET['wp-pcc-hash']) {
        setcookie("wp_pcc", $_GET['wp-pcc-hash']."|".$_GET['wp-pcc-id'], time() + (24*60*60), "/", $_SERVER['SERVER_NAME']);
        $_COOKIE['wp_pcc'] = $_GET['wp-pcc-hash']."|".$_GET['wp-pcc-id'];
        wp_redirect(get_the_permalink(WP_AED_ASOCIADA_EDIT_PROFILE_ID), 302);
        die;
      }
    }
  } else if(isset($_COOKIE['wp_pcc']) && isset($_GET['logoutAsociadas']) && $_GET['logoutAsociadas'] != '') {
    unset($_COOKIE['wp_pcc']);
    setcookie("wp_pcc", "", time() - (24*60*60), "/", $_SERVER['SERVER_NAME']);
    wp_redirect(get_the_permalink(WP_AED_ASOCIADA_EDIT_PROFILE_ID)."?hash=".date("YmdHis"), 302);
    die;
  }
}
add_action('plugins_loaded', 'wp_pcc_login_cookies', 0 );

/* wp_pcc_login */
function wp_pcc_edit_profile($params = array(), $content = null) {
  ob_start();
  if(isset($_COOKIE['wp_pcc'])) {
    list($hash, $asociada_id) = explode("|", $_COOKIE['wp_pcc']);
    $asociada = new contactClientify($asociada_id);
    if(isset($_POST['updateAsociada']) && $_POST['updateAsociada'] != '') { //Actualziamos el perfil
      if(isset($_POST['clientify_firstname']) && $_POST['clientify_firstname'] != '') $asociada->setFirstName($_POST['clientify_firstname']);
      if(isset($_POST['clientify_lastname']) && $_POST['clientify_lastname'] != '') $asociada->setLastName($_POST['clientify_lastname']);
      if(isset($_POST['clientify_company']) && $_POST['clientify_company'] != '') $asociada->setCustomField('Asociadas_Empresa', $_POST['clientify_company']);
      if(isset($_POST['clientify_cv']) && $_POST['clientify_cv'] != '') $asociada->setCustomField('Asociadas_CV', $_POST['clientify_cv']);
      $asociada->update(); ?>
      <script>
        jQuery.get( "/wp-admin/admin-ajax.php?action=asociadas", function( data ) {
          console.log("Load was performed." );
        });
      </script>

    <?php }
    if(wp_pcc_user_hash($asociada) == $hash) { 
      $company = $asociada->getCustomField('Asociadas_Empresa');
      $cv = $asociada->getCustomField('Asociadas_CV');
      ?><h1><?php _e("Editor de mi perfil", 'wp-perfil-contacto'); ?></h1>
      <a href="<?=get_the_permalink(WP_AED_ASOCIADA_EDIT_PROFILE_ID);?>?wp-pcc-date=<?=date("YmdHis");?>&logoutAsociadas=Desconectar"><?php _e("Desconectar", 'wp-perfil-contacto'); ?></a>
      <form method="post">
        <label><?php _e("Nombre", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_firstname" autocomplete="off" value="<?=$asociada->getFirstName()?>" required/></label><br/>
        <label><?php _e("Apellidos", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_lastname" autocomplete="off" value="<?=$asociada->getLastName()?>" required/></label><br/>
        <label><?php _e("Empresa", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_company" autocomplete="off" value="<?=(isset($company['value']) ? $company['value'] : '')?>" required/></label><br/>
        <label><?php _e("Curriculum Vitae", 'wp-perfil-contacto'); ?>
        <?php wp_editor((isset($cv['value']) ? $cv['value'] : ''), "clientify_cv", array( 'media_buttons' => false ) ); ?></label><br/>
        <input type="submit" name="updateAsociada" value="<?php _e("Guardar", 'wp-perfil-contacto'); ?>">
      </form>
      <?php return ob_get_clean();
    }
  }
}
add_shortcode('editor-asociadas', 'wp_pcc_edit_profile');