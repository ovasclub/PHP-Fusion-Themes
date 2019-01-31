<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: MDashboard/classes/AdminPanel.inc
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

use \PHPFusion\Admins;
use \PHPFusion\OutputHandler;

class AdminPanel {
    private $messages = [];
    private static $breadcrumbs = FALSE;

    public function __construct() {
        OutputHandler::addToFooter('<script type="text/javascript" src="'.INCLUDES.'jquery/jquery.cookie.js"></script>');
        OutputHandler::addToFooter('<script type="text/javascript" src="'.MD.'assets/js/scripts.min.js"></script>');
        OutputHandler::addToHead('<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,400i,700,700i&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese"/>');
        OutputHandler::addToHead('<link rel="stylesheet" type="text/css" href="'. MD.'assets/mCustomScrollbar/jquery.mCustomScrollbar.min.css"/>');
        OutputHandler::addToFooter('<script type="text/javascript" src="'. MD.'assets/mCustomScrollbar/jquery.mCustomScrollbar.min.js"></script>'); // https://github.com/malihu/malihu-custom-scrollbar-plugin
        OutputHandler::addToHead('<script src="'.MD.'assets/js/jquery.mousewheel.min.js"></script>');

        $this->Navigation();
        $this->Sidebar();

        echo '<div class="content-wrapper animate">';
            if (function_exists('renderNotices') && function_exists('getNotices')) {
                echo renderNotices(getNotices());
            }

            echo CONTENT;

            $this->Footer();
        echo '</div>';
    }

