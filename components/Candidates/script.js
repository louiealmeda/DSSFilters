var isEditing = false;

var folderList = {};
var profileList = {};
var prevActiveProfile = null;
//var foldersActivated = false;

var activeProfileInfo = {};
var activeProfileEducation = {};
var activeProfileEmployment = {};

var activeProfilePart = 0;
var repeat = 1;
$(document).ready(function(){
    
    
});

function InitializeManageProfiles()
{   
    var pane = $(".cp #foldersPane #folders");
    $(pane).html("");
    $.post("amsServer.php", {method:"GetFoldersWithContent"}, function(data){
//        alert(data);
        
       
        LoadFolders(data);
    });
}
//, nameFilter:"C"
function LoadFolders(data)
{
    data = data.split("<break>");
        
    var folders = data[0].split("|");
    var profiles = data[1].split("][");

    var pane = $(".cp #foldersPane #folders");
    var tally = "";
    
    profiles.forEach(function(p){
        var parts = p.split("|");
        tally += parts[0] + "|";
    });
    
    $(pane).html("");
    var deleteBtn = "";
    folders.forEach(function(f){
        var parts = f.split(",");
//            var count = folders.split(parts[0]).length;
        
        folderList[parts[0]] = parts[1];
        count = tally.split(parts[0]).length -1;
        
        if(parts[0] != -1)
            deleteBtn = "<span class = 'delete' onclick = 'DeleteFolder("+parts[0]+")'>x</span>";
        else
            deleteBtn = "";
        
        
        
        $(pane).append("<li id = 'folder"+parts[0]+"'><div>"+parts[1]+" <span>" +count+"</span>"+deleteBtn+" </div><ul class = 'group'></ul></li>");
    });

    profiles.forEach(function(p){
        var parts = p.split("|");
        profileList[parts[2]] = parts[1];
        
        
        deleteBtn = "<span class = 'delete' onclick = 'DeleteProfile("+parts[2]+")'>x</span>";
        
        var clickEvent = " onclick='LoadProfile("+parts[2]+")' ";
//            alert("#folder" + parts[0] + " ul");
        $(".cp #foldersPane #folder" + parts[0] + " ul").append("<li "+ clickEvent +" id = '"+parts[2]+"'>"+parts[1]+ deleteBtn  + "</li>");
    });
    
    
    
//    if(foldersActivated)
//    {
//        $(".cp #foldersPane .group").sortable("disable");
//        $(".cp #foldersPane .group").droppable("disable");
//        $( ".cp #foldersPane #folders>li>div").droppable("disable");
//    }
//    
    $(".cp #foldersPane .group").sortable({
        connectWith:".cp #foldersPane .group",
        axis:"y"
//        helper: function( event ) {
//            return $( "<div style='z-index: 99999;'>I'm a custom helper</div>" );
//      }
        
    }).disableSelection();
//    $(".cp #foldersPane .group li").draggable();
    $(".cp #foldersPane .group").droppable({
        drop: function(event, ui){
            var fID = $(this).parent().attr("id").replace("folder","");
            $.post("amsServer.php", {method:"ChangeFolder", folderID:fID , profileID: ui.draggable.attr("id") }, function(data){
                LoadFolders(data);
            });
        }
    });
    
//
    $( ".cp #foldersPane #folders>li>div").droppable({
        accept: ".group li",
        drop: function( event, ui ) {
//            alert(ui.draggable.html());
            var id = ui.draggable.attr("id");
            $(this).parent().children("ul").append("<li id = '"+id+"'>"+ui.draggable.html()+"</li>");
//            ui.draggable.parent().find(id).remove();
            
//            $(this).siblings("ul").append(ui.draggable.html().wrap('<li>'));
            
            var fID = $(this).parent().attr("id").replace("folder","");
            $.post("amsServer.php", {method:"ChangeFolder", folderID:fID , profileID: ui.draggable.attr("id") }, function(data){
                LoadFolders(data);
            });
      }
    });
    
//    foldersActivated = true;
}


