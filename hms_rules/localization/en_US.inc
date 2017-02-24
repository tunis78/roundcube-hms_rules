<?php

$labels = array();

$labels['rules'] = 'Rules';
$labels['editrules'] = 'Edit rules';
$labels['editrule'] = 'Edit rule';
$labels['editrulefromtitle'] = 'Edit form for rule';

$labels['newrule'] = 'New rule';
$labels['moveup'] = 'Move up';
$labels['movedown'] = 'Move down';
$labels['disabled'] = 'Disabled';

$labels['rulesettings'] = 'Rule settings';
$labels['name'] = 'Name';
$labels['active'] = 'Active';
$labels['useand'] = 'Use AND';
$labels['useor'] = 'Use OR';

$labels['criterias'] = 'Criterias';
$labels['criteria'] = 'Criteria';
$labels['field'] = 'Field';
$labels['comparison'] = 'Comparison';
$labels['value'] = 'Value';
$labels['addcriteria'] = 'Add new criteria';

$labels['newcriteria'] = 'New criteria';
$labels['editcriteria'] = 'Edit criteria';
$labels['predefinedfield'] ='Predefined field';
$labels['customheaderfield'] ='Custom header field';
$labels['headerfield'] ='Header field';
$labels['matchtype'] ='Search type';
$labels['matchvalue'] ='Value';
$labels['abort'] = 'Abort';


$labels['actions'] = 'Actions';
$labels['action'] = 'Action';
$labels['addaction'] = 'Add new action';

$labels['newaction'] = 'New action';
$labels['editaction'] = 'Edit action';
$labels['fromname'] = 'From (Name)';
$labels['fromaddress'] = 'From (Address)';
$labels['imapfolder'] = 'IMAP folder';
$labels['headername'] = 'Header name';
$labels['scriptfunction'] = 'Script function';


// Predefined Field Type
$labels['body'] = 'Body';
$labels['cc'] = 'CC';
$labels['from'] = 'From';
$labels['messagesize'] = 'Message size';
$labels['recipientlist'] = 'Recipient list';
$labels['subject'] = 'Subject';
$labels['to'] = 'To';
$labels['unknown'] = 'Unknown';
$labels['deliveryattempts'] = 'Delivery attempts';

$labels['eFT'] = $labels['unknown'];
$labels['eFT0'] = $labels['unknown'];
$labels['eFT1'] = $labels['from'];
$labels['eFT2'] = $labels['to'];
$labels['eFT3'] = $labels['cc'];
$labels['eFT4'] = $labels['subject'];
$labels['eFT5'] = $labels['body'];
$labels['eFT6'] = $labels['messagesize'];
$labels['eFT7'] = $labels['recipientlist'];
$labels['eFT8'] = $labels['deliveryattempts'];


// Match Type
$labels['contains'] = 'Contains';
$labels['equals'] = 'Equals';
$labels['greaterthan'] = 'Greater than';
$labels['lessthan'] = 'Less than';
$labels['notcontains'] = 'Not contains';
$labels['notequals'] = 'Not equals';
$labels['regexmatch'] = 'Regular expression';
$labels['wildcard'] = 'Wildcard';

$labels['eMT'] = $labels['unknown'];
$labels['eMT0'] = $labels['unknown'];
$labels['eMT1'] = $labels['equals'];
$labels['eMT2'] = $labels['contains'];
$labels['eMT3'] = $labels['lessthan'];
$labels['eMT4'] = $labels['greaterthan'];
$labels['eMT5'] = $labels['regexmatch'];
$labels['eMT6'] = $labels['notcontains'];
$labels['eMT7'] = $labels['notequals'];
$labels['eMT8'] = $labels['wildcard'];


// Rule Action
$labels['deleteemail'] = 'Delete e-mail';
$labels['forwardemail'] = 'Forward email';
$labels['movetoimapfolder'] = 'Move to IMAP folder';
$labels['reply'] = 'Reply';
$labels['runscriptfunction'] = 'Run function';
$labels['setheadervalue'] = 'Set header value';
$labels['stopruleprocessing'] = 'Stop rule processing';
$labels['sendusingroute'] = 'Send using route';
$labels['createcopy'] = 'Create and send copy';
$labels['bindtoaddress'] = 'Bind to local IP address';

$labels['eRA'] = $labels['unknown'];
$labels['eRA0'] = $labels['unknown'];
$labels['eRA1'] = $labels['deleteemail'];
$labels['eRA2'] = $labels['forwardemail'];
$labels['eRA3'] = $labels['reply'];
$labels['eRA4'] = $labels['movetoimapfolder'];
$labels['eRA5'] = $labels['runscriptfunction'];
$labels['eRA6'] = $labels['stopruleprocessing'];
$labels['eRA7'] = $labels['setheadervalue'];
$labels['eRA8'] = $labels['sendusingroute'];
$labels['eRA9'] = $labels['createcopy'];
$labels['eRA10'] = $labels['bindtoaddress'];


$messages = array();
$messages['rulesuccessfullyupdated'] = 'Rule successfully updated.';
$messages['actionsuccessfullyupdated'] = 'Action successfully updated.';
$messages['criteriasuccessfullyupdated'] = 'Criteria successfully updated.';

$messages['ruledeleteconfirm'] = 'Do you really want to delete selected rule?';

$messages['internalerror'] = 'Error with selected action.';
//$messages['connecterror'] = $messages['internalerror'] . ' Connection error.';
$messages['loaderror'] = 'Could not load rule(s).';
$messages['loadconnecterror'] = $messages['loaderror'] . ' Connection error.';
$messages['updateerror'] = 'Could not update rule.';
$messages['updateconnecterror'] = $messages['updateerror'] . ' Connection error.';