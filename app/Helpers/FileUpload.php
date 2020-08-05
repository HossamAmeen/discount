<?php

function uploadFile($requestFile , $folderName = "images")
{
    // $file = $request->{$inputName}; 
    $file = $requestFile;
    $path = public_path()."/$folderName/".date("Y-m-d");
    if(!File::isDirectory($path))
    {
        File::makeDirectory($path, 0777, true, true);
    }
    $name = rand().time().'.'.$file->getClientOriginalExtension();
    $file->move($path, $name);

    return asset( "/$folderName/".date("Y-m-d"). "/$name");
}

?>