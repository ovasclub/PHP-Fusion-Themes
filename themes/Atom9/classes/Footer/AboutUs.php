<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: AboutUs.php
| Author: Frederick MC Chan
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
namespace Atom9Theme\Footer;

class AboutUs {
    public static function panel() {
        $locale = fusion_get_locale('', ATOM9_LOCALE);
        $settings = fusion_get_settings();

        ob_start();

        echo '<h3>'.$locale['a9_002'].'</h3>';
        echo '<div>';
            echo '<b>'.$settings['sitename'].'</b><br/><br/>';
            echo '<p>'.$settings['description'].'</p>';
        echo '</div>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
