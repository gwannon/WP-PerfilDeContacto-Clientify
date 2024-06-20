<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Shortcodes
function wp_pcc_asociada_shortcode($params = array(), $content = null) {
  ob_start();
  $asociada_id= end(explode("-", get_query_var('asociada'))); 
  if($asociada_id != '') { $asociada = new contactClientify($asociada_id); ?>
      <h1><?php echo $asociada->getFirstName()." ".$asociada->getLastName(); ?></h1>
      <?php if($asociada->getPicture() != '') { ?>
          <img src="<?=$asociada->getPicture();?>" alt="<?php echo $asociada->getFirstName()." ".$asociada->getLastName(); ?>" width="200" />
      <?php } ?>
      <h2><?php _e("Datos de la asociada", 'wp-perfil-contacto'); ?></h2><?php
      $company = $asociada->getCustomField('Asociadas_Empresa');
      echo (isset($company['value']) && $company['value'] != '' ? "<h3>".__("Empresa", 'wp-perfil-contacto')."Empresa:</h3> ".$company['value']."<br/>" : "");

      $sector = $asociada->getCustomField('Asociadas_Sector');
      echo (isset($sector['value']) && $sector['value'] != '' ? "<h3>".__("Sector", 'wp-perfil-contacto').":</h3> ".apply_filters("the_content", $sector['value'])."<br/>" : "");

      $cv = $asociada->getCustomField('Asociadas_CV');
      echo (isset($cv['value']) && $cv['value'] != '' ? "<h3>".__("Mi CV", 'wp-perfil-contacto').":</h3> ".apply_filters("the_content", $cv['value'])."<br/>" : "");


      $emails = $asociada->getEmailsByType(1);
      if(count($emails)) {
          ?><h3><?php _e("Emails", 'wp-perfil-contacto'); ?></h3><ul><?php
          foreach($emails as $email) {
            echo "<li>".$email->email."</li>";
          }
          ?></ul><?php 
      }
      $phones = $asociada->getPhonesByType(3);
      if(count($phones)) {
          ?><h3><?php _e("Teléfono", 'wp-perfil-contacto'); ?></h3><ul><?php
          foreach($phones as $phone) {
              echo "<li>".$phone->phone."</li>";
          }
          ?></ul><?php 
      }

  }
  return ob_get_clean();
}
add_shortcode('aed-asociada', 'wp_pcc_asociada_shortcode');