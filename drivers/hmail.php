<?php

/**
 * hMailserver rules driver
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

    private function _rules_load($obRules)
    {
        $count = $obRules->Count();
        $data = array();

        for ($i = 0; $i < $count; $i++) {
            $obRule = $obRules->Item($i);
            $data[]=array(
                'name'    => $obRule->Name,
                'rid'     => $obRule->ID,
                'enabled' => $obRule->Active ?: 0
            );
        }

        return $data;
    }

    private function _rule_load($obRule)
    {
        $data = array(
            'name'      => $obRule->Name,
            'active'    => $obRule->Active ?: 0,
            'useand'    => $obRule->UseAND ?: 0,
            'criterias' => array(),
            'actions'   => array()
        );
        $obCriterias = $obRule->Criterias;
        $count = $obCriterias->Count;
        for ($i = 0; $i < $count; $i++) {
            $obCriteria = $obCriterias->Item($i);
            $c = array(
                'id'              => $obCriteria->ID,
                'usepredefined'   => $obCriteria->UsePredefined ?: 0,
                'predefinedfield' => $obCriteria->PredefinedField,
                'headerfield'     => $obCriteria->HeaderField,
                'matchtype'       => $obCriteria->MatchType,
                'matchvalue'      => $obCriteria->MatchValue
            );
            $data['criterias'][] = $c;
        }

        $obActions = $obRule->Actions;
        $count = $obActions->Count;
        for ($i = 0; $i < $count; $i++) {
            $obAction = $obActions->Item($i);
            $a = array(
                'id'   => $obAction->ID,
                'type' => $obAction->Type
            );
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
        if (strstr($username, '@')){
            $temparr = explode('@', $username);
            $domain = $temparr[1];
        }
        else {
            $domain = $rcmail->config->get('username_domain', false);
            if (!$domain) {
                rcube::write_log('errors','Plugin hms_rules (hmail driver): $config[\'username_domain\'] is not defined.');
                return HMS_ERROR;
            }
            $username = $username . '@' . $domain;
        }

        $password = $rcmail->decrypt($_SESSION['password']);

        $obApp->Authenticate($username, $password);
        try {
            $obAccount = $obApp->Domains->ItemByName($domain)->Accounts->ItemByAddress($username);

            switch($data['action']){
                case 'admin':
                    $admin = $obAccount->AdminLevel() == 2 ? 1 : 0;
                    return array('admin' => $admin);
                case 'rules_load':
                    return $this->_rules_load($obAccount->Rules());
                case 'rule_load':
                    return $this->_rule_load($obAccount->Rules->ItemByDBID((int)$data['rid']));
                case 'rule_edit':
                    if ($rid = (int)$data['rid'])
                        $obRule = $obAccount->Rules->ItemByDBID($rid);
                    else
                        $obRule = $obAccount->Rules->Add();

                    $obRule->Name = $data['name'];
                    $obRule->Active = $data['active'] == null ? 0 : 1;
                    $obRule->UseAND = (int)$data['useand'];
                    $obRule->Save();
                    return array('rid' => $obRule->ID);
                case 'rule_delete':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->Delete();
                    return HMS_SUCCESS; 
                case 'rule_moveup':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->MoveUp();
                    return HMS_SUCCESS; 
                case 'rule_movedown':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->MoveDown();
                    return HMS_SUCCESS; 
                case 'criteria_load':
                    $obCriteria = $obAccount->Rules->ItemByDBID((int)$data['rid'])->Criterias->ItemByDBID((int)$data['cid']);
                    $critdata=array(
                        'usepredefined'   => $obCriteria->UsePredefined ?: 0,
                        'predefinedfield' => $obCriteria->PredefinedField,
                        'headerfield'     => $obCriteria->HeaderField,
                        'matchtype'       => $obCriteria->MatchType,
                        'matchvalue'      => $obCriteria->MatchValue
                    );
                    return $critdata;
                case 'criteria_edit':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    if ($cid = (int)$data['cid'])
                        $obCriteria = $obRule->Criterias->ItemByDBID($cid);
                    else
                        $obCriteria = $obRule->Criterias->Add();

                    $obCriteria->UsePredefined = (int)$data['usepredefined'];
                    $obCriteria->PredefinedField = (int)$data['predefinedfield'];
                    $obCriteria->HeaderField = $data['headerfield'];
                    $obCriteria->MatchType = (int)$data['matchtype'];
                    $obCriteria->MatchValue = $data['matchvalue'];
                    $obCriteria->Save();
                    $obRule->Save();
                    return array('cid' => $obCriteria->ID);
                case 'criteria_delete':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->Criterias->ItemByDBID((int)$data['cid'])->Delete();
                    return HMS_SUCCESS;
                case 'action_load':
                    $obAction = $obAccount->Rules->ItemByDBID((int)$data['rid'])->Actions->ItemByDBID((int)$data['aid']);
                    $actdata = array(
                        'admin'          => $obAccount->AdminLevel() == 2 ? 1 : 0,
                        'to'             => $obAction->To,
                        'imapfolder'     => $obAction->IMAPFolder,
                        'scriptfunction' => $obAction->ScriptFunction,
                        'fromname'       => $obAction->FromName,
                        'fromaddress'    => $obAction->FromAddress,
                        'subject'        => $obAction->Subject,
                        'body'           => $obAction->Body,
                        'headername'     => $obAction->HeaderName,
                        'value'          => $obAction->Value,
                        'type'           => $obAction->Type
                    );
                    return $actdata;
                case 'action_edit':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    if ($aid = (int)$data['aid'])
                        $obAction = $obRule->Actions->ItemByDBID($aid);
                    else
                        $obAction = $obRule->Actions->Add();

                    $obAction->Type = (int)$data['type'];
                    $obAction->To = $data['to'];
                    $obAction->IMAPFolder = $data['imapfolder'];
                    $obAction->ScriptFunction = $data['scriptfunction'];
                    $obAction->FromName = $data['fromname'];
                    $obAction->FromAddress = $data['fromaddress'];
                    $obAction->Subject = $data['subject'];
                    $obAction->Body = $data['body'];
                    $obAction->HeaderName = $data['headername'];
                    $obAction->Value = $data['value'];
                    $obAction->Save();
                    $obRule->Save();
                    return array('aid' => $obAction->ID);
                case 'action_moveup':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->Actions->ItemByDBID((int)$data['aid'])->MoveUp();
                    return HMS_SUCCESS;
                case 'action_movedown':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->Actions->ItemByDBID((int)$data['aid'])->MoveDown();
                    return HMS_SUCCESS;
                case 'action_delete':
                    $obRule = $obAccount->Rules->ItemByDBID((int)$data['rid']);
                    $obRule->Actions->ItemByDBID((int)$data['aid'])->Delete();
                    return HMS_SUCCESS; 
            }
            return HMS_ERROR;
        }
        catch (Exception $e) {
            rcube::write_log('errors', 'Plugin hms_rules (hmail driver): ' . trim(strip_tags($e->getMessage())));
            rcube::write_log('errors', 'Plugin hms_rules (hmail driver): This problem is often caused by Authenticate permissions.');
            return HMS_ERROR;
        }
    }
}
