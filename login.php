<?php

use Gwannon\PHPClientifyAPI\contactClientify;

/* wp_pcc_login */
function wp_pcc_login($params = array(), $content = null) {
  if(isset($_COOKIE['wp_pcc'])) return; //Si existe la cookie ni seguimos.
  ob_start();?>
  <div id="wp-pcc-login">
      <form id="wp-pcc-form-login" method="post">
        <h2><?php _e("Accede a tu perfíl", "wp-perfil-contacto"); ?></h2>
        <p><?php _e("En el podrás cambiar actualizar tu perfil de asociada.", "wp-perfil-contacto"); ?></p>
        <?php if (isset($_REQUEST['wp-pcc-email']) && is_email($_REQUEST['wp-pcc-email'])) {
          if(contactClientify::existsContact($_REQUEST['wp-pcc-email'])) {
            $asociada = new contactClientify($_REQUEST['wp-pcc-email']);
            if($asociada->hasTag(WP_AED_ASOCIADAS_TAGS)) {
              wp_pcc_send_login_email($asociada, $_REQUEST['wp-pcc-email']);
              $ok = __('Para actualizar tu perfil, comprueba tu correo electrónico porque te hemos enviado un mensaje con los pasos para poder hacerlo.', 'wp-perfil-contacto');
            } else $error = __('Email incorrecto. El email suministrado no está en nuestra base de datos.', 'wp-perfil-contacto');
          } else $error = __('Email incorrecto. El email suministrado no está en nuestra base de datos.', 'wp-perfil-contacto');
        } else if (isset($_REQUEST['wp-pcc-email'])) $error = __('Email incorrecto. El email suministrado no tiene el formato adecuado.', 'wp-perfil-contacto');?>
        <?php if(isset($ok)) echo "<p style=' background-color: #21f3f3; color: #000; padding: 10px; text-align: center;'><b>".$ok."</b></p>"; ?>
        <?php if(isset($error)) echo "<p style='background-color: red; color: #fff; padding: 10px; text-align: center;'><b>".$error."</b></p>"; ?>
        <p><?php _e('Email', 'wp-perfil-contacto'); ?></p>
        <input type="email" name="wp-pcc-email" value="" placeholder="<?php _e('tunombre@email.com', 'wp-perfil-contacto'); ?>" required />
        <button type="submit" name="wp-pcc-send"><?php _e('Enviar', 'wp-perfil-contacto'); ?></button>
      </form>
  </div>
  <?php return ob_get_clean();
}
add_shortcode('login-asociadas', 'wp_pcc_login');


function wp_pcc_send_login_email($user, $email) {
  $headers = array(
    "From: jorge@enutt.net",
    "Reply-To: jorge@enutt.net",
    "X-Mailer: PHP/".phpversion(),
    "Content-type: text/html; charset=utf-8"
  );
  $message = str_replace("[LINK]", get_the_permalink()."?wp-pcc-date=".date("YmdHis")."&wp-pcc-hash=".wp_pcc_user_hash($user)."&wp-pcc-id=".$user->id, file_get_contents(dirname(__FILE__)."/emails/email_login_es.html"));
  $message = str_replace("[URL]", plugin_dir_url(__DIR__), $message);
  wp_mail ($email, __("Aquí puedes actualizar tu perfil de asociada de AED", 'wp-perfil-contacto'), $message, $headers);
}

function wp_pcc_user_hash($user) {
  return hash('ripemd160', $user->id.date("YW").WP_AED_HASH);
} 