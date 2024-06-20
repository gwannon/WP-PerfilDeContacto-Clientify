<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Shortcodes
function wp_pcc_listado_asociadas_shortcode($params = array(), $content = null) {
  global $wp_cc_sectores;
  ob_start(); 
  $asociadas = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE)); ?>
  <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
  <input type="text" placeholder="<?php _e('Buscar', 'wp-perfil-contacto'); ?>" class='quicksearch'/>
  <div class="filters-button-group">
    <select>
      <option value=""><?php _e('Selecciona sector', 'wp-perfil-contacto'); ?></option>
      <?php foreach ($wp_cc_sectores as $sector) { echo "<option value='sector-".sanitize_title($sector)."'>".$sector."</option>\n"; } ?>
    <select>
  </div>
  <span id="numberresults"><?php printf(__("Hemos encontrado <b>%d</b> asociadas.", 'wp-perfil-contacto'), count((array)$asociadas)); ?></span>
  <div class="asociadas-grid">
    <?php $counter = 1; foreach ($asociadas as $asociada) { 
      $key = array_search('Asociadas_Empresa', array_column(json_decode(json_encode($asociada->custom_fields), true), 'field'));
      $empresa = (isset($asociada->custom_fields[$key]->field) && $asociada->custom_fields[$key]->field == 'Asociadas_Empresa' && isset($asociada->custom_fields[$key]->value) && $asociada->custom_fields[$key]->value != '' ? $asociada->custom_fields[$key]->value : "");
      $key = array_search('Asociadas_Sector', array_column(json_decode(json_encode($asociada->custom_fields), true), 'field'));
      $sector = (isset($asociada->custom_fields[$key]->field) && $asociada->custom_fields[$key]->field == 'Asociadas_Sector' && isset($asociada->custom_fields[$key]->value) && $asociada->custom_fields[$key]->value != '' ? $asociada->custom_fields[$key]->value : "");
      
      $data_search = str_replace("-", " ", sanitize_title($asociada->first_name." ".$asociada->last_name." ".$empresa." ".$sector));

      if(isset($asociada->picture_url) && $asociada->picture_url != '') $photo_url = $asociada->picture_url;
      //else $photo_url = WP_AED_NO_PHOTO;
      else $photo_url = "https://dummyimage.com/600x400/000/fff&text=".sanitize_title($asociada->first_name." ".$asociada->last_name);
      echo "<div class='asociadas-item".($counter <= 8 ? " showimage " : " ")."sector-".sanitize_title($sector)."' data-search='".$data_search."' style=\"--bgimage: url('".$photo_url."');\">";
      

      echo "<p><b><a href='".wp_pcc_asociada_permalink($asociada)."/'>".$asociada->first_name." ".$asociada->last_name."</a></b></p>";
      echo "<p>".$empresa."</p>";
      echo "<p>".$sector."</p>";
      echo "</div>";
      $counter++;
    } ?>
  </div>
  <style>
    <?php echo file_get_contents(plugin_dir_path(__FILE__).'css/style.css'); ?>
  </style>
  <script>
    <?php echo file_get_contents(plugin_dir_path(__FILE__).'js/isotope.js'); ?>
  </script>
  <?php return ob_get_clean();
}
add_shortcode('listado-asociadas', 'wp_pcc_listado_asociadas_shortcode');