function LoadProfile(id)
{
    $(prevActiveProfile).removeClass("active");
//    alert(".cp #foldersPane #folders>li>ul>li#" + id );
    prevActiveProfile = $(".cp #foldersPane #folders>li>ul>li#" + id );
    
    prevActiveProfile.addClass("active");
    
    
    $.post("amsServer.php", {method:"LoadProfile", profileID:id}, function(data){
        
//        alert(data);
        var allInfo = JSON.parse(data);
//        alert(allInfo);
        activeProfileInfo = allInfo.personal;
        activeProfileEmployment = allInfo.employment;
        activeProfileEducation = allInfo.education;
        
        
//        alert(JSON.stringify(activeProfileEmployment));
        
        repeat = 1;
        LoadProfileComputations(id);
        LoadProfilePart(-1);
        LoadProfilePart(activeProfilePart);
    });
    
}

function LoadProfileComputations(id)
{
    var dateStr = activeProfileInfo['birthday'];
    var yr = 1000 * 60 * 60 * 24 * 365;
    var age = Math.abs(new Date() - new Date(dateStr.replace(/-/g,'/')))/yr;
    activeProfileInfo['age'] = Math.round(age);


    activeProfileInfo['folderName'] = folderList[activeProfileInfo['folderID']];
    $("#folderName").html(activeProfileInfo['folderName']);


    $("#profilePicture").attr("alt", "Image not found");
    $("#profilePicture").attr("onError","this.onerror=null;this.src='files/profilePictures/0.png';");

    $("#profilePicture").attr("src","files/profilePictures/" + id +".png");

}


function LoadProfilePart(part)
{
    if(part != -1)
        activeProfilePart = part;
    var IDs = [];
//    repeat = 1;

    var isRepeating = false;
    var containerName = "";
    
    var sourceArray = activeProfileInfo;
    switch(part)
    {
        case -1:
            IDs = ["firstName", "lastName", "email", "middleInitial"];
            break;
        case 0:
            IDs = ["appartmentUnitNumber", "streetAddress", "city", "country", "ZIP", "contactNumber"];
            break;
        case 1:
            IDs = ["gender","birthday", "age","skills","desiredSalary","overview"];
            break;
        case 2:
            isRepeating = true;
            
//            for(var i = 0; i < repeat; i++)
//            {
//                
//            }
            $("#employmentHistory").each(function(index, e){
                $(e).attr("id", $(e).attr("id") + index);
                
            });
            
            sourceArray = activeProfileEmployment;
            containerName = "employmentHistory";
//            repeat = activeProfileEmployment.length;
            
            IDs = ["company", "jobTitle", "phone","address",
                        "supervisor","startingSalary", "endingSalary",
                        "responsibilities", "reasonForLeaving"];
//            alert(JSON.stringify(sourceArray));
            break;
    }
    
    
    
    
        
    if(!isRepeating)
    {
        IDs.forEach(function(id){
            $("#" + id).val(activeProfileInfo[id]);
        });
    }
    else
    {   
        for(var i = 0; i < sourceArray.length; i++)
        {
            
            IDs.forEach(function(id){
//                alert("#" + containerName + i + " #" + id + " | " + sourceArray[i][id]);
                $("#" + containerName + i + " #" + id).val(sourceArray[i][id]);
            });
        }       
    }
            
    
    
    $('input[type="text"]').keyup(resizeInput).each(resizeInput);
}

function resizeInput() {
    $(this).attr('size', $(this).val().length);
}


function filterProfiles(sender)
{
    $.post("amsServer.php", {method:"GetFoldersWithContent", nameFilter:sender.value}, function(data){
//        alert(data);
        
       
        LoadFolders(data);
    });
}

function DeleteProfile(id)
{
//    alert(id);
    MessageBox.Show("Delete Profile", "Are you sure you want to delete the profile of " + profileList[id] + "?<br>There is no way to recover the profile once deleted.", [{"title":"No", "callBack":MessageBox.Hide}, {"title":"DELETE", "callBack":function(){
        $.post("amsServer.php", {method:"DeleteProfile", profileID:id}, function(data){
            LoadFolders(data);
            MessageBox.Hide();
        });
    }}]);
}

