<?php
/******************************************************************************
 * Sidebar Dates
 *
 * Version 1.5.0
 *
 * Plugin das die letzten X Termine in einer schlanken Oberflaeche auflistet
 * und so ideal in einer Seitenleiste eingesetzt werden kann
 *
 * Compatible with Admidio version 2.3.0
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

// create path to plugin
$plugin_folder_pos = strpos(__FILE__, 'adm_plugins') + 11;
$plugin_file_pos   = strpos(__FILE__, 'sidebar_dates.php');
$plugin_folder     = substr(__FILE__, $plugin_folder_pos+1, $plugin_file_pos-$plugin_folder_pos-2);

if(!defined('PLUGIN_PATH'))
{
    define('PLUGIN_PATH', substr(__FILE__, 0, $plugin_folder_pos));
}
require_once(PLUGIN_PATH. '/../adm_program/system/common.php');
require_once(PLUGIN_PATH. '/../adm_program/system/classes/table_date.php');
require_once(PLUGIN_PATH. '/../adm_program/system/classes/module_dates.php');
require_once(PLUGIN_PATH. '/'.$plugin_folder.'/config.php');

// Sprachdatei des Plugins einbinden
$gL10n->addLanguagePath(PLUGIN_PATH. '/'.$plugin_folder.'/languages');

// pruefen, ob alle Einstellungen in config.php gesetzt wurden
// falls nicht, hier noch mal die Default-Werte setzen
if(isset($plg_dates_count) == false || is_numeric($plg_dates_count) == false)
{
    $plg_dates_count = 2;
}

if(isset($plg_show_date_end) == false || is_numeric($plg_show_date_end) == false)
{
    $plg_show_date_end = 1;
}

if(isset($plg_max_char_per_word) == false || is_numeric($plg_max_char_per_word) == false)
{
    $plg_max_char_per_word = 0;
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

if(isset($plg_kal_cat) == false)
{
    $plg_kal_cat = array('all');
}

// Pr�fen ob the Link-URL gesetzt wurde oder leer ist
// wenn leer, dann Standardpfad zum Admidio-Modul
if(isset($plg_link_url) == false || ($plg_link_url) =="")
{
$plg_link_url = $g_root_path.'/adm_program/modules/dates/dates.php';
}

// set database to admidio, sometimes the user has other database connections at the same time
$gDb->setCurrentDB();


//create Object
$plgDates = new ModuleDates();

// read events for output
$plgDatesResult = $plgDates->getDates(0, $plg_dates_count);

$plg_date = new TableDate($gDb);

echo '<div id="plugin_'. $plugin_folder. '" class="admPluginContent">';
if($plg_show_headline==1)
{
    echo '<div class="admPluginHeader"><h3>'.$gL10n->get('PLG_DATES_HEADLINE').'</h3></div>';
}
echo '<div class="admPluginBody">';

if($plgDatesResult['numResults'] > 0)
{
    foreach($plgDatesResult['dates'] as $plg_row)
    {
        $plg_date->clear();
        $plg_date->setArray($plg_row);
        $plg_html_end_date = '';

        echo $plg_date->getValue('dat_begin', $gPreferences['system_date']). '&nbsp;&nbsp;';

        if ($plg_date->getValue('dat_all_day') != 1)
        {
            echo $plg_date->getValue('dat_begin', $gPreferences['system_time']);
        }

        // Bis-Datum und Uhrzeit anzeigen
        if($plg_show_date_end)
        {
            if($plg_date->getValue('dat_begin', $gPreferences['system_date']) != $plg_date->getValue('dat_end', $gPreferences['system_date']))
            {
                $plg_html_end_date .= $plg_date->getValue('dat_end', $gPreferences['system_date']);
            }
            if ($plg_date->getValue('dat_all_day') != 1)
            {
                $plg_html_end_date .= ' '. $plg_date->getValue('dat_end', $gPreferences['system_time']);
            }
            if(strlen($plg_html_end_date) > 0)
            {
                $plg_html_end_date = ' - '. $plg_html_end_date;
            }
        }

        // �ber $plg_link_url wird die Verbindung zum Date-Modul hergestellt.
        echo $plg_html_end_date. '<br /><a class="'. $plg_link_class. '" href="'. $plg_link_url. '?id='. $plg_date->getValue("dat_id"). '" target="'. $plg_link_target. '">';

        if($plg_max_char_per_word > 0)
        {
            $plg_new_headline = '';
            unset($plg_words);

            // Woerter unterbrechen, wenn sie zu lang sind
            $plg_words = explode(' ', $plg_date->getValue('dat_headline'));

            foreach($plg_words as $plg_key => $plg_value)
            {
                if(strlen($plg_value) > $plg_max_char_per_word)
                {
                    $plg_new_headline = $plg_new_headline.' '. substr($plg_value, 0, $plg_max_char_per_word). '-<br />'.
                                    substr($plg_value, $plg_max_char_per_word);
                }
                else
                {
                    $plg_new_headline = $plg_new_headline.' '. $plg_value;
                }
            }
            echo $plg_new_headline. '</a><hr />';
        }
        else
        {
            echo $plg_date->getValue('dat_headline'). '</a><hr />';
        }
    }

    // WEiterleitung �ber $plg_link_url ohne weiteren �bergabeparameter
    echo '<a class="'. $plg_link_class. '" href="'. $plg_link_url. '" target="'. $plg_link_target. '">'.$gL10n->get('PLG_DATES_ALL_EVENTS').'</a>';
}
else
{
    echo $gL10n->get('SYS_NO_ENTRIES');
}

echo '</div></div>';
?>