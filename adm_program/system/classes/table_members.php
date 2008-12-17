<?php
/******************************************************************************
 * Klasse fuer Datenbanktabelle adm_members
 *
 * Copyright    : (c) 2004 - 2008 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu ein Memberobjekt zu erstellen. 
 * Eine Mitgliedschaft kann ueber diese Klasse in der Datenbank verwaltet werden
 *
 * Neben den Methoden der Elternklasse TableAccess, stehen noch zusaetzlich
 * folgende Methoden zur Verfuegung:
 *
 * startMembership($rol_id, $usr_id, $leader = "")
 *                      - Methode setzt alle notwendigen Daten um eine 
 *                        Mitgliedschaft zu beginnen bzw. zu aktualisieren
 * stopMembership($rol_id, $usr_id)
 *                      - Methode setzt alle notwendigen Daten um eine 
 *                        Mitgliedschaft zu beginnen
 *
 *****************************************************************************/

require_once(SERVER_PATH. "/adm_program/system/classes/table_access.php");

class TableMembers extends TableAccess
{
    // Konstruktor
    function TableMembers(&$db)
    {
        $this->db            =& $db;
        $this->table_name     = TBL_MEMBERS;
        $this->column_praefix = "mem";
        
        $this->clear();
    }
    
    function readData($rol_id, $usr_id)
    {
        if(is_numeric($rol_id) && is_numeric($usr_id))
        {
            $condition = "       mem_rol_id = $rol_id
                             AND mem_usr_id = $usr_id ";
            parent::readData(0, $condition);
            
            $this->setValue("mem_rol_id", $rol_id);
            $this->setValue("mem_usr_id", $usr_id);
        }
    }       

    // Speichert die Mitgliedschaft und aktualisiert das
    function save()
    {
        global $g_current_session;
        $fields_changed = $this->columnsValueChanged;
        
        parent::save();
        
        if($fields_changed && is_object($g_current_session))
        {
            // einlesen des entsprechenden Userobjekts, da Aenderungen 
            // bei den Rollen vorgenommen wurden 
            $g_current_session->renewUserObject($this->getValue("mem_usr_id"));
        }
    } 
    
    // Methode setzt alle notwendigen Daten um eine Mitgliedschaft zu beginnen bzw. zu aktualisieren
    function startMembership($rol_id, $usr_id, $leader = "")
    {
        if($this->getValue("mem_rol_id") != $rol_id
        || $this->getValue("mem_usr_id") != $usr_id)
        {
            $this->readData($rol_id, $usr_id);
        }

        // Beginn nicht ueberschreiben, wenn schon existiert
        if(strlen($this->getValue("mem_begin")) == 0)
        {
            $this->setValue("mem_begin", DATE_NOW);
        }
        // Leiter sollte nicht ueberschrieben werden, wenn nicht uebergeben wird
        if(strlen($leader) == 0)
        {
            if($this->new_record == true)
            {
                $this->setValue("mem_leader", 0);
            }
        }
        else
        {
            $this->setValue("mem_leader", $leader);
        }
        $this->setValue("mem_end", "9999-12-31");
        $this->save();
    }

    // Methode setzt alle notwendigen Daten um eine Mitgliedschaft zu beginnen
    function stopMembership($rol_id, $usr_id)
    {
        if($this->getValue("mem_rol_id") != $rol_id
        || $this->getValue("mem_usr_id") != $usr_id)
        {
            $this->readData($rol_id, $usr_id);
        }

        if($this->new_record == false)
        {
            $this->setValue("mem_end", DATE_NOW);
            $this->save();
        }
    }
}
?>