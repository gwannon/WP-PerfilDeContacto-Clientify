<?php

use Gwannon\PHPClientifyAPI\contactClientify;

//Shortcodes
function wp_pcc_asociada_shortcode($params = array(), $content = null) {
  ob_start();
  $temp = explode("-", get_query_var('asociada'));
  $asociada_id= end($temp); 
  if($asociada_id != '') { $asociada = new contactClientify($asociada_id); ?>
    <div class="asociada-perfil">
        <?php if($asociada->getPicture() != '') { ?>
            <div class="imagen" style="--bgimage: url('<?=$asociada->getPicture();?>');"></div>
        <?php } ?>
        <h1><?php echo $asociada->getFirstName()." ".$asociada->getLastName(); ?></h1>
        <?php
            $position = $asociada->getPosition();
            echo (isset($position) && $position != '' ? "<h2>".$position."<h2>" : "");

            $company = $asociada->getCustomField('Asociadas_Empresa');
            $sector = $asociada->getCustomField('Asociadas_Sector');
            echo (isset($company['value']) && $company['value'] != '' ? "<h3>".$company['value']."".(isset($sector['value']) && $sector['value'] != '' ? " (".$sector['value'].")" : "")."</h3>" : "");
        ?>
        <hr>
        <ul class="contacto">
            <?php
                $email = $asociada->getCustomField('Asociadas_Emailpublico');
                echo (isset($email['value']) && $email['value'] != '' ? "<li><i class='icomoon-the7-font-the7-mail-011'></i> <a href='mailto:".$email['value']."'>".$email['value']."</a></li>" : "");

                $phone = $asociada->getCustomField('Asociadas_Telefonopublico');
                echo (isset($phone['value']) && $company['value'] != '' ? "<li><i class='icomoon-the7-font-the7-phone-01'></i> <a href='tel:".$phone['value']."'>".$phone['value']."</a></li>" : "");

                $website = $asociada->getCustomField('Asociadas_Paginaweb');
                echo (isset($company['value']) && $company['value'] != '' ? "<li><a href='".$website['value']."'><img class='img-icon' style='width: 1em;' src='".plugin_dir_url(__FILE__)."images/website.png'> ".$website['value']."</a></li>" : "");

                $linkedint_url = $asociada->getLinkedinUrl();
                echo (isset($linkedint_url) && $linkedint_url != '' ? "<li><a href='".$linkedint_url."'><img class='img-icon' style='width: 1em;' src='".plugin_dir_url(__FILE__)."images/linkedin.png'></a></li>" : "");
            ?>
        </ul>
        <hr>
        <?php

            $cv = $asociada->getCustomField('Asociadas_CV');
            echo (isset($cv['value']) && $cv['value'] != '' ? "<h4>".__("Perfil Profesional", 'wp-perfil-contacto')."</h4> ".apply_filters("the_content", stripslashes($cv['value'])) : "");

            //NO USAR ----------------------------
            /*$emails = $asociada->getEmailsByType(1);
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
            }*/
        ?>
    </div>
    <style>
        .asociada-perfil .imagen {
            background: white var(--bgimage) center center no-repeat;
            background-size: cover;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            border: 5px solid #ba5b80;
            margin: 0;
            margin-bottom: 40px;
            position: relative;
            margin-bottom: 15px;
        }
        .asociada-perfil .imagen:after {
            content: "";
            position: absolute;
            width: 233px;
            height: 233px;
            border-radius: 50%;
            border: 4px solid white;
            top: 0px;
            left: 0px;
            display: block;
        }

        .asociada-perfil h1 {
            font-family: 'Bodoni Moda';
            font-style: italic;
            font-weight: 800;
            color: #9e3159;
            font-size: 30px;
            line-height: 34px;
            position: relative;
            padding-bottom: 12px;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .asociada-perfil h1:after {
            content: "";
            position: absolute;
            width: 100px;
            height: 3px;
            bottom: 0px;
            left: 0px;
            background-color: #e3a6be;
        }

        .asociada-perfil h2, 
        .asociada-perfil h3 {
            text-transform: uppercase;
            font-family: 'Roboto Condensed';
            font-weight: 300;
            color: #333333;
            margin-top: 10px;
            font-size: 19px;
            line-height: 22px;
        }
        .asociada-perfil h3 {
            font-weight: 700;
            text-transform: none;
        }
        .asociada-perfil h4 {
            font-family: 'Roboto Condensed';
            font-weight: 500;
            color: #333333;
            margin-bottom: 10px;
            font-size: 22px;
        }
        .asociada-perfil p,
        .asociada-perfil li {
            font-family: 'Roboto Condensed';
            font-weight: 300;
            color: #333333;
            font-size: 18px;
        }
        .asociada-perfil hr {
            border: none;
            border-top: 1px solid #ebebeb;
            margin: 40px 0 !important;
        }

        .asociada-perfil ul.contacto {
            display: flex;
            gap: 30px;
            padding: 0px;
            margin: 0px;
        }

        .asociada-perfil ul.contacto li {
            list-style-type: none;
        }

        .asociada-perfil ul.contacto li a {
            text-decoration: none;
            color: #333333;
            transition: color 0.3s;
        }

        .asociada-perfil ul.contacto li a:hover {
            color: #e3a6be;
        }

        .asociada-perfil ul.contacto li i {
            color: #9e3159;
        }

    </style>
    <?php
  }
  return ob_get_clean();
}
add_shortcode('aed-asociada', 'wp_pcc_asociada_shortcode');
