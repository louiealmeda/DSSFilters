$(document).ready(function(){
    var str =  window.location.search;
    
    str = str.replace("?", "");
    $("#applicationID").attr( "value", str );
    
    $.post("amsServer.php", { method: "fetchApplicationName", urlID:str}, function(data){
        if(data == "invalid")
            window.location.replace("home.html");
        

        data = data.split("<break>");
        $("#lastName").html(data[0]);
        $("#firstName").html(data[1]);
        $("#footer").html(data[2]);
        
    });
    
});

function AddNewEmployment(sender)
{
    $(sender).parent().parent().before($("#tmpEmployment").children("tbody").html());   
}

function AddNewEducation(sender)
{
    $(sender).parent().parent().before($("#tmpEducation").children("tbody").html());   
}