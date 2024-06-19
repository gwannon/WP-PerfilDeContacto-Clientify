<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Shortcodes
function wp_pcc_listado_asociadas_shortcode($params = array(), $content = null) {
  ob_start(); 
  $asociadas = json_decode(file_get_contents(WP_AED_ASOCIADAS_CACHE_FILE)); ?>
  <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
  <input type="text" placeholder="<?php _e('Buscar', 'wp-perfil-contacto'); ?>" class='quicksearch'/>

  <?php $sectores = ["ADMINISTRACIÓN", "ASESORÍA DE EMPRESAS", "ASESORÍA JURÍDICA", "AUTOMOCIÓN", "COACHING", "COMERCIO, MODA, DISEÑO", "COMUNICACIÓN, MARKETING Y PUBLICIDAD", "CONSTRUCCIÓN", "CONSULTORÍA", "DEPORTE OCIO Y SALUD", "DISTRIBUCIÓN", "EDUCACIÓN", "FINANZAS", "FORMACIÓN", "HOSTELERÍA", "INDUSTRIA Y ENERGÍA", "INFORMÁTICA E INTERNET", "INMOBILIARIA", "MARÍTIMO PORTUARIO-COMBUSTIBLES SÓLIDOS-CONSTRUCCIÓN", "OUTPLACEMENT - RRHH", "SALUD Y ESTÉTICA", "SERVICIOS EMPRESARIALES", "SIDEROMETALURGIA", "TRANSPORTE", "SERVICIOS FAMILIARES", "TURISMO", "OTROS"]; ?>
  <div class="filters-button-group">
    <select>
      <option value="">Selecciona sector</option>
      <?php foreach ($sectores as $sector) { echo "<option value='sector-".sanitize_title($sector)."'>".$sector."</option>\n"; } ?>
    <select>
  </div>
  <span id="numberresults"><?php printf(__("Hemos encontrado <b>%d</b> asociadas.", 'wp-perfil-contacto'), count((array)$asociadas)); ?></span>
  <div class="asociadas-grid">
    <?php $counter = 0; foreach ($asociadas as $asociada) { 
      $key = array_search('Asociadas_Empresa', array_column(json_decode(json_encode($asociada->custom_fields), true), 'field'));
      $empresa = (isset($asociada->custom_fields[$key]->field) && $asociada->custom_fields[$key]->field == 'Asociadas_Empresa' && isset($asociada->custom_fields[$key]->value) && $asociada->custom_fields[$key]->value != '' ? $asociada->custom_fields[$key]->value : "");
      $sector = array_rand($sectores);
      $sector = $sectores[$sector];

      $data_search = str_replace("-", " ", sanitize_title($asociada->first_name." ".$asociada->last_name." ".$empresa." ".$sector));

      
      echo "<div class='asociadas-item sector-".sanitize_title($sector)."' data-search='".$data_search."'>";
      if(isset($asociada->picture_url) && $asociada->picture_url != '') {
        echo "<img src='".$asociada->picture_url."' alt='".$asociada->first_name." ".$asociada->last_name."' />";
      }

      echo "<p><b><a href='".wp_pcc_asociada_permalink($asociada)."/'>".$asociada->first_name." ".$asociada->last_name."</a></b></p>";
      echo "<p>".$empresa."</p>";
      echo "<p>".$sector."</p>";
      echo "</div>";
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