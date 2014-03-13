<?php

require_once("dbconn.php");

$db = new db();
$db->connect();

switch($_POST['method'])
{
    
    
    
    case "LoadProfile":
        LoadProfile();
        break;
    
    case "DeleteProfile":
        DeleteProfile();
        break;
    
    case "ChangeFolder":
        ChangeFolder();
        break;
    
    case "DeleteFolder":
        DeleteFolder();
        break;
    
    case "addFolder":
        AddFolder();
        break;
    case "SessionCheck":
        SessionCheck();
        break;
    
    case "logout":
        session_start();
        if( session_destroy())
            echo "out";
        else
            echo "not";
        break;
    
    case "Login":
        Login();
        break;
    
    case "CheckUserName":
        CheckUserName();
        break;
    
    case "SignUp":
        SignUp();
        break;
    
    case "GenerateApplicantFormID":
        GenerateApplicantFormID();
        break;

    case "CheckApplicationFormValidity":
        CheckApplicationFormValidity();
        break;
    
    case "SaveApplicantForm":
        SaveApplicantForm();
        break;
    
    case "fetchApplicationName":
        fetchApplicationName();
        break;
    
    case "GetFoldersWithContent":
        GetFoldersWithContent();
        break;
    
    case "GetFolders":
        GetFolders();
        break;
    
    
}



function LoadProfile()
{
    $profileID = $_POST['profileID'];
    
//    $query = "SELECT * FROM applicantProfile as a, educationHistory as b, employmentHistory as c 
//    WHERE a.applicantProfileID = $profileID AND b.applicantProfileID = $profileID AND c.applicantProfileID = $profileID" ;
    
//    $query = "SELECT * FROM applicantProfile WHERE applicantProfileID = $profileID";
//    $query = "SELECT * FROM educationHistory WHERE applicantProfileID = $profileID";
    
    echo '{"personal":';
    QueryToJSON("SELECT * FROM applicantProfile WHERE applicantProfileID = $profileID");
    echo ',"education":[';
    QueryToJSON("SELECT * FROM educationHistory WHERE applicantProfileID = $profileID");
    echo '],"employment":[';
    QueryToJSON("SELECT * FROM employmentHistory WHERE applicantProfileID = $profileID");
    echo ']}';
    
}


function QueryToJSON($query)
{
    $ret = mysql_query($query);
    
    if($ret)
    {
        $isMoreThanOne = false;
        while($row = mysql_fetch_assoc($ret))
        {
            if($isMoreThanOne)
                echo ",";
            
            echo json_encode($row);
//            print_r($row);
            $isMoreThanOne = true;
         
        }
    }
    else
    {
        die(mysql_error());
    }
}

function Login()
{   
    sanitizePost();
    
    $query = "SELECT HRManagerID, password FROM HRManager WHERE username = '{$_POST['txtUsername']}'";
    
    $ret = mysql_query($query);
    
    if(!$ret)
        die(mysql_error());
    else
    {
        $row = mysql_fetch_assoc($ret);
        if($row['HRManagerID'] == "")
            echo "username does not exist";
        else
        {
            
            if($_POST['txtPassword'] == $row['password'])
            {
                session_start();
                $_SESSION['username'] = $_POST['txtUsername'];
                $_SESSION['HRID'] = $row['HRManagerID'];
                header("Location: index.html");
            }
            else
                echo "Wrong password";
        }
    }
    
    /////////Check if user exist
    
}

function CheckUserName()
{
    $query = "SELECT HRManagerID FROM HRManager WHERE username = '{$_POST['userName']}'";
    
    $ret = mysql_query($query);
    
    if(!$ret)
        die(mysql_error());
    else
    {
        $row = mysql_fetch_assoc($ret)['HRManagerID'];
        if($row == "")
            echo "free";
        else
            echo "used";
    }
}

