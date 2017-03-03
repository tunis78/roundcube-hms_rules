<?php

/**
 * hMailserver rules driver
 *
 * @version 1.0
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

class rcube_hmail_rules
{
    
    public function load($data)
    {
        return $this->data_handler($data);
    }

    public function save($data)
    {
        return $this->data_handler($data);
    }

    private function _rules_load($rules)
    {
        $count = $rules->Count();
        $data=array();

        for ($i = 0; $i < $count; $i++) {
            $rule = $rules->Item($i);
            $data[]=array(
                'name'    => $rule->Name,
                'rid'     => $rule->ID,
                'enabled' => $rule->Active ?: 0
            );
        }

        return $data;
    }

    private function _rule_load($rule)
    {
        $data=array();

        $data['name'] = $rule->Name;
        $data['active'] = $rule->Active ?: 0;
        $data['useand'] = $rule->UseAND ?: 0;

        $data['criterias'] = array();
        $criterias = $rule->Criterias;
        $count = $criterias->Count;
        for ($i = 0; $i < $count; $i++) {
            $c = array();
            $criteria = $criterias->Item($i);
            $c['id'] = $criteria->ID;
            $c['usepredefined'] = $criteria->UsePredefined ?: 0;

            $c['predefinedfield'] = $criteria->PredefinedField;
            $c['headerfield'] = $criteria->HeaderField;

            $c['matchtype'] = $criteria->MatchType;
            $c['matchvalue'] = $criteria->MatchValue;
            $data['criterias'][] = $c;
        }

        $data['actions'] = array();
        $actions = $rule->Actions;
        $count = $actions->Count;
        for ($i = 0; $i < $count; $i++) {
            $a = array();
            $action = $actions->Item($i);
            $a['id'] = $action->ID;
            $a['type'] = $action->Type;
            $data['actions'][] = $a;
        }

        return $data;
    }

    private function data_handler($data)
    {
        $rcmail = rcmail::get_instance();

        try {
            $remote = $rcmail->config->get('hms_rules_remote_dcom', false);
            if ($remote)
                $obApp = new COM("hMailServer.Application", $rcmail->config->get('hms_rules_remote_server'), CP_UTF8);
            else
                $obApp = new COM("hMailServer.Application", NULL, CP_UTF8);
        }
        catch (Exception $e) {
            rcube::write_log('errors', 'Plugin hms_rules (hmail driver): ' . trim(strip_tags($e->getMessage())));
            rcube::write_log('errors', 'Plugin hms_rules (hmail driver): This problem is often caused by DCOM permissions not being set.');
            return HMS_ERROR;
        }

        $username = $rcmail->user->data['username'];
        if (strstr($username,'@')){
            $temparr = explode('@', $username);
            $domain = $temparr[1];
        }
        else {
            $domain = $rcmail->config->get('username_domain',false);
            if (!$domain) {
                rcube::write_log('errors','Plugin hms_rules (hmail driver): $config[\'username_domain\'] is not defined.');
                return HMS_ERROR;
            }
            $username = $username . '@' . $domain;
        }

        $pwd = $rcmail->decrypt($_SESSION['password']);

        $obApp->Authenticate($username, $pwd);
        try {
            $obAccount = $obApp->Domains->ItemByName($domain)->Accounts->ItemByAddress($username);

            switch($data['action']){
                case 'admin':
                    $admin = $obAccount->AdminLevel() == 2 ? 1 : 0;
                    return array('admin' => $admin);
                case 'rules_load':
                    return $this->_rules_load($obAccount->Rules());
                case 'rule_load':
                    return $this->_rule_load($obAccount->Rules->ItemByDBID($data['rid']));
                case 'rule_edit':
                    if ($data['rid'])
                        $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    else
                        $rule = $obAccount->Rules->Add();

                    $rule->Name = $data['name'];
                    $rule->Active = $data['active'] == null ? 0 : 1;
                    $rule->UseAND = $data['useand'];
                    $rule->Save();
                    return array('rid' => $rule->ID);
                case 'rule_delete':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->Delete();
                    return HMS_SUCCESS; 
                case 'rule_moveup':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->MoveUp();
                    return HMS_SUCCESS; 
                case 'rule_movedown':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->MoveDown();
                    return HMS_SUCCESS; 
                case 'criteria_load':
                    $criteria = $obAccount->Rules->ItemByDBID($data['rid'])->Criterias->ItemByDBID($data['cid']);
                    $critdata=array();
                    $critdata['usepredefined'] = $criteria->UsePredefined ?: 0;
                    $critdata['predefinedfield'] = $criteria->PredefinedField;
                    $critdata['headerfield'] = $criteria->HeaderField;
                    $critdata['matchtype'] = $criteria->MatchType;
                    $critdata['matchvalue'] = $criteria->MatchValue;
                    return $critdata;
                case 'criteria_edit':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    if ($data['cid'])
                        $criteria = $rule->Criterias->ItemByDBID($data['cid']);
                    else
                        $criteria = $rule->Criterias->Add();

                    $criteria->UsePredefined = $data['usepredefined'];
                    $criteria->PredefinedField = $data['predefinedfield'];
                    $criteria->HeaderField = $data['headerfield'];
                    $criteria->MatchType = $data['matchtype'];
                    $criteria->MatchValue = $data['matchvalue'];
                    $criteria->Save();
                    $rule->Save();
                    return array('cid' => $criteria->ID);
                case 'criteria_delete':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->Criterias->ItemByDBID($data['cid'])->Delete();
                    return HMS_SUCCESS;
                case 'action_load':
                    $action = $obAccount->Rules->ItemByDBID($data['rid'])->Actions->ItemByDBID($data['aid']);
                    $actdata=array();
                    $actdata['admin'] = $obAccount->AdminLevel() == 2 ? 1 : 0;
                    $actdata['to'] = $action->To;
                    $actdata['imapfolder'] = $action->IMAPFolder;
                    $actdata['scriptfunction'] = $action->ScriptFunction;
                    $actdata['fromname'] = $action->FromName;
                    $actdata['fromaddress'] = $action->FromAddress;
                    $actdata['subject'] = $action->Subject;
                    $actdata['body'] = $action->Body;
                    $actdata['headername'] = $action->HeaderName;
                    $actdata['value'] = $action->Value;
                    $actdata['type'] = $action->Type;
                    return $actdata;
                case 'action_edit':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    if ($data['aid'])
                        $action = $rule->Actions->ItemByDBID($data['aid']);
                    else
                        $action = $rule->Actions->Add();

                    $action->Type = $data['type'];
                    $action->To = $data['to'];
                    $action->IMAPFolder = $data['imapfolder'];
                    $action->ScriptFunction = $data['scriptfunction'];
                    $action->FromName = $data['fromname'];
                    $action->FromAddress = $data['fromaddress'];
                    $action->Subject = $data['subject'];
                    $action->Body = $data['body'];
                    $action->HeaderName = $data['headername'];
                    $action->Value = $data['value'];
                    $action->Save();
                    $rule->Save();
                    return array('aid' => $action->ID);
                case 'action_moveup':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->Actions->ItemByDBID($data['aid'])->MoveUp();
                    return HMS_SUCCESS;
                case 'action_movedown':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->Actions->ItemByDBID($data['aid'])->MoveDown();
                    return HMS_SUCCESS;
                case 'action_delete':
                    $rule = $obAccount->Rules->ItemByDBID($data['rid']);
                    $rule->Actions->ItemByDBID($data['aid'])->Delete();
                    return HMS_SUCCESS; 
            }

        }
        catch (Exception $e) {
            rcube::write_log('errors', 'Plugin hms_rules (hmail driver): ' . trim(strip_tags($e->getMessage())));
            rcube::write_log('errors', 'Plugin hms_rules (hmail driver): This problem is often caused by Authenticate permissions.');
            return HMS_ERROR;
        }
    }
}
