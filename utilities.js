function sessionCheck(isLogin)
{
    
    var url = "utilities.php";
    $.post( url, {method:"SessionCheck", isLogin:isLogin}, function(data){
        if(data != "")
        {
            if( data.search('.html') == -1)
                $("html").html(data);
            else
                 window.location.replace(data);
        }
    });
}