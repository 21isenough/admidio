<?php
/*****************************************************************************/
/** @class AutoLogin
 *  @brief Handle auto login with Admidio and manage it in the database
 *
 *  The class search in the database table @b adm_auto_login for the session id.
 *  If there is an entry for that id then it reads the user id and set this 
 *  user to the current session. Now the current session has become a valid user
 *  that is automatically login.
 *  @par Examples
 *  @code // create a valid user login for a Admidio session from auto login
 *  $autoLogin = new AutoLogin($gDb, $gSessionId);
 *  $autoLogin->setValidLogin($gCurrentSession, $_COOKIE['ADMIDIO_ID']);@endcode
 *  @code // delete an auto login
 *  $autoLogin = new AutoLogin($gDb, $gSessionId);
 *  $autoLogin->delete();@endcode
 */
/*****************************************************************************
 *
 *  Copyright    : (c) 2004 - 2013 The Admidio Team
 *  Homepage     : http://www.admidio.org
 *  License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class AutoLogin extends TableAccess
{
	/** Constuctor that will create an object of a recordset of the table adm_auto_login. 
	 *  If the id is set than the specific auto login will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $session The recordset of the auto login with this session will be loaded. If session isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $session = 0)
    {
        parent::__construct($db, TBL_AUTO_LOGIN, 'atl');

		// if not numeric than the session id is commited
		if(is_numeric($session))
		{
			$this->readDataById($session);
		}
		else
		{
			$this->readDataByColumns(array('atl_session_id' => $session));
		}

    }

	/** Save all changed columns of the recordset in table of database. Therefore the class remembers if it's 
	 *  a new record or if only an update is neccessary. The update statement will only update
	 *  the changed columns. If the table has columns for creator or editor than these column
	 *  with their timestamp will be updated.
	 *  The current organization, last login and ip adress will be set per default.
	 *  @param $updateFingerPrint Default @b true. Will update the creator or editor of the recordset if table has columns like @b usr_id_create or @b usr_id_changed
	 */
    public function save($updateFingerPrint = true)
    {
        if($this->new_record)
        {
            // Insert
            global $gCurrentOrganization;
            $this->setValue('atl_org_id', $gCurrentOrganization->getValue('org_id'));
            $this->setValue('atl_last_login', DATETIME_NOW);
            $this->setValue('atl_ip_address', $_SERVER['REMOTE_ADDR']);
            
            // Tabelle aufraeumen, wenn ein neuer Datensatz geschrieben wird
            $this->tableCleanup();
        }
        else
        {
            // Update
            $this->setValue('atl_last_login', DATETIME_NOW);
            $this->setValue('atl_ip_address', $_SERVER['REMOTE_ADDR']);
        }
        parent::save($updateFingerPrint);
    }
    
    /** Method checks the data of the cookie against the data stored in the
     *  database table @b adm_auto_login. If cookie data is ok then the 
     *  user id will be set in the current session. Now there is a valid login
     *  for this user.
     *  @param $session    The Session object of the current Admidio session.
     *  @param $cookieData The data of the cookie @b ADMIDIO_DATA.
     */
    public function setValidLogin(&$session, $cookieData)
    {
    	$dataArray = explode(';', $cookieData);
    
    	if($dataArray[0] == true         // autologin
    	&& is_numeric($dataArray[1]))    // user_id 
    	{   
    		// restore user if saved database user id == cookie user id
    		// if session is inactive than set it to an active session
    		if($this->getValue('atl_usr_id') == $dataArray[1])
    		{
    			$session->setValue('ses_timestamp', DATETIME_NOW);
    		}
    		else
    		{
    			// something is wrong -> for security reasons delete that auto login
    			$this->delete();
    		}
    
    		// auto login successful then create a valid session
    		$session->setValue('ses_usr_id',  $this->getValue('atl_usr_id'));
    	}        
    }  
    
    /** Method will clean the database table @b adm_auto_login. All login that had their last
     *  login one year ago will be deleted.
     */
    public function tableCleanup()
    {
        // Zeitpunkt bestimmen, ab dem die Auto-Logins geloescht werden, mind. 1 Jahr alt
        $date_session_delete = time() - 60*60*24*365;
            
        $sql    = 'DELETE FROM '. TBL_AUTO_LOGIN. ' 
                    WHERE atl_last_login < \''. date('Y.m.d H:i:s', $date_session_delete). '\'';
        $this->db->query($sql);
    }
}
?>