function SignUp()
{
    
    foreach($_POST as $p)
        $p = mysql_real_escape_string($p);
    
    $query = "INSERT INTO `HRManager`(`username`, `email`, `password`, `firstName`, `lastName`, `company`) 
    VALUES ('{$_POST['txtUserName']}','{$_POST['txtEmail']}','{$_POST['txtPassword']}', '{$_POST['txtFirstName']}','{$_POST['txtLastName']}','{$_POST['txtCompany']}' )";
    
    $ret = mysql_query($query);
    
    if(!$ret)
        die(mysql_error());
    
    
    session_start();
    $_SESSION['username'] = $_POST['txtUserName'];
    echo "Success";
    
}


function DeleteProfile()
{
    session_start();
    $query = "DELETE FROM applicantProfile WHERE HRManagerID = {$_SESSION[HRID]} AND applicantProfileID = {$_POST['profileID']}";
    $ret = mysql_query($query);

    if(!$ret)
        die(mysql_error());

    
    GetFoldersWithContent();
}

function DeleteFolder()
{
    session_start();
    $query = "DELETE FROM folder WHERE HRManagerID = {$_SESSION[HRID]} AND folderID = {$_POST['folderID']}";
    $ret = mysql_query($query);

    if(!$ret)
        die(mysql_error());
    
    $query = "UPDATE applicantProfile SET folderID= -1 WHERE HRManagerID = {$_SESSION[HRID]} AND folderID = {$_POST['folderID']}";
    $ret = mysql_query($query);

    if(!$ret)
        die(mysql_error());
    
    
    GetFoldersWithContent();
}


function ChangeFolder()
{
    session_start();
    $query = "UPDATE applicantProfile SET folderID = {$_POST['folderID']} WHERE HRManagerID = {$_SESSION[HRID]} AND applicantProfileID = {$_POST['profileID']}";
    $ret = mysql_query($query);

    if(!$ret)
        die(mysql_error());
    
    GetFoldersWithContent();
}

function AddFolder()
{
    session_start();
    $query = "INSERT INTO folder(name, HRManagerID) VALUES('{$_POST['name']}', {$_SESSION[HRID]})";
    $ret = mysql_query($query);

    if(!$ret)
        die(mysql_error());
    
    GetFoldersWithContent();
}

