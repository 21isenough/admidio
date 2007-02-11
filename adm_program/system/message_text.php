<?php
/******************************************************************************
 * Texte fuer Hinweistexten oder Fehlermeldungen
 *
 * Copyright    : (c) 2004 - 2007 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 *
 ******************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *****************************************************************************/

$message_text = array(
    "anmeldung" =>
        "<p>Deine Daten wurden gespeichert.</p>
        <p>Du kannst dich noch nicht einloggen.<br />
        Sobald deine Anmeldung vom Administrator best�tigt wurde, erh�lst du eine E-Mail.</p>",

    "assign_login" =>
        "Die Logindaten wurden erfolgreich zugeordnet.",

    "assign_login_mail" =>
        "Die Logindaten wurden erfolgreich zugeordnet und der
        Benutzer ist dar�ber per E-Mail benachrichtigt worden.",

    "category_exist" =>
        " Es existiert bereits eine Kategorie in dieser Organisation mit dem Namen.",

    "datum" =>
        "Es wurde kein g�ltiges Datum in das Feld <b>%VAR1%</b> eingegeben.",

    "delete" =>
        "Die Daten wurden gel�scht !",

    "delete_announcement" =>
        "Willst du die Ank�ndigung<br />
        <b>%VAR1%</b><br />wirklich l�schen ?",

    "delete_category" =>
        "<p>Willst du die Kategorie <b>%VAR1%</b> wirklich l�schen ?</p>",

    "delete_date" =>
        "Willst du den Termin<br />
        <b>%VAR1%</b><br />wirklich l�schen ?",

    "delete_role" =>
        "Willst du die Rolle <b>%VAR1%</b> wirklich l�schen ?<br><br>
        Es werden damit auch alle Mitgliedschaften entg�tig entfernt.",

    "delete_field" =>
        "<p>Willst du das Feld <b>%VAR1%</b> wirklich l�schen ?</p>
        <p>Es werden alle Daten, die Benutzer in diesem Feld gespeichert haben, gel�scht.</p>",

    "delete_user" =>
        "<p>Willst du <b>%VAR1%</b> wirklich l�schen ?</p>
        <p>Der Benutzer wird damit physikalisch in der Datenbank gel�scht und ein Zugriff auf
        seine Daten nicht mehr m�glich.</p>",

    "delete_new_user" =>
        "<p>Willst du die Web-Registrierung von
        <b>%VAR1%</b> wirklich l�schen ?</p>",

    "email_invalid" =>
        "Die E-Mail-Adresse ist nicht g�ltig.",

    "field_numeric" =>
        "Das Feld <b>%VAR1%</b> darf nur Zahlen enthalten.<br>
        Korrigier bitte deine Eingabe.",

    "field_exist" =>
        " Es existiert bereits ein Feld in dieser Organisation mit dem Namen.",

    "feld" =>
        "Das Feld <b>%VAR1%</b> ist nicht gef�llt.",

    "felder" =>
        "Es sind nicht alle Felder aufgef�llt worden.",

    "import" =>
        "%VAR1% Datens�tze wurden erfolgreich importiert !",

    "installFolderExists" =>
        "Das Installationsverzeichnis <b>adm_install</b> existiert noch auf dem Server.
         Aus Sicherheitsgr�nden muss dies gel�scht werden!",

    "invalid" =>
        "Ung�ltiger Seitenaufruf !",

    "login_failed" =>
        "Du hast dich innerhalb kurzer Zeit mehrmals mit einem
        falschen Passwort versucht einzuloggen.<br />Aus Sicherheitsgr�nden
        ist dein Zugang f�r <b>15 Minuten</b> gesperrt.",

    "login_name" =>
        "Der gew�hlte Benutzername existiert schon.<br /><br />
        W�hle bitte einen neuen Namen.",

    "login_unknown" =>
        "Der angegebene Benutzername existiert nicht.",

    "login" =>
        "Du hast dich erfolgreich angemeldet.",

    "logout" =>
        "Du wurdest erfolgreich abgemeldet.",

    "module_disabled" =>
        "Dieses Modul wurde nicht freigegeben.",

    "mysql" =>
        "Folgender Fehler trat beim Zugriff auf die Datenbank auf:<br /><br />
        <b>%VAR1%</b>",

    "new_user" =>
        "Bist du sicher, dass der Benutzer noch nicht in der Datenbank existiert ?",

    "noaccept" =>
        "Deine Anmeldung wurde noch nicht vom Administrator best�tigt.<br /><br />
        Einloggen ist nicht m�glich",

    "nodata" =>
        "Es sind keine Daten vorhanden !",

    "no_category_roles" =>
        "Es sind noch keine Rollen f�r diese Kategorie erstellt worden.<br /><br />
        Rollen k�nnen <a href=\"%VAR1%\">hier</a>
        erstellt und gepflegt werden.",

    "no_old_roles" =>
        "Es sind noch keine Rollen aus dem System entfernt worden.<br /><br />
        Erst wenn du in der Rollenverwaltung Rollen l�schst, erscheinen diese automatisch bei
        den \"Entfernten Rollen\".",

    "norights" =>
        "Du hast keine Rechte diese Aktion auszuf�hren",

    "nomembers" =>
        "Es sind keine Anmeldungen vorhanden.",

    "norolle" =>
        "Die Daten k�nnen nicht gespeichert werden.<br />
        Dem Benutzer sind keine Rollen zugeordnet.",

    "no_cookie" =>
        "Der Login kann nicht durchgef�hrt werden, da dein Browser
        das Setzen von Cookies verbietet !<br><br>
        Damit du dich erfolgreich anmelden kannst, musst du in deinem Browser
        einstellen, dass dieser Cookies von %VAR1% akzeptiert.",

    "passwort" =>
        "Das Passwort stimmt nicht mit der Wiederholung �berein.",

    "password_unknown" =>
        "Du hast ein falsches Passwort eingegeben und
        konntest deshalb nicht angemeldet werden.<br /><br />
        �berpr�f bitte dein Passwort und gib dieses dann erneut ein.",

    "remove_member" =>
        "Wollen Sie die Mitgliedschaft des Benutzers %VAR1% bei %VAR2% beenden ?",

    "remove_member_ok" =>
        "Die Mitgliedschaft des Benutzers bei %VAR1% wurde erfolgreich beendet !",

    "role_active" =>
        "Die Rolle <b>%VAR1%</b> wurde wieder auf <b>aktiv</b> gesetzt.",

    "role_inactive" =>
        "Die Rolle <b>%VAR1%</b> wurde auf <b>inaktiv</b> gesetzt.",

    "role_exist" =>
        " Es existiert bereits eine Rolle in dieser Kategorie mit demselben Namen.",

    "save" =>
        "Deine Daten wurden erfolgreich gespeichert.",

    "uhrzeit" =>
        "Es wurde keine sinnvolle Uhrzeit eingegeben.<br /><br />
        Die Uhrzeit muss zwischen 00:00 und 23:59 liegen !",

    "send_new_login" =>
        "M�chtest du <b>%VAR1%</b> eine E-Mail mit dem Benutzernamen
        und einem neuen Passwort zumailen ?",

    "max_members" =>
        "Speichern nicht m�glich, die maximale Mitgliederzahl w�rde �berschritten.",

    "max_members_profile" =>
        "Speichern nicht m�glich, bei der Rolle &bdquo;%VAR1%&rdquo;
        w�rde die maximale Mitgliederzahl �berschritten.",

    "max_members_roles_change" =>
        "Speichern nicht m�glich, die Rolle hat bereits mehr Mitglieder als die von Dir eingegeben Begrenzung.",

    "write_access" =>
        "Der Ordner <b>%VAR1%</b> konnte nicht angelegt werden. Du musst dich an
        den <a href=\"mailto:%VAR2%\">Webmaster</a>
        wenden, damit dieser <acronym title=\"�ber FTP die Dateiattribute auf 0777 bzw. drwxrwxrwx setzen.\">
        Schreibrechte</acronym> f�r den Ordner setzen kann.",


      //Meldungen Anmeldung im Forum
    "loginforum" =>
        "Du hast dich erfolgreich auf Admidio und <br />im Forum <b>%VAR2%</b>
        als User <b>%VAR1%</b> angemeldet.",

    "loginforum_pass" =>
        "Dein Password im Forum %VAR2% wurde auf das Admidio Password zur�ckgesetz.<br>
        Verwende beim n�chsten Login im Forum dein Admidio Password.<br><br>
        Du wurdest erfolgreich auf Admidio und <br />im Forum <b>%VAR2%</b>
        als User <b>%VAR1%</b> angemeldet.",

    "loginforum_admin" =>
        "Dein Administrator Account vom Forum %VAR2% wurde auf den
        Admidio Account zur�ckgesetz.<br>
        Verwende beim n�chsten Login im Forum dein Admidio Username und Password.<br><br>
        Du wurdest erfolgreich auf Admidio und <br />im Forum <b>%VAR2%</b>
        als User <b>%VAR1%</b> angemeldet.",

    "loginforum_new" =>
        "Dein Admidio Account wurde in das Forum %VAR2% exportiert und angelegt.<br>
        Verwende beim n�chsten Login im Forum dein Admidio Username und Password.<br><br>
        Du wurdest erfolgreich auf Admidio und <br />im Forum <b>%VAR2%</b>
        als User <b>%VAR1%</b> angemeldet.",

    "logoutforum" =>
        "Du wurdest erfolgreich auf Admidio und <br />im Forum abgemeldet.",

    "login_name_forum" =>
        "Der gew&auml;hlte Benutzername existiert im Forum schon.<br /><br />
        W&auml;hle bitte einen neuen Namen.",

      //Ende Meldungen Anmeldung im Forum

    //Fahlermeldungen Mitgliederzuordnung
    "members_changed" =>
        "Die &Auml;nderungen wurden erfolgreich gespeichert.",

    //Fehlermeldungen Linkmodul
    "delete_link" =>
        "Willst Du den Link<br />
        <b>%VAR1%</b><br />wirklich l�schen ?",


    //Fehlermeldungen Gaestebuchmodul
    "delete_gbook_entry" =>
        "Willst Du den G�stebucheintrag von<br />
        <b>%VAR1%</b><br />wirklich l�schen ?",

    "delete_gbook_comment" =>
        "Willst Du den Kommentar von<br />
        <b>%VAR1%</b><br />wirklich l�schen ?",

    "flooding_protection" =>
        "Dein letzter Eintrag im G�stebuch <br />
         liegt weniger als %VAR1% Sekunden zur�ck.",
    //Ende Fehlermeldungen Gaestebuchmodul

        //Fehlermeldungen Profilfoto
    "profile_photo_update" =>
        "Das neue Profilfoto wurde erfolgreich gespeichert.",

    "profile_photo_update_cancel" =>
        "Der Vorgang wurde abgebrochen.",

    "profile_photo_nopic" =>
        "Es wurde keine Bilddatei ausgew�hlt.",

    "profile_photo_deleted" =>
          "Das Profilfoto wurde gel&ouml;scht.",

    "profile_photo_2big" =>
        "Das hochgeladene Foto �bersteigt die vom Server zugelassene
        Dateigr��e von %VAR1%B.",


    //Fehlermeldungen Fotomodul
    "no_photo_folder"=>
        "Der Ordner adm_my_files/photos wurde nicht gefunden.",

    "photodateiphotoup" =>
        "Du hast keine Bilddatei ausgew�hlt, die hinzugef�gt
        werden sollen.<br />",

    "photoverwaltunsrecht" =>
        "Nur eingeloggte Benutzer mit Fotoverwaltungsrecht d�rfen Fotos verwalten.<br />",

    "dateiendungphotoup" =>
        "Die ausgew�hlte Datei ist nicht im JPG-Format gespeichert.<br />",

    "startdatum" =>
        "Es muss ein g�ltiges  Startdatum f�r die Veranstalltung eingegeben werden.<br />",

    "enddatum" =>
        "Das eingegebene Enddatum ist ung�ltig.<br />",

    "startvorend" =>
        "Das eingegebene Enddatum liegt vor dem Anfangsdatum.<br />",

    "veranstaltung" =>
        "Es muss ein Name f�r die Veranstaltung eingegeben weden.<br />",

    "delete_veranst" =>
        "Willst du die Veranstaltung:<br />
        <b>%VAR1%</b><br />wirklich l�schen ?<br>
        Alle enthaltenen Unterveranstaltungen und Bilder gehen verloren.",

    "delete_photo" =>
        "Soll das ausgew�hlte Foto wirklich gel�scht werden?",

    "photo_deleted" =>
        "Das Foto wurde erfolgreich gel�scht.",

    "photo_2big" =>
        "Mindestens eins der hochgeladenen Fotos �bersteigt die vom Server zugelassene
        Dateigr��e von %VAR1%B.",

    "empty_photo_post" =>
        "Die Seite wurde ung�ltig aufgerufen oder die Datei(en) konnte nicht hochgeladen werden.<br />
        Vermutlich wurde die vom Server vorgegebene, maximale Uploadgr��e,
        von %VAR1%B �bersteigen!",
    //Ende Fehlermeldungen Fotomodul


    //Fehlermeldungen Downloadmodul
    "invalid_folder" =>
        "Sie haben einen ung�ltigen Ordner aufgerufen !",

    "invalid_folder" =>
        "Sie haben eine ung�ltigen Datei aufgerufen !",

    "invalid_file_name" =>
        "Der ausgw�hlte Dateiname enth�lt ung�ltige Zeichen !<br><br>
        W�hle bitte einen anderen Namen f�r die Datei aus.",

    "invalid_file_extension" =>
        "Du kannst keine PHP, HTML oder Perl Dateien hochladen.",

    "file_not_exist" =>
        "Die ausgew�hlte Datei existiert nicht.",

    "folder_not_exist" =>
        "Der aufgerufene Ordner existiert nicht.",

    "delete_file_folder" =>
        "Willst du die Datei / den Ordner <b>%VAR1%</b> wirklich l�schen ?",

    "delete_file" =>
        "Die Datei <b>%VAR1%</b> wurde gel�scht.",

    "delete_folder" =>
        "Der Ordner <b>%VAR1%</b> wurde gel�scht.",

    "delete_error" =>
        "Beim L�schen ist ein unbekannter Fehler aufgetreten.",

    "upload_file" =>
        "Die Datei <b>%VAR1%</b> wurde hochgeladen.",

    "create_folder" =>
        "Der Ordner <b>%VAR1%</b> wurde angelegt.",

    "folder_exists" =>
        "Der Ordner <b>%VAR1%</b> existiert bereits!<br><br>
        W�hle bitte einen anderen Namen f�r den neuen Ordner aus.",

    "file_exists" =>
        "Die Datei <b>%VAR1%</b> existiert bereits!<br><br>
        W�hle bitte einen anderen Dateinamen aus.",

    "rename_folder" =>
        "Der Ordner <b>%VAR1%</b> wurde umbenannt.",

    "rename_file" =>
        "Die Datei <b>%VAR1%</b> wurde umbenannt.",

    "file_2big" =>
        "Die hochgeladene Datei �bersteigt die zul�ssige
        Dateigr��e von %VAR1%KB.",

    "file_2big_server" =>
        "Die hochgeladene Datei �bersteigt die vom Server zugelassene
        Dateigr��e von %VAR1%B.",

    "empty_upload_post" =>
        "Die Seite wurde ung�ltig aufgerufen oder die Datei konnte nicht hochgeladen werden.<br>
        Vermutlich wurde die vom Server vorgegebene, maximale Uploadgr��e, von %VAR1%B �bersteigen!",

    "file_upload_error" =>
        "Beim Hochladen der Datei <b>%VAR1%</b> ist ein unbekannter Fehler aufgetreten.",
    //Ende Fehlermeldungen Downloadmodul


    //Fehlermeldungen Mailmodul
    "mail_send" =>
        "Die E-Mail wurde erfolgreich an <b>%VAR1%</b> versendet.",

    "mail_not_send" =>
        "Die E-Mail konnte leider nicht an <b>%VAR1%</b> gesendet werden.",

    "attachment" =>
        "Dein Dateinanhang konnte nicht hochgeladen werden.<br />
        Vermutlich ist das Attachment zu gro�!",

    "attachment_or_invalid" =>
        "Die Seite wurde ung�ltig aufgerufen oder Dein Dateinanhang konnte nicht hochgeladen werden.<br />
        Vermutlich ist das Attachment zu gro�!",

    "mail_rolle" =>
        "Bitte w�hle eine Rolle als Adressat der Mail aus!",

    "profile_mail" =>
        "In Ihrem <a href=\"%VAR1%\">Profil</a>
        ist keine g�ltige Emailadresse hinterlegt!",

    "role_empty" =>
        "Die von Ihnen ausgew�hlte Rolle enth�lt keine Mitglieder
         mit g�ltigen Mailadressen, an die eine Mail versendet werden kann!",

    "usrid_not_found" =>
        "Die Userdaten der �bergebenen ID konnten nicht gefunden werden!",

    "usrmail_not_found" =>
        "Der User hat keine g�ltige Mailadresse in seinem Profil hinterlegt!",
    //Ende Fehlermeldungen Mailmodul


    //Fehlermeldungen RSSmodul
    "rss_disabled" =>
        "Die RSS-Funktion wurde vom Webmaster deaktiviert",
    //Ende Fehlermeldungen RSSmodul

    //Fehlermeldungen Capcha-Klasse
    "captcha_code" =>
        "Der Best�tigungscode wurde falsch eingegeben.",
    //Ende Fehlermeldungen Capcha-Klasse


    //Fehlermeldungen Servereinstellungen
    "no_file_upload_server" =>
        "Die Servereinstellungen lassen keine Dateiuploads zu.",
    //Fehlermeldungen Servereinstellungen

    "default" =>
        "Es ist ein Fehler aufgetreten.<br><br>
        Der gesuchte Hinweis <b>%VAR1%</b> konnte nicht gefunden werden !"
 )
?>
