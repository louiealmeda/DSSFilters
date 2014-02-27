$(document).ready(function(){
    var str =  window.location.search;
    
    str = str.replace("?", "");
    $("#applicationID").attr( "value", str );
});


function AddNewEmployment(sender)
{
    $(sender).parent().parent().before($("#tmpEmployment").children("tbody").html());   
}

function AddNewEducation(sender)
{
    $(sender).parent().parent().before($("#tmpEducation").children("tbody").html());   
}