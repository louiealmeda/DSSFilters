var isEditing = false;

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
                
                var applicationUrl = window.location.host + "/DSSFilters/applicationForm.html?" + data;
                var idField = "<input type = 'text' id = 'applicationID' value ='"+applicationUrl+"'>";

                MessageBox.Show('Application form link', 'Please copy then send the link to the applicant' + idField,
                            [{'title':'Copy link and close', 'callBack':msgCb2_2}]);
            
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
    
    LoadPart( "Candidates/profileTabs/profile" + id + ".html", "#profileContents", 
            function(){
                 ToggleTextBoxes(isEditing);
            });
    
    
});

$(document).on('click', '.cp #foldersPane #folders>li>div', function(e){

    if($(this).parent().hasClass("hidden"))
        $(this).parent().removeClass("hidden");
    else
    {
        var ul = $(this).parent().children("ul");
        $(ul).css({"transition": "0s", height: $(ul).outerHeight() + "px" });
        $(this).parent().addClass("hidden");
        $(ul).removeAttr("style");
    }
    
//    $(this).parent().children("ul").slideToggle(10000);
    
});

