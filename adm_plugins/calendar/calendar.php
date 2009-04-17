<?php
/******************************************************************************
 * Sidebar-Kalender
 *
 * Version 1.4.1
 *
 * Plugin das den aktuellen Monatskalender auflistet und die Termine und Geburtstage
 * des Monats markiert und so ideal in einer Seitenleiste eingesetzt werden kann
 *
 * Kompatible ab Admidio-Versions 2.1.0
 * �bergaben: date_id (Format MMJJJJ Beispiel: 052011 = Mai 2011)
 *
 * Copyright    : (c) 2007-2009 Matthias Roberg
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
 
// Pfad des Plugins ermitteln
$plugin_folder_pos = strpos(__FILE__, "adm_plugins") + 11;
$plugin_file_pos   = strpos(__FILE__, "calendar.php");
$plugin_folder     = substr(__FILE__, $plugin_folder_pos+1, $plugin_file_pos-$plugin_folder_pos-2);

if(!defined('PLUGIN_PATH'))
{
    define('PLUGIN_PATH', substr(__FILE__, 0, $plugin_folder_pos));
}
require_once(PLUGIN_PATH. "/../adm_program/system/common.php");
require_once(PLUGIN_PATH. "/$plugin_folder/config.php");

// Auf gesetzte Standardwerte aus config.php �berpr�fen und notfalls setzen
if(isset($plg_ajaxbox) == false)
{
    $plg_ajaxbox = 1;
}
if(isset($plg_link_target) == false)
{
    $plg_link_target = "_self";
}
if(isset($plg_ter_aktiv) == false)
{
    $plg_ter_aktiv = 1;
}
if(isset($plg_ter_login) == false)
{
    $plg_ter_login = 0;
}
if(isset($plg_geb_aktiv) == false)
{
    $plg_geb_aktiv = 0;
}
if(isset($plg_geb_login) == false)
{
    $plg_geb_login = 0;
}
if(isset($plg_geb_icon) == false)
{
    $plg_geb_icon = 0;
}
if(isset($plg_kal_cat) == false)
{
    $plg_kal_cat =  array("all");
}

// Date ID auslesen oder aktuellen Monat und Jahr erzeugen
if(array_key_exists("date_id", $_GET))
	{
    if(is_numeric($_GET["date_id"]) == false)
		{
        $g_message->show("invalid");
		}
	else
		{
		$date_id = $_GET['date_id'];
		$monat = substr($date_id,0,2);
		$jahr = substr($date_id,2,4);
		$heute = 0;
		if($monat == date("m") AND $jahr == date("Y"))
			{
			$heute = date("d");
			}
		}
	}
else
	{
	$monat = date("m");
	$jahr = date("Y");
	$heute = date("d");
	}
$sql_dat = $jahr. "-". $monat;

// DB auf Admidio setzen, da evtl. noch andere DBs beim User laufen
$g_db->setCurrentDB();

// Abfrage der Termine
if($plg_ter_aktiv == 1)
{
	// Ermitteln, welche Kalender angezeigt werden sollen
	if(in_array("all",$plg_kal_cat))
	{
		// ALLE EINTR�GE
		$sql_syntax = "AND dat_global = 0 ";
	}
	else
	{
		$sql_syntax = "AND cat_type = 'DAT' AND (";
		for($i=0;$i<count($plg_kal_cat);$i++)
		{
			$sql_syntax = $sql_syntax. "cat_name = '".$plg_kal_cat[$i]."' OR ";
		}
		$sql_syntax = substr($sql_syntax,0,-4). ") AND cat_id = dat_cat_id ";
	}
	
	// Dummy-Z�hler f�r Schleifen definieren
	$ter = 1;
	$ter_anzahl = 0;
	$ter_aktuell = 0;

	// Datenbankabfrage mit Datum (Monat / Jahr)
	$sql    = "SELECT DISTINCT dat_id, dat_cat_id, dat_begin, dat_all_day, dat_location, dat_headline FROM ". TBL_DATES. ", ".TBL_CATEGORIES." 
				WHERE DATE_FORMAT(dat_begin, '%Y-%m') = '$sql_dat' 
				".$sql_syntax."
				ORDER BY dat_begin ASC";				
						
	$result = $g_db->query($sql);

	while($row = $g_db->fetch_array($result))
	{
		$termin_id[$ter]= $row['dat_id'];
		$termin_tag[$ter]= mysqldatetime("d", $row['dat_begin']);
		$termin_uhr[$ter]= mysqldatetime("h:i", $row['dat_begin']);
		$termin_ganztags[$ter]= $row['dat_all_day'];
		$termin_ort[$ter]= $row['dat_location'];
		$termin_titel[$ter]= $row['dat_headline'];
		$ter++;
	}
}

// Abfrage der Geburtstage
if($plg_geb_aktiv == 1)
{
	// Dummy-Z�hler f�r Schleifen definieren
	$geb = 1;
	$geb_anzahl = 0;
	$geb_aktuell = 0;
	
	// Datenbankabfrage nach Geburtstagen im Monat
	$sql =	"SELECT DISTINCT usr_id, last_name.usd_value as last_name, first_name.usd_value as first_name, birthday.usd_value as birthday
		       FROM ". TBL_ROLES. ", ". TBL_CATEGORIES. ", ". TBL_MEMBERS. ", ". TBL_USERS. "
			  RIGHT JOIN ". TBL_USER_DATA. " AS birthday ON birthday.usd_usr_id = usr_id
				AND birthday.usd_usf_id = ". $g_current_user->getProperty("Geburtstag", "usf_id"). "
				AND MONTH(birthday.usd_value) = $monat
		   	   LEFT JOIN ". TBL_USER_DATA. " AS last_name ON last_name.usd_usr_id = usr_id
				AND last_name.usd_usf_id = ". $g_current_user->getProperty("Nachname", "usf_id"). "
			   LEFT JOIN ". TBL_USER_DATA. " AS first_name ON first_name.usd_usr_id = usr_id
				AND first_name.usd_usf_id = ". $g_current_user->getProperty("Vorname", "usf_id"). "
			  WHERE rol_cat_id = cat_id
                AND cat_org_id = ". $g_current_organization->getValue("org_id"). "
                AND rol_id     = mem_rol_id
                AND mem_usr_id = usr_id
                AND usr_valid  = 1
		      ORDER BY Month(birthday.usd_value) ASC, DayOfMonth(birthday.usd_value) ASC, last_name, first_name";

	$result = $g_db->query($sql);
	$anz_geb = $g_db->num_rows($result);
	
	while($row = $g_db->fetch_array($result))
	{		
		$geb_day[$geb] = mysqldatetime("d", $row['birthday']);
		$geb_year[$geb] = mysqldatetime("y", $row['birthday']);
		$alter[$geb] = $jahr-$geb_year[$geb];
		$geb_name[$geb] = $row['first_name']. " ". $row['last_name'];
		$geb++;
	}
}

// Kalender erstellen
$erster = date("w", mktime(0,0,0,$monat,1,$jahr));
$insgesamt = date("t");
$monate = array("Januar","Februar","M&auml;rz","April","Mai","Juni","Juli","August", "September","Oktober","November","Dezember");
if($erster == 0)
{
	$erster = 7;
}
echo "<table border=\"0\" id=\"plgCalendarTable\">\n";
echo "<th colspan=\"7\" align=\"center\" id=\"plgCalendarHeader\">".$monate[$monat-1]." ".$jahr."</th>";
echo "<tr>\n<td><b>Mo</b></td><td><b>Di</b></td>";
echo "<td><b>Mi</b></td><td><b>Do</b></td>";
echo "<td><b>Fr</b></td><td id=\"plgCalendarSaturday\"><b>Sa</b></td>";
echo "<td id=\"plgCalendarSunday\"><b>So</b></td></tr>\n";
echo "<tr>\n";
$i = 1;
while($i<$erster)
{
	echo "<td>&nbsp;</td>";
	$i++;
}
$i = 1;
while($i<=$insgesamt)
{
	$ter_link = "";
	$ter_title = "";
	$geb_link = "";
	$geb_title = "";
	// Terminanzeige generieren
	if($plg_ter_aktiv == 1)
	{
		$ter_valid = 0;
		if($plg_ter_login == 0)
		{
			$ter_valid = 1;
		}
		if($plg_ter_login == 1)
		{
			if($g_valid_login == TRUE)
			{
				$ter_valid = 1;
			}
		}
		if($ter_valid == 1)
		{
			for($j=1;$j<=$ter-1;$j++)
			{
				if($i==$termin_tag[$j])
				{
					if($ter_anzahl == 0)
					{
						$ter_aktuell = $termin_tag[$j];
						if($plg_ajaxbox == 1)
						{
							$ter_link = "titel=$termin_titel[$j]&uhr=$termin_uhr[$j]&ort=$termin_ort[$j]";
							if($termin_ganztags[$j] == 1)
							{
								$ter_link = $ter_link. "&ganztags=1";
							}
						}
						else
						{
							if($termin_ort[$j] != "")
							{
								$termin_ort[$j] = ", ". $termin_ort[$j];
							}
							if($termin_ganztags[$j] == 1)
							{
								$ter_title = "$termin_titel[$j]$termin_ort[$j], ganzt&auml;gig";
							}
							else
							{
								$ter_title = "$termin_titel[$j], $termin_uhr[$j] Uhr$termin_ort[$j]";							
							}
						}
					}
					$ter_anzahl++;
				}
			}
			if($ter_anzahl >> 0)
			{
				if($i <= 9)
				{
					$plg_link = "$g_root_path/adm_program/modules/dates/dates.php?date=$jahr$monat". "0". $i;
				}
				else
				{
					$plg_link = "$g_root_path/adm_program/modules/dates/dates.php?date=$jahr$monat$i";
				}
			}
			if($ter_anzahl >> 1)
			{
				if($plg_ajaxbox == 1)
				{
					$ter_link = $ter_link. "&weitere=1";
				}
				else
				{
					$ter_title = $ter_title. " (... weitere)";
				}		
			}
		}
	}
	
	// Geburtstagsanzeige generieren
	if($plg_geb_aktiv == 1)
	{
		$geb_valid = 0;
		if($plg_geb_login == 0)
		{
			$geb_valid = 1;
		}
		if($plg_geb_login == 1)
		{
			if($g_valid_login == TRUE)
			{
				$geb_valid = 1;
			}
		}
		if($geb_valid == 1)
		{
			for($k=1;$k<=$geb-1;$k++)
			{
				if($i==$geb_day[$k])
				{
					$geb_aktuell = $geb_day[$k];
					if($plg_ajaxbox == 1)
					{
						if($geb_anzahl >> 0)
						{
							$geb_link = $geb_link. "&";
						}
						$geb_anzahl++;
						$geb_link = $geb_link. "gebname$geb_anzahl=$geb_name[$k]&gebalter$geb_anzahl=$alter[$k]";
					}
					else
					{
						if($geb_anzahl >> 0)
						{
							$geb_title = $geb_title. ", ";
						}
						$geb_anzahl++;
						$geb_title = $geb_title. "$geb_name[$k] ($alter[$k])";
					}
				}
			}
			if($plg_ajaxbox == 1)
			{
				$geb_link = "gebanzahl=$geb_anzahl&". $geb_link;
			}			
		}
	}
	
	$rest = ($i+$erster-1)%7;
	if($i == $heute)
	{
		echo "<td align=center id=\"plgCalendarToday\">";
	}
    else
	{
		echo "<td align=center>";
	}
		
    if($i == $heute OR $i == $ter_aktuell OR $i == $geb_aktuell)
	{
		if($i != $ter_aktuell && $i == $geb_aktuell)
		{
			$plg_link = "#";
		}		
		if($i == $ter_aktuell || $i == $geb_aktuell)
		{
			if($plg_ajaxbox == 1)
			{
				if($ter_link != "" && $geb_link != "")
				{
					$geb_link = "&". $geb_link;
				}
				//echo $ter_link. $geb_link;
				echo "<a href=\"$plg_link\" target=\"$plg_link_target\" onmouseover=\"ajax_showTooltip(event,'$g_root_path/adm_plugins/$plugin_folder/calendar_msg.php?$ter_link$geb_link',this)\" onmouseout=\"ajax_hideTooltip()\">$i</a>";
			}
			else
			{
				if($ter_title != "" && $geb_title != "")
				{
					$geb_title = ", ". $geb_title;
				}
				echo "<a href=\"$plg_link\" title=\"$ter_title$geb_title\" href=\"$plg_link\" target=\"$plg_link_target\">$i</a>";
			}
		}
		else if($i == $heute)
		{
			echo "<span id=\"plgCalendarToday\">$i</span>";
		}
	}
    else if($rest == 6)
	{
		echo "<span id=\"plgCalendarSaturday\">$i</span>";
	}
    else if($rest == 0)
	{
		echo "<span id=\"plgCalendarSunday\">$i</span>";
	}
    else
	{
		echo "$i";
	}
    echo "</td>\n";
    if($rest == 0)
	{
		echo "</tr>\n<tr>\n";
	}
    $i++;
	$ter_anzahl = 0;
	$geb_anzahl = 0;
}
echo "</tr>\n";
echo "</table>\n";

?>