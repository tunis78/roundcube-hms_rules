<?php

/**
 * hMailServer Rules Plugin for Roundcube
 *
 * @version 1.2
 * @author Andreas Tunberg <andreas@tunberg.com>
 *
 * Copyright (C) 2017, Andreas Tunberg
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 */

define('HMS_ERROR', 1);
define('HMS_CONNECT_ERROR', 2);
define('HMS_SUCCESS', 0);

/**
 * Change hMailServer rules plugin
 *
 * Plugin that adds functionality to edit hMailServer rules.
 * It provides common functionality and user interface and supports
 * several backends to finally update the rules.
 *
 * For installation and configuration instructions please read the README file.
 *
 * @author Andreas Tunberg
 */
 
class hms_rules extends rcube_plugin
{
    public $task = "settings";
    private $rc;
    private $driver;
    private $rid = 0;
    private $cid = 0;
    private $aid = 0;
    private $reload = false;
    public $steptitle;

    function init()
    {
        $this->add_texts('localization/');
        $this->include_stylesheet($this->local_skin_path() . '/hms_rules.css');

        $this->register_action('plugin.rules', array($this, 'rules'));
        $this->register_action('plugin.rules-edit', array($this, 'rules_edit'));
        $this->register_action('plugin.rules-actions', array($this, 'rules_actions'));
        $this->register_action('plugin.rules-action', array($this, 'rules_action'));
        $this->register_action('plugin.rules-criteria', array($this, 'rules_criteria'));

        $this->add_hook('settings_actions', array($this, 'settings_actions'));
    }

    function settings_actions($args)
    {
        $args['actions'][] = array(
            'action' => 'plugin.rules',
            'class'  => 'filter',
            'label'  => 'rules',
            'title'  => 'editrules',
            'domain' => 'hms_rules'
        );

        return $args;
    }

    function steptitle()
    {
        return $this->gettext($this->steptitle);
    }

