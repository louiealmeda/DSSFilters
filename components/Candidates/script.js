var isEditing = false;

function ToggleEdit()
{
    var textboxes = $(".cp #profileContent input, .cp #profileContent textarea"); //.disabled = isEditing;
    
    for(var i = 0; i < textboxes.length; i++)
    {
        textboxes[i].disabled = isEditing;   
    }
    
    var caption = "Edit";
    if(!isEditing)
        caption = "Done";
    
    $(".cp #profileEditButton").html(caption);
    
    
    isEditing = !isEditing;
}

$(document).on('click', '.cp #profileTabs li', function(e){
    var id = $(this).attr("id") - 1;
    
    $(".cp #profileTabs li").removeClass("selected");
    $(this).addClass("selected");
    
    LoadPart( "Candidates/profileTabs/profile" + id + ".html", "#profileContents");
    
});