    private function Sidebar() {
        $locale = fusion_get_locale('', MD_LOCALE);
        $userdata = fusion_get_userdata();

        echo '<aside class="sidebar animate"><div class="sidebar-content">';
            echo '<div class="logo overflow-hide"><i class="php-fusion"></i><span>'.$locale['MD_001'].'</span></div>';

            echo '<ul class="user-actions">';
                echo '<li class="dropdown">';
                    echo '<a id="user-actions" class="icon-down" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="'.$userdata['user_name'].'"><i class="fa fa-user"></i></a>';
                    echo '<ul class="dropdown-menu m-t-10" aria-labelledby="user-actions">';
                        echo '<li><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pencil fa-fw"></i> '.$locale['UM080'].'</a></li>';
                        echo '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye fa-fw"></i> '.$locale['view'].' '.$locale['profile'].'</a></li>';
                        echo '<li class="divider"></li>';

                        if (IS_V9) {
                            echo '<li><a href="'.FUSION_REQUEST.'&amp;logout"><i class="fa fa-sign-out fa-fw"></i> '.$locale['admin-logout'].'</a></li>';
                        }
                        echo '<li><a href="'.BASEDIR.'index.php?logout=yes"><i class="fa fa-sign-out fa-fw"></i> <span class="text-danger">'.$locale['logout'].'</span></a></li>';
                    echo '</ul>';
                echo '</li>';

                $languages = fusion_get_enabled_languages();
                if (count($languages) > 1) {
                    echo '<li class="dropdown languages-menu">';
                        echo '<a id="languages-list" class="icon-down" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="'.translate_lang_names(LANGUAGE).'"><i class="fa fa-globe"></i></a>';
                        echo '<ul class="dropdown-menu m-t-10" aria-labelledby="languages-list">';
                            foreach ($languages as $language_folder => $language_name) {
                                echo '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'"><img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> '.$language_name.'</a></li>';
                            }
                        echo '</ul>';
                    echo '</li>';
                }

                $messages_count = $this->Messages();
                $messages_count = !empty($messages_count) ? '<span class="label label-danger msg-count">'.count($messages_count).'</span>' : '';

                echo '<li><a '.(!empty($messages_count) ? 'id="messages-box"' : '').' href="'.BASEDIR.'messages.php" title="'.$locale['message'].'"><i class="fa fa-envelope"></i>'.$messages_count.'</a></li>';
                echo '<li><a href="'.BASEDIR.'index.php" title="'.fusion_get_settings('sitename').'"><i class="fa fa-home"></i></a></li>';
            echo '</ul>';

            echo '<div id="sidebar-menu">';
                if (class_exists('\PHPFusion\AdminSearch')) {
                    echo '<div class="sidebar-form">';
                        echo '<input type="text" id="search_pages" name="search_pages" class="form-control" placeholder="'.$locale['MD_002'].'">';
                        echo '<i class="fa fa-search input-search-icon"></i>';
                    echo '</div>';
                    echo '<ul class="admin-vertical-link" id="search_result" style="display: none;"></ul>';
                    echo '<img id="ajax-loader" style="width: 30px; display: none;" class="img-responsive center-x m-t-10" alt="Ajax Loader" src="'.IMAGES.'loader.svg"/>';
                }

                echo Admins::getInstance()->vertical_admin_nav(TRUE);
            echo '</div>';

            $messages = $this->GetMessages();

            if (!empty($messages)) {
                echo '<ul id="pms-box" style="display: none;">';
                foreach ($messages as $message) {
                    echo '<li>';
                        echo '<div class="msg-block">';
                            echo '<div class="clearfix">';
                                echo $message['user']['user_name'];
                                echo '<br/><a href="'.BASEDIR.'messages.php?folder=inbox&amp;msg_read='.$message['link'].'" title="'.$message['title'].'">'.trim_text($message['title'], 18).'</a><small class="pull-right msg-date">'.$message['datestamp'].'</small>';
                            echo '</div>';
                        echo '</div>';
                    echo '</li>';
                }
                echo '</ul>';
            }
        echo '</div></aside>';

        if (class_exists('\PHPFusion\AdminSearch')) {
            OutputHandler::addToJQuery('$("#search_pages").bind("keyup", function (e) {
                $.ajax({
                    url: "'.ADMIN.'includes/acp_search.php'.fusion_get_aidlink().'",
                    method: "get",
                    data: $.param({"pagestring": $(this).val()}),
                    dataType: "json",
                    beforeSend: function () {
                        $("#ajax-loader").show();
                    },
                    success: function (e) {
                        if ($("#search_pages").val() == "") {
                            $("#adl").show();
                            $("#search_result").html(e).hide();
                            $("#search_result li").html(e).hide();
                        } else {
                            var result = "";

                            if (!e.status) {
                                $.each(e, function (i, data) {
                                    if (data) {
                                        result += "<li><a href=\"" + data.link + "\"><img class=\"admin-image\" alt=\"" + data.title + "\" src=\"" + data.icon + "\"/> " + data.title + "</a></li>";
                                    }
                                });
                            } else {
                                result = "<li class=\"text-center\"><span class=\"p-5\">" + e.status + "</span></li>";
                            }

                            $("#search_result").html(result).show();
                            $("#adl").hide();
                        }
                    },
                    complete: function () {
                        $("#ajax-loader").hide();
                    }
                });
            });');
        }
    }

    private function Navigation() {
        echo '<nav class="topnav animate">';
            echo '<ul class="left top-navbar navbar-left pull-left m-b-0">';
                echo '<li><a href="#" id="toggle-sidebar" title="Sidebar"><i class="fa fa-bars"></i></a></li>';
            echo '</ul>';

            if (IS_V9) {
                $this->GetPageTitle();
            }

            echo '<ul class="right top-navbar navbar-right pull-right m-r-5 m-b-0">';
                $sections = Admins::getInstance()->getAdminSections();
                if (!empty($sections)) {
                    $i = 0;
                    foreach ($sections as $section_name) {
                        $active = (isset($_GET['pagenum']) && $_GET['pagenum'] == $i || !isset($_GET['pagenum']) && Admins::getInstance()->_isActive() == $i) ? ' class="active"' : '';
                        echo '<li'.$active.'><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&amp;pagenum='.$i.'" title="'.$section_name.'">'.Admins::getInstance()->get_admin_section_icons($i).'</a></li>';
                        $i++;
                    }
                }
            echo '</ul>';
        echo '</nav>';
    }

    private function Footer() {
        $locale = fusion_get_locale();

        echo '<footer class="main-footer">';
            if (function_exists('showFooterErrors')) {
                echo showFooterErrors();
            } else {
                if (!IS_V9) {
                    global $_errorHandler;

                    if (iADMIN && checkrights('ERRO') && count($_errorHandler) > 0) {
                        echo '<div>'.str_replace('[ERROR_LOG_URL]', ADMIN.'errors.php'.fusion_get_aidlink(), $locale['err_101']).'</div>';
                    }
                }
            }

            if (fusion_get_settings('rendertime_enabled')) {
                echo showrendertime().' '.showMemoryUsage().'<br />';
            }

            echo '<strong>MDashboard Theme &copy; '.date('Y').' '.$locale['MD_003'].' <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a></strong>';
            echo '<br/>'.str_replace('<br />', ' | ', showcopyright());
        echo '</footer>';
    }

    private function GetPageTitle() {
        $sections = Admins::getInstance()->getAdminSections();
        $pages = Admins::getInstance()->getAdminPages();
        $current_page = Admins::getInstance()->getCurrentPage();

        if (!empty($sections) && !empty($pages)) {
            $pages = flatten_array($pages);

            if (!empty($current_page)) {
                foreach ($pages as $page_data) {
                    if ($page_data['admin_link'] == $current_page) {
                        echo '<div class="page-title hidden-xs hidden-sm"><img class="img-responsive" src="'.get_image('ac_'.$page_data['admin_rights']).'" alt="'.$page_data['admin_title'].'"/> <span>'.$page_data['admin_title'].'</span></div>';
                    }
                }
            }
        }
    }

    private function Messages() {
        $userdata = fusion_get_userdata();

        if (column_exists(DB_MESSAGES, 'message_user')) {
            $msg_user = 'message_user';
        } else {
            $msg_user = 'message_to';
        }

        $result = dbquery("
            SELECT message_id, message_subject, message_from user_id, u.user_name, u.user_status, u.user_avatar, u.user_lastvisit, message_datestamp
            FROM ".DB_MESSAGES."
            INNER JOIN ".DB_USERS." u ON u.user_id=message_from
            WHERE message_to='".$userdata['user_id']."' AND ".$msg_user."='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'
            GROUP BY message_id
        ");

        if (dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND ".$msg_user."='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'")) {
            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    $this->messages[] = [
                        'link'      => $data['message_id'],
                        'title'     => $data['message_subject'],
                        'user'      => [
                            'user_id'        => $data['user_id'],
                            'user_name'      => $data['user_name'],
                            'user_status'    => $data['user_status'],
                            'user_avatar'    => $data['user_avatar'],
                            'user_lastvisit' => $data['user_lastvisit']
                        ],
                        'datestamp' => timer($data['message_datestamp'])
                    ];
                }
            }
        }

        return $this->messages;
    }

    private function GetMessages() {
        return $this->messages;
    }

    public static function OpenTable($title = FALSE, $class = NULL) {
        if (!empty($title)) {
            echo '<section class="content-header">';
            echo '<h1>'.$title.'</h1>';

            if (self::$breadcrumbs == FALSE) {
                echo render_breadcrumbs();
                self::$breadcrumbs = TRUE;
            }
            echo '</section>';
        }

        echo '<section class="content '.$class.'">';
    }

    public static function CloseTable() {
        echo '</section>';
    }
}
