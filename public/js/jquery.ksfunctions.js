/**
 * knowledge-share.com added functions for jquery
 * @Author Yariv Katz
 * @copyright Knowledge-Share.com Ltd.
 */

//TODO minify this file

/**
 * the percent the content will be on screen
 */
var constant_IntSizeFactor = 1.3;

var SEARCH_TREE_INITIAL_TEXT = "Search for your course";

var windowInitialized = false;

var gDropZone = 0;
/**
 * the size of the menu bar
 */
var constant_headersite_nav_big_init_width = 450;

var initiallyopen = [];

var gUploadRequests = [];

var gUploadRequestsCounter = 0;

//the item in the course list that is currently highlighted
var iHighlightedCourse = 0;


/***************************CONSTANTS*****************************/

//the ascii code of the down arrow
var cDOWN_ARROW = 40;

//the ascii code of the up arrow
var cUP_ARROW = 38;

//teh ascii code for the enter key
var cEnter = 13;

//for the mouse dragging event
var gMouseDrag = 0;

/*****************************************************************/


$(document).ready(function() {
     preload();
}); 


/**
 * this function will position .content , .content_left ,.content_right,.content_center
 */
jQuery.fn.center = function () { 
    //get the screen size
    var intScreenWidth = $(window).width();
    var intScreenHeight = $(window).height();
    
    //var widthContentCenter = intScreenWidth / 3;
    
    
    //get the contnent size this value is based on the constantIntSizeFactor
    var floatContentWidth = intScreenWidth / constant_IntSizeFactor;
    
    //set the width of the content 
    this.css("width","" + floatContentWidth.toString() + "px");
    
    //position content in the center of the screen
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    
    //center logo and menu
    $(".headersite_top_img").css("margin-left" , (($(window).width() - this.outerWidth()) / 1.5) + $(window).scrollLeft() + "px");
    $(".headersite_nav_big_item_first").css("margin-left" , (($(window).width() - this.outerWidth()) / 1.3) + $(window).scrollLeft() + "px");
    $(".headersite_nav_big").css("width","" + (constant_headersite_nav_big_init_width + (($(window).width() - this.outerWidth()) / 2.5) + $(window).scrollLeft()) + "px");
     
    return this;
}


/**
 * this function will position .content , .content_left ,.content_right,.content_center
 */
jQuery.fn.fullscreen= function () { 
    //get the screen size
    var intScreenWidth = $(window).width();
    var intScreenHeight = $(window).height();
    
    //var widthContentCenter = intScreenWidth / 3;
    
    
    //get the contnent size this value is based on the constantIntSizeFactor
    var floatContentWidth = intScreenWidth / constant_IntSizeFactor;
    
    //set the width of the content 
    this.css("width","100%");
    
    //position content in the center of the screen
    //this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    
    //center logo and menu
    //$(".headersite_top_img").css("margin-left" , (($(window).width() - this.outerWidth()) / 1.5) + $(window).scrollLeft() + "px");
    $(".headersite_nav_big_item_first").css("margin-left" , "50px");
    $(".headersite_nav_big").css("width","300px");
     
    $(".content_second").css("width" , "50%");
     
    return this;
}

jQuery.fn.cancelUploadDialog = function () {
    //$(".glass").slideToggle("normal");
    //$(".uploaddialog").slideToggle("normal");
    $(".glassnoloading").css("display" , "none");
    $(".uploaddialog").css("display" , "none");
    //TODO cancel all downloads
    
}



function setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name)
{
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
{
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
}

function checkCookie()
{
    setCookie("test","test",1);
    var test=getCookie("test");
    if (test==null || test=="")
    {
        return false;
    }
    else{
        return true;
    }
}

function loadingScreen(){
    $("#glassloading").css("display" , "block");
    $(".glass_loaddialog").css("display" , "block");
    //$(".glass").css("z-index" , "4");
}

function removeLoadingScreen(){
    /*$(".glass img").css("display" , "none");
    $(".glass").css("z-index" , "2");
    $(".glass").css("display" , "none");*/
    
    $("#glassloading").css("display" , "none");
    $(".glass_loaddialog").css("display" , "none");
}

function displaySuccessFailure(){
    $('#success').fadeIn(1500, function(){
        $('#success').fadeOut(1000);
    });
}

function removeLoadingGlassScreen(){
    $(".glass").css("display" , "none");
    $(".glass img").css("display" , "none");
    $(".glass").css("z-index" , "2");
}

/**
 * this function will resize two float left divs where the left one has a fixed size and the right one 
 * is determined by the papa object
 * @param String left the identifier of the left div 
 * @param String right the identifier of the right object
 * @param String papa the identifier of the parent div
 */
function resizeTwoFloatObejctsLeftKnown(left , right , papa){
    var widthPapa = $(papa).width();
    var widthLeft = $(left).width();
    $(right).width(widthPapa - widthLeft - 10);
}

/**
 * displays the legal disclaimer window in the moddle of the screen
 */
function showDisclaimer(){
    //$(".glass").css("display" , "block");
    $(".legaldisclaimer_notes").css("display" , "block");
}

/**
 * hides the legal disclaimer window
 */
function hideDisclaimer(){
    //$(".glass").css("display" , "none");
    $(".legaldisclaimer_notes").css("display" , "none");
}

/**
 * shows the create new uni faculty course dialog
 */
function showNewUniFacultyCourse(){
    $(".glass").slideToggle('normal');
    $("#newUniFacultyCourseDialog").slideToggle('normal');
}

/**
 * when the user submits a request for new uni faculty course
 */
function sendNewUniFacultyCourse(){
    //put the loading screen on
    loadingScreen();
    
    //grab the message 
    var message = $("#newunifacultycourse").val();
    
    //create the object to send 
    var obj=new Object();
    obj.message = message;
    
    //send it via jquery and update the description 
    $.ajax({
            type: "POST",
            url: "/index/newunifacultycourse/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    showNewUniFacultyCourse(); 
                    alert('Successfully submitted your request');
                }
                else{
                    alert(""+res.items[0].message);
		}
                removeLoadingScreen();
            },
            error: function(data) {
                alert("Error sending your request. Try again")
                removeLoadingScreen();
            }
    });
}

function sendPushNotification(){
    //grab the params
    var message = $("#message").val();
    var dt = $("#dt").val();
    
    //create the object to send 
    var obj=new Object();
    obj.message = message;
    obj.dt = dt;
    
    //send it via jquery and update the description 
    $.ajax({
            type: "POST",
            url: "/service/pushnotificationsend/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                alert('Successfully submitted your request');
            },
            error: function(data) {
                alert("Error sending your request. Try again")
            }
    });
}


/**
 * ajax call to search for specific item in tree
 */
function searchcourse(){
    //grab the search string 
    	
    var search = $('#tree_search').val();
    
    //create the object to send 
    var obj=new Object();
    obj.search = search;
    
    //send it via jquery and update the description 
    $.ajax({
            type: "POST",
            url: "/index/searchtree/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    $("#treeviewlist").html(getNewTree(res.items[0].unifacultycourse , 0));
                    $("#treeviewlist")
                    .jstree({
		  "types" : {
		  	"max_depth" : -2,
		       	"max_children" : -2,
		        "valid_children" : [ "uni" ],
                        "types" : {
                            "uni" : {
		        	"icon" : {
		        	    "image" : "/img/tiny_uni.png"
		        	 },
		        	"valid_children" : [ "faculty" , "uni"],
		        	"max_depth" : 4,
		        	"hover_node" : false
                            },
                            "faculty" : {
                                 "icon" : {
                                    "image" : "/img/tiny_faculty.png"
                                    },
		        	 "valid_children" : [ "course" ],
		        	 "max_depth" : 3,
		        	 "hover_node" : false
                            },
                            "course" : {
                                    "icon" : {
		                        "image" : "/img/tiny_course.png"
                                    },                                    
                                    "valid_children" : [ "message" ],
                                    "max_depth" : 3,
                                    "hover_node" : false
                            }
                         }
		        },		        		
		        "core" : {
		        	"initially_open" : initiallyopen
		        			     },
		        					        		
		            // the `plugins` array allows you to configure the active plugins on this instance
		            "plugins" : ["themes","html_data","crrm","types"]
		            // each plugin you have included can have its own config object
		            // it makes sense to configure a plugin only if overriding the defaults
		        });
                        
                }
                else{
                    //alert(""+res.items[0].message);
		}
            },
            error: function(data) {
                 
            }
    });
}

