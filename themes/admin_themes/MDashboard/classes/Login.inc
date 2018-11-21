<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: MDashboard/classes/Login.inc
| Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
namespace MDashboard;

class Login {
    public function __construct() {
        $locale = fusion_get_locale('', MD_LOCALE);
        $userdata = fusion_get_userdata();

        add_to_head('<style type="text/css">body, html {height: 100%;}body{background: url("'.MD.'images/login.jpg") no-repeat fixed center;background-size: cover;}</style>');
        add_to_jquery('$("#admin_password").focus();');

        echo '<div class="login-container">';
            echo '<h1 class="title">'.$locale['MD_001'].'</h1>';
            echo '<div class="login-box">';
                echo '<div class="clearfix m-b-20">';
                    echo '<div class="pull-left m-r-10">';
                        echo display_avatar($userdata, '90px', '', FALSE, 'img-rounded');
                    echo '</div>';
                    echo '<div class="text-left">';
                        echo '<h3>'.$locale['welcome'].',<br/>'.$userdata['user_name'].'</h3>';
                    echo '</div>';
                echo '</div>';

                $form_action = FUSION_SELF.fusion_get_aidlink() == ADMIN.'index.php'.fusion_get_aidlink() ? FUSION_SELF.fusion_get_aidlink().'&amp;pagenum=0' : FUSION_REQUEST;
                echo openform('admin-login-form', 'post', $form_action, ['class' => 'm-t-30']);
                    echo form_text('admin_password', '', '', ['type' => 'password', 'callback_check' => 'check_admin_pass', 'placeholder' => $locale['281'], 'error_text' => $locale['global_182'], 'autocomplete_off' => TRUE, 'required' => TRUE]);
                    echo form_button('admin_login', $locale['login'], $locale['login'], ['class' => 'btn-primary btn-block m-t-15']);
                echo closeform();
            echo '</div>';
        echo '</div>';
    }
}