function DeleteFolder(id)
{
    MessageBox.Show("Delete Folder", "Are you sure you want to delete folder " + folderList[id] + "?<br>All profiles under this folder will be moved to unsorted folder.", [{"title":"No", "callBack":MessageBox.Hide}, {"title":"DELETE", "callBack":function(){
        $.post("amsServer.php", {method:"DeleteFolder", folderID:id}, function(data){
            LoadFolders(data);
            MessageBox.Hide();
        });
    }}]);
}

function AddFolder()
{
    MessageBox.Show("New Folder","<input type = 'text' id = 'txtNewFolderName' placeholder='Folder Name'>",[ {"title":"Cancel", "callBack":function(){MessageBox.Hide();}}, {"title":"Create", "callBack":function(){
        if($("#txtNewFolderName").val().trim() != "")
        {
            $.post("amsServer.php", {method:"addFolder", name: $("#txtNewFolderName").val()}, function(data){
                MessageBox.Hide();
                LoadFolders(data);
            });
        }
    }} ] );
}

function ToggleEdit()
{
    var caption = "Edit";
    if(!isEditing)
        caption = "Done";
    
    $(".cp #profileEditButton").html(caption);
    
    
    isEditing = !isEditing;
    ToggleTextBoxes(isEditing);
}

function AddNewProfile()
{
    
    var nameFields = "<div>Last Name:<input type = 'text' id = 'newLastName'><br>First Name<input type = 'text' id = 'newFirstName'></div>";
    
    MessageBox.Show('Create New Applicant Profile', 'How do you want the profile to be created?', 
                    [ {'title':'I will do it', 'callBack':msgCb1}, {'title':'Let the applicant fill up a form','callBack':msgCb2} ]);
    
    function msgCb1(){
        MessageBox.Hide();
    }
    
    function msgCb2(){
        MessageBox.Show("Application form", "Enter applicant's name" + nameFields,
                        [{'title':'Next', 'callBack':msgCb2_1}]);
        
        function msgCb2_1()
        {
            
            $.post( "amsServer.php", { lastName:$("#newLastName").val(), firstName:$("#newFirstName").val(), method: "GenerateApplicantFormID" }, function(data){
                
                if(data != "wrong")
                {
                    var applicationUrl = window.location.host + "/DSSFilters/applicationForm.html?" + data;
                    var idField = "<input type = 'text' id = 'applicationID' value ='"+applicationUrl+"'>";

                    MessageBox.Show('Application form link', 'Please copy then send the link to the applicant' + idField,
                                [{'title':'Copy link and close', 'callBack':msgCb2_2}]);
                }
            
            });
            function msgCb2_2(){
                MessageBox.Hide();
            }
        }
        
    }
}

function ToggleTextBoxes(value)
{
    var textboxes = $(".cp #profileContent input, .cp #profileContent textarea"); //.disabled = isEditing;
    
    for(var i = 0; i < textboxes.length; i++)
    {
        textboxes[i].disabled = !value;   
    }
}

$(document).on('click', '.cp #profileTabs li', function(e){
    var id = $(this).attr("id") - 1;
    
    $(".cp #profileTabs li").removeClass("selected");
    $(this).addClass("selected");
    
    
    switch(activeProfilePart)
    {
        case 2:
            repeat = activeProfileEmployment.length;
            break;
            
        case 3:
            repeat = activeProfileEducation.length;
            break;
        default:
            repeat = 1;
            break;
    }
//    alert(activeProfilePart + "|" + repeat);
    
    LoadPart( "Candidates/profileTabs/profile" + id + ".html", "#profileContents", 
            function(){
                ToggleTextBoxes(isEditing);
                LoadProfilePart(id);
            },repeat);
    
    
});

$(document).on('click', '.cp #foldersPane #folders>li>div', function(e){

    if($(this).parent().hasClass("hidden"))
    {
        $(this).parent().removeClass("hidden");

    }
    else
    {
        var ul = $(this).parent().children("ul");
        $(ul).css({"transition": "0s", height: $(ul).outerHeight() + "px" });
        $(this).parent().addClass("hidden");
        $(ul).removeAttr("style");
    }
    
//    $(this).parent().children("ul").slideToggle(10000);
    
});



