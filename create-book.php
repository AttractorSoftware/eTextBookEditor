<?php
    require_once "lib/eTextBook.class.php";

    header('Content-Type: application/json');

    $book = $_POST['book'];
    $slug = Util::slugGenerate($book['title']);

    $response = array('status' => 'success', 'data' => array(
        'slug' => $slug
    ));

    if(is_file(Util::getRootDir() . 'books/' . $slug . '.etb')) {
        $response = array(
            'status' => 'failed'
            ,'reason' => 'Учебник с таким названием уже существует'
        );
    }

    eTextBook::create($book);

    print_r(json_encode($response));