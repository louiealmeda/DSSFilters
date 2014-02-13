<?php 

$method = $_POST['method'];

if($method == "LoadPage")
{
    LoadPage();
}

function LoadPage()
{
//    InsertValueInLine("test[[key]]test", "boom", "key");
    $path = "components/" . $_POST['path'];

    $file=fopen($path,"r") or exit("Unable to open $path!");
    while (!feof($file))
    {
        $line = fgetc($file);
        
        
        echo $line;
    }
    fclose($file);
}

function InsertValueInLine($original, $key, $new)
{
    
    $original = "Name: [[key]] end of name";
    $key = "[[key]]";
    $new = "Mark Louie Almeda";
    echo str_replace($key,$new, $original);
    
}

function InsertValuesIntoHTML($data)
{
    
}

?>