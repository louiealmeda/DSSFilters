<?php

require_once("dbconn.php");

$db = new db();
$db->connect();

switch($_POST['method'])
{
    case "SessionCheck":
        SessionCheck();
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

    case "SaveApplicantForm":
        SaveApplicantForm();
        break;
    
    case "fetchApplicationName":
        fetchApplicationName();
        break;
    
    case "GetFoldersWithContent":
        GetFoldersWithContent();
        break;
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
    
    echo "Success";
    
}

function GetFoldersWithContent()
{
    
}

function fetchApplicationName()
{
    $url = $_POST['urlID'];
    
    $query = "SELECT firstName, lastName FROM applicationForm WHERE URLID = '$url'";
    
    $ret = mysql_query($query);
    
    if($ret)
    {
        $ret = mysql_fetch_assoc($ret);
        echo $ret['lastName'] . "<break>" . $ret['firstName'];
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
    
    $query = "INSERT INTO applicationForm(lastName, firstName, URLID) VALUES('$last', '$first', '$url')";
    
    
    
    $ret = mysql_query($query);
    if(!$ret)
        die(mysql_error());
    
    echo $url;
}


function SaveApplicantForm()
{
    //////////REMOVE THIS AFTERWARDS
    $_POST["urlID"] = "wkAwpjKqD3qByBMPXMHZxYZMSLwdTb1DA1zpX0TZ20an9n1Vx9uiWKumTgFk";
    $_POST['selectGender'] = 'male';
    $_POST['txtDesiredSalary'] = implode("", explode("," , trim($_POST['txtDesiredSalary'])));
    $_POST['txtOverview'] = "test";
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
    
    //////////////
    
    foreach ($_POST as $value)
    {
        $value = mysql_real_escape_string($value);
    }
    
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
    
    $ret = mysql_query($query);
    
    if($ret)
    {
        print_r(mysql_fetch_assoc($ret));
        
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

?>