<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: Error.php
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
namespace MaterialTheme\Templates;

use MaterialTheme\Core;
use PHPFusion\Panels;

class Error extends Core {
    public static function displayErrorPage($data) {
        $locale = fusion_get_locale();
        $settings = fusion_get_settings();

        self::setParam('menu', FALSE);
        self::setParam('footer', FALSE);
        self::setParam('left_panel', FALSE);
        self::setParam('right', FALSE);
        self::setParam('container', FALSE);
        Panels::getInstance(TRUE)->hide_panel('RIGHT');
        Panels::getInstance(TRUE)->hide_panel('AU_CENTER');
        Panels::getInstance(TRUE)->hide_panel('U_CENTER');
        Panels::getInstance(TRUE)->hide_panel('L_CENTER');
        Panels::getInstance(TRUE)->hide_panel('BL_CENTER');
        self::setParam('notices', FALSE);
        self::setTplCss('error');

        $locale['error'] = str_replace('!', '', $locale['error']);

        set_title($locale['error'].' '.$data['status']);

        echo '<header><!-- --></header>';
        echo '<div class="block">';
            echo '<div class="block1">';
                echo '<div class="title">'.$locale['error'].' '.$data['status'].'</div>';

                if ($data['status'] == 404) {
                    echo '<p>'.$data['title'].'</p>';
                }
            echo '</div>';

            echo '<div class="button">';
                echo '<a href="'.$settings['siteurl'].$settings['opening_page'].'" class="btn-home">';
                    echo '<svg fill="#fff" height="50" viewBox="0 0 24 24" width="50" xmlns="http://www.w3.org/2000/svg">';
                        echo '<path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>';
                        echo '<path d="M0 0h24v24H0z" fill="none"/>';
                    echo '</svg>';
                    echo '<span>'.$locale['home'].'</span>';
                echo '</a>';
            echo '</div>';

            echo '<div class="block2">';
                if ($data['status'] == 404) {
                    echo openform('searchform', 'post', $settings['siteurl'].'search.php?stype=all', [
                        'remote_url' => $settings['site_path'].'search.php'
                    ]);

                    echo '<div class="row">';
                        echo '<div class="col-xs-6 col-sm-9 col-md-9 col-lg-9">';
                            echo form_text('stext', '', '', ['placeholder' => $locale['search']]);
                        echo '</div>';
                        echo '<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">';
                            echo '<button type="submit" title="'.$locale['submit'].'" class="btn btn-default"><i class="fa fa-search"></i></button>';
                        echo '</div>';
                    echo '</div>';
                    echo closeform();
                } else {
                    echo '<div class="code-text">'.$data['title'].'</div>';
                }
            echo '</div>';

            echo '<dov class="copyright">';
                echo '<div class="pull-left">';
                    echo self::themeCopyright();
                    echo '<br/>';
                    echo showcopyright();
                echo '</div>';
                echo '<div class="pull-right">';
                    echo nl2br(parse_textarea($settings['footer'], FALSE, TRUE));
                echo '</div>';
            echo '</dov>';
        echo '</div>';

        add_to_jquery('
            ResizeBlock();
            $(window).resize(function() {ResizeBlock();});

            function ResizeBlock() {
                var block1 = $(".block1");
                $(".block1, .block2").height($(window).height() / 4);
                block1.css("margin-top", "-" + block1.outerHeight() + "px");
            }
        ');
    }
}
