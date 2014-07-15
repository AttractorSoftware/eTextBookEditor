<?php
    require_once "lib/eTextBook.class.php";

    header('Content-Type: application/json');

    $module = $_POST['module'];
    $slug = Util::slugGenerate($module['title']);
    $book = new eTextBook($module['bookSlug']);

    $response = array('status' => 'success', 'data' => array(
        'slug' => $slug
    ));

    $book->createModule($module);

    print_r(json_encode($response));