/**
 * gets a tree like structure and prints the tree
 */
function getNewTree(unifacultycourse , depth){
    if(unifacultycourse == null) return '';
    var result = '';
    for(var i = 0 ; i < unifacultycourse.length ; i++){
        var title = unifacultycourse[i].title;
        var id = unifacultycourse[i].id;
        initiallyopen.push("treenode_" + id);
        var newarray = unifacultycourse[i].son;
        var link = '/index/unifaculty/id/';
        if(depth == 0){
            var rel = 'uni';
        }
        else if(depth == 1){
            var rel = 'faculty';
        }
        else{
            var rel = 'course';
            link = '/index/course/id/'
        }
        result+='<ul><li rel="' + rel + '" id ="treenode_' + id +'">';
        result += '<a href="' + link + id + '">' + title  + '</a>'
        result += getNewTree(newarray , depth + 1);
        result+='</li></ul>';
    }
    return result;
}


function clearCourseSearchText(){
    if ($("#tree_search").val() == SEARCH_TREE_INITIAL_TEXT){
        $("#tree_search").val('');
        $(".tree_searchtext input").css("color" , "#000000");
    }
}

function insertCourseSearchExplanation(){
    if ($("#tree_search").val() == ''){
        $("#tree_search").val(SEARCH_TREE_INITIAL_TEXT);
        $(".tree_searchtext input").css("color" , "#999999");
    }
}

/**
 * displays the dialog to add new uni faculty course
 */
function showAddUniFacultyCourseDialog(){
    $(".glass").slideToggle('normal');
    $("#AddUniFacultyCourseDialog").slideToggle('normal');
}

/**
 * loads all the resources
 */
function preload(){
    var i = 0, max = 0, o = null,

    // list of stuff to preload
    preload = [
        '/js/static.min.js',
        '/styles/static.min.css',
    ];
    //$.preload(preload);
    isIE = navigator.appName.indexOf('Microsoft') === 0;

    for (i = 0, max = preload.length; i < max; i += 1) {
        if (isIE) {
            new Image().src = preload[i];
            continue;
        }
        o = document.createElement('object');
        o.data = preload[i];

        // IE stuff, otherwise 0x0 is OK
        o.width  = 0;
        o.height = 0;

        // only FF appends to the head
        // all others require body 
        document.body.appendChild(o);
    }
    
    //set the content area width
    $('.wrapper').css('width' , '' + Math.floor(screen.width * 2 / 3));
    
    //set the span placeholders
    $('.placeholder').click(passThrough);
    
    //set the span placeholders to disappear if there is text in input
    $('input').each(function(){
        if ($(this).val().length > 0 ){
            $(this).parent().children('.placeholder').css('z-index' , '-1');
        }
    });
    
    //if there is message or error display success or error
    var sStatus = jQuery.trim($('#status').text());
    var sError = jQuery.trim($('#error').text());
    if (sError != null && sError !== ''){
        setSuccessToFailed();
        displaySuccessFailure();
    }
    else{
        if(sStatus != null && sStatus !== ''){
            setSuccessToSuccess();
            displaySuccessFailure();
        }
    }
    
    //disable the default behavior on file drops to browser
    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });
    
    //add dragenter dragleave dragdrop events to window
    $(document).on('dragenter', function (e) {
        e.preventDefault();
        //$('.dropzone').fadeIn('normal');
        $('.dropzone').addClass('dragover');
    });
    $(document).on('drop', function (e) {
        e.preventDefault();
        $('.dropzone').removeClass('dragover');
    });
    
    
     
}



/**
 * generic function to print errors
 * @param String obj the object that has a message
 * @param String message the message to print to the user
 */
function printFormError(obj , message){
    //if this object has an error message than print a new message
    if ($('#' + obj + '_error').length > 0){
        $('#' +  obj + '_error_message').text(message);
        $('#' +  obj + '_error').fadeIn('fast', function(){
            setTimeout("$('#" + obj + "_error').fadeOut('normal');",5000);
        });
        return;
    }
    
    //if we got here we have no object than we need to create a new one
    var html = '';
    html = '<div class="formerror" id="'+ obj + '_error">';
    html+='<img src="/img/Error.png" />';
    html+='<span class="formerror_message" id="' + obj + '_error_message">';
    html+=message;
    html+='</span>';
    html+='</div>';
    $('body').append(html);
    
    //get the location top and left
    var top , left;
    top = $('#' + obj).offset().top+ $('#' + obj).outerHeight() - $('#' + obj).height() - 15;//$('#' + obj).position().top;
    left = $('#' + obj).offset().left + $('#' + obj).outerWidth();//$('#' + obj).position().left + $('#' + obj).width() + 10;
    
    //we created the object now it's time to make it appear and disappear in the correct location
    $('#' + obj + '_error').css("top" , top);
    $('#' + obj + '_error').css("left" , left);
    //$('#' + obj + '_error').css('display', 'block');
    $('#' + obj + '_error').fadeIn('normal' , function(){
        setTimeout("$('#" + obj + "_error').fadeOut('normal');",5000);
    });
    
}

/**
 * generic function to clear the initial text when textarea has focus
 * @param e from here i can extract the textarea we are checking
 * @param String message the initial message to delete on focus
 */
function clearTextField(e, message){
    if ($(e.currentTarget).val() === message){
        $(e.currentTarget).val('');
        $(e.currentTarget).css("color" , "black");
    }
}

/**
 * generic function to fill the text area when it's empty
 * @param e from here i can extract the textarea we are checking
 * @param String message the initial to input if the field is empty
 */
function inputTextField(e , message){
    if ($(e.currentTarget).val() === ''){
        $(e.currentTarget).val(message);
        $(e.currentTarget).css("color" , "#999999");
    }
}

/**
 * make the text inside the input disappear
 * @param e from here i can extract the textarea we are checking
 */
function ksSetTextHelper(e){
    if(e.val().length > 0){
        e.parent().addClass("hasome");
    }
    else{
        e.parent().removeClass("hasome");
        e.parent().children('span').css('z-index','0');
    }
}

/**
 * detect what key pressed if down than access the course box
 */
function ksSearchCourseKeyController(e){
    //get the key code
    var iKeycode = e.keyCode;
    
    //get the scroll amount of the courselist div
    var iScrollAmount = 0;
    
    //should i change the txt in the search field
    var bIsChangeText = false;

    //if key code is equal to down arrow and list is invisible than make it pop
    if (iKeycode === cDOWN_ARROW && $('#courselist').css('display') === 'none'){
        $('#courselist').fadeIn('normal' , function(){
            $('#courselist').css('opacity' , '1.0');
        });
        iHighlightedCourse = 0;
        $('#courselist').scrollTop(0);
        $('#expendcourselist').addClass('active');
    }
    
    //if the list is already displayed than the down arrow cause the next item to be highlighted
    else if(iKeycode === cDOWN_ARROW && $('#courselist').css('display') !== 'none'){
        iHighlightedCourse++;
        if (iHighlightedCourse == $('.courseitem').length) iHighlightedCourse--;
        bIsChangeText = true;
    }
    
    //if the list is already displayed than the up arrow cause the prevoius item to be highlighted
    else if(iKeycode === cUP_ARROW && $('#courselist').css('display') !== 'none'){
        iHighlightedCourse--;
        if(iHighlightedCourse < 0)  iHighlightedCourse = 0;
        bIsChangeText = true;
    }
    
    
    
    //highlight the item
    ksCourseOver(iHighlightedCourse);
    
    //get the top offset of course list and the top offset of highlighted item
    var objoffset = $('.courseitem.active').offset().top;
    var courselistoffset = $('#courselist').offset().top;
    
    //get the number of amount we need to scroll
    var iScrollAmount = 0;
    if (objoffset < courselistoffset  || objoffset + $('.courseitem.active').height() > courselistoffset){
        //need to lower the scroll
        iScrollAmount =iHighlightedCourse* $('.courseitem.active').height(); //$('#courselist').scrollTop() - $('.courseitem.active').height();
        $('#courselist').scrollTop(iScrollAmount);
    }
    
    //if needed change the text in the search field and set the id of the hidden field
    if (bIsChangeText){
        $('#searchcourse_input').val($('.courseitem.active h3 .titlecontent').text().trim());
        
        //get the id of the highlighted item and set the hidden field
        var id = $('.courseitem:nth-child(' + (iHighlightedCourse+1) +') .id').text();
        $('#searchcourse_courseid').val(id);
    }
    
    //if the enter is pressed then go to selected item page
    if(iKeycode === cEnter ){
        id = $('.courseitem.active .id').text();
        document.location.href = '/course/course/id/' + id;
    }
    
    
}

