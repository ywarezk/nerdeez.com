/* 
    Document   : admin
    Created on : 12:37:24 PM
    Author     : Yariv Katz
    Copyright  : Nerdeez.com Ltd.
    Description:
        admin controller page
*/

/**
 * delete a row from the selected database
 * @param int iId the row id to delete
 * @param String sModel the model from which we are deleting
 * 
 */
function ksDeleteRow(iId , sModel){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iId;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDeleteRows(aIds , sModel);
    
    removeLoadingScreen();
}

/**
 * delete multiple rows from the database
 * @param array aIds the list of ids to delete
 * @param String sModel the model to delete from
 */
function ksDeleteRows(aIds , sModel){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object with the params
    var obj=new Object();
    obj.ids = sIds;
    obj.model = sModel;

    //check via ajax if user is authorized 
    $.ajax({ 
            type: "POST",
            url: "/admin/deleterows/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    location.reload();
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
 * delete all the checked rows
 * @param String sModel the model to delete from
 * 
 */
function ksDeleteSelectedRows(sModel){
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
    ksDeleteRows(aIds , sModel);
    
    removeLoadingScreen();
}

/**
 * updates a row in the database
 * @param int iId the id of the row to update
 * @param String sModel the identifier of the model class 
 */
function ksUpdateRow(iId , sModel){
    //put the loading screen on
    loadingScreen();
    
    //put the columns in array
    var aCols = new Array();
    $('.admin_table_header_cols').each(function(i, val){
        aCols[i] = $.trim($(this).text());
    });
    
    //create an object to send
    var obj=new Object();
    for (var i = 0; i < aCols.length; i++){
        if ($('#row_' + iId + ' .' + aCols[i]).is("select")){
            obj[aCols[i]] = jQuery.trim($('#row_' + iId + ' .' + aCols[i]).val());
        }
        else{
            obj[aCols[i]] = jQuery.trim($('#row_' + iId + ' .' + aCols[i]).text());
        }
    }
    obj.id = iId;
    obj.model = sModel;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/admin/updaterow/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){ 
                    location.reload();
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
 * download a row from the dattabase
 * @param int iId the id of the row to download
 * @param String sModel the model of the table
 */
function ksDownloadRow(iId , sModel){
    //user is authorized to download the files continue with download
    var iframe = document.createElement("iframe");
    iframe.src = "/admin/download/id/" + iId + "/model/" + sModel;
    iframe.onload = function() {
        // iframe has finished loading, download has started
    }
    iframe.style.display = "none";
    document.body.appendChild(iframe);
    
}

