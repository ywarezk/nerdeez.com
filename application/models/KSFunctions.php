<?php
/**
 * This class contains diffrent auxilary function thats in use by all the classes
 *
 * @author Yariv Katz
 * @copyright Knowledge-Share.com Ltd.
 */
class Application_Model_KSFunctions {  
    
    //constants
    //constants will always start with c
    const cPOSTERRORTITLE = 'Bad message post params';
    
    const cPOSTERRORVALUESERIAL = "Please don't modify the serial field";
    
    const cPOSTERRORVALUETITLE = "Title must be shorter than 200 characters";
    
    const cPOSTERRORVALUEDISCLAIMER = "You must read and agree to the disclaimer";
    
    const cPOSTERRORVALUEDIR = "Bad Directory please dont change the hidden fields";
    
    const cPOSTERRORVALUEDIRECTORYCREATE = "Failed to create the directory";
    
    const cPOSTERRORVALUEFAILEDMOVE = "Failed to move files to their appropriate directory";
    
    const cMESSAGEERROR = 'Invalid Message';
    
    const cMESSAGENOTFOUND = 'The message you specified was not found';
    
    const cMETADESCRIPTIONMAIN = 'Students place to share knowledge , create your classroom and knowledge sharing community and start uploading and downloading lectures  , notes and homework';
    
    const cDESCRIPTIONEDITABLETIME = 3600;
    
    const cUPDATEMESSAGEERROR = 'Update message error';
    
    const cUPDATEMESSAGEERRORAUTH = 'User not authorized to edit this message';

    const cMAXFILESIZEALLOWED = 104857600;  //* 1024 * 1024;
    
    const cMAXFILESIZEPROFILEPICALLOWED = 1048576;
    
    const c_FOLDER_PROFILES = "/upload/profiles/";
    
    const c_FOLDER_COURSES = "/upload/";

    const cBADID = "Bad post id";
    
    const cBADHTML = "Unsafe html - videos allowed are only from youtube and pictures ,ust be valid image files with no php embeded";
    
    const cBADSEARCHFORM = "Bad params for search form";
    
    const cBADSEARCHFORMSTRING = "Search string must not exceed 300 characters";
    
    CONST cBADIMAGE = "Detected php code in image";
    
    CONST cBADNAME = "Your name can be up to 100 characters";
    
    CONST cBADNAME2 = "The name length must be larger than 0 and smaller than 100 ";
    
    CONST cBADLOGIN = "Problem with your login please try again";
    
    /**
     * number of search results per page
     */
    const cNUMSEARCHRESULTS = 10;
    
    const cSTATICSALT = 'tBHrUMVcHRV';
    
    const c_UPDATEIMAGE_ERROR_MULFILES = 'Only single profile image is allowed please upload only one file';
    
    const c_UPDATEIMAGE_ERROR_MOVEFILE = 'Failed to move file';
    
    const c_BAD_PARAMS = 'Bad params';
    
    //the limit from which the search file is not displayed
    const c_ELFINDER_LOWLIMIT = -100;
    
    //the count download limit from which a semester recieves a file
    const c_ELFINDER_DOWNLOADLIMIT = 5;
    
    //the times hw file need to be uploaded to be considered an hw file
    const c_ELFINDER_HWLIMIT = 5;
    
    //the number of points a file gets and after that it becomes an answer to hw 
    const c_ELFINDER_HWPOINTS = 10;
    
    //the number of points a file gets after he was downloaded due to hw
    const c_ELFINDER_HWDOWNLOADPOINTS = 10;
    
    //black list of characters to remove 
    const c_CHARACTERS_BLIST = 'אבגדהוזחטיכלמנסעפצקרשתךםןףץ: ';
    
    //characters whits list
    const c_CHARACTERS_WLIST = '{}()<>=|-+−';
    
    /****************const error messages *********************/
    
    const cERROR_404 = 'You entered a bad URL';
    
    //private members
    
}

?>