/**
 * when the user mouse is over an item
 * @param int iCourseNum the number of the courrse to highlight
 */
function ksCourseOver(iCourseNum){
    //delete the hover affect from all the items
    $('.courseitem').removeClass('active');
    
    //set the global var to the item that is highlighted
    iHighlightedCourse = iCourseNum;
    
    //set active the mouse over item
    var counter = 0;
    $('.courseitem').each(
        function(){
            if (counter === iCourseNum){
                $(this).addClass('active');
            }
            counter++;
        }
    );
}


/**
 * user posts his opinion
 */
function sendFeedback(){
    //check if the form is valid
    if(!$('.sendfeedback_form').valid()) return;
    
    //put the loading screen on
    loadingScreen();
    
    //grab the message 
    var message = $('#about_message').val();
    var mail = $("#about_mail").val();
    
    //create the object to send 
    var obj=new Object();
    obj.message = message;
    obj.mail = mail;
    
    //send it via jquery and update the description 
    $.ajax({
            type: "POST",
            url: "/index/sendreport/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    $("#status").text('Report sent successfully');
                    setSuccessToSuccess();
                }
                else{
                    $("#error").text(res.items[0].data);
                    setSuccessToFailed();
                }
            },
            error: function(data) {
                $("#error").text("Error sending report. Try again")
                setSuccessToFailed();
            },
            
            complete: function(){
                //loading screen
                removeLoadingScreen();
                 
                 //display success failure screen
                 displaySuccessFailure();
            }
    });
    
}

function setSuccessToSuccess(){
    $('#success > img').attr('src' , '/img/success.png');
    $('#success > span').text('Success');
    $('#success > span').css('color' , 'green');
}

function setSuccessToFailed(){
    $('#success > img').attr('src' , '/img/failed.png');
    $('#success > span').text('Failed');
    $('#success > span').css('color' , 'red');
}

/**
 * ajax search result when searching course
 */
function searchCourse(e){
    //if down or up arrow than dont search
    var iKeycode = e.keyCode;
    if (iKeycode === cDOWN_ARROW || iKeycode === cUP_ARROW) return; 
    
    //set the hidden field to -1 again
    $('#searchcourse_courseid').val('-1');
    
    //grab the search 
    var search = $('#searchcourse_input').val();
    
    //create the object to send 
    var obj=new Object();
    obj.search = search;
    
    //send it via jquery and update the courselist
    $.ajax({
            type: "POST",
            url: "/index/searchcourse/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    $("#courselist").html(res.items[0].data);
                    if($('.courseitem').length > 0){
                        $("#courselist").css('display' , 'block');
                    }
                    else{
                        $("#courselist").css('display' , 'none');
                    }
                }
            }
    });
}

/**
 * go to course page
 * @param int id the course id to go to
 */
function ksGotoCoursePage(id){
    window.location = "/course/course/id/" + id;
}

/**
 * to pass the click through events
 */
function passThrough(e) {
    $("input").each(function() {
        if ($(this).attr('type') === 'file')return;
       // check if clicked point (taken from event) is inside element
       var mouseX = e.pageX;
       var mouseY = e.pageY;
       var offset = $(this).offset();
       var width = $(this).width();
       var height = $(this).height();

       if (mouseX > offset.left && mouseX < offset.left+width 
           && mouseY > offset.top && mouseY < offset.top+height)
         $(this).focus(); // force click event
    });
}

/**
 * display the post on wall dialog
 */
function displayPostDialog(iPapa){
    iPapa = typeof iPapa !== 'undefined' ? iPapa : -1;

    $('#postOnWallDialog').slideToggle('normal');
    $('#glassnoloading').slideToggle('normal');
    
    //empty the textarea
    $('#redactor_content').val('');
    
    //set the papa hidden field
    $('#postOnWallDialog_papa').val(iPapa);
}

/**
 * when the user selects in browser to mark all check boxes
 */
function ksFileBrowserCheckAll(){
    $('.ksFileBrowserCheckbox').attr('checked', true);
    $('.ksFolderBrowserCheckbox').attr('checked', true);
}

/**
 * when the user choose to uncheck all in the file browser
 */
function ksFileBrowserUncheckAll(){
    $('.ksFileBrowserCheckbox').attr('checked', false);
    $('.ksFolderBrowserCheckbox').attr('checked', false);
}



/**
 * download all the checked files
 */
function ksDownloadChecked(iCourseId){
    //create the array of ids to download
    var aIds=new Array();
    var aFolders=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    counter = 0;
    $('.ksFolderBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aFolders[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDownloadFiles(aIds , aFolders , iCourseId);
    
    removeLoadingScreen();
}



/**
 * when the user clicks the like button
 * @param int id the id of the post which he liked
 * @param Boolean bIsLike true if the user liked or false if disliked
 * @param String sQuestions json string that represents the questions can be null
 * @return void
 */
function likePost(id , bIsLike , sQuestions){
    //put the loading screen on
    loadingScreen();        
    
    //determine the url
    var sUrl = null;
    if (bIsLike){
        sUrl = '/filemanager/rateup/'
    }
    else{
        sUrl = '/filemanager/ratedown/'
    }

    //create the object to send 
    var obj=new Object();
    obj.id = id;
    obj.questions = sQuestions;
    
    //send it via jquery and update rating
    $.ajax({
            type: "POST",
            url: sUrl,
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    //$("#likes_"+ id).text((parseInt($("#likes_"+ id).text()) + 1));
                    //$("#elfinder_tdup_" + id).html('<img src="/img/like_off.png" width="16" height="16" />');
                    //$("#elfinder_tddown_" + id).html('<img src="/img/unlike_off.png" width="16" height="16" />');
                    if (res.items[0].data == 0){
                        setSuccessToSuccess();
                        //loading screen
                        removeLoadingScreen();

                        //display success failure screen
                        displaySuccessFailure();
                        
                        //increase the like or dislike 
                        if(bIsLike){
                            $('#filebrowser_like_' + id).text(parseInt(jQuery.trim($('#filebrowser_like_' + id).text())) + 1);
                        }
                        else{
                            $('#filebrowser_dislike_' + id).text(parseInt(jQuery.trim($('#filebrowser_like_' + id).text())) + 1);
                        }
                        
                        //create a disabled pictures and remove the anchors
                        $("#filebrowser_likeimg_" + id).html('<img src="/img/like_off.png" width="24" height="20" />');
                        $("#filebrowser_dislikeimg_" + id).html('<img src="/img/unlike_off.png" width="24" height="20" />');
                    }
                    else{
                        setSuccessToFailed();
                        //loading screen
                        removeLoadingScreen();

                        //display success failure screen
                        displaySuccessFailure();
                        if(res.items[0].data == 1){
                            $('#error').text('Params Error');
                        }
                        if(res.items[0].data == 2){
                            $('#error').text('You can only rate a file once');
                        }
                        if(res.items[0].data == 3){
                            $('#error').text('No such post');
                        }
                    }
                    
                }
                
                //removeLoadingGlassScreen();
            },
            error: function(res) { 
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
 * when the user clicks to edit his nickname
 * make the nickname dialog appear
 */
function displayChangeNameDialog(){
    $('#changeNicknameDialog').slideToggle('normal');
    $('#glassnoloading').slideToggle('normal');
}

/**
 * update the user nickname
 */
function updateNickname(){
    //put the loading screen on
    loadingScreen();  
    
    //create the object to send 
    var obj=new Object();
    obj.title = $('#nickname_input').val();
    
    //send it via jquery and update rating
    $.ajax({
            type: "POST",
            url: '/user/updateusertitle/',
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    if (res.items[0].data == 0){
                        setSuccessToSuccess();
                        //loading screen
                        removeLoadingScreen();

                        //display success failure screen
                        displaySuccessFailure();
                        
                        //change the name of the user in all the places
                        $('.usernickname').text($('#nickname_input').val());
                        
                    }
                    else{
                        setSuccessToFailed();
                        //loading screen
                        removeLoadingScreen();

                        //display success failure screen
                        displaySuccessFailure();
                        if(res.items[0].data == 1){
                            $('#error').text('Params Error');
                        }
                        if(res.items[0].data == 2){
                            $('#error').text("You're not logged in!");
                        }
                    }
                    
                }
                
                //removeLoadingGlassScreen();
            },
            error: function(res) { 
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            },
            complete: function(){
                $('#changeNicknameDialog').fadeOut('normal');
                $('#glassnoloading').fadeOut('normal');
            }
    });
}

/**
 * display the change profile picture dialog
 */
function displayChangePictureDialog(){
    $('#changeProfilePicDialog').slideToggle('normal');
    $('#glassnoloading').slideToggle('normal');
    
}

/**
 * @deprecated
 * initiate the upload 
 * @param String sId the id of the form
 * @param int iSerial the serial to pass
 * @param int iNumDownloads the number of uploads allowed
 * @param int iMaxFileSize the max file size allowed for upload
 * @param RegExp sAcceptFileTypes a regular expression representing the file types allowed
 * @param String sDropOverElement the id of the element below the drop target 
 */
function ksInitUpload1(sId , iSerial , iNumDownloads , iMaxFileSize , sAcceptFileTypes){
    if (iNumDownloads == 0)iNumDownloads = undefined;
    if (iMaxFileSize == 0)iMaxFileSize = undefined;
    
    
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#' + sId).fileupload({
        url: '/filemanager/upload/',
        autoUpload: true,
        maxNumberOfFiles: iNumDownloads,
        maxFileSize: iMaxFileSize,
        acceptFileTypes: sAcceptFileTypes, 
        dropZone: $('#' + sId +' .dropzone'),
        formData: [
                    {
                        name: 'serial',
                        value: iSerial
                    }
        ]
    });
    
    $('#' + sId)
    .bind('fileuploadadd', function (e, data) 
    {
       //make the table header visible
       $('#' + sId + ' .filesheader').fadeIn('slow');
    })
    .bind('fileuploadfail', function (e, data) 
    {
        //alert('5');
        if ($(this).find('tr').length == 2){
            $('#' + sId + ' .filesheader').fadeOut('slow');
            $('.filebrowsertooltip').css('display' , 'none');
        }
    })
    .bind('fileuploaddestroy', function (e, data) {
        if ($(this).find('tr').length == 2){
            $('#' + sId + ' .filesheader').fadeOut('slow');
            $('.filebrowsertooltip').css('display' , 'none');
        }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#' + sId).fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/user/result.html?%s'
        )
    );

    
    
}


