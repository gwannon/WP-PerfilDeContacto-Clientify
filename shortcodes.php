<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Shortcodes
function wp_pcc_listado_asociadas_shortcode($params = array(), $content = null) {
  ob_start(); 
  $asociadas = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE)); ?>
  <ol>
  <?php
  foreach ($asociadas as $asociada) {
    echo "<li><b><a href='".wp_pcc_asociada_permalink($asociada)."/'>".$asociada->first_name." ".$asociada->last_name."</a></b>".
      (isset($asociada->company_name) && $asociada->company_name != '' ? "<br>".$asociada->company_name : "").
      "</li>";
  } ?>
  </ol>
  <?php return ob_get_clean();
}
add_shortcode('listado-asociadas', 'wp_pcc_listado_asociadas_shortcode');


function wp_pcc_asociada_shortcode($params = array(), $content = null) {
    ob_start();
    $asociada_id= end(explode("-", get_query_var('asociada'))); 
    if($asociada_id != '') { ?>
        
        <?php $json = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE), true);
        $asociada = json_decode(json_encode($json[$asociada_id])); ?>
        <h1><?php the_title(); ?></h1>
        <h2><?php _e("Datos de la asociada", 'wp-perfil-contacto'); ?></h2><?php
        echo (isset($asociada->company_name) && $asociada->company_name != '' ? "<b>Empresa:</b> ".$asociada->company_name."<br/>" : "");

        $asociada = new contactClientify($asociada_id);
        $emails = $asociada->getEmails();
        if(count($emails)) {
            ?><h3><?php _e("Emails", 'wp-perfil-contacto'); ?></h3><ul><?php
            foreach($emails as $email) {
                echo "<li>".$email->email."</li>";
            }
            ?></ul><?php 
        }
        $phones = $asociada->getPhones();
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