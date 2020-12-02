<?php

$labels = array();

$labels['rules'] = 'Szabályok';
$labels['editrules'] = 'Szabályok szerkeztése';
$labels['editrule'] = 'Szabály szerkeztése';
$labels['editrulefromtitle'] = 'Edit form for rule';

$labels['newrule'] = 'Új szabály';
$labels['moveup'] = 'Mozgat Fel';
$labels['movedown'] = 'Mozgat Le';
$labels['disabled'] = 'Tiltva';

$labels['rulesettings'] = 'Szabály beállítása';
$labels['name'] = 'Név';
$labels['active'] = 'Aktív';
$labels['useand'] = '"ÉS" kapcsolat';
$labels['useor'] = '"VAGY" kapcsolat';

$labels['criterias'] = 'Kritériumok';
$labels['criteria'] = 'Kritérium';
$labels['field'] = 'Mező';
$labels['comparison'] = 'Összehasonlítás';
$labels['value'] = 'Érték';
$labels['addcriteria'] = 'Új kritérium hozzáadása';

$labels['newcriteria'] = 'Új kritérium';
$labels['editcriteria'] = 'Kritérium szerkeztése';
$labels['predefinedfield'] ='Előre definiált mező';
$labels['customheaderfield'] ='Egyedi fejléc mező';
$labels['headerfield'] ='Fejléc mező';
$labels['matchtype'] ='Keresés típusa';
$labels['matchvalue'] ='Érték';
$labels['abort'] = 'Elvet';


$labels['actions'] = 'Műveletek';
$labels['action'] = 'Művelet';
$labels['addaction'] = 'Új művelet hozzáadása';

$labels['newaction'] = 'Új művelet';
$labels['editaction'] = 'Művelet szerkeztése';
$labels['fromname'] = 'Kitől (Név)';
$labels['fromaddress'] = 'Kitől (Cím)';
$labels['imapfolder'] = 'IMAP mappa';
$labels['headername'] = 'Fejléc neve';
$labels['scriptfunction'] = 'Script funkció';


// Predefined Field Type
$labels['body'] = 'Üzenet';
$labels['cc'] = 'CC';
$labels['from'] = 'Kitől';
$labels['messagesize'] = 'Üzenet mérete';
$labels['recipientlist'] = 'Címzett lista';
$labels['subject'] = 'Tárgy';
$labels['to'] = 'Kinek';
$labels['unknown'] = 'Ismeretlen';
$labels['deliveryattempts'] = 'Kézbesítési kísérletek';

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
$labels['contains'] = 'Tartalmazza';
$labels['equals'] = 'Egyenlő';
$labels['greaterthan'] = 'Nagyobb mint';
$labels['lessthan'] = 'Kisebb mint';
$labels['notcontains'] = 'Nem tartalmazza';
$labels['notequals'] = 'Nem egyenlő';
$labels['regexmatch'] = 'Reguláris kifejezés';
$labels['wildcard'] = 'Helyettesítő karakter';

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
$labels['deleteemail'] = 'Üzenet törlése';
$labels['forwardemail'] = 'Üzenet továbbítása';
$labels['movetoimapfolder'] = 'Üzenet IMAP mappába helyezése';
$labels['reply'] = 'Válasz';
$labels['runscriptfunction'] = 'Script futtatása';
$labels['setheadervalue'] = 'Fejléc értékének beállítása';
$labels['stopruleprocessing'] = 'Szabály megállítása';
$labels['sendusingroute'] = 'Küldés használt útvonalon';
$labels['createcopy'] = 'Másolat létrehozása és elküldése';
$labels['bindtoaddress'] = 'Kötés helyi IP-címhez';

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
$messages['rulesuccessfullyupdated'] = 'Szabály sikeresen frissítve.';
$messages['actionsuccessfullyupdated'] = 'Művelet sikeresen frissítve.';
$messages['criteriasuccessfullyupdated'] = 'Kritérium sikeresen frissítve.';

$messages['ruledeleteconfirm'] = 'Tényleg törölni kívánja a kijelölt szabályt?';

$messages['internalerror'] = 'Hiba a kiválasztott művelettel.';
//$messages['connecterror'] = $messages['internalerror'] . ' Connection error.';
$messages['loaderror'] = 'Szabály(ok) betöltési hiba.';
$messages['loadconnecterror'] = $messages['loaderror'] . ' Connection error.';
$messages['updateerror'] = 'Szabály frissítési hiba.';
$messages['updateconnecterror'] = $messages['updateerror'] . ' Connection error.';
