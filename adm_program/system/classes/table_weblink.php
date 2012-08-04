<?php
/******************************************************************************
 * Class manages access to database table adm_links
 *
 * Copyright    : (c) 2004 - 2012 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This class creates objects of the database table links. 
 * You can read, change and create weblinks in the database.
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class TableWeblink extends TableAccess
{
	/** Constuctor that will create an object of a recordset of the table adm_links. 
	 *  If the id is set than the specific weblink will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $lnk_id The recordset of the weblink with this id will be loaded. If id isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $lnk_id = 0)
    {
		// read also data of assigned category
		$this->connectAdditionalTable(TBL_CATEGORIES, 'cat_id', 'lnk_cat_id');

		parent::__construct($db, TBL_LINKS, 'lnk', $lnk_id);
    }

    /** Get the value of a column of the database table.
     *  If the value was manipulated before with @b setValue than the manipulated value is returned.
     *  @param $columnName The name of the database column whose value should be read
     *  @param $format For date or timestamp columns the format should be the date/time format e.g. @b d.m.Y = '02.04.2011'. @n
     *                 For text columns the format can be @b plain that would return the original database value without any transformations
     *  @return Returns the value of the database column.
     *          If the value was manipulated before with @b setValue than the manipulated value is returned.
     */ 
    public function getValue($columnName, $format = '')
    {
		global $gL10n;

        if($columnName == 'lnk_description')
        {
			if(isset($this->dbColumns['lnk_description']) == false)
			{
				$value = '';
			}
			elseif($format == 'plain')
			{
				$value = html_entity_decode(strStripTags($this->dbColumns['lnk_description']));
			}
			else
			{
				$value = $this->dbColumns['lnk_description'];
			}
        }
        else
        {
            $value = parent::getValue($columnName, $format);
        }

		if($columnName == 'cat_name' && $format != 'plain')
		{
			// if text is a translation-id then translate it
			if(strpos($value, '_') == 3)
			{
				$value = $gL10n->get(admStrToUpper($value));
			}
		}

        return $value;
    }
    
    /** Set a new value for a column of the database table.
     *  The value is only saved in the object. You must call the method @b save to store the new value to the database
     *  @param $columnName The name of the database column whose value should get a new value
     *  @param $newValue The new value that should be stored in the database field
     *  @param $checkValue The value will be checked if it's valid. If set to @b false than the value will not be checked.  
     *  @return Returns @b true if the value is stored in the current object and @b false if a check failed
     */ 
    public function setValue($columnName, $newValue, $checkValue = true)
    {
        if($columnName == 'lnk_url' && strlen($newValue) > 0)
        {
			// Homepage darf nur gueltige Zeichen enthalten
			if (!strValidCharacters($newValue, 'url'))
			{
				return false;
			}
			// Homepage noch mit http vorbelegen
			if(strpos(admStrToLower($newValue), 'http://')  === false
			&& strpos(admStrToLower($newValue), 'https://') === false )
			{
				$newValue = 'http://'. $newValue;
			}
        }
        elseif($columnName == 'lnk_description')
        {
            return parent::setValue($columnName, $newValue, false);
        }
        return parent::setValue($columnName, $newValue, $checkValue);
    } 
}
?>