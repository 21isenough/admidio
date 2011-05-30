<?php
/******************************************************************************
 * Klasse fuer den Zugriff auf die Datenbanktabelle adm_categories
 *
 * Copyright    : (c) 2004 - 2011 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu einen Kategorieobjekt zu erstellen.
 * Eine Kategorieobjekt kann ueber diese Klasse in der Datenbank verwaltet werden
 *
 * Es stehen die Methoden der Elternklasse TableAccess zur Verfuegung.
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class TableCategory extends TableAccess
{
    // Konstruktor
    public function __construct(&$db, $cat_id = 0)
    {
        parent::__construct($db, TBL_CATEGORIES, 'cat', $cat_id);
    }

    // Methode bearbeitet die Referenzen, wenn die Kategorie geloescht wird
    // Rueckgabe ist true, wenn das Loeschen erfolgreich war und false, falls es nicht durchgefuehrt werden konnte
    public function delete()
    {
        global $g_current_session;
        
        // pruefen, ob noch mind. eine Kategorie fuer diesen Typ existiert, ansonsten das Loeschen nicht erlauben
        $sql = 'SELECT count(1) AS anzahl FROM '. TBL_CATEGORIES. '
                 WHERE (  cat_org_id = '. $g_current_session->getValue('ses_org_id'). '
                       OR cat_org_id IS NULL )
                   AND cat_type     = \''. $this->getValue('cat_type'). '\'';
        $result = $this->db->query($sql);
        
        $row = $this->db->fetch_array($result);

        if($row['anzahl'] > 1)
        {
            // Luecke in der Reihenfolge schliessen
            $sql = 'UPDATE '. TBL_CATEGORIES. ' SET cat_sequence = cat_sequence - 1
                     WHERE (  cat_org_id = '. $g_current_session->getValue('ses_org_id'). '
                           OR cat_org_id IS NULL )
                       AND cat_sequence > '. $this->getValue('cat_sequence'). '
                       AND cat_type     = \''. $this->getValue('cat_type'). '\'';
            $this->db->query($sql);
    
            // Abhaenigigkeiten loeschen
            if($this->getValue('cat_type') == 'DAT')
            {
                $sql    = 'DELETE FROM '. TBL_DATES. '
                            WHERE dat_cat_id = '. $this->getValue('cat_id');
                $this->db->query($sql);
            }
            elseif($this->getValue('cat_type') == 'INV')
            {
                //Alle Inventarpositionen auslesen, die in der Kategorie enthalten sind
                $sql_inventory = 'SELECT *
                                  FROM '. TBL_INVENTORY. '
                                  WHERE inv_cat_id = '. $this->getValue('cat_id');
                $result_inventory = $this->db->query($sql_subfolders);

                while($row_inventory = $this->db->fetch_object($result_inventory))
                {
                    //Jeder Verleihvorgang zu den einzlenen Inventarpositionen muss geloescht werden
                    $sql    = 'DELETE FROM '. TBL_RENTAL_OVERVIEW. '
                                WHERE rnt_inv_id = '. $row_inventory->inv_id;
                    $this->db->query($sql);
                }

                //Jetzt koennen auch die abhaengigen Inventarposition geloescht werden
                $sql    = 'DELETE FROM '. TBL_INVENTORY. '
                            WHERE inv_cat_id = '. $this->getValue('cat_id');
                $this->db->query($sql);
            }
            elseif($this->getValue('cat_type') == 'LNK')
            {
                $sql    = 'DELETE FROM '. TBL_LINKS. '
                            WHERE lnk_cat_id = '. $this->getValue('cat_id');
                $this->db->query($sql);
            }
            elseif($this->getValue('cat_type') == 'ROL')
            {
                $sql    = 'DELETE FROM '. TBL_MEMBERS. '
                            WHERE mem_rol_id IN (SELECT rol_id FROM '. TBL_ROLES. '
                                                  WHERE rol_cat_id = '.$this->getValue('cat_id').')';
                $this->db->query($sql);
    
                $sql    = 'DELETE FROM '. TBL_ROLES. '
                            WHERE rol_cat_id = '. $this->getValue('cat_id');
                $this->db->query($sql);
            }
            elseif($this->getValue('cat_type') == 'USF')
            {
                $sql    = 'DELETE FROM '. TBL_USER_DATA. '
                            WHERE usd_usf_id IN (SELECT usf_id FROM '. TBL_USER_FIELDS. '
                                                  WHERE usf_cat_id = '.$this->getValue('cat_id').')';
                $this->db->query($sql);
    
                $sql    = 'DELETE FROM '. TBL_USER_FIELDS. '
                            WHERE usf_cat_id = '. $this->getValue('cat_id');
                $this->db->query($sql);
    
                // einlesen aller Userobjekte der angemeldeten User anstossen,
                // da Aenderungen in den Profilfeldern vorgenommen wurden
                $g_current_session->renewUserObject();
            }
            return parent::delete();
        }
        else
        {
            // die letzte Kategorie darf nicht geloescht werden
            return false;
        }
    }

    // diese rekursive Methode ermittelt fuer den uebergebenen Namen einen eindeutigen Namen
    // dieser bildet sich aus dem Namen in Grossbuchstaben und der naechsten freien Nummer (index)
    // Beispiel: 'Gruppen' => 'GRUPPEN_2'
    private function getNewNameIntern($name, $index)
    {
        $newNameIntern = strtoupper(str_replace(' ', '_', $name));
        if($index > 1)
        {
            $newNameIntern = $newNameIntern.'_'.$index;
        }
        $sql = 'SELECT cat_id FROM '.TBL_CATEGORIES.' WHERE cat_name_intern = \''.$newNameIntern.'\'';
        $this->db->query($sql);
        
        if($this->db->num_rows() > 0)
        {
            $index++;
            $newNameIntern = $this->getNewNameIntern($name, $index);
        }
        return $newNameIntern;
    }

    // die Kategorie wird um eine Position in der Reihenfolge verschoben
    public function moveSequence($mode)
    {
        global $g_current_organization;

        // Anzahl orgaunabhaengige ermitteln, da diese nicht mit den abhaengigen vermischt werden duerfen
        $sql = 'SELECT COUNT(1) as count FROM '. TBL_CATEGORIES. '
                 WHERE cat_type = \''. $this->getValue('cat_type'). '\'
                   AND cat_org_id IS NULL ';
        $this->db->query($sql);
        $row = $this->db->fetch_array();

        // die Kategorie wird um eine Nummer gesenkt und wird somit in der Liste weiter nach oben geschoben
        if(admStrToUpper($mode) == 'UP')
        {
            if($this->getValue('cat_org_id') == 0
            || $this->getValue('cat_sequence') > $row['count']+1)
            {
                $sql = 'UPDATE '. TBL_CATEGORIES. ' SET cat_sequence = '.$this->getValue('cat_sequence').'
                         WHERE cat_type = \''. $this->getValue('cat_type'). '\'
                           AND (  cat_org_id = '. $g_current_organization->getValue('org_id'). '
                                          OR cat_org_id IS NULL )
                           AND cat_sequence = '.$this->getValue('cat_sequence').' - 1 ';
                $this->db->query($sql);
                $this->setValue('cat_sequence', $this->getValue('cat_sequence')-1);
                $this->save();
            }
        }
        // die Kategorie wird um eine Nummer erhoeht und wird somit in der Liste weiter nach unten geschoben
        elseif(admStrToUpper($mode) == 'DOWN')
        {
            if($this->getValue('cat_org_id') > 0
            || $this->getValue('cat_sequence') < $row['count'])
            {
                $sql = 'UPDATE '. TBL_CATEGORIES. ' SET cat_sequence = '.$this->getValue('cat_sequence').'
                         WHERE cat_type = \''. $this->getValue('cat_type'). '\'
                           AND (  cat_org_id = '. $g_current_organization->getValue('org_id'). '
                                          OR cat_org_id IS NULL )
                           AND cat_sequence = '.$this->getValue('cat_sequence').' + 1 ';
                $this->db->query($sql);
                $this->setValue('cat_sequence', $this->getValue('cat_sequence')+1);
                $this->save();
            }
        }
    }

    // interne Funktion, die Defaultdaten fur Insert und Update vorbelegt
    public function save($updateFingerPrint = true)
    {
        global $g_current_organization, $g_current_session;
        $fields_changed = $this->columnsValueChanged;

        if($this->new_record)
        {
            if($this->getValue('cat_org_id') > 0)
            {
                $org_condition = ' AND (  cat_org_id  = '. $g_current_organization->getValue('org_id'). '
                                       OR cat_org_id IS NULL ) ';
            }
            else
            {
               $org_condition = ' AND cat_org_id IS NULL ';
            }
            // beim Insert die hoechste Reihenfolgennummer der Kategorie ermitteln
            $sql = 'SELECT COUNT(*) as count FROM '. TBL_CATEGORIES. '
                     WHERE cat_type = "'. $this->getValue('cat_type'). '"
                           '.$org_condition;
            $this->db->query($sql);

            $row = $this->db->fetch_array();

            $this->setValue('cat_sequence', $row['count'] + 1);

            if($this->getValue('cat_org_id') == 0)
            {
                // eine Orga-uebergreifende Kategorie ist immer am Anfang, also Kategorien anderer Orgas nach hinten schieben
                $sql = 'UPDATE '. TBL_CATEGORIES. ' SET cat_sequence = cat_sequence + 1
                         WHERE cat_type = "'. $this->getValue('cat_type'). '"
                           AND cat_org_id IS NOT NULL ';
                $this->db->query($sql);
            }
        }
        
        // wurde der Name veraendert, dann nach einem neuen eindeutigen internen Namen suchen
        if($this->columnsInfos['cat_name']['changed'])
        {
            $this->setValue('cat_name_intern', $this->getNewNameIntern($this->getValue('cat_name'), 1));
        }

        parent::save($updateFingerPrint);

        // Nach dem Speichern noch pruefen, ob Userobjekte neu eingelesen werden muessen,
        if($fields_changed && $this->getValue('cat_type') == 'USF' && is_object($g_current_session))
        {
            // einlesen aller Userobjekte der angemeldeten User anstossen,
            // da Aenderungen in den Profilfeldern vorgenommen wurden
            $g_current_session->renewUserObject();
        }
    }

    // prueft die Gueltigkeit der uebergebenen Werte und nimmt ggf. Anpassungen vor
    public function setValue($field_name, $field_value)
    {
		global $g_current_organization;

        // Systemkategorien duerfen nicht umbenannt werden
        if($field_name == 'cat_name' && $this->getValue('cat_system') == 1)
        {
            return false;
        }
		elseif($field_name == 'cat_default' && $field_value == '1')
		{
			// es darf immer nur eine Default-Kategorie je Bereich geben
			$sql = 'UPDATE '. TBL_CATEGORIES. ' SET cat_default = 0
					 WHERE cat_type = \''. $this->getValue('cat_type'). '\'
					   AND (  cat_org_id IS NOT NULL 
					       OR cat_org_id = '.$g_current_organization->getValue('org_id').')';
			$this->db->query($sql);
		}

        return parent::setValue($field_name, $field_value);
    }
}
?>