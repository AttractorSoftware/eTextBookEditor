<?php

    require_once "lib/Util.class.php";

    $content = $_POST['content'];
    $slug = $_POST['book'];

    $rootDir = $tempDir = dirname(__FILE__)."/books/" . $slug;
    $tmpDir = dirname(__FILE__)."/tmp/" . $slug;
    mkdir($rootDir);
    $rootDir .= "/" . $slug;

    $templateDir = dirname(__FILE__)."/template";
    $cssDir = $rootDir . '/css';
    $fontsDir = $rootDir . '/fonts';
    $jsDir = $rootDir . '/js';
    $imgDir = $rootDir . '/img';
    $modulesDir = $rootDir . '/modules';
    $contentDir = $rootDir . '/content';
    $videoContentDir = $contentDir . '/video';
    $audioContentDir = $contentDir . '/audio';
    $imgContentDir = $contentDir . '/img';
    $infoFilePath = $rootDir . '/book.info';
    $moduleFilePath = $rootDir . '/modules/' . $_POST['module'];

    mkdir($rootDir);
    mkdir($cssDir);
    mkdir($jsDir);
    mkdir($fontsDir);
    mkdir($contentDir);
    mkdir($videoContentDir);
    mkdir($audioContentDir);
    mkdir($imgContentDir);
    mkdir($modulesDir);

    Util::copyFilesFromDirectory($templateDir . "/css", $cssDir);
    Util::copyFilesFromDirectory($templateDir . "/js", $jsDir);
    Util::copyFilesFromDirectory($templateDir . "/fonts", $fontsDir);
    Util::copyFilesFromDirectory($templateDir . "/img", $imgDir);

    if(is_dir($tmpDir)) {
        Util::copyFilesFromDirectory($tmpDir . '/content/img', $imgContentDir);
        Util::copyFilesFromDirectory($tmpDir . '/content/audio', $audioContentDir);
        Util::copyFilesFromDirectory($tmpDir . '/content/video', $videoContentDir);
    }

    $indexContent = file_get_contents($templateDir . "/index.html");

    $content = str_replace('/tmp/' . $slug . '/', '', $content);

    file_put_contents(
        $moduleFilePath,
        str_replace(
            array("-- title --", "-- content --"),
            array('', $content),
            $indexContent
        )
    );

    if(is_file($rootDir . "/../../" . $slug . ".etb")) {
        unlink($rootDir . "/../../" . $slug . ".etb");
    }

    Util::zip($rootDir . "/../", $rootDir . "/../../" . $slug . ".etb");
    Util::removeDir($tempDir);

    echo $slug. '.etb';