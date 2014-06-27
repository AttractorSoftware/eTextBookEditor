<?php

    require_once 'lib/Util.class.php';

    $file = $_FILES['upload-file'];
    $slug = $_POST['slug'];

    $fileType = explode('/', $file['type']);

    switch($fileType[0]) {
        case 'image': {
            $uploadFilePath = 'tmp/' . $slug . '/content/img/' . $file['name'];
            copy($file['tmp_name'], Util::getRootDir() . $uploadFilePath);
            break;
        }
        case 'audio': {
            $uploadFilePath = 'tmp/' . $slug . '/content/audio/' . $file['name'];
            copy($file['tmp_name'], Util::getRootDir() . $uploadFilePath);
            break;
        }
        case 'video': {
            $uploadFilePath = 'tmp/' . $slug . '/content/video/' . $file['name'];
            copy($file['tmp_name'], Util::getRootDir() . $uploadFilePath);
            break;
        }
    }

    echo $fileType[0] . '||' . $file['name'];
