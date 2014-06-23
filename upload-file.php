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
    }

    echo '/' . $uploadFilePath;
