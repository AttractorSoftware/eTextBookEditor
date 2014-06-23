<?php

    require_once "lib/Util.class.php";

    $title = $_POST['title'];
    $content = $_POST['content'];
    $slug = Util::slugGenerate($title);

    $rootDir = $tempDir = dirname(__FILE__)."/books/" . $slug;
    $tmpDir = dirname(__FILE__)."/tmp/" . $slug;
    mkdir($rootDir);
    $rootDir .= "/" . $slug;

    $templateDir = dirname(__FILE__)."/template";
    $cssDir = $rootDir . '/css';
    $fontsDir = $rootDir . '/fonts';
    $jsDir = $rootDir . '/js';
    $imgDir = $rootDir . '/img';
    $contentDir = $rootDir . '/content';
    $videoContentDir = $contentDir . '/video';
    $audioContentDir = $contentDir . '/audio';
    $imgContentDir = $contentDir . '/img';
    $infoFilePath = $rootDir . '/book.info';
    $indexFilePath = $rootDir . '/index.html';

    mkdir($rootDir);
    mkdir($cssDir);
    mkdir($jsDir);
    mkdir($fontsDir);
    mkdir($contentDir);
    mkdir($videoContentDir);
    mkdir($audioContentDir);
    mkdir($imgContentDir);

    Util::copyFilesFromDirectory($templateDir . "/css", $cssDir);
    Util::copyFilesFromDirectory($templateDir . "/js", $jsDir);
    Util::copyFilesFromDirectory($templateDir . "/fonts", $fontsDir);
    Util::copyFilesFromDirectory($templateDir . "/img", $imgDir);
    Util::copyFilesFromDirectory($tmpDir . '/content/img', $imgContentDir);

    file_put_contents($infoFilePath, "title =+= " . $title);

    $indexContent = file_get_contents($templateDir . "/index.html");

    $content = str_replace('/tmp/' . $slug . '/', '', $content);

    file_put_contents(
        $indexFilePath,
        str_replace(
            array("-- title --", "-- content --"),
            array($title, $content),
            $indexContent
        )
    );

    unlink($rootDir . "/../../" . $slug . ".etb");
    Util::zip($rootDir . "/../", $rootDir . "/../../" . $slug . ".etb");
    Util::removeDir($tempDir);

    echo $slug. '.etb';