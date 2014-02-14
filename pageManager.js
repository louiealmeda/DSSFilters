function Create2DArray(rows) {
  var arr = [];

  for (var i=0;i<rows;i++) {
     arr[i] = [];
  }

  return arr;
}


$(document).ready(function(){
    
    var navbarPages = Create2DArray(4);
    var id = 0;
    var subId = 0;
    
    navbarPages[0] = "";
    navbarPages[1][0] = "Candidates/page_ManageProfiles.html";
        navbarPages[1][1] = "Candidates/page_CandidateSearch.html";
    navbarPages[2] = "";
    navbarPages[3] = "";
    
    $("#header #navBar li").click(function(){

        id = parseInt( $(this).attr("id")) -1;
        $("#header #navBar li").removeClass("selected");
        
        $(this).addClass("selected");
        
        LoadPage(navbarPages[id][subId]);
        
    });
    
    $("#submenuBarContainer #submenuBar li").click(function(){
         $("#submenuBarContainer #submenuBar *").removeClass("selected");
         $(this).addClass("selected");
         
         subId = parseInt( $(this).attr("id")) -1;
         
//         alert(navbarPages[id][subId]);
         LoadPage(navbarPages[id][subId]);
         
    });
    
    
    

//    LoadPage("Candidates/page_CandidateSearch.html");
//    LoadPage("Candidates/index.html");
    

});


function RefreshResults()
{
    var url = "ProfileSearch.php";
    $.post( url, {count: 10}, function(data){
        $("#overFlow").html(data);
    });
    
}

function LoadPart(path, part)
{
    var url = "PageLoader.php";
    $.post( url, {path: path, method: "LoadPage"}, function(data){
        $(part).html(data);
    });
}

function LoadPage(path)
{
    var url = "PageLoader.php";
    $.post( url, {path: path, method: "LoadPage"}, function(data){
       
        $("#pageArea").html(data);
        
    });
}