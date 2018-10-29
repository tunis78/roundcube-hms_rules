<?php

$labels = array();

$labels['rules'] = 'Regeln';
$labels['editrules'] = 'Regeln bearbeiten';
$labels['editrule'] = 'Regel bearbeiten';
$labels['editrulefromtitle'] = 'Änderungsforumular für Regel';

$labels['newrule'] = 'Neue Regel';
$labels['moveup'] = 'Nach oben bewegen';
$labels['movedown'] = 'Nach unten bewegen';
$labels['disabled'] = 'Deaktiviert';

$labels['rulesettings'] = 'Regeleinstellungen';
$labels['name'] = 'Name';
$labels['active'] = 'Aktiviert';
$labels['useand'] = 'Benutze UND';
$labels['useor'] = 'Benutze ODER';

$labels['criterias'] = 'Kriterien';
$labels['criteria'] = 'Kriterium';
$labels['field'] = 'Feld';
$labels['comparison'] = 'Vergleich';
$labels['value'] = 'Wert';
$labels['addcriteria'] = 'Neues Kriterium hinzufügen';

$labels['newcriteria'] = 'Neues Kriterium';
$labels['editcriteria'] = 'Kriterium bearbeiten';
$labels['predefinedfield'] ='Vordefiniertes Feld';
$labels['customheaderfield'] ='Manuelles Header-Feld';
$labels['headerfield'] ='Header-Feld';
$labels['matchtype'] ='Vergleichsart';
$labels['matchvalue'] ='Wert';
$labels['abort'] = 'Abbrechen';


$labels['actions'] = 'Aktionen';
$labels['action'] = 'Aktion';
$labels['addaction'] = 'Neue Aktion hinzufügen';

$labels['newaction'] = 'Neue Aktion';
$labels['editaction'] = 'Aktion bearbeiten';
$labels['fromname'] = 'Von (Name)';
$labels['fromaddress'] = 'Von (Adresse)';
$labels['imapfolder'] = 'IMAP-Ordner';
$labels['headername'] = 'Header-Name';
$labels['scriptfunction'] = 'Script-Funktion';


// Predefined Field Type
$labels['body'] = 'Inhalt der E-Mail';
$labels['cc'] = 'CC';
$labels['from'] = 'Von';
$labels['messagesize'] = 'Nachrichtengröße';
$labels['recipientlist'] = 'Empfängerliste';
$labels['subject'] = 'Betreff';
$labels['to'] = 'An';
$labels['unknown'] = 'Unbekannt';
$labels['deliveryattempts'] = 'Zustellversuche';

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
$labels['contains'] = 'enthält';
$labels['equals'] = 'entspricht';
$labels['greaterthan'] = 'größer als';
$labels['lessthan'] = 'kleiner als';
$labels['notcontains'] = 'enthält nicht';
$labels['notequals'] = 'ist ungleich';
$labels['regexmatch'] = 'regulärer Ausdruck';
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
$labels['deleteemail'] = 'E-Mail löschen';
$labels['forwardemail'] = 'E-Mail weiterleiten';
$labels['movetoimapfolder'] = 'In IMAP-Ordner verschieben';
$labels['reply'] = 'Antworten';
$labels['runscriptfunction'] = 'Funktion ausführen';
$labels['setheadervalue'] = 'Header-Wert setzen';
$labels['stopruleprocessing'] = 'Regelauswärtung stoppen';
$labels['sendusingroute'] = 'Sende über Route';
$labels['createcopy'] = 'Erstelle und sende Kopie';
$labels['bindtoaddress'] = 'An lokale IP-Adresse binden';

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
$messages['rulesuccessfullyupdated'] = 'Regel erfolgreich aktualisiert.';
$messages['actionsuccessfullyupdated'] = 'Aktion erfolgreich aktualisiert.';
$messages['criteriasuccessfullyupdated'] = 'Kriterium erfolgreich aktualisiert.';

$messages['ruledeleteconfirm'] = 'Soll die ausgewählte Regel wirklich gelöscht werden?';

$messages['internalerror'] = 'Fehler bei der ausgewählten Aktion.';
//$messages['connecterror'] = $messages['internalerror'] . ' Connection error.';
$messages['loaderror'] = 'Regeln konnten nicht geladen werden.';
$messages['loadconnecterror'] = $messages['loaderror'] . ' Connection error.';
$messages['updateerror'] = 'Regel konnte nicht aktualisiert werden.';
$messages['updateconnecterror'] = $messages['updateerror'] . ' Connection error.';
