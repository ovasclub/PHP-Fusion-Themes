<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: PrivateMessages.php
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
use \PHPFusion\Panels;

class PrivateMessages extends Core {
    public static function displayInbox($info) {
        $locale = fusion_get_locale();

        Panels::getInstance(TRUE)->hide_panel('RIGHT');
        echo '<div class="card">';
        echo '<div class="row">';
            echo '<div class="col-xs-12 col-sm-3">';
                echo '<div class="text-center"><a class="btn btn-primary btn-block" href="'.$info['button']['new']['link'].'">'.$locale['401'].'</a></div>';

                echo '<ul class="nav nav-pills nav-stacked m-t-20">';
                    $i = 0;
                    foreach ($info['folders'] as $key => $folder) {
                        $active = ($_GET['folder'] == $key ? ' active' : '');
                        echo '<li class="m-t-5'.$active.'"><a class="p-t-5 p-b-5" href="'.$folder['link'].'">';
                            if (!empty($folder['icon'])) {
                                echo '<i class="'.$folder['icon'].' fa-fw"></i> ';
                            }

                            echo $folder['title'];

                            if ($i < count($info['folders']) - 1) {
                                $total_key = $key.'_total';
                                echo '<span class="badge m-t-3 pull-right">'.$info[$total_key].'</span>';
                            }
                        echo '</a></li>';
                        $i++;
                    }
                echo '</ul>';
            echo '</div>';

            echo '<div class="col-xs-12 col-sm-9">';
                if (!isset($_GET['msg_send']) && (!empty($info['actions_form']) || isset($_GET['msg_read']))) {
                    echo '<div class="m-b-20">';
                        if (isset($_GET['msg_read'])) {
                            echo '<a class="btn btn-default m-r-20" href="'.$info['button']['back']['link'].'" title="'.$info['button']['back']['title'].'"><i class="fa fa-long-arrow-alt-left"></i></a>';
                        }

                        echo '<div class="display-inline-block">';
                            if (is_array($info['actions_form'])) {
                                echo $info['actions_form']['openform'];

                                if (isset($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']])) {
                                    echo '<div class="btn-group display-inline-block m-r-10">';
                                        if ($_GET['folder'] == 'archive') {
                                            echo $info['actions_form']['unlockbtn'];
                                        } else if ($_GET['folder'] == 'inbox') {
                                            echo $info['actions_form']['lockbtn'];
                                        }
                                        echo $info['actions_form']['deletebtn'];
                                    echo '</div>';
                                } else {
                                    echo '<div class="dropdown display-inline-block m-r-10">';
                                        echo '<a id="ddactions" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-sm dropdown-toggle"><i id="chkv" class="fa fa-square-o"></i><span class="caret m-l-5"></span></a>';
                                        echo '<ul class="dropdown-menu" aria-labelledby="ddactions">';
                                            foreach ($info['actions_form']['check'] as $id => $title) {
                                                echo '<li><a id="'.$id.'" data-action="check" class="pointer">'.$title.'</a></li>';
                                            }
                                        echo '</ul>';
                                    echo '</div>';

                                    echo '<div class="btn-group display-inline-block m-r-10">';
                                        if ($_GET['folder'] == 'archive') {
                                            echo $info['actions_form']['unlockbtn'];
                                        } else if ($_GET['folder'] !== 'outbox') {
                                            echo $info['actions_form']['lockbtn'];
                                        }
                                        echo $info['actions_form']['deletebtn'];
                                    echo '</div>';

                                    echo '<div class="dropdown display-inline-block m-r-10">';
                                        echo '<a id="ddactions2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-sm dropdown-toggle">'.$locale['444'].'&hellip; <span class="caret"></span></a>';
                                        echo '<ul class="dropdown-menu" aria-labelledby="ddactions2">';
                                            echo '<li>'.$info['actions_form']['mark_all'].'</li>';
                                            echo '<li>'.$info['actions_form']['mark_read'].'</li>';
                                            echo '<li>'.$info['actions_form']['mark_unread'].'</li>';
                                            echo '<li>'.$info['actions_form']['unmark_all'].'</li>';
                                        echo '</ul>';
                                    echo '</div>';
                                }
                                echo $info['actions_form']['closeform'];
                            } else {
                                echo $info['actions_form'];
                            }
                        echo '</div>';

                        echo !empty($info['pagenav']) ? '<div class="display-inline-block pull-right">'.$info['pagenav'].'</div>' : '';
                    echo '</div>';
                }

                switch ($_GET['folder']) {
                    case 'options':
                        echo $info['options_form'];
                        break;
                    case 'inbox':
                        self::inbox($info);
                        break;
                    default:
                        self::inbox($info);
                }
            echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private static function inbox($info) {
        $locale = fusion_get_locale();

        if (isset($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']])) {
            $data = $info['items'][$_GET['msg_read']];

            echo '<h4>'.$data['message']['message_header'].'</h4>';
            echo '<div class="m-b-20">';
                echo display_avatar($data, '40px', '', FALSE, 'img-rounded pull-left m-t-5 m-r-10');
                echo profile_link($data['user_id'], $data['user_name'], $data['user_status'],'display-block');
                echo '<span>'.showdate($locale['date_day'], $data['message_datestamp']).', '.timer($data['message_datestamp']).'</span>';
            echo '</div>';

            echo nl2br(parse_textarea($data['message']['message_text'], TRUE, TRUE, FALSE));
            echo '<hr/>';
            echo $info['reply_form'];
        } else if (isset($_GET['msg_send'])) {
            echo $info['reply_form'];
        } else {
            if (!empty($info['items'])) {
                $unread = [];
                $read = [];

                foreach ($info['items'] as $message_id => $data) {
                    if ($data['message_read']) {
                        $read[$message_id] = $data;
                    } else {
                        $unread[$message_id] = $data;
                    }
                }

                echo '<h4><a data-target="#unread_inbox" class="pointer text-dark" data-toggle="collapse" aria-expanded="false" aria-controls="unread_inbox">'.$locale['446'].' <span class="caret"></span></a></h4>';
                echo '<div id="unread_inbox" class="collapse in">';
                    if (!empty($unread)) {
                        echo '<div class="table-responsive"><table id="unread_tbl" class="table table-hover table-striped">';
                            foreach ($unread as $id => $message_data) {
                                echo '<tr>';
                                    echo '<td class="col-xs-1 align-middle">'.form_checkbox('pmID', '', '', [
                                        'input_id' => 'pmID-'.$id,
                                        'value'    => $id,
                                        'class'    => 'm-b-0'
                                    ]).'</td>';
                                    echo '<td class="col-xs-2 align-middle"><b>'.$message_data['contact_user']['user_name'].'</b></td>';
                                    echo '<td class="col-xs-7">';
                                        echo '<a class="display-block" href="'.$message_data['message']['link'].'"><b>'.$message_data['message']['name'].'</b></a>';
                                        echo '<small>'.trim_text($message_data['message_message'], 80).'</small>';
                                    echo'</td>';
                                    echo '<td class="col-xs-2 align-middle">'.timer($message_data['message_datestamp']).'</td>';
                                echo '</tr>';
                            }
                        echo '</table></div>';
                    } else {
                        echo '<div class="well text-center">'.$locale['471'].'</div>';
                    }
                echo '</div>';

                echo '<h4><a data-target="#read_inbox" class="pointer text-dark" data-toggle="collapse" aria-expanded="false" aria-controls="read_inbox">'.$locale['447'].' <span class="caret"></span></a></h4>';
                echo '<div id="read_inbox" class="collapse in">';
                    if (!empty($read)) {
                        echo '<div class="table-responsive"><table id="read_tbl" class="table table-hover table-striped">';
                            foreach ($read as $id => $message_data) {
                                echo '<tr>';
                                    echo '<td class="col-xs-1">'.form_checkbox('pmID', '', '', [
                                        'input_id' => 'pmID-'.$id,
                                        'value'    => $id,
                                        'class'    => 'm-b-0'
                                    ]).'</td>';
                                    echo '<td class="col-xs-2">'.$message_data['contact_user']['user_name'].'</td>';
                                    echo '<td class="col-xs-7"><a href="'.$message_data['message']['link'].'">'.$message_data['message']['name'].'</a></td>';
                                    echo '<td class="col-xs-2">'.timer($message_data['message_datestamp']).'</td>';
                                echo '</tr>';
                            }
                        echo '</table></div>';
                    } else {
                        echo '<div class="well text-center">'.$locale['471'].'</div>';
                    }
                echo '</div>';
            } else {
                echo '<div class="well text-center">'.$info['no_item'].'</div>';
            }
        }
    }
}
