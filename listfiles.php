<?php


if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $response = [];
    $response['success'] = FALSE;
    $response['errs']    = null;
    $errors = [];


    if(isset($_GET['user']))
    {
        $username = trim((string)$_GET['user']);

        $uploads_path = "..\\Uploads\\";
        $uploads_path = sprintf("%s%s\\",$uploads_path, $username);


        if(file_exists($uploads_path))
        {
            $directory = dir($uploads_path);
            
            while(($content = $directory -> read()) !== FALSE){
                if(is_file($uploads_path.$content)){
                    $filehash = md5_file($uploads_path.$content);
                   // print_r($content);
                    echo "$content\t\t\t($filehash)<br>";
                }
                
            }
           
        }else
        {
            $errors[] = sprintf("The user %s have not a folder. :(",$username);

        }


    }else
    {
        $errors[] = "You must provide a user.";
    }


    if(empty($errors)){
        $response['success'] = TRUE;
    }else{
        $response['errs'] = $errors;
    }

    echo json_encode($response);


    echo '<pre>';
    print_r($_SERVER);
    echo '</pre>';
}

?>