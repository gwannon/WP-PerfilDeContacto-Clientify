<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Shortcodes
function wp_pcc_listado_asociadas_shortcode($params = array(), $content = null) {
  ob_start(); 
  $asociadas = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE)); ?>
  <ol>
  <?php
  foreach ($asociadas as $asociada) { 
    echo "<li>";
    if(isset($asociada->picture_url) && $asociada->picture_url != '') {
      echo "<img src='".$asociada->picture_url."' alt='".$asociada->first_name." ".$asociada->last_name."' width='100' /><br/>";
    }
    echo "<b><a href='".wp_pcc_asociada_permalink($asociada)."/'>".$asociada->first_name." ".$asociada->last_name."</a></b>";
    $key = array_search('Asociadas_Empresa', array_column(json_decode(json_encode($asociada->custom_fields), true), 'field'));
    echo (isset($asociada->custom_fields[$key]->field) && $asociada->custom_fields[$key]->field == 'Asociadas_Empresa' && isset($asociada->custom_fields[$key]->value) && $asociada->custom_fields[$key]->value != '' ? "<br>".$asociada->custom_fields[$key]->value : "");
    echo "</li>";
  } ?>
  </ol>
  <?php return ob_get_clean();
}
add_shortcode('listado-asociadas', 'wp_pcc_listado_asociadas_shortcode');


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
        echo (isset($company['value']) && $company['value'] != '' ? "<h3>Empresa:</h3> ".$company['value']."<br/>" : "");

        $cv = $asociada->getCustomField('Asociadas_CV');
        echo (isset($cv['value']) && $cv['value'] != '' ? "<h3>Mi CV:</h3> ".apply_filters("the_content", $cv['value'])."<br/>" : "");

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