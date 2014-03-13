var loadedCount = 0;
var limit = 10;


var skills = ["c#", "vb", "test", "a", "ab", "abc", "abcd", "abcde", "abcdef", "abcdefg", "abcdefgh", "abcdefghij"];
var selectedSkills = [];

var courses = ["BSCS", "BSMA", "COE", "ECE", "BSIT", "BSA"];
var selectedCourses = [];

var jobTitles = ["Programmer", "Software Engineer", "Mobile Developer", "Wev Developer", "Graphics Artist"];
var selectedJobTitles = [];

var desiredSalaryRange = [0,0];
var yearsOfExperienceRange = [0,0];

function InitializeCandidateSearch()
{
    CreateSlider("#desiredSalary", desiredSalaryRange, ["$", " - $", ""]);
    CreateSlider("#yearsOfExperience", yearsOfExperienceRange, ["<br>", "-", "yrs"], 0, 50, 2);
    CreateAutoComplete("#addSkill", skills, "#activatedSkills", selectedSkills, "skills", "selectedSkills");
    CreateAutoComplete("#addCourse", courses, "#activatedCourses", selectedCourses, "courses", "selectedCourses");
    CreateAutoComplete("#addJobTitle", jobTitles, "#activatedJobTitles", selectedJobTitles, "jobTitles", "selectedJobTitles");
    
    
    $.post("amsServer.php", {method:"GetFolders"}, function(data){
//        alert(data);
        var folders = data.split("|");
        folders.forEach(function(e, index){
            var parts = e.split(",");
            $("#selectBatchFolder").append("<option value = '"+parts[0]+"' >"+parts[1]+"</option>");
        });
        
    });
    
    
}


function CreateAutoComplete(id, sourceArray, activatedBoxID, selectedArray, sourceArrayStringName, selectedArrayStringName)
{
    $(id).autocomplete({
        source: sourceArray,
        select: function( event, ui ) 
        {
            $(activatedBoxID).append("<span id = '"+ui.item.value+"' >" + ui.item.value + "<span onclick = \"removeFromSelectedBox('"+ui.item.value+"', "+sourceArrayStringName+", "+selectedArrayStringName+", '"+activatedBoxID+"');\">x</span></span>");
            selectedArray.push(ui.item.value);
            removeElement(sourceArray, ui.item.value);
            
            $(id).autocomplete("option", { source: sourceArray });
            $(this).val(''); return false;
        }
    });
}


function CreateSlider(id, valueHolder, unit, min, max, step, values)
{
    
    min = min || 0;
    max = max || 100;
    step = step || 10;
    values = values || [min, max];
    unit = unit || ["","",""];
    $( id ).slider({
        range: true,
        min: min,
        max: max,
        step: step,
        values: values,
        slide: function( event, ui ) {
            if(ui.values[1] - ui.values[0] < step){
                return false;
            }
            valueHolder = [ui.values[ 0 ] , ui.values[ 1 ]];     
            $(this).siblings("#amount").children("span" ).html(unit[0] + ui.values[ 0 ] + unit[1] + ui.values[ 1 ] + unit[2] );
        }
    });
}

function removeFromSelectedBox(value, src, selected, boxID )
{

    src.push(value);
    src.sort();
    removeElement(selected, value);
    $(boxID + " #" + value).remove();
}

function removeElement(collection, element)
{
    var index = collection.indexOf(element);
    if (index > -1)
        collection.splice(index, 1);
}

function SetBatchFolder()
{
    var folder = $("#selectBatchFolder").val();
    
    MessageBox.Show("Apply to all", "Are you sure you want to put all the profile results to "+folder+" folder?</br>You cannot undo this action",
                   [{'title':'Cancel', 'callBack': MessageBox.Hide}, {'title':'Yes', 'callBack': function(){
                       
                   }}]);
}


function RefreshResults(append)
{
    if(!append)
    {
        $("#overFlow").html("");
    }
    else
    {
        loadedCount += limit;
    }
    
    $.post( "filtersCore.php", { method: "Search", limit: 10, loaded:loadedCount}, function(data){
//        alert(data);
        if(!append)
            loadedCount = loadedCount;
            
//        alert($("#overFlow #loadMore").html());
        $("#overFlow").append(data);
        
    });
    
//    var url = "ProfileSearch.php";
//    $.post( url, {count: 10}, function(data){
//        $("#overFlow").html(data);
//    });
}