/**
 * initiate the upload 
 * @param String sId the id of the form
 * @param int iSerial the serial to pass
 * @param int iNumDownloads the number of uploads allowed
 * @param int iMaxFileSize the max file size allowed for upload
 * @param RegExp sAcceptFileTypes a regular expression representing the file types allowed
 * @param String sDropOverElement the id of the element below the drop target 
 * @param String sUrl the url to upload the file to
 */
function ksInitUpload(sId , iSerial , iNumDownloads , iMaxFileSize , sAcceptFileTypes , sUrl){
    if (iNumDownloads == 0)iNumDownloads = undefined;
    if (iMaxFileSize == 0)iMaxFileSize = undefined;
    
    
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#' + sId).fileupload({
        url: sUrl,
        autoUpload: true,
        maxNumberOfFiles: iNumDownloads,
        maxFileSize: iMaxFileSize,
        acceptFileTypes: sAcceptFileTypes, 
        filesContainer: $('#' + sId + '_files .files'),
        fileInput: $('#' + sId + '_input'),
        dropZone: $('#' + sId +'_dropzone'),
        maxChunkSize: 10000000,
        multipart: true,
        formData: [
                    {
                        name: 'serial',
                        value: iSerial
                    },
                    {
                        name: 'chunk',
                        value: 1000000
                    }
        ]
    });
    
    $('#' + sId)
    .bind('fileuploadadd', function (e, data) 
    {
       //make the table header visible
       $('#' + sId + '_files .files .filesheader').fadeIn('slow');
       $('#' + sId + '_files').fadeIn('slow');
       
       //make the classify dialog visible
       $('#fileclassify').fadeIn('slow');
       handleSubmitFilesButton();
    })
    .bind('fileuploadfail', function (e, data) 
    {
        //alert('5');
        if ($('#' + sId + '_files .files').find('tr').length == 2 && $('#' + sId + '_files .files').find('error').length == 0){
            $('#' + sId + '_files .files .filesheader').fadeOut('normal');
            $('#' + sId + '_files').fadeOut('normal');
            $('#fileclassify').fadeOut('normal');
            $('.filebrowsertooltip').fadeOut('normal');
        }
    })
    .bind('fileuploadfailed', function (e, data) 
    {
        alert('5');
        
    })
    .bind('fileuploaddestroyed', function (e, data) 
    {
        alert('6');
        
    })
    .bind('fileuploaddone', function (e, data) {
        //handleSubmitFilesButton();
    })
    .bind('fileuploadcompleted', function (e, data) {
        handleSubmitFilesButton();
    })
    .bind('fileuploaddestroy', function (e, data) {
        if ($('#' + sId + '_files .files').find('tr').length == 2){
            $('#' + sId + '_files .files .filesheader').fadeOut('normal');
            $('.filebrowsertooltip').fadeOut('normal');
            $('#' + sId + '_files').fadeOut('normal');
            $('#fileclassify').fadeOut('normal');
        }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#' + sId).fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/user/result.html?%s'
        )
    );

    
    
}

/**
 * change the users profile image
 */
function updateUserPicture(){
    //put the loading screen on
    loadingScreen();  
    
    //create the object to send 
    var obj=new Object();
    obj.serial = $('#changeProfilePicDialog_serial').val();
    
    //send it via jquery and update rating
    $.ajax({
            type: "POST",
            url: '/user/updateimage/',
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    if (res.items[0].data == 0){
                        setSuccessToSuccess();
                        //loading screen
                        removeLoadingScreen();

                        //display success failure screen
                        displaySuccessFailure();
                        
                        //change the nprofile picture
                        d = new Date();
                        $('.userprofileimage').attr("src" , "/user/showpicture?"  +d.getTime());
                        
                        $('#changeProfilePicDialog').remove();
                        
                        $('body').append(res.items[0].html);
                        
                        
                    }
                    else{
                        setSuccessToFailed();
                        //loading screen
                        removeLoadingScreen();

                        //display success failure screen
                        displaySuccessFailure();
                        if(res.items[0].data == 1){
                            $('#error').text('Invalid image file');
                        }
                        if(res.items[0].data == 2){
                            $('#error').text("File is to big");
                        }
                    }
                    
                }
                
                //removeLoadingGlassScreen();
            },
            error: function(res) { 
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            },
            complete: function(){
                $('#changeProfilePicDialog').fadeOut('normal');
                $('#glassnoloading').fadeOut('normal');
            }
    });
}

/**
 * when the user wants to update his password
 */
function updatePassword(){
    //check if the form is valid
    if(!$('#changePasswordDialog * form').valid()) return;
    
    //put the loading screen on
    loadingScreen();  
    
    //create the object to send 
    var obj=new Object();
    obj.oldpass = $('#oldpass_input').val();
    obj.password = $('#newpass_input').val();
    obj.repassword = $('#renewpass_input').val();
    
    //send it via jquery and update rating
    $.ajax({
            type: "POST",
            url: 'https://' + window.location.hostname + '/user/changepasswordin/',
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    setSuccessToSuccess();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
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
            error: function(res) { 
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            },
            complete: function(){
                $('#changePasswordDialog').fadeOut('normal');
                $('#glassnoloading').fadeOut('normal');
            }
    });
}

/**
 * display the change password dilog
 */
