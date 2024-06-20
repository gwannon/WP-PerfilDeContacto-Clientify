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
        wp_redirect(get_page_link(WP_AED_ASOCIADA_EDIT_PROFILE_ID));
        die;
      }
    }
  } else if(isset($_COOKIE['wp_pcc']) && isset($_GET['logoutAsociadas']) && $_GET['logoutAsociadas'] != '') {
    unset($_COOKIE['wp_pcc']);
    setcookie("wp_pcc", "", time() - (24*60*60), "/", $_SERVER['SERVER_NAME']);
    wp_redirect(get_page_link(WP_AED_ASOCIADA_EDIT_PROFILE_ID)."?hash=".date("YmdHis"));
    die;
  }
}
add_action('wp_loaded', 'wp_pcc_login_cookies', 0 );

/* wp_pcc_login */
function wp_pcc_edit_profile($params = array(), $content = null) {
  global $wp_cc_sectores;
  ob_start();
  if(isset($_COOKIE['wp_pcc'])) {
    list($hash, $asociada_id) = explode("|", $_COOKIE['wp_pcc']);
    $asociada = new contactClientify($asociada_id);
    if(isset($_POST['updateAsociada']) && $_POST['updateAsociada'] != '') { //Actualziamos el perfil
      if(isset($_POST['clientify_firstname']) && $_POST['clientify_firstname'] != '') $asociada->setFirstName($_POST['clientify_firstname']);
      if(isset($_POST['clientify_lastname']) && $_POST['clientify_lastname'] != '') $asociada->setLastName($_POST['clientify_lastname']);
      if(isset($_POST['clientify_company']) && $_POST['clientify_company'] != '') $asociada->setCustomField('Asociadas_Empresa', $_POST['clientify_company']);
      if(isset($_POST['clientify_cv']) && $_POST['clientify_cv'] != '') $asociada->setCustomField('Asociadas_CV', $_POST['clientify_cv']);
      if(isset($_POST['clientify_sector'])) $asociada->setCustomField('Asociadas_Sector', $_POST['clientify_sector']);
      $dir_subida = plugin_dir_path(__FILE__).'cache/pictures/';
      $extension = [
        "image/jpeg" => ".jpg",
        "image/png" => ".png",
        "image/gif" => ".gif",
      ];

      $file_name = sanitize_title("asociada-".$asociada->id).$extension[$_FILES['clientify_picture']['type']];
      $fichero_subido = $dir_subida . $file_name ;
      if (move_uploaded_file($_FILES['clientify_picture']['tmp_name'], $fichero_subido)) {
        $picture_url = plugin_dir_url(__FILE__).'cache/pictures/'.$file_name;
        $asociada->setPicture($picture_url);
      }
      $asociada->update();
      //if(file_exists($fichero_subido)) unlink($fichero_subido); ?>
      <script>
        jQuery.get( "/wp-admin/admin-ajax.php?action=asociadas", function( data ) {
          console.log("Load was performed." );
        });
      </script>
    <?php }
    if(wp_pcc_user_hash($asociada) == $hash) { 
      $mycompany = $asociada->getCustomField('Asociadas_Empresa');
      $mysector = $asociada->getCustomField('Asociadas_Sector');
      $mycv = $asociada->getCustomField('Asociadas_CV');
      ?><h1><?php _e("Editor de mi perfil", 'wp-perfil-contacto'); ?></h1>
      <a href="<?=get_the_permalink(WP_AED_ASOCIADA_EDIT_PROFILE_ID);?>?wp-pcc-date=<?=date("YmdHis");?>&logoutAsociadas=Desconectar"><?php _e("Desconectar", 'wp-perfil-contacto'); ?></a> | 
      <a href="<?=wp_pcc_asociada_permalink($asociada);?>?wp-pcc-date=<?=date("YmdHis");?>"><?php _e("Ver mi perfil", 'wp-perfil-contacto'); ?></a>
      <form method="post" enctype="multipart/form-data">
        <label><?php _e("Nombre", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_firstname" autocomplete="off" value="<?=$asociada->getFirstName()?>" required/></label><br/>
        <label><?php _e("Apellidos", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_lastname" autocomplete="off" value="<?=$asociada->getLastName()?>" required/></label><br/>
        <label><?php _e("Empresa", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_company" autocomplete="off" value="<?=(isset($mycompany['value']) ? $mycompany['value'] : '')?>" required/></label><br/>
        <label><?php _e("Sector", 'wp-perfil-contacto'); ?>
          <select name="clientify_sector">
            <option value=""><?php _e('Selecciona sector', 'wp-perfil-contacto'); ?></option>
            <?php foreach ($wp_cc_sectores as $sector) { 
              echo "<option value='".$sector."'".( $sector == $mysector['value'] ? " selected='selected'": "").">".$sector."</option>\n";
            } ?>
          <select>
        </label><br/>
        <label><?php _e("Curriculum Vitae", 'wp-perfil-contacto'); ?>
        <?php wp_editor((isset($mycv['value']) ? $mycv['value'] : ''), "clientify_cv", array( 'media_buttons' => false, 'quicktags' => false ) ); ?></label><br/>
        <label><?php _e("Imagen (máximo 2mg)", 'wp-perfil-contacto'); ?> <input name="clientify_picture" max-size="2000" type="file" accept="image/png, image/gif, image/jpeg" /></label>
        <input type="submit" name="updateAsociada" value="<?php _e("Guardar", 'wp-perfil-contacto'); ?>">
      </form>
      <?php return ob_get_clean();
    }
  }
}
add_shortcode('editor-asociadas', 'wp_pcc_edit_profile');