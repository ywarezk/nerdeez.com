/* 
    Document   : admin
    Created on : 12:37:24 PM
    Author     : Yariv Katz
    Copyright  : Knowledge-Share.com Ltd.
    Description:
        admin controller page
*/

/**
 * delete country from the database
 * @param int iCountryId the country to delete from database
 */
function ksDeleteCountry(iCountryId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iCountryId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteCountries(aIds);
    
    removeLoadingScreen();
}

/**
 * delete university from the database
 * @param int iUniversityId the university to delete from database
 */
function ksDeleteUniversity(iUniversityId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iUniversityId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteUniversities(aIds);
    
    removeLoadingScreen();
}

/**
 * delete semester from the database
 * @param int iSemesterId the semester to delete from database
 */
function ksDeleteSemester(iSemesterId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iSemesterId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteSemesters(aIds);
    
    removeLoadingScreen();
}

/**
 * delete university from the database
 * @param int iUniversityId the university to delete from database
 */
function ksDeleteCourse(iCourseId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iCourseId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteCourses(aIds);
    
    removeLoadingScreen();
}

/**
 * delete rule from the database
 * @param int iRuleId the rule to delete from database
 */
function ksDeleteRule(iRuleId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iRuleId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteRules(aIds);
    
    removeLoadingScreen();
}

/**
 * delete file from the database and disk
 * @param int iUniversityId the university to delete from database
 */
function ksDeleteFile(iFileId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iFileId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteFiles(aIds);
    
    removeLoadingScreen();
}


/**
 * delete row from the pnggs table
 * @param int iPngId the row to delete
 */
function ksDeletePng(iPngId){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iPngId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeletePngs(aIds);
    
    removeLoadingScreen();
}

/**
 * download the list of files
 * @param array aIds the list of ids to download
 */
function ksDeleteCountries(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //check via ajax if user is authorized 
    $.ajax({ 
            type: "POST",
            url: "/admin/deletecountries/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/countries/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * delete allt his rows from the pngs table
 * @param array aIds the list of ids to download
 */
function ksDeletePngs(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //check via ajax if user is authorized 
    $.ajax({ 
            type: "POST",
            url: "/login/resetpassword",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/pngs/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * download the list of files
 * @param array aIds the list of ids to download
 */
function ksDeleteUniversities(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/deleteuniversities/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/universities/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * delete the list of semesters
 * @param array aIds the list of ids to delete
 */
function ksDeleteSemesters(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/deletesemesters/", 
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/semesters/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * delete the list of courses
 * @param array aIds the list of ids to delete
 */
function ksDeleteCourses(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/deletecourses/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/courses/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * delete the list of rules
 * @param array aIds the list of ids to delete
 */
function ksDeleteRules(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/deleterules/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/rules/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * delete the list of courses
 * @param array aIds the list of ids to delete
 */
function ksDeleteFiles(aIds){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //grab the course id
    var iCourseId = jQuery.trim($('#courseid').text());

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/deletefiles/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/file/id/" + iCourseId + "/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * delete all the checked countries
 */
function ksDeleteSelectedCountries(){
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteCountries(aIds);
    
    removeLoadingScreen();
}

/**
 * delete all the checked countries
 */
function ksDeleteSelectedUniversities(){
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteUniversities(aIds);
    
    removeLoadingScreen();
}

/**
 * delete all the checked semesters
 */
function ksDeleteSelectedSemesters(){
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteSemesters(aIds);
    
    removeLoadingScreen();
}

/**
 * delete all the checked courses
 */
function ksDeleteSelectedCourses(){ 
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteCourses(aIds);
    
    removeLoadingScreen();
}

/**
 * delete all the checked courses
 */
function ksDeleteSelectedRules(){ 
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteRules(aIds);
    
    removeLoadingScreen();
}

/**
 * delete all the checked courses
 */
function ksDeleteSelectedFiles(){  
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteFiles(aIds);
    
    removeLoadingScreen();
}

/**
 * the user wants to update the country details
 */
function ksUpdateCountry(iCountryId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.title = jQuery.trim($('#country_' + iCountryId + ' .title').text());
    obj.image = jQuery.trim($('#country_' + iCountryId + ' .image').text());
    obj.shorttitle = jQuery.trim($('#country_' + iCountryId + ' .shorttitle').text());
    obj.id = iCountryId;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updatecountry/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    window.location = "/admin/countries/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * the user wants to update the country details
 */
function ksUpdateUniversity(iUniversityId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.title = jQuery.trim($('#university_' + iUniversityId + ' .title').text());
    obj.description = jQuery.trim($('#university_' + iUniversityId + ' .description').text());
    obj.image = jQuery.trim($('#university_' + iUniversityId + ' .image').text());
    obj.website = jQuery.trim($('#university_' + iUniversityId + ' .website').text());
    obj.country = jQuery.trim($('#university_' + iUniversityId + ' select').val());
    obj.id = iUniversityId;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updateuniversity/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    window.location = "/admin/universities/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * the user wants to update the semester details
 */
function ksUpdateSemester(iSemesterId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.title = jQuery.trim($('#semester_' + iSemesterId + ' .title').text());
    obj.from = jQuery.trim($('#semester_' + iSemesterId + ' .from').text());
    obj.to = jQuery.trim($('#semester_' + iSemesterId + ' .to').text());
    obj.university = jQuery.trim($('#semester_' + iSemesterId + ' select').val());
    obj.id = iSemesterId;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updatesemester/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    window.location = "/admin/semesters/status/success";
                }
                else{
                    setSuccessToFailed(); 
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * the user wants to update the course details
 */
function ksUpdateCourse(iCourseId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.title = jQuery.trim($('#course_' + iCourseId + ' .title').text());
    obj.description = jQuery.trim($('#course_' + iCourseId + ' .description').text());
    obj.connections = jQuery.trim($('#course_' + iCourseId + ' .connections').text());
    obj.website = jQuery.trim($('#course_' + iCourseId + ' .website').text());
    obj.university = jQuery.trim($('#course_' + iCourseId + ' select').val());
    obj.id = iCourseId;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updatecourse/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    window.location = "/admin/courses/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * the user wants to update a rule
 */
function ksUpdateRule(iRuleId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.rule = jQuery.trim($('#rule_' + iRuleId + ' .rule').text());
    obj.priority = jQuery.trim($('#rule_' + iRuleId + ' .priority').text());
    obj.comment = jQuery.trim($('#rule_' + iRuleId + ' .comment').text());
    obj.id = iRuleId;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updaterule/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    window.location = "/admin/rules/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * the user wants to update the file details
 */
function ksUpdateFile(iFileId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.title = jQuery.trim($('#file_' + iFileId + ' .title').text());
    obj.path = jQuery.trim($('#file_' + iFileId + ' .path').text());
    obj.papa = jQuery.trim($('#file_' + iFileId + ' .papa').text());
    obj.date = jQuery.trim($('#file_' + iFileId + ' .date').text());
    obj.semesters = jQuery.trim($('#file_' + iFileId + ' .semesters').text());
    obj.id = iFileId;
    
    //grab the course id
    var iCourseId = jQuery.trim($('#courseid').text());

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updatefile/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    window.location = "/admin/file/id/" + iCourseId + "/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}

/**
 * the user wants to update the file details
 */
function ksUpdatePng(iPngId){
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.latex = jQuery.trim($('#png_' + iPngId + ' .latex').text());
    obj.id = iPngId;
    
    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updatepng/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    window.location = "/admin/pngs/status/success";
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#error').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            }
    });
}