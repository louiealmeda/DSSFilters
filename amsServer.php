<?php

require_once("dbconn.php");

$db = new db();
$db->connect();


switch($_POST['method'])
{
    case "GenerateApplicantFormID":
        GenerateApplicantFormID();
        break;

    case "SaveApplicantForm":
        SaveApplicantForm();
        break;
    
    case "fetchApplicationName":
        fetchApplicationName();
        break;
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
//Applicant Information
    $txtStreetAddress = $_POST['txtStreetAddress'];
    $txtUnitNumber = $_POST['txtUnitNumber'];
    $dateBirthday = $_POST['dateBirthday'];

    $profilePicture = $_FILES['profilePicture'];
    $resume = $_FILES['resume'];

    $txtCity = $_POST['txtCity'];
    $txtCountry = $_POST['txtCountry'];
    $txtZIP = $_POST['txtZIP'];

    $txtPhone = $_POST['txtPhone'];
    $txtEmail = $_POST['txtEmail'];

    $txtPositionAppliedFor = $_POST['txtPositionAppliedFor'];
    $txtDesiredSalary = $_POST['txtDesiredSalary'];

    $txtSkills = $_POST['txtSkills'];

//Education
    $txtSchool = $_POST['txtSchool'];
    $txtSchoolAddress = $_POST['txtSchoolAddress'];

    $txtDegree = $_POST['txtDegree'];
    $DidGraduated = $_POST['DidGraduated'];
    $dateSchoolFrom = $_POST['dateSchoolFrom'];
    $dateSchoolTo = $_POST['dateSchoolTo'];

//Previous Employment
    $txtCompany = $_POST['txtCompany'];
    $txtCompanyPhone = $_POST['txtCompanyPhone'];

    $txtCompanyAddress = $_POST['txtCompanyAddress'];
    $txtCompanySupervisor = $_POST['txtCompanySupervisor'];

    $txtCompanyJobTitle = $_POST['txtCompanyJobTitle'];
    $txtCompanyStartingSalary = $_POST['txtCompanyStartingSalary'];
    $txtCompanyEndingSalary = $_POST['txtCompanyEndingSalary'];

    $txtCompanyResponsibilities = $_POST['txtCompanyResponsibilities'];

    $dateCompanyFrom = $_POST['dateCompanyFrom'];
    $dateCompanyTo = $_POST['dateCompanyTo'];
    $txtCompanyReasonForLeaving = $_POST['txtCompanyReasonForLeaving'];

//Disclaimer and Signature
    $radioAgreement = $_POST['radioAgreement'];

    
    //////////////////
//    $url = $_POST['urlID'];
//    
//    $query = "SELECT firstName, lastName FROM applicationForm WHERE URLID = '$url'";
//    
//    $ret = mysql_query($query);
//    
//    if($ret)
//    {
//        $ret = mysql_fetch_assoc($ret);
//        echo $ret['lastName'] . "<break>" . $ret['firstName'];
//    }
//    else
//        die(mysql_error());
//    
//    
//    
//    $query = "";
    
}


    




?>