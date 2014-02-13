<?php 
    $count = $_POST['count'];

    for($i = 0; $i< $count; $i++)
    {

        $file=fopen("components/profileResult.html","r") or exit("Unable to open file!");
        while (!feof($file))
        {
            echo fgetc($file);
        }
        fclose($file);
        
    }
?>