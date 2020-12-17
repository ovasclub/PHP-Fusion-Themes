<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: Profile.php
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
use MaterialTheme\Main;

class Profile extends Core {
    private static function profileHeader($info) {
        $locale = fusion_get_locale();

        ob_start();

        echo '<div class="container p-t-20 p-b-20">';
            echo '<div class="row">';
                echo '<div class="col-xs-12 col-sm-10 col-md-10 col-md-10">';
                    echo '<div class="m-50 p-b-60">';
                        echo '<div class="pull-left m-r-20">'.$info['avatar'].'</div>';
                        echo '<div class="overflow-hide text-white">';
                            echo '<h4 class="m-b-0 display-inline-block"><strong style="text-shadow: 0 1px 1px rgba(0,0,0,0.2);">'.$info['username'].'</strong></h4>';
                            echo '<div class="display-inline-block m-l-10">';
                                echo '<i class="fa fa-circle m-r-5 '.($info['useronline'] ? 'text-success' : 'text-danger').'"></i>';
                                echo '<span>'.($info['useronline'] ? $locale['online'] : $locale['offline']).'</span>';
                            echo '</div><br/>';
                            echo '<span>'.$info['userlevel'].'</span><br/>';
                            echo $info['buttons'];
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="hidden-xs col-sm-2 col-md-2 col-md-2">';
                    echo !empty($info['profile']) ? $info['profile'] : '';
                echo '</div>';
            echo '</div>';

            if (!empty($info['social'])) {
                echo '<div class="social display-block">';
                    echo '<div class="pull-right">';
                        foreach ($info['social'] as $type => $social) {
                            echo '<a href="'.$social['link'].'" target="_blank">'.$social['icon'].'</a>';
                        }
                    echo '</div>';
                echo '</div>';
            }

        echo '</div>';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function displayProfile($info) {
        global $userFields;

        $locale = fusion_get_locale();
        self::setTplCss('profile');
        $userdata = fusion_get_userdata();
        $user_data = $userFields->getUserData();

        if (!empty($info['core_field'])) {
            foreach ($info['core_field'] as $field_id => $field_data) {
                if ($field_id == 'profile_user_avatar') {
                    $edit_photo = '<div class="edit-photo">';
                    $edit_photo .= '<a href="'.BASEDIR.'edit_profile.php" id="change-photo">';
                    $edit_photo .= '<i class="fa fa-camera fa-fw m-r-5"></i> '.self::setLocale('profile_01');
                    $edit_photo .= '</a>';
                    $edit_photo .= '</div>';
                    $user_avatar = '<div class="img-profile">';
                    $user_avatar .= display_avatar($user_data, '115px', '', FALSE, 'img-circle');
                    $user_avatar .= $userdata['user_id'] == $_GET['lookup'] ? $edit_photo : '';
                    $user_avatar .= '</div>';

                    if ($userdata['user_id'] == $_GET['lookup'])
                        self::changeAvatar();
                }
            }
        }

        if (!empty($info['buttons'])) {
            $info['buttons_'][] = ['link' => BASEDIR.'messages.php?folder=inbox&msg_send='.$user_data['user_id'], 'name' => $locale['u043']];
            $info['buttons_'][] = ['link' => ADMIN.'members.php'.fusion_get_aidlink().'&ref=log&lookup='.$user_data['user_id'], 'name' => $locale['u054']];

            $buttons = '<div class="btn-group m-t-5 m-b-0" style="position: absolute;">';
            foreach ($info['buttons_'] as $id => $button) {
                $buttons .= '<a class="btn btn-primary btn-sm" href="'.$button['link'].'">'.$button['name'].'</a>';
            }
            $buttons .= '</div>';
        }

        if ($userdata['user_id'] == $_GET['lookup']) {
            $editprofile = '<div class="pull-right"><a class="btn btn-success btn-sm" href="'.BASEDIR.'edit_profile.php">'.$locale['UM080'].'</a></div>';
        }

        if (!empty($info['user_field'])) {
            foreach ($info['user_field'] as $cat_id => $category_data) {
                if (!empty($category_data['fields'])) {
                    foreach ($category_data['fields'] as $field_id => $field_data) {
                        if (!empty($field_data['type']) && $field_data['type'] == 'social') {
                            $social[] = $field_data;
                        }
                    }
                }
            }
        }

        $header_data = [
            'username'   => $info['core_field']['profile_user_name']['value'],
            'userlevel'  => $info['core_field']['profile_user_level']['value'],
            'useronline' => $user_data['user_lastvisit'] >= time() - 300, // After 5 minutes user is offline
            'avatar'     => !empty($user_avatar) ? $user_avatar : '',
            'buttons'    => !empty($buttons) ? $buttons : '',
            'profile'    => !empty($editprofile) ? $editprofile : '',
            'social'     => !empty($social) ? $social : ''
        ];

        Main::headerContent([
            'id'     => 'profile',
            'custom' => self::profileHeader($header_data)
        ]);

        if (!empty($info['section'])) {
            $tab_title = [];
            foreach ($info['section'] as $page_section) {
                $tab_title['title'][$page_section['id']] = $page_section['name'];
                $tab_title['id'][$page_section['id']] = $page_section['id'];
                $tab_title['icon'][$page_section['id']] = $page_section['icon'];
            }

            $tab_active = tab_active($tab_title, $_GET['section']);

            echo '<div class="card profile-card">';
                echo opentab($tab_title, $_GET['section'], 'profile_tab', TRUE, '', 'section', ['section']);
                    echo opentabbody($tab_title['title'][$_GET['section']], $tab_title['id'][$_GET['section']], $tab_active, TRUE);

                    if ($tab_title['id'][$_GET['section']] == $tab_title['id'][1]) {
                        echo '<div class="card-block info">';

                        if (!empty($info['core_field'])) {
                            foreach ($info['core_field'] as $field_id => $field_data) {
                                switch ($field_id) {
                                    case 'profile_user_group':
                                        if (!empty($field_data['value']) && is_array($field_data['value'])) {
                                            foreach ($field_data['value'] as $groups) {
                                                $user_groups[] = $groups;
                                            }
                                        }
                                        break;
                                    case 'profile_user_avatar':
                                        $avatar['user_avatar'] = $field_data['value'];
                                        $avatar['user_status'] = $field_data['status'];
                                        break;
                                    case 'profile_user_name':
                                        $user_level['user_name'] = $field_data['value'];
                                        break;
                                    case 'profile_user_level':
                                        $user_level['user_level'] = $field_data['value'];
                                        break;
                                    default:
                                        if (!empty($field_data['value'])) {
                                            echo '<div id="'.$field_id.'" class="row m-0 m-b-5 cat-field">';
                                                echo '<label class="pull-left"><strong>'.$field_data['title'].'</strong></label>';
                                                echo '<div class="pull-right">'.$field_data['value'].'</div>';
                                            echo '</div>';
                                        }
                                }
                            }
                        }

                        echo '<div class="row m-0 m-b-5 cat-field">';
                            echo '<label class="pull-left"><strong>'.$locale['u057'].'</strong></label>';
                            echo '<div class="pull-right">';
                                if (!empty($user_groups) && is_array($user_groups)) {
                                    $i = 0;
                                    foreach ($user_groups as $id => $group) {
                                        echo $i > 0 ? ', ' : '';
                                        echo '<a href="'.$group['group_url'].'">'.$group['group_name'].'</a>';
                                        $i++;
                                    }
                                } else {
                                    echo !empty($locale['u117']) ? $locale['u117'] : $locale['na'];
                                }
                            echo '</div>';
                        echo '</div>';

                        if (!empty($info['user_admin'])) {
                            $link = $info['user_admin'];
                            echo '<div class="btn-group m-t-10">';
                                echo '<a class="btn btn-default" href="'.$link['user_edit_link'].'">'.$link['user_edit_title'].'</a>';
                                echo '<a class="btn btn-default" href="'.$link['user_ban_link'].'">'.$link['user_ban_title'].'</a>';
                                echo '<a class="btn btn-default" href="'.$link['user_suspend_link'].'">'.$link['user_suspend_title'].'</a>';
                                echo '<a class="btn btn-danger" href="'.$link['user_delete_link'].'">'.$link['user_delete_title'].'</a>';
                            echo '</div>';
                        }

                        if (!empty($info['group_admin'])) {
                            $group = $info['group_admin'];

                            echo '<div class="m-t-10">';
                                echo $group['ug_openform'];
                                echo '<div class="strong">'.$group['ug_title'].'</div>';
                                echo '<div class="spacer-xs">'.$group['ug_dropdown_input'].'</div>';
                                echo '<div>'.$group['ug_button'].'</div>';
                                echo $group['ug_closeform'];
                            echo '</div>';
                        }
                        echo '</div>';
                    }

                    if (!empty($info['user_field'])) {
                        echo '<div class="row equal-height">';
                            foreach ($info['user_field'] as $cat_id => $category_data) {
                                if (!empty($category_data['fields'])) {
                                    if (isset($category_data['fields'])) {
                                        foreach ($category_data['fields'] as $field_id => $field_data) {
                                            if (isset($field_data['type']) && $field_data['type'] == 'social') {
                                                // Hide Social UF
                                            } else {
                                                $fields[] = $field_data;
                                            }
                                        }
                                    }

                                    if (!empty($fields)) {
                                        echo '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">';
                                            echo '<div class="card-block">';
                                                echo '<div class="cat-title">'.$category_data['title'].'</div>';

                                                if (isset($category_data['fields'])) {
                                                    foreach ($category_data['fields'] as $field_id => $field_data) {
                                                        if (isset($field_data['type']) && $field_data['type'] == 'social') {
                                                            // Hide Social UF
                                                        } else {
                                                            echo '<div id="field-'.$field_id.'" class="row m-0 m-b-10 cat-field">';
                                                                echo '<label class="pull-left"><strong>'.(!empty($field_data['icon']) ? $field_data['icon'] : '').' '.$field_data['title'].'</strong></label>';
                                                                echo '<div class="pull-right">'.$field_data['value'].'</div>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                }
                                            echo '</div>';
                                        echo '</div>';
                                    }
                                }
                            }
                        echo '</div>';

                    } else {
                        echo '<div class="card-block"><div class="text-center well">'.$locale['uf_108'].'</div></div>';
                    }

                    echo closetabbody();
                echo closetab();
            echo '</div>';
        }
    }

    private static function changeAvatar() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();

        $img_path = IMAGES.'avatars/';
        $modal = '';

        $modal .= openmodal('change-photo', self::setLocale('profile_01'), ['button_id' => 'change-photo']);
        $modal .= openform('change-photo', 'post', FUSION_REQUEST, ['enctype' => TRUE]);

        if (!empty($userdata['user_avatar']) && $userdata['user_avatar'] != '') {
            if (isset($_POST['delete_avatar'])) {
                if ($userdata['user_avatar'] != ''
                    && file_exists($img_path.$userdata['user_avatar'])
                    && is_file($img_path.$userdata['user_avatar'])) {
                    unlink($img_path.$userdata['user_avatar']);
                }

                if (\defender::safe()) {
                    $db = ['user_id' => $userdata['user_id'], 'user_avatar' => ''];
                    dbquery_insert(DB_USERS, $db, 'update');
                    addNotice('success', self::setLocale('profile_notice_01'));
                    redirect(FUSION_REQUEST);
                }
            }

            $modal .= '<div class="form-group display-block overflow-hide m-t-10 m-b-0">';
            $modal .= '<label class="control-label col-xs-12 col-sm-3 col-md-3 col-lg-3 p-l-0">'.$locale['u185'].'</label>';
            $modal .= '<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">';
            $modal .= display_avatar($userdata, '100px', '', FALSE, 'img-thumbnail');
            $modal .= form_checkbox('delete_avatar', $locale['delete'], '', ['reverse_label' => TRUE]);
            $modal .= '</div>';
            $modal .= '</div>'; // .form-group
            $modal .= form_button('save', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-save']);
        } else {
            if (isset($_POST['update'])) {
                $photo = '';

                if (isset($_FILES['user_avatar']) && $_FILES['user_avatar']['name']) {
                    if (!empty($_FILES['user_avatar']) && is_uploaded_file($_FILES['user_avatar']['tmp_name'])) {
                        $upload = (array)form_sanitizer($_FILES['user_avatar'], '', 'user_avatar');
                        if ($upload['error'] === 0) $photo = $upload['image_name'];
                    }
                }

                if (\defender::safe()) {
                    $db = ['user_id' => $userdata['user_id'], 'user_avatar' => $photo];
                    dbquery_insert(DB_USERS, $db, 'update');
                    addNotice('success', self::setLocale('profile_notice_02'));
                    redirect(FUSION_REQUEST);
                }
            }

            $modal .= form_fileinput('user_avatar', $locale['u185'], '', [
                'upload_path'     => $img_path,
                'type'            => 'image',
                'max_byte'        => $settings['avatar_filesize'],
                'max_height'      => $settings['avatar_width'],
                'max_width'       => $settings['avatar_height'],
                'inline'          => TRUE,
                'thumbnail'       => 0,
                'width'           => '100%',
                'delete_original' => FALSE,
                'class'           => 'm-t-10 m-b-0',
                'error_text'      => $locale['u180'],
                'template'        => 'modern',
                'ext_tip'         => sprintf($locale['u184'], parsebytesize($settings['avatar_filesize']), $settings['avatar_width'], $settings['avatar_height'])
            ]);

            $modal .= form_button('update', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-save']);
        }

        $modal .= closeform();
        $modal .= closemodal();

        add_to_footer($modal);
    }
}
