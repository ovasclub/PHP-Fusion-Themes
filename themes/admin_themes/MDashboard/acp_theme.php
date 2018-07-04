<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: MDashboard/acp_theme.php
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
if (!defined('IN_FUSION')) {
    die('Access Denied');
}

define('MD', THEMES.'admin_themes/MDashboard/');
require_once INCLUDES.'theme_functions_include.php';
require_once MD.'acp_autoloader.php';

if (!defined('MD_LOCALE')) {
    if (file_exists(MD.'locale/'.LANGUAGE.'.php')) {
        define('MD_LOCALE', MD.'locale/'.LANGUAGE.'.php');
    } else {
        define('MD_LOCALE', MD.'locale/English.php');
    }
}

define('BOOTSTRAP', TRUE);
define('FONTAWESOME', TRUE);

if (isset($_COOKIE['sidebar-toggled']) && $_COOKIE['sidebar-toggled'] == 1) {
    define('THEME_BODY', '<body class="sidebar-toggled">');
}

\PHPFusion\Admins::getInstance()->setAdminBreadcrumbs();

function render_admin_panel() {
    new MDashboard\AdminPanel();
}

function render_admin_login() {
    new MDashboard\Login();
}

function render_admin_dashboard() {
    MDashboard\Dashboard::AdminDashboard();
}

function openside($title = FALSE, $class = NULL) {
    echo '<div class="sidepanel '.$class.'">';
    echo $title ? '<div class="sidepanel-header">'.$title.'</div>' : '';
    echo '<div class="sidepanel-body">';
}

function closeside($title = FALSE) {
    echo '</div>';
    echo $title ? '<div class="sidepanel-footer">'.$title.'</div>' : '';
    echo '</div>';
}

function opentable($title, $class = NULL) {
    MDashboard\AdminPanel::OpenTable($title, $class);
}

function closetable() {
    MDashboard\AdminPanel::CloseTable();
}
