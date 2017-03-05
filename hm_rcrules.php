<?php

/**
 * hMailserver remote rules changer
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
 
$rc_remote_ip = 'YOUR ROUNDCUBE SERVER IP ADDRESS';

/*****************/

if($_SERVER['REMOTE_ADDR'] !== $rc_remote_ip)
{
	header('HTTP/1.0 403 Forbidden');
	exit('You are forbidden!');
}

define('HMS_ERROR', 1);
define('HMS_SUCCESS', 0);

if (empty($_POST['action']) || empty($_POST['email']) || empty($_POST['password']))
	sendResult('Required fields can not be empty.', HMS_ERROR);

$action = $_POST['action'];
$email = $_POST['email'];
$password = $_POST['password'];

try {
	$obApp = new COM("hMailServer.Application", NULL, CP_UTF8);
}
catch (Exception $e) {
	sendResult(trim(strip_tags($e->getMessage())), HMS_ERROR);
}
$temparr = explode('@', $email);
$domain = $temparr[1];
$obApp->Authenticate($email, $password);
try {
	$obAccount = $obApp->Domains->ItemByName($domain)->Accounts->ItemByAddress($email);
		
	switch($action){
		case 'admin':
			$admin = $obAccount->AdminLevel() == 2 ? 1 : 0;
			sendResult(array('admin' => $admin));
		case 'rules_load':
			sendResult(rulesLoad($obAccount->Rules()));
		case 'rule_load':
			sendResult(ruleLoad($obAccount->Rules->ItemByDBID((int)$_POST['rid'])));
		case 'rule_edit':
			if ($rid = (int)$_POST['rid'])
				$obRule = $obAccount->Rules->ItemByDBID($rid);
			else
				$obRule = $obAccount->Rules->Add();

			$obRule->Name = $_POST['name'];
			$obRule->Active = isset($_POST['active']) ?: 0;
			$obRule->UseAND = (int)$_POST['useand'];
			$obRule->Save();
			sendResult(array('rid' => $obRule->ID));
		case 'rule_delete':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->Delete();
			sendResult(HMS_SUCCESS);
		case 'rule_moveup':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->MoveUp();
			sendResult(HMS_SUCCESS);
		case 'rule_movedown':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->MoveDown();
			sendResult(HMS_SUCCESS);
		case 'criteria_load':
			$obCriteria = $obAccount->Rules->ItemByDBID((int)$_POST['rid'])->Criterias->ItemByDBID((int)$_POST['cid']);
			$critdata = array(
				'usepredefined'   => $obCriteria->UsePredefined ?: 0,
				'predefinedfield' => $obCriteria->PredefinedField,
				'headerfield'     => $obCriteria->HeaderField,
				'matchtype'       => $obCriteria->MatchType,
				'matchvalue'      => $obCriteria->MatchValue
			);
			sendResult($critdata);
		case 'criteria_edit':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			if ($cid = (int)$_POST['cid'])
				$obCriteria = $obRule->Criterias->ItemByDBID($cid);
			else
				$obCriteria = $obRule->Criterias->Add();

			$obCriteria->UsePredefined = (int)$_POST['usepredefined'];
			$obCriteria->PredefinedField = (int)$_POST['predefinedfield'];
			$obCriteria->HeaderField = $_POST['headerfield'];
			$obCriteria->MatchType = (int)$_POST['matchtype'];
			$obCriteria->MatchValue = $_POST['matchvalue'];
			$obCriteria->Save();
			$obRule->Save();
			sendResult(array('cid' => $obCriteria->ID));
		case 'criteria_delete':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->Criterias->ItemByDBID((int)$_POST['cid'])->Delete();
			sendResult(HMS_SUCCESS);
		case 'action_load':
			$obAction = $obAccount->Rules->ItemByDBID((int)$_POST['rid'])->Actions->ItemByDBID((int)$_POST['aid']);
			$actdata = array(
				'admin' => $obAccount->AdminLevel() == 2 ? 1 : 0,
				'to' => $obAction->To,
				'imapfolder' => $obAction->IMAPFolder,
				'scriptfunction' => $obAction->ScriptFunction,
				'fromname' => $obAction->FromName,
				'fromaddress' => $obAction->FromAddress,
				'subject' => $obAction->Subject,
				'body' => $obAction->Body,
				'headername' => $obAction->HeaderName,
				'value' => $obAction->Value,
				'type' => $obAction->Type
			);
			sendResult($actdata);
		case 'action_edit':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			if ($aid = (int)$_POST['aid'])
				$obAction = $obRule->Actions->ItemByDBID($aid);
			else
				$obAction = $obRule->Actions->Add();

			$obAction->Type = (int)$_POST['type'];
			$obAction->To = $_POST['to'];
			$obAction->IMAPFolder = $_POST['imapfolder'];
			$obAction->ScriptFunction = $_POST['scriptfunction'];
			$obAction->FromName = $_POST['fromname'];
			$obAction->FromAddress = $_POST['fromaddress'];
			$obAction->Subject = $_POST['subject'];
			$obAction->Body = $_POST['body'];
			$obAction->HeaderName = $_POST['headername'];
			$obAction->Value = $_POST['value'];
			$obAction->Save();
			$obRule->Save();
			sendResult(array('aid' => $obAction->ID));
		case 'action_moveup':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->Actions->ItemByDBID((int)$_POST['aid'])->MoveUp();
			sendResult(HMS_SUCCESS);
		case 'action_movedown':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->Actions->ItemByDBID((int)$_POST['aid'])->MoveDown();
			sendResult(HMS_SUCCESS);
		case 'action_delete':
			$obRule = $obAccount->Rules->ItemByDBID((int)$_POST['rid']);
			$obRule->Actions->ItemByDBID((int)$_POST['aid'])->Delete();
			sendResult(HMS_SUCCESS);
	}
	sendResult('Action unknown', HMS_ERROR);
}
catch (Exception $e) {
	sendResult(trim(strip_tags($e->getMessage())), HMS_ERROR);
}

function sendResult($message, $error = 0)
{
	$out = array('error' => $error, 'text' => $message);
	exit(serialize($out));
}

function rulesLoad($obRules)
{
	$count = $obRules->Count();
	$data = array();

	for ($i = 0; $i < $count; $i++) {
		$obRule = $obRules->Item($i);
		$data[] = array(
			'name'    => $obRule->Name,
			'rid'     => $obRule->ID,
			'enabled' => $obRule->Active ?: 0
		);
	}
	return $data;
}

function ruleLoad($obRule)
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
