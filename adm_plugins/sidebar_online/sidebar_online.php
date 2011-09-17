<?php
/******************************************************************************
 * Sidebar Online
 *
 * Version 1.3.0
 *
 * Plugin zeigt Besucher und aktive registrierte Mitglieder der Homepage an
 *
 * Kompatible ab Admidio-Versions 2.2.0
 *
 * Copyright    : (c) 2004 - 2011 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

// Pfad des Plugins ermitteln
$plugin_folder_pos = strpos(__FILE__, 'adm_plugins') + 11;
$plugin_file_pos   = strpos(__FILE__, 'sidebar_online.php');
$plugin_folder     = substr(__FILE__, $plugin_folder_pos+1, $plugin_file_pos-$plugin_folder_pos-2);

if(!defined('PLUGIN_PATH'))
{
    define('PLUGIN_PATH', substr(__FILE__, 0, $plugin_folder_pos));
}
require_once(PLUGIN_PATH. '/../adm_program/system/common.php');
require_once(PLUGIN_PATH. '/'.$plugin_folder.'/config.php');

// Sprachdatei des Plugins einbinden
$gL10n->addLanguagePath(PLUGIN_PATH. '/'.$plugin_folder.'/languages');
 
// pruefen, ob alle Einstellungen in config.php gesetzt wurden
// falls nicht, hier noch mal die Default-Werte setzen
if(isset($plg_time_online) == false || is_numeric($plg_time_online) == false)
{
    $plg_time_online = 10;
}

if(isset($plg_show_visitors) == false || is_numeric($plg_show_visitors) == false)
{
    $plg_show_visitors = 1;
}

if(isset($plg_show_self) == false || is_numeric($plg_show_self) == false)
{
    $plg_show_self = 1;
}

if(isset($plg_show_users_side_by_side) == false || is_numeric($plg_show_users_side_by_side) == false)
{
    $plg_show_users_side_by_side = 0;
}

if(isset($plg_link_class))
{
    $plg_link_class = strip_tags($plg_link_class);
}
else
{
    $plg_link_class = '';
}

if(isset($plg_link_target))
{
    $plg_link_target = strip_tags($plg_link_target);
}
else
{
    $plg_link_target = '_self';
}

// DB auf Admidio setzen, da evtl. noch andere DBs beim User laufen
$gDb->setCurrentDB();

// Referenzzeit setzen
$ref_date = date('Y.m.d H:i:s', time() - 60 * $plg_time_online);

// User IDs alles Sessons finden, die in genannter aktueller und referenz Zeit sind
$sql = 'SELECT ses_usr_id, usr_login_name
          FROM '. TBL_SESSIONS. ' 
          LEFT JOIN '. TBL_USERS. '
            ON ses_usr_id = usr_id
         WHERE ses_timestamp BETWEEN "'.$ref_date.'" AND "'.DATETIME_NOW.'" ';
if($plg_show_visitors == 0)
{
    $sql = $sql. ' AND ses_usr_id IS NOT NULL ';
}
if($plg_show_self == 0 && $gValidLogin)
{
    $sql = $sql. ' AND ses_usr_id <> '. $gCurrentUser->getValue('usr_id');
}
$sql = $sql. " ORDER BY ses_usr_id ";
$result = $gDb->query($sql);

echo '<div id="plugin_'. $plugin_folder. '" class="admPluginContent">
<div class="admPluginHeader"><h3>'.$gL10n->get('SYS_VISITORS').'</h3></div>
<div class="admPluginBody">';

if($gDb->num_rows($result) > 0)
{
    echo $plg_online_text;
    $usr_id_merker  = 0;
    $count_visitors = 0;
    
    while($row = $gDb->fetch_object($result))
    {
        if($row->ses_usr_id > 0)
        {
            if($row->ses_usr_id != $usr_id_merker)
            {
                echo '<b><a class="'. $plg_link_class. '" target="'. $plg_link_target. '" title="'.$gL10n->get('SYS_SHOW_PROFILE').'" alt="'.$gL10n->get('SYS_SHOW_PROFILE').'"
                    href="'. $g_root_path. '/adm_program/modules/profile/profile.php?user_id='. $row->ses_usr_id. '">'. $row->usr_login_name. '</a></b>';

                // User neben-/untereinander anzeigen
                if($plg_show_users_side_by_side)
                {
                    echo ', ';
                }
                else
                {
                    echo '<br />';
                }
                $usr_id_merker = $row->ses_usr_id;
            }
        }
        else
        {
            $count_visitors++;
        }
    }
    
    if($plg_show_visitors && $count_visitors > 0)
    {
        echo $gL10n->get('PLG_ONLINE_SHOW_PROFILE', $count_visitors);
    }
}
else
{
    echo $gL10n->get('PLG_ONLINE_NO_VISITORS_ON_WEBSITE');
}

echo '</div></div>';

?>