    function abort_button()
    {
        $abort_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-abort',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'hms_rules.abort',
                'prop'    => $this->rid
        ));
        return $abort_button;
    }

    function rules_init()
    {
        $this->rc = rcube::get_instance();
        $this->load_config();
        $this->include_script('hms_rules.js');
    }
    function rules()
    {
        $this->rules_init();

        if ($rid = rcube_utils::get_input_value('_rid', rcube_utils::INPUT_GET)) {
            $this->rc->output->set_env('rules_selected', $rid);
        }

        $this->rc->output->add_handlers(array(
            'ruleframe' => array($this, 'rules_frame'),
            'ruleslist' => array($this, 'rules_list'),
        ));

        $this->rc->output->include_script('list.js');

        $this->rc->output->set_pagetitle($this->gettext('editrules'));
        $this->rc->output->add_label('hms_rules.ruledeleteconfirm', 'hms_rules.disabled');

        $this->rc->output->send('hms_rules.rules');
    }

    function rules_actions()
    {
        $this->rules_init();

        if (($rid = rcube_utils::get_input_value('_rid', rcube_utils::INPUT_POST)) && ($action = rcube_utils::get_input_value('_act', rcube_utils::INPUT_POST))) {

            switch($action) {
                case 'delete':
                    $result = $this->_save(array('action' => 'rule_delete', 'rid' => $rid));
                    $rid='';
                    break;
                case 'moveup':
                    $result = $this->_save(array('action' => 'rule_moveup', 'rid' => $rid));
                    break;
                case 'movedown':
                    $result = $this->_save(array('action' => 'rule_movedown', 'rid' => $rid));
                    break;
            }

            if (!$result) {
                $this->rc->output->command('plugin.rules-reload', $rid);
                $this->rc->output->command('display_message', $this->gettext('rulesuccessfullyupdated'), 'confirmation');
            }
            else {
                $this->rc->output->command('display_message', $result, 'error');
            }
        }
    }

    function rules_edit()
    {
        $this->rules_init();

        if (!empty($_GET['_rid']) || !empty($_POST['_rid'])) {
            $this->rid = rcube_utils::get_input_value('_rid', rcube_utils::INPUT_GPC);
        }

        if ($action = rcube_utils::get_input_value('_act', rcube_utils::INPUT_GPC)) {

            switch($action) {
                case 'save':
                    $dataToSave = array(
                        'action' => 'rule_edit',
                        'rid'    => $this->rid,
                        'name'   => rcube_utils::get_input_value('_name', rcube_utils::INPUT_POST),
                        'active' => rcube_utils::get_input_value('_active', rcube_utils::INPUT_POST),
                        'useand' => rcube_utils::get_input_value('_useand', rcube_utils::INPUT_POST)
                    );
                    $result = $this->_save($dataToSave, true);
                    break;
                case 'criteriadelete':
                    $cid = rcube_utils::get_input_value('_cid', rcube_utils::INPUT_GET);
                    $result = $this->_save(array('action' => 'criteria_delete', 'rid' => $this->rid, 'cid' => $cid));
                    break;
                case 'actionup':
                    $aid = rcube_utils::get_input_value('_aid', rcube_utils::INPUT_GET);
                    $result = $this->_save(array('action' => 'action_moveup', 'rid' => $this->rid, 'aid' => $aid));
                    break;
                case 'actiondown':
                    $aid = rcube_utils::get_input_value('_aid', rcube_utils::INPUT_GET);
                    $result = $this->_save(array('action' => 'action_movedown', 'rid' => $this->rid, 'aid' => $aid));
                    break;
                case 'actiondelete':
                    $aid = rcube_utils::get_input_value('_aid', rcube_utils::INPUT_GET);
                    $result = $this->_save(array('action' => 'action_delete', 'rid' => $this->rid, 'aid' => $aid));
                    break;
            }

            if (!$result || is_array($result)) {
                $this->rc->output->command('display_message', $this->gettext('rulesuccessfullyupdated'), 'confirmation');
                if ($action == 'save') $this->reload = true;
                if (!$this->rid) $this->rid = $result['rid'];
            }
            else {
                if ($result == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('updateconnecterror');
                } else {
                    $error = $this->gettext('updateerror');
                }
                $this->rc->output->command('display_message', $error, 'error');
            }

        }
        $this->steptitle = $this->rid ? 'editrule' : 'newrule';

        $this->register_handler('plugin.steptitle', array($this, 'steptitle'));
        $this->register_handler('plugin.ruleform', array($this, 'rule_edit'));
        $this->rc->output->send('hms_rules.ruleedit');
    }

    function rules_action()
    {
        $this->rules_init();

        if (!empty($_GET['_rid']) || !empty($_POST['_rid'])) {
            $this->rid = rcube_utils::get_input_value('_rid', rcube_utils::INPUT_GPC);
        } else {
            $this->rc->output->command('display_message', $this->gettext('internalerror'), 'error');
            return;
        }
        if (!empty($_GET['_aid']) || !empty($_POST['_aid'])) {
            $this->aid = rcube_utils::get_input_value('_aid', rcube_utils::INPUT_GPC);
        }

        if ($action = rcube_utils::get_input_value('_act', rcube_utils::INPUT_POST)) {

            $dataToSave = array(
                'action'         => 'action_edit', 
                'rid'            => $this->rid,
                'aid'            => $this->aid,
                'to'             => rcube_utils::get_input_value('_to', rcube_utils::INPUT_POST),
                'imapfolder'     => rcube_utils::get_input_value('_imapfolder', rcube_utils::INPUT_POST),
                'scriptfunction' => rcube_utils::get_input_value('_scriptfunction', rcube_utils::INPUT_POST),
                'fromname'       => rcube_utils::get_input_value('_fromname', rcube_utils::INPUT_POST),
                'fromaddress'    => rcube_utils::get_input_value('_fromaddress', rcube_utils::INPUT_POST),
                'subject'        => rcube_utils::get_input_value('_subject', rcube_utils::INPUT_POST),
                'body'           => rcube_utils::get_input_value('_body', rcube_utils::INPUT_POST, true),
                'headername'     => rcube_utils::get_input_value('_headername', rcube_utils::INPUT_POST),
                'value'          => rcube_utils::get_input_value('_value', rcube_utils::INPUT_POST),
                'type'           => rcube_utils::get_input_value('_type', rcube_utils::INPUT_POST)
            );
            $result = $this->_save($dataToSave, true);

            if (!$result || is_array($result)) {
                $this->rc->output->command('display_message', $this->gettext('actionsuccessfullyupdated'), 'confirmation');
                $this->aid = $result['aid'];
            } else {
                if ($result == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('updateconnecterror');
                } else {
                    $error = $this->gettext('updateerror');
                }
                $this->rc->output->command('display_message', $error, 'error');
            }

        }
        $this->steptitle = $this->aid ? 'editaction' : 'newaction';

        $this->register_handler('plugin.steptitle', array($this, 'steptitle'));
        $this->register_handler('plugin.ruleform', array($this, 'action_edit'));
        $this->register_handler('plugin.abort', array($this, 'abort_button'));

        $this->rc->output->send('hms_rules.ruleedit');
    }

    function rules_criteria()
    {
        $this->rules_init();

        if (!empty($_GET['_rid']) || !empty($_POST['_rid'])) {
            $this->rid = rcube_utils::get_input_value('_rid', rcube_utils::INPUT_GPC);
        } else {
            $this->rc->output->command('display_message', $this->gettext('internalerror'), 'error');
            return;
        }
        if (!empty($_GET['_cid']) || !empty($_POST['_cid'])) {
            $this->cid = rcube_utils::get_input_value('_cid', rcube_utils::INPUT_GPC);
        }

        if ($action = rcube_utils::get_input_value('_act', rcube_utils::INPUT_POST)) {

            $dataToSave = array(
                'action'          => 'criteria_edit',
                'rid'             => $this->rid,
                'cid'             => $this->cid,
                'usepredefined'   => rcube_utils::get_input_value('_usepredefined', rcube_utils::INPUT_POST),
                'predefinedfield' => rcube_utils::get_input_value('_predefinedfield', rcube_utils::INPUT_POST),
                'headerfield'     => rcube_utils::get_input_value('_headerfield', rcube_utils::INPUT_POST),
                'matchtype'       => rcube_utils::get_input_value('_matchtype', rcube_utils::INPUT_POST),
                'matchvalue'      => rcube_utils::get_input_value('_matchvalue', rcube_utils::INPUT_POST)
            );
            $result = $this->_save($dataToSave, true);

            if (!$result || is_array($result)) {
                $this->rc->output->command('display_message', $this->gettext('criteriasuccessfullyupdated'), 'confirmation');
                $this->cid = $result['cid'];
            } else {
                if ($result == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('updateconnecterror');
                } else {
                    $error = $this->gettext('updateerror');
                }
                $this->rc->output->command('display_message', $error, 'error');
            }

        }

        $this->steptitle = $this->cid ? 'editcriteria' : 'newcriteria';

        $this->register_handler('plugin.steptitle', array($this, 'steptitle'));
        $this->register_handler('plugin.ruleform', array($this, 'criteria_edit'));
        $this->register_handler('plugin.abort', array($this, 'abort_button'));
        $this->rc->output->send('hms_rules.ruleedit');
    }

    function rule_edit()
    {
        if ($this->reload) {
            $this->rc->output->set_env('rules_reload', $this->rid); 
            return;
        }

        if($this->rid){
            $currentData = $this->_load(array('action' => 'rule_load', 'rid' => $this->rid));

            if (!is_array($currentData)) {
                if ($currentData == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('loadconnecterror');
                }
                else {
                    $error = $this->gettext('loaderror');
                }

                $this->rc->output->command('display_message', $error, 'error');
                return;
            }
        } else {
            $currentData = array(
                'name'   => '',
                'active' => 0,
                'useand' => 1
            );
        }

        $input_act = new html_hiddenfield(array (
                'name'  => '_act',
                'value' => 'save'
        ));

        $input_rid = new html_hiddenfield(array (
                'name'  => '_rid',
                'value' => $this->rid
        ));


        $table = new html_table(array('cols' => 2, 'class' => 'propform'));

        $field_id = 'name';
        $input_name = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_name',
                'id'        => $field_id,
                'maxlength' => 100
        ));

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('name'))));
        $table->add(null, $input_name->show($currentData['name']));

        $field_id = 'active';
        $input_active = new html_checkbox(array (
                'name'  => '_active',
                'id'    => $field_id,
                'value' => 1
        ));

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('active'))));
        $table->add(null, $input_active->show($currentData['active']));

        $field_id = 'useand';
        $input_useand = new html_radiobutton(array (
                'name'  => '_useand',
                'id'    => $field_id,
                'value' => 1
        ));

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('useand'))));
        $table->add(null, $input_useand->show($currentData['useand']));

        $field_id = 'useor';
        $input_useor = new html_radiobutton(array (
                'name'  => '_useand',
                'id'    => $field_id,
                'value' => 0
        ));

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('useor'))));
        $table->add(null, $input_useor->show($currentData['useand']));


        $legend = html::tag('legend', array(), rcube::Q($this->gettext('rulesettings')));
        $fieldset = html::tag('fieldset', array(), $legend . $table->show());

        $form = $this->rc->output->form_tag(array(
            'id'     => 'rule-form',
            'name'   => 'rule-form',
            'class'  => 'propform',
            'method' => 'post',
            'action' => './?_task=settings&_action=plugin.rules-edit',
        ), $input_act->show() . $input_rid->show() . $fieldset . ($this->rid ? $this->rule_criterias($currentData['criterias']) . $this->rule_actions($currentData['actions']) : ''));

        $this->rc->output->add_gui_object('ruleform', 'rule-form');

        return $form;
    }

    function rule_criterias($criterias)
    {
        $table = new html_table(array('cols' => 4, 'class' => 'propform'));
        $table->add_header(null, $this->gettext('field'));
        $table->add_header(null, $this->gettext('comparison'));
        $table->add_header(null, $this->gettext('value'));
        foreach ($criterias as $c) {
            $edit_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-criteria-edit',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'edit',
                'prop'    => $this->rid . ':' . $c['id']
            ));
            $delete_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-criteria-delete',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'delete',
                'prop'    => $this->rid . ':' . $c['id']
            ));

            if ($c['usepredefined'])
                $fieldName = $this->gettext('eFT' . $c['predefinedfield']);
            else
                $fieldName = $c['headerfield'];
            $table->add('tdcriteria', $fieldName);
            $table->add('tdtype', $this->gettext('eMT' . $c['matchtype']));
            $table->add('tdvalue', $c['matchvalue']);
            $table->add('tdactions', $edit_button.$delete_button);
        }

        $add_criteria_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-criteria-edit',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'hms_rules.addcriteria',
                'prop'    => $this->rid . ':0'
        ));

        $legend = html::tag('legend', array(), rcube::Q($this->gettext('criterias')));
        $fieldset = html::tag('fieldset', array(), $legend . $table->show() . $add_criteria_button);

        return $fieldset;

    }

    function rule_actions($actions)
    {
        $table = new html_table(array('cols' => 3, 'class' => 'propform'));
        $table->add_header(null, $this->gettext('action'));
        $count = count($actions);
        for ($i = 0; $i < $count; $i++) {
            $a = $actions[$i];
            $edit_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-action-edit',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'edit',
                'prop'    => $this->rid . ':' . $a['id']
            ));
            $delete_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-action-delete',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'delete',
                'prop'    => $this->rid . ':' . $a['id']
            ));

            $move = '';
            if ($i > 0) {
                $moveup_button = $this->rc->output->button(array(
                    'command' => 'plugin.hmsrule-action-moveup',
                    'type'    => 'input',
                    'class'   => 'button',
                    'label'   => 'hms_rules.moveup',
                    'prop'    => $this->rid . ':' . $a['id']
                ));
                $move .= $moveup_button;
            }
            if ($i < $count-1) {
                $movedown_button = $this->rc->output->button(array(
                    'command' => 'plugin.hmsrule-action-movedown',
                    'type'    => 'input',
                    'class'   => 'button',
                    'label'   => 'hms_rules.movedown',
                    'prop'    => $this->rid . ':' . $a['id']
                ));
                $move .= $movedown_button;
            }
            $table->add('tdaction',$this->gettext('eRA' . $a['type']));
            $table->add('tdmove',$move);
            $table->add('tdactions',$edit_button.$delete_button);
        }

        $add_action_button = $this->rc->output->button(array(
                'command' => 'plugin.hmsrule-action-edit',
                'type'    => 'input',
                'class'   => 'button',
                'label'   => 'hms_rules.addaction',
                'prop'    => $this->rid . ':0'
        ));

        $legend = html::tag('legend', array(), rcube::Q($this->gettext('actions')));
        $fieldset = html::tag('fieldset', array(), $legend . $table->show() . $add_action_button);

        return $fieldset;

    }

    function criteria_edit()
    {
        if($this->cid){ 
            $currentData = $this->_load(array('action' => 'criteria_load', 'rid' => $this->rid, 'cid' => $this->cid));

            if (!is_array($currentData)) {
                if ($currentData == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('loadconnecterror');
                }
                else {
                    $error = $this->gettext('loaderror');
                }

                $this->rc->output->command('display_message', $error, 'error');
                return;
            }
        } else {
            $currentData = array(
                'usepredefined'   => 1,
                'predefinedfield' => 0,
                'matchtype'       => 0,
                'matchvalue'      => '',
                'headerfield'     => ''
            );
        }


        $input_act = new html_hiddenfield(array (
                'name'  => '_act',
                'value' => 'save'
        ));

        $input_rid = new html_hiddenfield(array (
                'name'  => '_rid',
                'value' => $this->rid
        ));

        $input_cid = new html_hiddenfield(array (
                'name'  => '_cid',
                'value' => $this->cid
        ));


        $table = new html_table(array('cols' => 2, 'class' => 'propform'));


        $field_id = 'predefinedfield';
        $input_predefinedfield = new html_radiobutton(array (
                'name'  => '_usepredefined',
                'value' => 1,
                'id'    => $field_id
        ));

        $select_predefinedfield = new html_select(array (
                'name' => '_predefinedfield'
        ));
        $select_predefinedfield->add($this->gettext('eFT1'), 1);
        $select_predefinedfield->add($this->gettext('eFT2'), 2);
        $select_predefinedfield->add($this->gettext('eFT3'), 3);
        $select_predefinedfield->add($this->gettext('eFT4'), 4);
        $select_predefinedfield->add($this->gettext('eFT5'), 5);
        $select_predefinedfield->add($this->gettext('eFT6'), 6);
        $select_predefinedfield->add($this->gettext('eFT7'), 7);
        $select_predefinedfield->add($this->gettext('eFT8'), 8);

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('predefinedfield'))));
        $table->add(null, $input_predefinedfield->show($currentData['usepredefined']) . '&nbsp;' . $select_predefinedfield->show($currentData['predefinedfield']));


        $field_id = 'customheaderfield';
        $input_customheaderfield = new html_radiobutton(array (
                'name'  => '_usepredefined',
                'value' => 0,
                'id'    => $field_id
        ));

        $input_headerfield = new html_inputfield(array (
                'type'      => 'text',
                'title'     => $this->gettext('headerfield'),
                'name'      => '_headerfield',
                'maxlength' => 255
        ));

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('customheaderfield'))));
        $table->add(null, $input_customheaderfield->show($currentData['usepredefined']) . '&nbsp;' . $input_headerfield->show($currentData['headerfield']));


        $field_id = 'matchtype';
        $select_matchtype = new html_select(array (
                'name' => '_matchtype',
                'id'   => $field_id
        ));
        $select_matchtype->add($this->gettext('eMT1'), 1);
        $select_matchtype->add($this->gettext('eMT2'), 2);
        $select_matchtype->add($this->gettext('eMT3'), 3);
        $select_matchtype->add($this->gettext('eMT4'), 4);
        $select_matchtype->add($this->gettext('eMT5'), 5);
        $select_matchtype->add($this->gettext('eMT6'), 6);
        $select_matchtype->add($this->gettext('eMT7'), 7);
        $select_matchtype->add($this->gettext('eMT8'), 8);

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('matchtype'))));
        $table->add(null, $select_matchtype->show($currentData['matchtype']));


        $field_id = 'matchvalue';
        $input_matchvalue = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_matchvalue',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('matchvalue'))));
        $table->add(null, $input_matchvalue->show($currentData['matchvalue']));

        $legend = html::tag('legend', array(), rcube::Q($this->gettext('criteria')));
        $fieldset = html::tag('fieldset', array(), $legend . $table->show());

        $form = $this->rc->output->form_tag(array(
            'id'     => 'rule-form',
            'name'   => 'rule-form',
            'class'  => 'propform',
            'method' => 'post',
            'action' => './?_task=settings&_action=plugin.rules-criteria',
        ), $input_act->show() . $input_rid->show() . $input_cid->show() . $fieldset);

        $this->rc->output->add_gui_object('ruleform', 'rule-form');

        return $form;
    }

    function action_edit()
    {
        if($this->aid){ 
            $currentData = $this->_load(array('action' => 'action_load', 'rid' => $this->rid, 'aid' => $this->aid));

            if (!is_array($currentData)) {
                if ($currentData == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('loadconnecterror');
                }
                else {
                    $error = $this->gettext('loaderror');
                }

                $this->rc->output->command('display_message', $error, 'error');
                return;
            }
        } else {
            $admin = $this->_load(array('action' => 'admin'));

            if (!is_array($admin)) {
                if ($admin == HMS_CONNECT_ERROR) {
                    $error = $this->gettext('loadconnecterror');
                }
                else {
                    $error = $this->gettext('loaderror');
                }

                $this->rc->output->command('display_message', $error, 'error');
                return;
            }

            $currentData=array(
                'admin'          => $admin['admin'],
                'to'             => '',
                'imapfolder'     => '',
                'scriptfunction' => '',
                'fromname'       => '',
                'fromaddress'    => '',
                'subject'        => '',
                'body'           => '',
                'headername'     => '',
                'value'          => '',
                'type'           => 0
            );
        }


        $input_act = new html_hiddenfield(array (
                'name'  => '_act',
                'value' => 'save'
        ));

        $input_rid = new html_hiddenfield(array (
                'name'  => '_rid',
                'value' => $this->rid
        ));

        $input_aid = new html_hiddenfield(array (
                'name'  => '_aid',
                'value' => $this->aid
        ));

        $script = html::script(array(), 'function togglePanel(){$("[id^=\'panel-\'").hide();$("#panel-"+$("#type").val()).show();}jQuery(document).ready(function(){togglePanel()});');

        $table = new html_table(array('cols' => 2, 'class' => 'propform'));

        $field_id = 'type';
        $select_type = new html_select(array (
                'name'     => '_type',
                'onchange' => 'togglePanel()',
                'id'       => $field_id
        ));
        $select_type->add($this->gettext('eRA1'),1);
        $select_type->add($this->gettext('eRA2'),2);
        $select_type->add($this->gettext('eRA3'),3);
        $select_type->add($this->gettext('eRA4'),4);
        $select_type->add($this->gettext('eRA7'),7);
        $select_type->add($this->gettext('eRA6'),6);

        $disabled = $currentData['admin'] ? array() : array('disabled' => 'disabled');
        $select_type->add($this->gettext('eRA5'), 5, $disabled);
        $select_type->add($this->gettext('eRA9'), 9, $disabled);

        //$select_type->add($this->gettext('eRA8'), 8);
        //$select_type->add($this->gettext('eRA10'), 10);

        $table->add('title', html::label($field_id, rcube::Q($this->gettext('action'))));
        $table->add(null, $select_type->show($currentData['type']));

        // panel 1
        $panels .= html::div(array('id' => 'panel-1', 'style' => 'display: none', 'class' => 'panel'), '');


        // panel 2
        $field_id = 'to';
        $input_to = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_to',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel = html::label($field_id, rcube::Q($this->gettext('to'))) . html::br() . $input_to->show($currentData['to']);

        $panels .= html::div(array('id' => 'panel-2', 'style'=>'display: none', 'class' => 'panel'), $panel);


        // panel 3
        $field_id = 'fromname';
        $input_fromname = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_fromname',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel = html::label($field_id, rcube::Q($this->gettext('fromname'))) . html::br() . $input_fromname->show($currentData['fromname']);

        $field_id = 'fromaddress';
        $input_fromaddress = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_fromaddress',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel .= html::br() . html::label($field_id, rcube::Q($this->gettext('fromaddress'))) . html::br() . $input_fromaddress->show($currentData['fromaddress']);

        $field_id = 'subject';
        $input_subject = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_subject',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel .= html::br() . html::label($field_id, rcube::Q($this->gettext('subject'))) . html::br() . $input_subject->show($currentData['subject']);

        $field_id = 'body';
        $input_body = new html_textarea(array (
                'name' => '_body',
                'rows' => '5',
                'id'   => $field_id
        ));

        $panel .= html::br() . html::label($field_id, rcube::Q($this->gettext('body'))) . html::br() . $input_body->show($currentData['body']);

        $panels .= html::div(array('id' => 'panel-3', 'style' => 'display: none', 'class' => 'panel'), $panel);


        // panel 4
        $field_id = 'imapfolder';
        $input_imapfolder = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_imapfolder',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel = html::label($field_id, rcube::Q($this->gettext('imapfolder'))) . html::br() . $input_imapfolder->show($currentData['imapfolder']);

        $panels .= html::div(array('id' => 'panel-4', 'style' => 'display: none', 'class' => 'panel'), $panel);

        // panel 6
        $panels .= html::div(array('id' => 'panel-6', 'style' => 'display: none', 'class' => 'panel'), '');


        // panel 7
        $field_id = 'headername';
        $input_headername = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_headername',
                'id'        => $field_id,
                'maxlength' => 80
        ));

        $panel = html::label($field_id, rcube::Q($this->gettext('headername'))) . html::br() . $input_headername->show($currentData['headername']);

        $field_id = 'value';
        $input_value = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_value',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel .= html::br() . html::label($field_id, rcube::Q($this->gettext('value'))) . html::br() . $input_value->show($currentData['value']);

        $panels .= html::div(array('id' => 'panel-7', 'style' => 'display: none', 'class' => 'panel'), $panel);


        // panel 5
        $field_id = 'scriptfunction';
        $input_scriptfunction = new html_inputfield(array (
                'type'      => 'text',
                'name'      => '_scriptfunction',
                'id'        => $field_id,
                'maxlength' => 255
        ));

        $panel = html::label($field_id, rcube::Q($this->gettext('scriptfunction'))) . html::br() . $input_scriptfunction->show($currentData['scriptfunction']);

        $panels .= html::div(array('id' => 'panel-5', 'style' => 'display: none', 'class' => 'panel'), $panel);


        // panel 9
        $panels .= html::div(array('id' => 'panel-9', 'style' => 'display: none', 'class' => 'panel'), '');


        $table->add('title', null);
        $table->add(null, $panels);

        $legend = html::tag('legend', array(), rcube::Q($this->gettext('action')));
        $fieldset = html::tag('fieldset', array(), $legend . $table->show());


        $form = $this->rc->output->form_tag(array(
            'id'     => 'rule-form',
            'name'   => 'rule-form',
            'class'  => 'propform',
            'method' => 'post',
            'action' => './?_task=settings&_action=plugin.rules-action',
        ), $input_act->show() . $input_rid->show() . $input_aid->show() . $fieldset);

        $this->rc->output->add_gui_object('ruleform', 'rule-form');

        return $script . $form;
    }

    function rules_list($attrib)
    {

        $attrib += array('id' => 'rcmRulesList', 'tagname' => 'table');

        $plugin = $this->rc->plugins->exec_hook('rule_list', array(
            'list' => $this->list_rules(),
            'cols' => array('name')
        ));

        $out = $this->rc->table_output($attrib, $plugin['list'], $plugin['cols'], 'rid');

        $disabled_rules = array();
        foreach ($plugin['list'] as $item) {
            if (!$item['enabled']) {
                $disabled_rules[] = $item['rid'];
            }
        }

        $this->rc->output->add_gui_object('rulelist', $attrib['id']);
        $this->rc->output->set_env('disabled_rules', $disabled_rules);

        return $out;
    }


    function rules_frame($attrib)
    {
        if (!$attrib['id']) {
            $attrib['id'] = 'rcmRulesFrame';
        }

        $this->rc->output->set_env('contentframe', $attrib['id']);

        return $this->rc->output->frame($attrib, true);
    }

    function list_rules()
    {
        $currentData = $this->_load(array('action' => 'rules_load'));

        if (!is_array($currentData)) {
            if ($currentData == HMS_CONNECT_ERROR) {
                $error = $this->gettext('loadconnecterror');
            }
            else {
                $error = $this->gettext('loaderror');
            }

            $this->rc->output->command('display_message', $error, 'error');
            return array();
        }

        return $currentData;
    }


    private function _load($data)
    {
        if (is_object($this->driver)) {
            $result = $this->driver->load($data);
        }
        elseif (!($result = $this->load_driver())){
            $result = $this->driver->load($data);
        }
        return $result;
    }

    private function _save($data, $response = false)
    {
        if (is_object($this->driver)) {
            $result = $this->driver->save($data);
        }
        elseif (!($result = $this->load_driver())){
            $result = $this->driver->save($data);
        }

        if ($response) return $result;

        switch ($result) {
            case HMS_SUCCESS:
                return;
            case HMS_CONNECT_ERROR:
                $reason = $this->gettext('updateconnecterror');
                break;
            case HMS_ERROR:
            default:
                $reason = $this->gettext('updateerror');
        }
        return $reason;
    }

    private function load_driver()
    {
        $driver = $this->rc->config->get('hms_rules_driver', 'hmail');
        $class  = "rcube_{$driver}_rules";
        $file   = $this->home . "/drivers/$driver.php";

        if (!file_exists($file)) {
            rcube::raise_error(array(
                'code' => 600,
                'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "hms_rules plugin: Unable to open driver file ($file)"
            ), true, false);
            return HMS_ERROR;
        }

        include_once $file;

        if (!class_exists($class, false) || !method_exists($class, 'save') || !method_exists($class, 'load')) {
            rcube::raise_error(array(
                'code' => 600,
                'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "hms_rules plugin: Broken driver $driver"
            ), true, false);
            return $this->gettext('internalerror');
        }

        $this->driver = new $class;
    }
}
