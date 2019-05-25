<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type:application/json');

define('MAX_SIZE_PICTURE', 2097152);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [];
    $response['success'] = FALSE;
    $response['errs']    = [];
    
    if (isset($_FILES)) {
       // $response['filesInfo'] = get_files_objects();
       
        $errors = [];
        $uploads_path = "..\\Uploads\\";
        $allow_extensions = ['jpg', 'jpeg', 'png'];

        if(isset($_POST['userdata'])){
            $userData = json_decode($_POST['userdata']);

            $uploads_path = sprintf("%s%s\\",$uploads_path, $userData -> user);

            if(!file_exists($uploads_path) && mkdir($uploads_path, 0777, true)){
                $response['userDirectory'] = "El directorio fue creado exitosamente";
            }
            
            if(!file_exists($uploads_path)){
                $errors[] = "The directory for this user can't be created";
            }   
        }
 
        $count_files = count($_FILES['files']['tmp_name']);

        for ($i = 0; $i < $count_files; $i++) {
            $file_name = $_FILES['files']['name'][$i];
            $file_tmp  = $_FILES['files']['tmp_name'][$i];
            $file_type = $_FILES['files']['type'][$i];
            $file_size = $_FILES['files']['size'][$i];
            $parts = explode('.', $file_name);
            $file_ext =  strtolower(end($parts));

            //echo mime_content_type($file_tmp);

            $path = $uploads_path . $file_name;

            if (!in_array($file_ext, $allow_extensions)) {
                $errors[] = sprintf("Esta extension no está permitida '%s' en %s", $file_type, $file_name);
                $errors[] = mime_content_type($file_tmp);
            }


            if ($file_size > MAX_SIZE_PICTURE) {
                $fs =  $file_size / pow(1024, 2);
                $errors[] = sprintf("%s es demasiado grande. Tamaño actual: %.2fMB, permitido: %dMB", $file_name, $fs, MAX_SIZE_PICTURE / pow(1024, 2));
                break;
            }

            if(!file_exists($path)){
                if (empty($errors)) {
                    move_uploaded_file($file_tmp, $path);
                } else {
                    break;
                }
            }
            
        }

        if (empty($errors)) {
            $response['success'] = TRUE;
        }

        $response['errs'] = $errors;
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}


class File extends stdClass
{
    public $filename;
    public $tmp_name;
    public $type;
    public $size;
    public $mimetype;
    public $error;
    public $extension;
}

function get_files_objects()
{
    $files = array();

    $count_files = count($_FILES['files']['tmp_name']);

    for ($i = 0; $i < $count_files; $i++) {
        $file = new File();

        $file -> filename = $_FILES['files']['name'][$i];
        $file -> tmp_name  = $_FILES['files']['tmp_name'][$i];
        $file -> type = $_FILES['files']['type'][$i];
        $file -> size = $_FILES['files']['size'][$i];
        $parts = explode('.', $file -> filename);
        $file ->extension =  strtolower(end($parts));
        $file -> mimetype = mime_content_type($file -> tmp_name);
        $file -> error    = $_FILES['files']['error'][$i];

        $files[] = $file;
    }

    return $files;
}
