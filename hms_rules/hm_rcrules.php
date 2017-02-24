<?php

/**
 * hMailserver remote rules changer
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
 
$rc_remote_ip = 'YOUR ROUNDCUBE IP ADDRESS';

/*****************/

if($_SERVER['REMOTE_ADDR'] !== $rc_remote_ip)
{
	header('HTTP/1.0 403 Forbidden');
	exit('You are forbidden!');
}

define('HMS_ERROR',1);

if (empty($_POST['action']) || empty($_POST['email']) || empty($_POST['password']))
	sendResult('Required fields can not be empty.',HMS_ERROR);

$action = $_POST['action'];
$email = $_POST['email'];
$password = $_POST['password'];

try {
	$obApp = new COM("hMailServer.Application", NULL, CP_UTF8);
}
catch (Exception $e) {
	sendResult(trim(strip_tags($e->getMessage())),HMS_ERROR);
}
$temparr = explode('@', $email);
$domain = $temparr[1];
$obApp->Authenticate($email, $password);
try {
	$obAccount = $obApp->Domains->ItemByName($domain)->Accounts->ItemByAddress($email);
		
	switch($action){
		case 'admin':
			$admin = $obAccount->AdminLevel() == 2 ? 1 : 0;
			return array('admin' => $admin);
		case 'rules_load':
			sendResult(rulesLoad($obAccount->Rules()));
		case 'rule_load':
			sendResult(ruleLoad($obAccount->Rules->ItemByDBID((int)$_POST['rid'])));
		case 'rule_edit':
			if ((int)$_POST['rid'])
				$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			else
				$rule = $obAccount->Rules->Add();

			$rule->Name = $_POST['name'];
			$rule->Active = $_POST['active'] == null ? 0 : 1;
			$rule->UseAND = $_POST['useand'];
			$rule->Save();
			sendResult(array('rid' => $rule->ID));
		case 'rule_delete':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->Delete();
			sendResult(HMS_SUCCESS);
		case 'rule_moveup':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->MoveUp();
			sendResult(HMS_SUCCESS);
		case 'rule_movedown':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->MoveDown();
			sendResult(HMS_SUCCESS);
		case 'criteria_load':
			$criteria = $obAccount->Rules->ItemByDBID((int)$_POST['rid'])->Criterias->ItemByDBID((int)$_POST['cid']);
			$critdata=array();
			$critdata['usepredefined'] = $criteria->UsePredefined ?: 0;
			$critdata['predefinedfield'] = $criteria->PredefinedField;
			$critdata['headerfield'] = $criteria->HeaderField;
			$critdata['matchtype'] = $criteria->MatchType;
			$critdata['matchvalue'] = $criteria->MatchValue;
			sendResult($critdata);
		case 'criteria_edit':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			if ($data['cid'])
				$criteria = $rule->Criterias->ItemByDBID((int)$_POST['cid']);
			else
				$criteria = $rule->Criterias->Add();

			$criteria->UsePredefined = (int)$_POST['usepredefined'];
			$criteria->PredefinedField = (int)$_POST['predefinedfield'];
			$criteria->HeaderField = $_POST['headerfield'];
			$criteria->MatchType = (int)$_POST['matchtype'];
			$criteria->MatchValue = $_POST['matchvalue'];
			$criteria->Save();
			$rule->Save();
			sendResult(array('cid' => $criteria->ID));
		case 'criteria_delete':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->Criterias->ItemByDBID((int)$_POST['cid'])->Delete();
			sendResult(HMS_SUCCESS);
		case 'action_load':
			$action = $obAccount->Rules->ItemByDBID((int)$_POST['rid'])->Actions->ItemByDBID((int)$_POST['aid']);
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
			sendResult($actdata);
		case 'action_edit':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			if ((int)$_POST['aid'])
				$action = $rule->Actions->ItemByDBID((int)$_POST['aid']);
			else
				$action = $rule->Actions->Add();

			$action->Type = (int)$_POST['type'];
			$action->To = $_POST['to'];
			$action->IMAPFolder = $_POST['imapfolder'];
			$action->ScriptFunction = $_POST['scriptfunction'];
			$action->FromName = $_POST['fromname'];
			$action->FromAddress = $_POST['fromaddress'];
			$action->Subject = $_POST['subject'];
			$action->Body = $_POST['body'];
			$action->HeaderName = $_POST['headername'];
			$action->Value = $_POST['value'];
			$action->Save();
			$rule->Save();
			sendResult(array('aid' => $action->ID));
		case 'action_moveup':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->Actions->ItemByDBID((int)$_POST['aid'])->MoveUp();
			sendResult(HMS_SUCCESS);
		case 'action_movedown':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->Actions->ItemByDBID((int)$_POST['aid'])->MoveDown();
			sendResult(HMS_SUCCESS);
		case 'action_delete':
			$rule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$rule->Actions->ItemByDBID((int)$_POST['aid'])->Delete();
			sendResult(HMS_SUCCESS);
	}
	sendResult('Action unknown.',HMS_ERROR);
}
catch (Exception $e) {
	sendResult(trim(strip_tags($e->getMessage())),HMS_ERROR);
}

function sendResult($message,$error=0)
{
	$out=array('error'=>$error,'text'=>$message);
	exit(serialize($out));
}

function rulesLoad($rules)
{
	$count = $rules->Count();
	$data=array();

	for ($i = 0; $i < $count; $i++) {
		$rule = $rules->Item($i);
		$data[]=array(
			'name'	=> $rule->Name,
			'rid'	 => $rule->ID,
			'enabled' => $rule->Active ?: 0
		);
	}
	return $data;
}

function ruleLoad($rule)
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