function showChangePasswordDialog(){
    $('#changePasswordDialog').slideToggle('normal');
    $('#glassnoloading').slideToggle('normal');
}

/**
 * when the user searches in the oursepicker dialog
 */
function searchCoursePickerCourses(){
    //grab the search string
    var search = $('#coursepicker_input').val();
    
    //create the object from the search string 
    var obj=new Object();
    obj.search = search;
    
    //send everything via ajax to the server
    $.ajax({
            type: "POST",
            url: "/user/searchcourse/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    //var html = $(res.items[0].data);
                    
                    var theString = res.items[0].data
                    theString = theString.replace(/(\r\n|\n|\r|\t)/gm,"");
                    theString = theString.replace(" " , "");
                    var theResult = $(theString);
                    var $currentHtml = $('<div>').append(theResult);

                    //var theResult = theString;
                    //var $html = html;
                    $('#coursepicker_enrolledlist .course_singlerow').each(function(index){
                        //$html = $(html).not($('#' + $(this).attr('id')));
                        //$(theResult +' #' + $(this).attr('id')).remove();
                        $currentHtml.find('#' + $(this).attr('id')).remove();
                        //theResult = $.strRemove('#' + $(this).attr('id'), theResult);
                    });
                    $('#coursepicker_courselist').html($currentHtml.html());
                }
            },
            error: function(data) { 
                 
            }
    });
    
}

/**
 * transfer item from the list of courses to the list of enrolled courses
 * the id of the item to transfer
 */
function transferCourse(id){
    //grab the title description before we remove the item
    var title = $('#course_singlerow_' + id + ' .course_singlerow_title').text();
    var description = $('#course_singlerow_' + id + ' .course_singlerow_description').text();
    var image = $('#course_singlerow_' + id + ' .course_singlerow_image').html();
    
    //move the item from the list
    $('#course_singlerow_' + id).remove();
    
    //create a new item in the enrolled list
    var html = '<div id="course_singlerow_' + id + '" class="course_singlerow">';
    html+='<table style="width: 100%;">';
    html+='<tbody>';
    html+='<tr>';
    html+='<td class="course_singlerow_transfertd"  rowspan="2" onclick="retransferCourse(\''+ id +'\')"  onmouseover="tooltip.show(\'UNEnroll from course\');" onmouseout="tooltip.hide();">';
    html+='<img src="/img/bullet_arrow_left_off.png" />';
    html+='</td>';
    html+='<td style="width: 50px;" rowspan="2" class="course_singlerow_image">';
    html+='' + image;
    html+='</td>';
    html+='<td style="padding-left: 10px; width: 100%;" class="course_singlerow_title">';
    html+='' + title;
    html+='</td>';
    html+='</tr>';
    html+='<tr>';
    html+='<td style="font-size: 11px;font-weight: bold;padding-left: 4px;" class="course_singlerow_description">';
    html+=description;
    html+='</td>';
    html+='</tr>';
    html+='</tbody>';
    html+='</table>';
    html+='</div>';
        
    //append the html to the enrolled list
    $('.coursepicker_enrolledlist').html($('.coursepicker_enrolledlist').html() + html);
    
    //put the ids in the hidden field
    fixHiddenFieldValues();
}

function fixHiddenFieldValues(){
    var ids = new Array();
    $('#' + 'coursepicker' + '_enrolledlist .course_singlerow').each(function(index){
        var id = $(this).attr('id');
        var coursenumber = id.substring(17);
        ids.push(coursenumber);
    });
    
    //create the json string from the array
    var myJSON = JSON.stringify(ids);
    
    //put the string in the hideen field
    $('#coursepicker_courses').val(myJSON);
}

function retransferCourse(id){
    //grab the text before we remove the item
    var title = $('#course_singlerow_' + id + ' .course_singlerow_title').text();
    var description = $('#course_singlerow_' + id + ' .course_singlerow_description').text();
    var image = $('#course_singlerow_' + id + ' .course_singlerow_image').html();
    
    //move the item from the list
    $('#course_singlerow_' + id).remove();
    
    var html = '<div id="course_singlerow_' + id + '" class="course_singlerow">';
    html+='<table style="width: 100%;">';
    html+='<tbody>';
    html+='<tr>';
    html+='<td style="width: 50px;" rowspan="2" class="course_singlerow_image">';
    html+='' + image;
    html+='</td>';
    html+='<td style="padding-left: 10px; width: 100%;" class="course_singlerow_title">';
    html+='' + title;
    html+='</td>';
    html+='<td class="course_singlerow_transfertd"  rowspan="2" onclick="transferCourse(\''+ id +'\')"  onmouseover="tooltip.show(\'Enroll to course\');" onmouseout="tooltip.hide();">';
    html+='<img src="/img/bullet_arrow_right.png" />';
    html+='</td>';
    html+='</tr>';
    html+='<tr>';
    html+='<td style="font-size: 11px;font-weight: bold;padding-left: 4px;" class="course_singlerow_description">';
    html+=description;
    html+='</td>';
    html+='</tr>';
    html+='</tbody>';
    html+='</table>';
    html+='</div>';
    
    $('#coursepicker_courselist').html($('#coursepicker_courselist').html() + html);
    
    fixHiddenFieldValues();
}

/**
 * show the course picker dialog
 */
function ksShowEnrollDialog(){
    $('#enrollCoursesDialog').slideToggle('normal');
    $('#glassnoloading').slideToggle('normal');
}

/**
 * when the user submits the enrolled courses dialog
 */
