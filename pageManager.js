function Create2DArray(rows) {
  var arr = [];

  for (var i=0;i<rows;i++) {
     arr[i] = [];
  }

  return arr;
}

sessionCheck(false);

$(document).ready(function(){
    
    
    var pageInitializers = Create2DArray(4);
    var navbarPages = Create2DArray(4);
    var id = 0;
    var subId = 0;
    
    
//    pageInitializers[0] = "";
//    pageInitializers[1] = "";
    pageInitializers[1][0] = InitializeManageProfiles;
    pageInitializers[1][1] = InitializeCandidateSearch;
    

    
    navbarPages[0] = "";
    navbarPages[1][0] = "Candidates/page_ManageProfiles.html";
        navbarPages[1][1] = "Candidates/page_CandidateSearch.html";
    navbarPages[2] = "";
    navbarPages[3] = "";
    
    LoadPage(navbarPages[1][1], pageInitializers[1][1]);
    
    $("#header #navBar li").click(function(){

        id = parseInt( $(this).attr("id")) -1;
        $("#header #navBar li").removeClass("selected");
        
        $(this).addClass("selected");
        
        LoadPage(navbarPages[id][subId], pageInitializers[id][subId]);
//        pageInitializers[id][subId]();
        
    });
    
    $("#submenuBarContainer #submenuBar li").click(function(){
         $("#submenuBarContainer #submenuBar *").removeClass("selected");
         $(this).addClass("selected");
         
         subId = parseInt( $(this).attr("id")) -1;
         
//         alert(navbarPages[id][subId]);
         LoadPage(navbarPages[id][subId], pageInitializers[id][subId]);
//         pageInitializers[id][subId]();
    });
    

//    LoadPage("Candidates/page_CandidateSearch.html");
//    LoadPage("Candidates/index.html");
});

function Logout()
{
    $.post("amsServer.php", {method:"logout"}, function(data){
        alert(data);
        window.location.replace("home.html");
        
    });
}


function LoadPart(path, part, callback, repeat)
{
    var url = "PageLoader.php";
    $.post( url, {path: path, method: "LoadPage", repeat:repeat}, function(data){
        $(part).html(data);
        callback();
    });
    
}

function LoadPage(path, callback)
{
    callback = callback || function(){};
    
    var url = "PageLoader.php";
    $.post( url, {path: path, method: "LoadPage"}, function(data){
       
        $("#pageArea").html(data);
        callback();
    });
}