function GetFolders()
{
    session_start();
    $folders = mysql_query("SELECT name, folderID FROM folder WHERE HRManagerID = {$_SESSION['HRID']} 
    ORDER BY name ASC");
    
    
    if($folders)
    {
        while($row = mysql_fetch_assoc($folders))
        {
            echo  "{$row['folderID']}, {$row['name']}|";
     
        }
    }
    else
        die(mysql_error());
    
    echo "-1, unsorted";
}

function GetFoldersWithContent()
{
    $filter = "";
    
    if(isset($_POST['nameFilter']))
    {
        $key = $_POST['nameFilter'];
        $filter = " AND (a.firstName LIKE '%$key%' OR a.lastName LIKE '%$key%') ";
    }
    
    session_start();
    $query = "SELECT applicantProfileID, a.firstName, a.lastName, a.middleInitial , folderID 
    FROM applicantProfile as a WHERE HRManagerID = {$_SESSION['HRID']} $filter
    ORDER BY a.lastName ASC
    ";
    
    $folders = mysql_query("SELECT name, folderID FROM folder WHERE HRManagerID = {$_SESSION['HRID']} 
    ORDER BY name ASC");
    
    
    if($folders)
    {
        while($row = mysql_fetch_assoc($folders))
        {
            echo  "{$row['folderID']}, {$row['name']}|";
     
        }
    }
    else
        die(mysql_error());
    
    echo "-1, unsorted";
//    echo "-2, pending";
    echo "<break>";
    
    $ret = mysql_query($query);
    if($ret)
    {
        $separator = "";
        while($row = mysql_fetch_assoc($ret))
        {
        echo $separator. $row['folderID'] . "|" . $row['lastName'] . ", " . $row['firstName'] . " " . $row['middleInitial'] . ". |" . $row['applicantProfileID'];
            $separator = "][";
        }
    }
    else
        die(mysql_query());
    

    
}

function fetchApplicationName()
{
    $url = $_POST['urlID'];
    
    $query = "SELECT firstName, lastName, HRID FROM applicationForm WHERE URLID = '$url'";
    
    
    $ret = mysql_query($query);
    
    if($ret)
    {
        $ret = mysql_fetch_assoc($ret);
        
        if($ret['lastName'] == "")
        {
            die("invalid");
        }
        
        echo $ret['lastName'] . "<break>" . $ret['firstName']."<break>";
//        echo $ret['HRID'] . "[[]]]]";
        
        $query = "SELECT company FROM HRManager WHERE HRManagerID = {$ret['HRID']}";
        $ret = mysql_query($query);
        
        if($ret)
        {
            $ret = mysql_fetch_assoc($ret);
            echo $ret['company'];
        }
        else
            die(mysql_error());
        
    }
    else
        die(mysql_error());
    
}

function GenerateApplicantFormID()
{
    $last = $_POST['lastName'];
    $first = $_POST['firstName'];
    
    if(trim($last) == "" || trim($first) == "")
    {
        echo "wrong";
        return;
    }
    
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    do
    {
        $url = ""; 

        for($i = 0; $i < 60; $i++)
        {
            $url .= $chars[rand() % strlen($chars)];
        }
        
        $query = "SELECT applicationFormID FROM applicationForm WHERE URLID = '$url'";
        $ret = mysql_fetch_assoc( mysql_query($query) )["id"];
        
        
    }while($ret);
    
    $last = mysql_real_escape_string($last);
    $first = mysql_real_escape_string($first);
    
    
    session_start();
    $HRID = $_SESSION['HRID'];
    $query = "INSERT INTO applicationForm(HRID, lastName, firstName, URLID) VALUES($HRID, '$last', '$first', '$url')";
    
    
    $ret = mysql_query($query);
    if(!$ret)
        die(mysql_error());
    
    echo $url;
}


function CheckApplicationFormValidity()
{
    $url = $_POST['urlID'];
    
    $query = "SELECT firstName, lastName, HRID FROM applicationForm WHERE URLID = '$url'";
    
    $ret = mysql_query($query);
    
    $firstName = "";
    $lastName = "";
    $HRID = "";
    
    if($ret)
    {
        $ret = mysql_fetch_assoc($ret);
        $firstName = $ret['firstName'];
        $lastName = $ret['lastName'];
        $HRID = $ret['HRID'];
        
        
        if($firstName == "")
            header("index.html");
        
    }
    else
        die(mysql_error());
}


function SaveApplicantForm()
{
    //////////REMOVE THIS AFTERWARDS
//    $_POST["urlID"] = "wkAwpjKqD3qByBMPXMHZxYZMSLwdTb1DA1zpX0TZ20an9n1Vx9uiWKumTgFk";
//    $_POST['selectGender'] = 'male';
//    $_POST['txtDesiredSalary'] = implode("", explode("," , trim($_POST['txtDesiredSalary'])));
//    $_POST['txtOverview'] = "test";
//    echo "<pre>";
//    print_r($_POST);
//    print_r($_FILES);
//    echo "</pre>";
    
    //////////////
    
    sanitizePost();
    
    $url = $_POST['applicationID'];
    
    $query = "SELECT firstName, lastName, HRID FROM applicationForm WHERE URLID = '$url'";
    
    $ret = mysql_query($query);
//    echo $query;
    $firstName = "";
    $lastName = "";
    $HRID = "";
    
    if($ret)
    {
        $ret = mysql_fetch_assoc($ret);
        
//        print_r($ret);
        
        $firstName = $ret['firstName'];
        $lastName = $ret['lastName'];
        $HRID = $ret['HRID'];
        
        
        if($firstName == "")
            header("index.html");
        
    }
    else
        die(mysql_error());

    
    $query = "INSERT INTO 
    applicantProfile(
        HRManagerID, 
        firstName, 
        lastName, 
        middleInitial, 
        overview, 
        contactNumber, 
        email, 
        appartmentUnitNumber, 
        streetAddress, 
        city, 
        country, 
        ZIP, 
        gender, 
        birthday, 
        positionAppliedfor, 
        skills, 
        desiredSalary, 
        folderID
    ) 
    VALUES(
        $HRID,
        '$firstName',
        '$lastName',
        '{$_POST['txtMiddleInitial']}',
        '{$_POST['txtOverview']}',
        '{$_POST['txtPhone']}',
        '{$_POST['txtEmail']}',
        '{$_POST['txtUnitNumber']}',
        '{$_POST['txtStreetAddress']}',
        '{$_POST['txtCity']}',
        '{$_POST['txtCountry']}',
        {$_POST['txtZIP']},
        '{$_POST['selectGender']}',
        '{$_POST['dateBirthday']}',
        '{$_POST['txtPositionAppliedFor']}',
        '{$_POST['txtSkills']}',
        {$_POST['txtDesiredSalary']},
        -1
    );";
    
    echo $query;
    
    $ret = mysql_query($query);
    
    if($ret)
    {
//        print_r(mysql_fetch_assoc($ret));
        
        $query = "SELECT applicantProfileID FROM applicantProfile ORDER BY applicantProfileID DESC LIMIT 1 ";
        $id = mysql_fetch_assoc(mysql_query($query))['applicantProfileID'];
//        echo $id . "/////";
        
//        got the id, add resume, picture, educational and working background
        
        $schools = $_POST['txtSchool'];
        
        for($i = 0; $i < count($schools); $i++)
        {
            $query = "INSERT INTO `educationHistory`(`entryDate`, `exitDate`, `school`, `schoolAddress`, `areaOfStudy`, `didGraduated`, `applicantProfileID`)
            VALUES( '{$_POST['dateSchoolFrom'][$i]}', '{$_POST['dateSchoolTo'][$i]}', '{$schools[$i]}', '{$_POST['txtSchoolAddress'][$i]}' , '{$_POST['txtDegree'][$i]}', {$_POST['DidGraduated'][$i]}, $id ) ";
            
            $ret = mysql_query($query);
            if(!$ret)
                die(mysql_error());
        }
        
        //-----------------//
        
        $companies = $_POST['txtCompany'];
        
        for($i = 0; $i < count($companies); $i++)
        {
            $query = "INSERT INTO `employmentHistory`(`entryDate`, `exitDate`, `company`, `jobTitle`, `phone`, `address`, `supervisor`, `startingSalary`, `endingSalary`, `responsibilities`, `reasonForLeaving`, `applicantProfileID`) 
            VALUES ('{$_POST['dateCompanyFrom'][$i]}', '{$_POST['dateCompanyTo'][$i]}', '{$_POST['txtCompany'][$i]}', '{$_POST['txtCompanyJobTitle'][$i]}', '{$_POST['txtCompanyPhone'][$i]}', '{$_POST['txtCompanyAddress'][$i]}', '{$_POST['txtCompanySupervisor'][$i]}', '{$_POST['txtCompanyStartingSalary'][$i]}','{$_POST['txtCompanyEndingSalary'][$i]}','{$_POST['txtCompanyResponsibilities'][$i]}','{$_POST['txtCompanyReasonForLeaving'][$i]}', $id) ";
            
            $ret = mysql_query($query);
            if(!$ret)
                die(mysql_error());
        }
        
        $picture = $_FILES['profilePicture'];
        $resume = $_FILES['resume'];
        
        
        $pictureExt = explode(".",$picture['name']);
        $pictureExt = $pictureExt[count($pictureExt) -1];
        move_uploaded_file($picture['tmp_name'], "files/profilePictures/$id.$pictureExt");
        move_uploaded_file($resume['tmp_name'], "files/resumes/$id.pdf");
        
        
        
        //////////Remove applicationFormID
        $query = "DELETE FROM applicationForm WHERE URLID = '$url'";
    
        $ret = mysql_query($query);
        if(!$ret)
            die(mysql_error());
        
        header("Location: ThankYou.html");
        
    }
    else
        die(mysql_error());
}

function SessionCheck()
{
    session_start();
    
    if( isset($_SESSION['sessionID']))
    {
        
    }
    else
    {
        header("Location: home.html");
    }
    
}

function sanitizePost()
{
    foreach ($_POST as $value)
    {
        $value = mysql_real_escape_string($value);
    }
}

?>