function updateCourses(){
    //get the array ids of the courses
    var aIds; 
    try{
        aIds = jQuery.parseJSON($('#coursepicker_courses').val());
    }
    catch(err)
    {
        return;
    }
    
    //if array is empty than return 
    if (aIds.length == 0)return;
    
    //put the loading screen on
    loadingScreen();  
    
    //create the object to send 
    var obj=new Object();
    obj.courses = $('#coursepicker_courses').val();
    
    //send it via jquery and update rating
    $.ajax({
            type: "POST",
            url: '/user/updatecourses/',
            dataType: "json",
            data: obj ,
            success: function(res) {
                if(res.items[0].status ==='success'){
                    setSuccessToSuccess();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    //change the url of all the tabs
                    $('#tabs-courses').html(res.items[0].courseshtml);
                    $('#tabs-calendar').html(res.items[0].calendarhtml);
                    $('#tabs-schedule').html(res.items[0].schedulehtml);
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
            error: function(res) { 
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                $("#error").text("Connection failure! Try again");
            },
            complete: function(){
                $('#enrollCoursesDialog').fadeOut('normal');
                $('#glassnoloading').fadeOut('normal');
            }
    });
}

/**
 * collect all the checked chekboxes and unenroll them all
 */
function ksUnEnroll(){
    //create the array of ids to download
    var aIds=new Array();
    var counter=0;
    $('.ksFileBrowserCheckbox').each(function(){
        if ($(this).attr("checked") === "checked"){
            aIds[counter] = $(this).attr('val');
            counter++;
        }
    });
    
    //unenroll from courses
    ksUnenrollCourses(aIds);
}

/**
 * unenroll from the list of courses
 * @param Array aIds the ids of the courses to remove
 */
function ksUnenrollCourses(aIds){
    //put the loading screen on
    loadingScreen();
    
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.courses = sIds;

    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/user/unenroll/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    setSuccessToSuccess();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    //change the url of all the tabs
                    $('#tabs-courses').html(res.items[0].courseshtml);
                    $('#tabs-calendar').html(res.items[0].calendarhtml);
                    $('#tabs-schedule').html(res.items[0].schedulehtml);
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
 * unenroll from single course
 * @param int iId the id of the course to unenroll
 */
function unenrollFromSingleCourse(iId){
    var aIds=new Array();
    aIds[0] = iId;
    
    //unenroll from courses
    ksUnenrollCourses(aIds);
}

/**
 * when the user posts a message in the wall of a course
 */
function postOnWall(){
    //put the loading screen on
    loadingScreen();
    
    //grab the message serial and course
    var obj=new Object();
    obj.message = $('#redactor_content').val();
    obj.id = $('#postonwall_course').val();
    obj.serial = $('#postOnWallDialog_serial').val();
    obj.papa = $('#postOnWallDialog_papa').val();
    obj.equations = $('#postOnWallDialog_equations').val();
    
    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/wall/postmessage/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    setSuccessToSuccess();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    //change the url of the wall
                    $('#tabs-wall * .comment_section').html(res.items[0].html);
                    
                    //disappear dialog
                    $('#postOnWallDialog').fadeOut('normal');
                    $('#glassnoloading').fadeOut('normal');
                    
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
 * shows the equation editor
 */
function showEquationEditor(){
    //count number of equations
    /*var iCount = $('.equations').length ; 
    
    //set the html to append
    var sHtml = '<p class="equations" id="equation_'+ (iCount + 1) + '"></p>';
    
    //apend the html
    //$('#page').append(sHtml);
    //$('#redactor').insertHtml('<p>insert</p>');
    //$('#redactor_content').insertHtml(sHtml);
    
    //launch the equation editor
    OpenLatexEditor('equation_' + (iCount + 1),'html','');*/
    
    $('#postOnWallDialog').css('z-index' , '2');
    $('#equationDialog').fadeIn('normal');
}

/**
 * takes the equation and moves it to the redactor
 */
function exportEquation(){
    //grab the image element
    var sHtml = $("<div />").append($('#equation').clone()).html();
    
    //grab the latex equation
    var sEquation = $('#testbox').val();
    
    //append equation to hidden field
    var sExistingEquations = $('#postOnWallDialog_equations').val();
    var aExistingEquations = jQuery.parseJSON(sExistingEquations);
    aExistingEquations[aExistingEquations.length] = sEquation;
    var sEquationJson = JSON.stringify(aExistingEquations);
    $('#postOnWallDialog_equations').val(sEquationJson);

    $('#redactor_content').insertHtml(sHtml);
    //$('#redactor_content').insertHtml('<span style="display: none">' + sEquation +'</span>');
    $('#postOnWallDialog').css('z-index' , '3');
    $('#equationDialog').fadeOut('normal');
    
    //clear the equation box and image
    $('#testbox').val('');
    $('#equation').attr('src' , '');
}

/**
 * download the hw id
 * @param int iHwId the id of the hw to download
 */
function ksDownloadHw(iHwId){
    var iframe = document.createElement("iframe");
    iframe.src = "/filemanager/downloadhw/id/" + iHwId;
    iframe.onload = function() {
        // iframe has finished loading, download has started
    }
    iframe.style.display = "none";
    document.body.appendChild(iframe);
}

/**
 * peek on hw file
 * @param int iHwId the hw to peek on
 * @param String 
 */
function ksPeekHw(iHwId , sTitle){
    var iframeurl = "http://docs.google.com/gview?url=http://"+ window.location.host  + "/filemanager/downloadhw/id/"+ iHwId +"&embedded=true";
    $("#peeking_iframe").html("");
    $('<iframe />', {
        name: 'myFrame',
        id:   'myFrame',
        src: iframeurl,
        width: "100%",
        height: "100%"
    }).appendTo('#peeking_iframe');
    $('#glassnoloading').fadeIn('fast');
    $("#peeking").css('display' , 'block');
    $('#peeking * h2').text(sTitle);
}

/**
 * refresh the wall every minute
 * @param int iCourseId the id of the course that wall need to be refreshed
 */
function refreshWall(iCourseId){
    //grab the message serial and course
    var obj=new Object();
    obj.id = iCourseId;
    
    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/wall/refreshwall/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    //update the wall messages
                    $('#tabs-wall * .comment_section').html(res.items[0].html);
                }
            },
            error: function(res){
            }
    });
}

/**
 * fetch files for the file browser
 * @param int iCourseId the course id
 * @param int iKSPostId the folder
 * @param int iSemesterId the semester
 * @param int iHwId for virtual hws
 */
function ksFileBrowser(iCourseId , iKSPostId , iSemesterId , iHwId){
    //fade in the ks file browser loading screen and fade out the filebrowser
    $('.filebrowser-loading').fadeIn('normal');
    $('.filebrowser').fadeOut('normal' , function(){
        $('.filebrowser').remove();
    });
    
    //create the object to send
    var obj=new Object();
    obj.iCourseId = iCourseId;
    obj.iKSPostId = iKSPostId;
    obj.iSemesterId = iSemesterId;
    obj.iHwId = iSemesterId;
    
    //check via ajax if user is authorized 
    $.ajax({
            type: "POST",
            url: "/filemanager/displayfiles/",
            dataType: "json",
            data: obj ,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    $('#tabs-files').append(res.items[0].html);
                }
                else{
                    $('.filebrowser-error').fadeIn('normal');
                    $('.filebrowser-error').text(res.items[0].msg);
                }
            },
            error: function(res){
                $('.filebrowser-error').text("Connection failure! Try again");
            },
            complete: function(){
                $('.filebrowser-loading').fadeOut('normal');
            }
    });
}

/**
 * when the user submits the forgot password form
 */
function ksForgotPassword(){
    //check if the form is valid
    if(!$('#forgotpassword').valid()) return;
    
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.email = $('#forgotpassword input[name="email"]').val();
    
    //send the reset information via ajax
    $.ajax({
            type: "POST",
            url: "/login/resetpassword/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    setSuccessToSuccess();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    
                    $('#glassnoloading').fadeOut('normal');
                    
                    //change the dialog to be more informative
                    $('.registerdialog img').attr("src" , "/img/success.png");
                    $('.registerdialog * .message').text('Successfully identified your account. A mail was sent to the address you supplied. To proceed with password change follow the link in the mail sent.');
                    $('.registerdialog > .front-card').remove();
                    
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();

                    $('#glassnoloading').fadeOut('normal');
                    
                    //change the dialog to be more informative
                    $('.registerdialog img').attr("src" , "/img/failed.png");
                    $('.registerdialog * .message').text(res.items[0].msg);
                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                //$("#error").text("Connection failure! Try again");
                //change the dialog to be more informative
                $('.registerdialog img').attr("src" , "/img/failed.png");
                $('.registerdialog * .message').text('Error! Connection failure. Try again');
            }
    });
}

/**
 * loads the about page
 */
function loadAbout(){
    //toggle the about button to look clicked
    $('#about').toggleClass("active");
    
    //bind the js events 
    $('.sendfeedback_form button').on('click' , function(){
        sendFeedback();
    });
    $('.sendfeedback_form textarea').on('keyup' , function(){
        ksSetTextHelper($('.sendfeedback_form textarea'));
    });
    $('.sendfeedback_form input').on('keyup' , function(){
        ksSetTextHelper($('.sendfeedback_form input'));
    });
    
    //create the js validation
    $('.sendfeedback_form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            message:{
                required: true,
                maxlength: 300
            }
        },
        messages: {
            email: {
                required: 'Email is required',
                email: 'Invalid email'
            },
            message: {
                required: 'Message is required',
                maxlength: 'Your message must be up to 300 letters'
            }
        },
        errorPlacement: function(error, element) {
             if (element.attr("name") == "email"){
                 $('.register-error-placeholder.mail').html('');
                 $('.register-error-placeholder.mail').append(error);
                 //error.insertAfter("#lastname");
             } 
             if (element.attr("name") == "message"){
                 //error.insertAfter("#lastname");
                 $('.register-error-placeholder.message').html('');
                 $('.register-error-placeholder.message').append(error);
             } 
       }
    });
    
    
}

/**
 * init the search course item
 */
function loadSearchCourse(){
    
    
    $('#searchcourseform * input[type="text"]').on('keyup' , function(ev){
        ksSetTextHelper($('#searchcourseform * input[type="text"]'));
        searchCourse(ev); 
    });
    $('#searchcourseform * input[type="text"]').on('keydown' , function(ev){
        ksSearchCourseKeyController(ev);
    });
    $('#expendcourselist').on('click' , function(){
        $('#expendcourselist').toggleClass('active');
        $('#courselist').fadeToggle('normal');
    });
    
    $('#searchcourseform * input[type="text"]').dblclick(function(){
        $('#courselist').fadeIn('normal' , function(){
            $('#courselist').css('opacity' , '1.0');
        });
        iHighlightedCourse = 0;
        $('#courselist').scrollTop(0);
        $('#expendcourselist').addClass('active');
    });
}

