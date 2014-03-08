$(document).ready(function(){
//    alert();
//    validatePassword(null, true);
});


function checkUsername(sender)
{
    $.post("amsServer.php", {method:"CheckUserName", userName: sender.value}, function(data){
//        alert(data);
        if(data == "used")
        {
            $("#txtUserName").css({"border-color":"red"});
            $("#submitBtn").prop("disabled",true);
        }
        else
        {
            $("#txtUserName").css({"border-color":"green"});
            $("#submitBtn").prop("disabled",false);
        }
    });
}

function validatePassword(sender, matching)
{
    var pass = $("#txtPassword").val();
    var state = pass == $("#txtConfirmPassword").val() && pass.trim() != "" && pass.length > 4;
    
    if(state)
    {
        $("#txtPassword").css({"border-color":"green"});
        $("#txtConfirmPassword").css({"border-color":"green"}); 
    }
    else
    {
        $("#txtPassword").css({"border-color":"red"}); 
        $("#txtConfirmPassword").css({"border-color":"red"}); 
    }
    
    $("#submitBtn").prop("disabled",!state);
    
}