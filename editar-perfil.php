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
      if(isset($_POST['clientify_position']) && $_POST['clientify_position'] != '') $asociada->setPosition($_POST['clientify_position']);
      if(isset($_POST['clientify_cv']) && $_POST['clientify_cv'] != '') $asociada->setCustomField('Asociadas_CV', $_POST['clientify_cv']);
      if(isset($_POST['clientify_sector'])) $asociada->setCustomField('Asociadas_Sector', $_POST['clientify_sector']);
      if(isset($_POST['clientify_public_email'])) $asociada->setCustomField('Asociadas_Emailpublico', $_POST['clientify_public_email']);
      if(isset($_POST['clientify_public_phone'])) $asociada->setCustomField('Asociadas_Telefonopublico', $_POST['clientify_public_phone']);
      if(isset($_POST['clientify_website'])) $asociada->setCustomField('Asociadas_Paginaweb', $_POST['clientify_website']);
      //TODO: Guardar websites


      //TODO: Guardas teléfonos
      /*if(isset($_POST['clientify_email'])) {
        foreach ($_POST['clientify_email'] as $key => $email) {
          if($email['id'] == 0 && !$asociada->hasEmail($email['email'])) { //Si es nuevo
            $asociada->addEmail($email['email'], $email['type']);
          } else if($email['id'] == -1 && $asociada->hasEmail($email['email'])) { //Si quremos borrarlo
            $asociada->deleteEmail($email['email']);
          }
        }
      }*/


      if(isset($_FILES['clientify_picture']['error']) && $_FILES['clientify_picture']['error'] == 0) {
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
      }
      if(isset($_POST['clientify_linkedin_url'])/* && $_POST['clientify_linkedin_url'] != ''*/) $asociada->setLinkedinUrl($_POST['clientify_linkedin_url']);
      $asociada->update();
      //if(file_exists($fichero_subido)) unlink($fichero_subido); ?>
      <script>
        jQuery.get( "/wp-admin/admin-ajax.php?action=asociadas", function( data ) {
          console.log("Load was performed." );
        });
      </script>
    <?php }
    if(wp_pcc_user_hash($asociada) == $hash) { 
      $mylinkedin = $asociada->getLinkedinUrl();
      $mycompany = $asociada->getCustomField('Asociadas_Empresa');
      $myposition = $asociada->getPosition();
      $mysector = $asociada->getCustomField('Asociadas_Sector');
      $mycv = $asociada->getCustomField('Asociadas_CV');
      //$mywebsites = $asociada->getWebsitesByType(1); //1 = Corporativa
      //$myemails = $asociada->getEmailsByType(1); //1 = Corporativa
      $mypublicemail = $asociada->getCustomField('Asociadas_Emailpublico'); 
      $mypublicphone = $asociada->getCustomField('Asociadas_Telefonopublico'); 
      $mywebsite = $asociada->getCustomField('Asociadas_Paginaweb'); 

      ?><h1><?php _e("Editor de mi perfil", 'wp-perfil-contacto'); ?></h1>
      <a href="<?=get_the_permalink(WP_AED_ASOCIADA_EDIT_PROFILE_ID);?>?wp-pcc-date=<?=date("YmdHis");?>&logoutAsociadas=Desconectar"><?php _e("Desconectar", 'wp-perfil-contacto'); ?></a> | 
      <a href="<?=wp_pcc_asociada_permalink($asociada);?>?wp-pcc-date=<?=date("YmdHis");?>"><?php _e("Ver mi perfil", 'wp-perfil-contacto'); ?></a>
      <form method="post" enctype="multipart/form-data">
        <label><?php _e("Nombre", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_firstname" autocomplete="off" value="<?=$asociada->getFirstName()?>" required/></label><br/>
        <label><?php _e("Apellidos", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_lastname" autocomplete="off" value="<?=$asociada->getLastName()?>" required/></label><br/>
        <label><?php _e("Empresa", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_company" autocomplete="off" value="<?=(isset($mycompany['value']) ? $mycompany['value'] : '')?>" required/></label><br/>
        <label><?php _e("Cargo", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_position" autocomplete="off" value="<?=(isset($myposition) ? $myposition : '')?>" required/></label><br/>
        <label><?php _e("Sector", 'wp-perfil-contacto'); ?>
          <select name="clientify_sector">
            <option value=""><?php _e('Selecciona sector', 'wp-perfil-contacto'); ?></option>
            <?php foreach ($wp_cc_sectores as $sector) { 
              echo "<option value='".$sector."'".( $sector == $mysector['value'] ? " selected='selected'": "").">".$sector."</option>\n";
            } ?>
          <select>
        </label><br/>
        <label><?php _e("Sobre mí", 'wp-perfil-contacto'); ?>
        <?php wp_editor((isset($mycv['value']) ? $mycv['value'] : ''), "clientify_cv", array( 'media_buttons' => false, 'quicktags' => false ) ); ?></label><br/>
        
        
        <?php /* <label><?php _e("Páginas web", 'wp-perfil-contacto'); ?> 
          <?php foreach ($mywebsites as $key => $website) { ?>
            <input type="text" name="clientify_website[<?=$key; ?>]" autocomplete="off" value="<?=$website->website; ?>" /></label><br/>
          <?php } ?>
          <input type="text" name="clientify_website[<?=($key+1); ?>]" autocomplete="off" value="" />
        </label><br/> */ ?>
        
        
        <?php /* <label><?php _e("Emails", 'wp-perfil-contacto'); ?> 
          <?php foreach ($myemails as $key => $email) { ?>
          <div class="myemail" id="email<?=$email->id; ?>"> 
            <input type="hidden" name="clientify_email[<?=$key; ?>][id]" value="<?=$email->id; ?>" />
            <input type="hidden" name="clientify_email[<?=$key; ?>][type]" value="<?=$email->type; ?>" />
            <input type="text" name="clientify_email[<?=$key; ?>][email]" autocomplete="off" value="<?=$email->email; ?>" />
            <button><?php _e("Borrar", ''); ?></button>
          </div>
          <?php } $key ++; ?>
          <input type="hidden" name="clientify_email[<?=$key; ?>][id]" value="0" />
          <input type="hidden" name="clientify_email[<?=$key; ?>][type]" value="1" />
          <input type="text" name="clientify_email[<?=$key; ?>][email]" autocomplete="off" value="" />
        </label><br/> */ ?>



        <label><?php _e("Email público", 'wp-perfil-contacto'); ?> <input type="email" name="clientify_public_email" autocomplete="off" value="<?=(isset($mypublicemail['value']) ? $mypublicemail['value'] : '')?>" /></label><br/>
        <label><?php _e("Teléfono público", 'wp-perfil-contacto'); ?> <input type="text" name="clientify_public_phone" autocomplete="off" value="<?=(isset($mypublicphone['value']) ? $mypublicphone['value'] : '')?>" /></label><br/>
        <label><?php _e("Página web", 'wp-perfil-contacto'); ?> <input type="url" name="clientify_website" autocomplete="off" value="<?=(isset($mywebsite['value']) ? $mywebsite['value'] : '')?>" /></label><br/>
        <label><?php _e("Linkedin", 'wp-perfil-contacto'); ?> <input type="url" name="clientify_linkedin_url" autocomplete="off" value="<?=(isset($mylinkedin) ? $mylinkedin : '')?>" /></label><br/>
        <label><?php _e("Imagen (máximo 2mg)", 'wp-perfil-contacto'); ?> <input name="clientify_picture" max-size="2000" type="file" accept="image/png, image/gif, image/jpeg" /></label>
        <input type="submit" name="updateAsociada" value="<?php _e("Guardar", 'wp-perfil-contacto'); ?>">
      </form>
      <script>

      <script>
      <?php return ob_get_clean();
    }
  }
}
add_shortcode('editor-asociadas', 'wp_pcc_edit_profile');