/**
 * init js in registration form
 * @param String sId the id of the form
 */
function loadRegistration(sId){
    //atach events to all the text inputs
    $('#' + sId + ' * .registerform * input[name="email"]').on('keyup' , function(){
       ksSetTextHelper($('#' + sId + ' * .registerform * input[name="email"]')); 
    });
    $('#' + sId + ' * .registerform * input[name="password"]').on('keyup' , function(){
       ksSetTextHelper($('#' + sId + ' * .registerform * input[name="password"]')); 
    });
    $('#' + sId + ' * .registerform * input[name="repassword"]').on('keyup' , function(){
       ksSetTextHelper($('#' + sId + ' * .registerform * input[name="repassword"]')); 
    });
    
    //init the js validation
    $('#' + sId +' * .registerform').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password:{
                required: true,
                minlength: 5
            },
            repassword:{
                required: true,
                minlength: 5,
                equalTo: '#' + sId + ' * .pass'
            }
        },
        messages: {
            email: {
                required: 'Email is required',
                email: 'Invalid email'
            },
            password: {
                required: 'Password is required',
                minlength: 'Must be more than 5 chars'
            },
            repassword: {
                required: 'Retype password is required',
                minlength: 'Must be more than 5 chars',
                equalTo: 'must match the password field'
            }
        },
        errorPlacement: function(error, element) {
             if (element.attr("name") == "email"){
                 $('.register-error-placeholder.email').html('');
                 $('.register-error-placeholder.email').append(error);
                 //error.insertAfter("#lastname");
             } 
             if (element.attr("name") == "password"){
                 //error.insertAfter("#lastname");
                 $('.register-error-placeholder.password').html('');
                 $('.register-error-placeholder.password').append(error);
             } 
             if (element.attr("name") == "repassword"){
                 //error.insertAfter("#lastname");
                 $('.register-error-placeholder.repassword').html('');
                 $('.register-error-placeholder.repassword').append(error);
             } 
             
       }
    });
}

/**
 * loads the js for the login form
 * @params String sId the id of the form
 */
function loadLogin(sId){
    //init js events
    $('#' + sId + ' * input[name="email"]').on('keyup' , function(){
        ksSetTextHelper($('#' + sId + ' * input[name="email"]'));
    });
    $('#' + sId + ' * input[name="password"]').on('keyup' , function(){
        ksSetTextHelper($('#' + sId + ' * input[name="password"]'));
    });
    
    //init the js validation
    $('#' + sId).validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password:{
                required: true,
                minlength: 5
            }
        },
        messages: {
            email: {
                required: 'Email is required',
                email: 'Invalid email'
            },
            password: {
                required: 'Password is required',
                minlength: 'Must be more than 5 chars'
            }
        },
        errorPlacement: function(error, element) {
             if (element.attr("name") == "email"){
                 $('#' + sId + ' * .register-error-placeholder.email').html('');
                 $('#' + sId + ' * .register-error-placeholder.email').append(error);
                 //error.insertAfter("#lastname");
             } 
             if (element.attr("name") == "password"){
                 //error.insertAfter("#lastname");
                 $('#' + sId + ' * .register-error-placeholder.password').html('');
                 $('#' + sId + ' * .register-error-placeholder.password').append(error);
             } 
       }
    });
    
}

var isEventSupported = (function() {

  var TAGNAMES = {
    'select': 'input', 'change': 'input',
    'submit': 'form', 'reset': 'form',
    'error': 'img', 'load': 'img', 'abort': 'img'
  };

  function isEventSupported( eventName, element ) {

    element = element || document.createElement(TAGNAMES[eventName] || 'div');
    eventName = 'on' + eventName;

    // When using `setAttribute`, IE skips "unload", WebKit skips "unload" and "resize", whereas `in` "catches" those
    var isSupported = eventName in element;

    if ( !isSupported ) {
      // If it has no `setAttribute` (i.e. doesn't implement Node interface), try generic element
      if ( !element.setAttribute ) {
        element = document.createElement('div');
      }
      if ( element.setAttribute && element.removeAttribute ) {
        element.setAttribute(eventName, '');
        isSupported = typeof element[eventName] == 'function';

        // If property was created, "remove it" (by setting value to `undefined`)
        if ( typeof element[eventName] != 'undefined' ) {
          element[eventName] = undefined;
        }
        element.removeAttribute(eventName);
      }
    }

    element = null;
    return isSupported;
  }
  return isEventSupported;
})();


/**
 * js init for the course page
 */
function loadCourse(){
    //init the text on the upload explain based on browser drag drop
    if (isEventSupported('dragstart') && isEventSupported('drop')) {
        $('#uploaddialog_explain').text("1. Click upload, or drag n'drop files here, to upload them to course");
    }
    else{
        $('#uploaddialog_explain').text("1. Click upload to upload files to course");
    }
    
    $('#otherfolder_input').on('keyup' , function(ev){
        //ksSetTextHelper($('#searchcourseform * input[type="text"]'));
        ksSetTextHelper($('#otherfolder_input'));
    });
}

/**
 * when the user selects the number 2 selects or upload finishes
 */
function handleSubmitFilesButton(){
    //grab the text of the select 
    var sClassifyCombo = $('#fileclassify select[name="folder_papa"] option:selected').text();
    sClassifyCombo = $.trim(sClassifyCombo);
    
    //compare it to H.W if so make the h.w number visible
    if ('H.W' === sClassifyCombo){
        bIsSecondSelectVisible = true;
        $('.fileclassify_body_hwnumber').css('display' , 'block');
    }
    else{
        bIsSecondSelectVisible = false;
        $('.fileclassify_body_hwnumber').css('display', 'none');
    }
    
    //compare the selection to other, if its other pop up the folder textbox
    if ('Other' === sClassifyCombo){
        $('.fileclassify_body_otherfolder').css('display' , 'block');
    }
    else{
        $('.fileclassify_body_otherfolder').css('display', 'none');
    }
    
    //if there is a progress bar than button should be disabled
    if($('.progress').length > 0){
        disableFileBrowserSubmit()
        return;
    }
    
    //if there is no rows in the files table than should be diabled
    /*if ($('#filebrowser_upload_files').find('tr').length >=2){
        disableFileBrowserSubmit()
        return;
    }*/
    
    //if there is no complete in the table than disable
    if ($('.filebrowsercomplete').length == 0){
        disableFileBrowserSubmit()
        return;
    }
    
    //if the first select had a null value
    if ($('#fileclassify select[name="folder_papa"] option:selected').val() == 0){
        disableFileBrowserSubmit()
        return;
    }
    
    //if the second is visible select check if the value iss not null
    /*if (bIsSecondSelectVisible){
        if ($('.fileclassify_body_hwnumber select').val() == 0){
            disableFileBrowserSubmit()
            return;
        }
    }*/
    
    enableFileBrowserSubmit();
}

function disableFileBrowserSubmit(){
    $('#submitfilebrowser').addClass('disable');
    $('#submitfilebrowser').removeClass('enable');
}
function enableFileBrowserSubmit(){
    $('#submitfilebrowser').addClass('enable');
    $('#submitfilebrowser').removeClass('disable');
    $('.filebrowsertooltip').fadeOut('normal');
}

/**
 * check if there is no upload in progress and that there is atleast one file uploaded
 */
function checkFilesUpload(){
    //if the button is enabled than return true
    if ($('#submitfilebrowser').hasClass('enable')){
        return true;
    }
    
    //if there is a progress than 
    if($('.progress').length > 0){
        showFileTableError('Wait until upload is finished');
    }
    else{
        hideFileTableError();
    }
    
    if ($('.filebrowsercomplete').length == 0){
        showFileTableError('There has to be atleast one successful file upload');
    }
    else{
        hideFileTableError();
    }
    
    if ($('#fileclassify select[name="folder_papa"] option:selected').val() == 0){
        showFolderPapaError('Please choose a value from the list');
    }
    else{
        hideFolderPapaError();
    }
    
    //if the second is visible select check if the value iss not null
    if ($('.fileclassify_body_hwnumber').css('display') === 'block'){
        if ($('.fileclassify_body_hwnumber select').val() == 0){
            showFolderSonError('Please choose a value from the list');
        }
        else{
            hideFolderSonError();
        }
    }
    
    return false
}

