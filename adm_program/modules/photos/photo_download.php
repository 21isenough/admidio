<?php
/******************************************************************************
 * Photo download
 *
 * creates a zipfile on the fly with all fotos including sub-albums and returns it
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 * pho_id: id of album to download
 * photo_nr: Number of photo that should be downloaded
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/table_photos.php');

// Initialize and check the parameters
$getPhotoId = admFuncVariableIsValid($_GET, 'pho_id', 'numeric', 0);
$getPhotoNr = admFuncVariableIsValid($_GET, 'photo_nr', 'numeric', null);

// tempfolder 
// change this value if your provider requires the usage of special directories (e.g. HostEurope)
//$tempfolder = "/is/htdocs/user_tmp/xxxxxx/";
$tempfolder = sys_get_temp_dir();

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($gPreferences['enable_photo_module'] == 0)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}
elseif($gPreferences['enable_photo_module'] == 2)
{
    // nur eingeloggte Benutzer duerfen auf das Modul zugreifen
    require_once('../../system/login_valid.php');
}

// check if download function is enabled
if ($gPreferences['photo_download_enabled'] == 0)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('PHO_DOWNLOAD_DISABLED'));
}

//nur von eigentlicher OragHompage erreichbar
if($gCurrentOrganization->getValue('org_shortname')!= $g_organization)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_ACCESS_FROM_HOMEPAGE_ONLY', $gHomepage));
}

$gNavigation->addUrl(CURRENT_URL);

// Fotoalbumobjekt anlegen
$photo_album = new TablePhotos($gDb);

// get id of album
$photo_album->readDataById($getPhotoId);
    
// check whether album belongs to the current organization
if($photo_album->getValue('pho_org_shortname') != $gCurrentOrganization->getValue('org_shortname'))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// check whether album is locked
if($photo_album->getValue('pho_locked')==1 && (!$gCurrentUser->editPhotoRight()))
{
    $gMessage->show($gL10n->get('PHO_ALBUM_NOT_APPROVED'));
}

$ordner = SERVER_PATH. '/adm_my_files/photos/'.$photo_album->getValue('pho_begin', 'Y-m-d').'_'.$photo_album->getValue('pho_id');

// check whether to take original version instead of scaled one
$takeOriginalsIfAvailable = false;
if ($gPreferences['photo_keep_original'] == 1)
{
    $takeOriginalsIfAvailable = true;
}

// check folder vs single download
if($getPhotoNr == null)
{
    // get number of photos in total
    $quantity = $photo_album->getValue('pho_quantity');

    $zipname = tempnam($tempfolder, 'zip');
    error_log($zipname, 0);
    $nicename = $photo_album->getValue('pho_name').' - '.$photo_album->getValue('pho_photographers').'.zip';

    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);

    for ($i = 1; $i <= $quantity; $i++) 
    {
        if ($takeOriginalsIfAvailable)
        {
            // try to find the original version if available, if not fallback to the scaled one
            $path = $ordner.'/originals/'.$i;
            if(file_exists($path.'.jpg'))
            {
                $path = $path.'.jpg';
                $zip->addFromString(basename($path),  file_get_contents($path));
                continue;
            } 
            elseif(file_exists($path.'.png'))
            {
                $path = $path.'.png';
                $zip->addFromString(basename($path),  file_get_contents($path));
                continue;
            }
        }

        $path = $ordner.'/'.$i.'.jpg';
        if(file_exists($path)){
            $zip->addFromString(basename($path),  file_get_contents($path));
        }
    }

    /************************add sub albums as subfolders*************************************/

    // get sub albums
    $sql='      SELECT *
                FROM '. TBL_PHOTOS. '
                WHERE pho_org_shortname = \''.$gCurrentOrganization->getValue('org_shortname').'\'';
    if($getPhotoId == 0)
    {
        $sql = $sql.' AND (pho_pho_id_parent IS NULL) ';
    }
    if($getPhotoId > 0)
    {
        $sql = $sql.' AND pho_pho_id_parent = '.$getPhotoId.'';
    }
    if (!$gCurrentUser->editPhotoRight())
    {
        $sql = $sql.' AND pho_locked = 0 ';
    }

    $sql = $sql.' ORDER BY pho_begin DESC ';
    $result_list = $gDb->query($sql);

    // number of sub albums
    $albums = $gDb->num_rows($result_list);

    for($x = 0; $x < $albums; $x++)
    {
        $adm_photo_list = $gDb->fetch_array($result_list);

        // get id of album
        $photo_album->readDataById($adm_photo_list['pho_id']);

        // ignore locked albums owned by others
        if($photo_album->getValue('pho_locked')==0 || $gCurrentUser->editPhotoRight())
        {
            $ordner = SERVER_PATH. '/adm_my_files/photos/'.$photo_album->getValue('pho_begin', 'Y-m-d').'_'.$photo_album->getValue('pho_id');
            // get number of photos in total
            $quantity = $photo_album->getValue('pho_quantity');
            $photo_album_name = $photo_album->getValue('pho_name');
            for ($i = 1; $i <= $quantity; $i++) 
            {
                if ($takeOriginalsIfAvailable)
                {
                    // try to find the original version if available, if not fallback to the scaled one
                    $path = $ordner.'/originals/'.$i;
                    if(file_exists($path.'.jpg'))
                    {
                        $path = $path.'.jpg';
                        $zip->addFromString($photo_album_name."/".basename($path),  file_get_contents($path));
                        continue;
                    } 
                    elseif(file_exists($path.'.png'))
                    {
                        $path = $path.'.png';
                        $zip->addFromString($photo_album_name."/".basename($path),  file_get_contents($path));
                        continue;
                    }
                }
                $path = $ordner.'/'.$i.'.jpg';
                if(file_exists($path))
                {
                    $zip->addFromString($photo_album_name."/".basename($path),  file_get_contents($path));
                }
            }
        }
    }


    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Description: File Transfer'); 
    header('Content-disposition: attachment; filename="'.$nicename.'"');
    header('Expires: 0'); 
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: private');

    // send the file, use fpassthru for chunkwise transport
    $fp = fopen($zipname, 'rb');
    fpassthru($fp);

    unlink($zipname);
} else
{
    // download single file
    header('Content-Description: File Transfer'); 
    header('Expires: 0'); 
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: private');

    if ($takeOriginalsIfAvailable)
    {
        // try to find the original version if available, if not fallback to the scaled one
        $path = $ordner.'/originals/'.$getPhotoNr;
        if(file_exists($path.'.jpg'))
        {
            header('Content-Type: application/jpeg');
            header('Content-disposition: attachment; filename="'.$getPhotoNr.'.jpg"');
            $fp = fopen($path.'.jpg', 'rb');
            fpassthru($fp);
            exit;
        } 
        elseif(file_exists($path.'.png'))
        {
            header('Content-Type: application/png');
            header('Content-disposition: attachment; filename="'.$getPhotoNr.'.png"');
            $fp = fopen($path.'.png', 'rb');
            fpassthru($fp);
            exit;
        }
    }

    $path = $ordner.'/'.$getPhotoNr.'.jpg';

    if(file_exists($path)){
        header('Content-Type: application/jpeg');
        header('Content-disposition: attachment; filename="'.$getPhotoNr.'.jpg"');
        $fp = fopen($path, 'rb');
        fpassthru($fp);
    }

}
?>