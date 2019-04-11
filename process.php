<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type:application/json');

define('MAX_SIZE_PICTURE', 2097152);

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $response = [];
    $response['success'] = FALSE;
    $response['errs']   = [];

    if(isset($_FILES))
    {
        
        $errors = [];
        $uploads_path = "..\\Uploads\\";
        $allow_extensions = ['jpg', 'jpeg', 'png'];

        $count_files = count($_FILES['files']['tmp_name']);

        //$response['files'] = $_FILES;
        
        //print_r($_FILES);

        for($i = 0; $i < $count_files; $i++)
        {
            $file_name = $_FILES['files']['name'][$i];
            $file_tmp  = $_FILES['files']['tmp_name'][$i];
            $file_type = $_FILES['files']['type'][$i];
            $file_size = $_FILES['files']['size'][$i];
            $parts = explode('.',$file_name);
            $file_ext =  strtolower(end($parts));

            //echo mime_content_type($file_tmp);

            $path = $uploads_path.$file_name;

            

            if(!in_array($file_ext, $allow_extensions))
            {
                $errors[] = sprintf("Esta extension no está permitida '%s' en %s",$file_type, $file_name);
                $errors[] = mime_content_type($file_tmp);
            }


            if($file_size > MAX_SIZE_PICTURE)
            {
                $fs =  $file_size / pow(1024,2);
                $errors[] = sprintf("%s es demasiado grande. Tamaño actual: %.2fMB, permitido: %dMB", $file_name, $fs, MAX_SIZE_PICTURE/ pow(1024,2));
                break;
            }

            if(empty($errors))
            {
                move_uploaded_file($file_tmp, $path);
            }else
            {
                break;
            }

        }

        if(empty($errors))
        {
            $response['success'] = TRUE;
        }

        $response['errs'] = $errors;
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