function showFileTableError(sMessage){
    $('#filebrowsertooltip_filetable .filebrowsertooltip_text span').text(sMessage);
    $('#filebrowsertooltip_filetable').fadeIn('normal');
}

function showFolderPapaError(sMessage){
    $('#filebrowsertooltip_folderpapa .filebrowsertooltip_text span').text(sMessage);
    $('#filebrowsertooltip_folderpapa').fadeIn('normal');
}

function showFolderSonError(sMessage){
    $('#filebrowsertooltip_folderson .filebrowsertooltip_text span').text(sMessage);
    $('#filebrowsertooltip_folderson').fadeIn('normal');
}
function hideFileTableError(){
    $('#filebrowsertooltip_filetable').fadeOut('normal');
}

function hideFolderPapaError(){
    $('#filebrowsertooltip_folderpapa').fadeOut('normal');
}

function hideFolderSonError(){
    $('#filebrowsertooltip_folderson').fadeOut('normal');
}

/**
 * shows our dialog
 * @param String header the dialog header
 * @param String message the message to display
 */
function showNerdeezDialog(header , message){
    $('#nerdeez_error_dialog .nerdeez_error_dialog_header h2').text(header);
    $('#nerdeez_error_dialog .nerdeez_error_dialog_body .kscenter-text').text(message);
    $(".glass").slideToggle('normal');
    $('#nerdeez_error_dialog').fadeIn('normal');
    
}

/**
 * redirect to the folder
 * @param int the folder id to go to
 */
function gotoFolder(iId , iCourse_id){
    document.location.href = '/course/course/id/' + iCourse_id + '/folder/' + iId;
}

/**
 * display peeking window
 * @param id the id of the file to peek
 * @deprecated
 */
function showPeeking(id){ 
    var iframeurl = "http://docs.google.com/gview?url=http://www."+ window.location.host  + "/filemanager/downloadmulposts/id0/"+ id +"&embedded=true";
    $("#peeking_iframe").html("");
    $('<iframe />', {
        name: 'myFrame',
        id:   'myFrame',
        src: iframeurl,
        width: "100%",
        height: "100%"
    }).appendTo('#peeking_iframe');
    $("#peeking").css('display' , 'block');
}

/**
 * peek on file 
 * @param int iId the id of the file to peek on
 * @param String sTitle the file title
 */
function ksPeek(iId , sTitle){
    var aIds = new Array();
    aIds[0] = iId;
    var sIds = JSON.stringify(aIds);
    if (bIsImage(sTitle)){
        var iframeurl = "http://"+ window.location.host  + "/course/downloadfiles/ids/"+ iId +"/disposition/inline/";
    }
    else{
        var iframeurl = "http://docs.google.com/gview?url=http://"+ window.location.host  + "/course/downloadfiles/ids/"+ iId +"&embedded=true";
    }
    
    $("#peeking_iframe").html("");
    $('<iframe />', {
        name: 'myFrame',
        id:   'myFrame',
        src: iframeurl,
        width: "100%",
        height: "100%"
    }).appendTo('#peeking_iframe');
    $('#glassnoloading').fadeIn('fast');
    $("#peeking").css('display' , 'block');
    $('#peeking * h2').text(sTitle);
}

/**
 * download a single file with the id sent
 * @param int id the kspost id of the file to download
 * @return void
 */
function ksDownloadFile(id){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = id;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDownloadFiles(aIds , [] , 0);
    
    removeLoadingScreen();
}

/**
 * when the user chose to download a folder
 */
function downloadFolder(iFolder , iCourse){
    //create the array of files to download
    var aIds = new Array();
    aIds[0] = iFolder;
    
    //put the loading screen on
    loadingScreen();
    
    //download the files
    ksDownloadFiles([] , aIds , iCourse);
    
    removeLoadingScreen();
}

/**
 * download the list of files
 * @param array aIds the list of ids to download
 */
function ksDownloadFiles(aIds , aFolders , iCourseId){
    //convert the array to json string
    var sIds = JSON.stringify(aIds);
    var sFolders = JSON.stringify(aFolders);
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.ids = sIds;

    //user is authorized to download the files continue with download
    var iframe = document.createElement("iframe");
    iframe.src = "/course/downloadfiles/ids/" + sIds + '/folders/' + sFolders + '/id/' + iCourseId + '/disposition/attachment/';
    iframe.onload = function() {
        // iframe has finished loading, download has started
    }
    iframe.style.display = "none";
    document.body.appendChild(iframe);
    
}

/**
 * return true if sFile is name of image file   
 * @param sFile String of file name
 * @return true if file ext is jpg , bmp, png , gif
 */
function bIsImage(sFile){
    var sExt = sFile.split('.').pop();
    sExt = sExt.toLowerCase();
    if (sExt === 'bmp' || sExt === 'jpg' || sExt ==='png' || sExt === 'gif'){
        return true;
    }
    return false;
}

/**
 * display the dialog for reporting flag
 * @param int iId the id of the file that we are reporting
 */
function showReportFlag(iId){
    //center the dialog
    var left = ($(document).width()/2) - ($('#flagdialog').width()/2);
    var top = ($('html').height()/2) - ($('#flagdialog').height()/2);
    $('#flagdialog').css('top', "" + top + "px");
    $('#flagdialog').css('left', "" + left + "px");
    
    //show the dialog and put the shade
    $('#flagdialog').fadeIn('normal');
    $('#glassloading').slideToggle('normal');
    
    //put the right values in the file hidden fields
    $('#reasonshidden').val(iId);
    
}

/**
 * put events to the reasons menus
 */
function loadReasonsMenu(){
    $('#reasonsfreetexttextarea').on('keyup' , function(ev){
        ksSetTextHelper($('#reasonsfreetexttextarea'));
        
    });
    
    $('li.leaf').each(function(){
        $(this).on('click' , function(){
            $('#reasonspanmesseage').text($.trim($(this).children('span').text()));
            //$('#reasonmessage').fadeIn('normal');
            $('#reasonmessage').css('display' , 'inline');
            $('#reasonsfreetext').fadeIn('normal');
            $('#reasonlist').fadeOut('normal');
            $('#reasonsbutton').toggleClass('active');
        });
    });
}

/**
 * submit the flag dialog
 */
function sendFlagReport(){
    //check if the form is valid
    if($('#reasonmessage').css('display') === 'none'){
        $('#flagdialog .about_status').text('Please choose a reason why this content is inappropriate');
        return;
    } 
    
    //put the loading screen on
    loadingScreen();
    
    //create an object to send to varify auth
    var obj=new Object();
    obj.id = $('#reasonshidden').val();
    obj.message = $('#reasonsfreetexttextarea').val();
    obj.title = $.trim($('#reasonspanmesseage').text());
    
    //send the reset information via ajax
    $.ajax({
            type: "POST",
            url: "/course/flag/",
            dataType: "json",
            data: obj ,
            async: false,
            success: function(res) {
                if (res.items[0].status ==='success'){
                    setSuccessToSuccess();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    
                    $('#glassnoloading').fadeOut('normal');
                    $('#flagdialog').fadeOut('normal');
                }
                else{
                    setSuccessToFailed();
                    //loading screen
                    removeLoadingScreen();

                    //display success failure screen
                    displaySuccessFailure();
                    
                    $('#flagdialog .about_status').text(res.items[0].msg);

                }
            },
            error: function(res){
                setSuccessToFailed();
                //loading screen
                removeLoadingScreen();
                 
                //display success failure screen
                displaySuccessFailure();
                
                
                $('#flagdialog .about_status').ext('Error! Connection failure. Try again');
            }
    });
}

/**
 * common actions that i preform at start up
 */
function initCommonActions(){
   $("#footer").pinFooter();
   //center the search course
    iHeightSearchcourse = $('.front-searchcourse').height();
    iHeightDocument = $(document).height();
    
    iMargin = (iHeightDocument / 3) - (iHeightSearchcourse / 2) - $('#footer').height();
    $('.front-searchcourse').css('margin-top' , "" + iMargin + "px");
}

function clickedUploadInCourse(){
    $('#filebrowser_upload_input').click();
}
