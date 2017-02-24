<?php

$labels['rules'] = 'Regler';
$labels['editrules'] = 'Ändra regler';
$labels['editrule'] = 'Ändra regel';
$labels['editrulefromtitle'] = 'Ändringsformulär för regel';

$labels['newrule'] = 'Ny regel';
$labels['moveup'] = 'Flytta upp';
$labels['movedown'] = 'Flytta ned';
$labels['disabled'] = 'Avaktiverad';

$labels['rulesettings'] = 'Regelinställningar';
$labels['name'] = 'Namn';
$labels['active'] = 'Aktiv';
$labels['useand'] = 'Använd OCH';
$labels['useor'] = 'Använd ELLER';

$labels['criterias'] = 'Kriterier';
$labels['criteria'] = 'Kriteria';
$labels['field'] = 'Fält';
$labels['comparison'] = 'Jämförelse';
$labels['value'] = 'Värde';
$labels['addcriteria'] = 'Lägg till ny kriteria';

$labels['newcriteria'] = 'Ny kriteria';
$labels['editcriteria'] = 'Ändra kriteria';
$labels['predefinedfield'] ='Fördefinierat fält';
$labels['customheaderfield'] ='Valfritt header-fält';
$labels['headerfield'] ='Header-fält';
$labels['matchtype'] ='Söksätt';
$labels['matchvalue'] ='Värde';
$labels['abort'] = 'Avbryt';


$labels['actions'] = 'Åtgärder';
$labels['action'] = 'Åtgärd';
$labels['addaction'] = 'Lägg till ny åtgärd';

$labels['newaction'] = 'New action';
$labels['editaction'] = 'Edit action';
$labels['fromname'] = 'Från (Namn)';
$labels['fromaddress'] = 'Från (Address)';
$labels['imapfolder'] = 'IMAP-mapp';
$labels['headername'] = 'Header-namn';
$labels['scriptfunction'] = 'Skriptfunktion';


// Predefined Field Type
$labels['body'] = 'Body';
$labels['cc'] = 'CC';
$labels['from'] = 'Från';
$labels['messagesize'] = 'Meddelandestorlek';
$labels['recipientlist'] = 'Mottagarlista';
$labels['subject'] = 'Ämne';
$labels['to'] = 'Till';
$labels['unknown'] = 'Okänt';
$labels['deliveryattempts'] = 'Leveransförsök';

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
$labels['contains'] = 'Innehåller';
$labels['equals'] = 'Är';
$labels['greaterthan'] = 'Större än';
$labels['lessthan'] = 'Mindre än';
$labels['notcontains'] = 'Inte innehåller';
$labels['notequals'] = 'Inte är';
$labels['regexmatch'] = 'Reguljärt uttryck';
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
$labels['deleteemail'] = 'Radera epost';
$labels['forwardemail'] = 'Vidarebefodra epost';
$labels['movetoimapfolder'] = 'Flytta till IMAP-mapp';
$labels['reply'] = 'Svara';
$labels['runscriptfunction'] = 'Kör funktion';
$labels['setheadervalue'] = 'Sätt header-värde';
$labels['stopruleprocessing'] = 'Avbryt regelexekvering';
$labels['sendusingroute'] = 'Skicka via rutt';
$labels['createcopy'] = 'Skapa och skicka kopia';
$labels['bindtoaddress'] = 'Lokal IP-address vid leverans';

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
$messages['rulesuccessfullyupdated'] = 'Regel har uppdaterats.';
$messages['actionsuccessfullyupdated'] = 'Åtgärd har uppdaterats.';
$messages['criteriasuccessfullyupdated'] = 'Villkor har uppdaterats.';

$messages['ruledeleteconfirm'] = 'Vill du verkligen ta bort markerad regel?';

$messages['internalerror'] = 'Fel uppstod i vald åtgärd.';
//$messages['connecterror'] = $messages['internalerror'] . ' Connection error.';
$messages['loaderror'] = 'Det gick inte att ladda regeln/regler.';
$messages['loadconnecterror'] = $messages['loaderror'] . ' Anslutningsfel.';
$messages['updateerror'] = 'Det gick inte att uppdatera regeln.';
$messages['updateconnecterror'] = $messages['updateerror'] . ' Anslutningsfel.';
