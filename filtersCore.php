<?php 


    require_once("dbconn.php");
    $db = new db();
    $db->connect();

    switch($_POST['method'])
    {
        case "Search":
            Search();
            break;
    }



function Search()
{
    session_start();
    
    $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;
    $limit = "LIMIT " . $_POST['limit'];
    $filters = "";
    $order = "applicantProfileID";
    $query = "SELECT * FROM applicantProfile 
    WHERE HRManagerID = {$_SESSION[HRID]} $filters ORDER BY $order $limit OFFSET $loaded";
    
    
    $ret = mysql_query($query);
    
    if($ret)
    {
        $hasNew = false;
        while($e = mysql_fetch_assoc($ret))
        {
            $hasNew = true;
            include("components/profileResult.php");
        }
        if($hasNew)
            echo '<div id = "loadMore" onclick=\'RefreshResults(true); $("#loadMore").remove();\'>Load More</div>';
    }
    else
        die(mysql_error());
    
    
    
}


function ExecuteQuery($query, $callback)
{
    
//    
}
    


?>