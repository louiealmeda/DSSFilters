<?php 
    $count = $_POST['count'];

    for($i = 0; $i< $count; $i++)
    {
        $name = "Dante";
        $jobTitle = "Programmer";
        $yearsOfExperience = "5yrs";
        $desiredPosition = "Software Engineer";
        $overview = "The quick brown fox jumps over the lazy dog. Big blue eyes, pointy nose chasing mice and digging holes. Tiny paws up the hill, suddenly you're standing still";
        
        include("components/profileResult.php");   
    }
?>