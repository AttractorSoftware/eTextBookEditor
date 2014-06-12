<?php

$title = $_POST['title'];
$content = $_POST['content'];
$slug = slugGenerate($title);

$rootDir = $tempDir = dirname(__FILE__)."/books/" . $slug;
mkdir($rootDir);
$rootDir .= "/" . $slug;

$templateDir = dirname(__FILE__)."/template";
$cssDir = $rootDir . '/css';
$fontsDir = $rootDir . '/fonts';
$jsDir = $rootDir . '/js';
$videoDir = $rootDir . '/video';
$audioDir = $rootDir . '/audio';
$imgDir = $rootDir . '/img';
$infoFilePath = $rootDir . '/book.info';
$indexFilePath = $rootDir . '/index.html';

mkdir($rootDir);
mkdir($cssDir);
mkdir($jsDir);
mkdir($fontsDir);
mkdir($videoDir);
mkdir($audioDir);

copyFilesFromDirectory($templateDir . "/css", $cssDir);
copyFilesFromDirectory($templateDir . "/js", $jsDir);
copyFilesFromDirectory($templateDir . "/fonts", $fontsDir);
copyFilesFromDirectory($templateDir . "/video", $videoDir);
copyFilesFromDirectory($templateDir . "/audio", $audioDir);
copyFilesFromDirectory($templateDir . "/img", $imgDir);


file_put_contents($infoFilePath, "title =+= " . $title);

$indexContent = file_get_contents($templateDir . "/index.html");

file_put_contents(
    $indexFilePath,
    str_replace(
        array("-- title --", "-- content --"),
        array($title, $content),
        $indexContent
    )
);

Zip($rootDir . "/../", $rootDir . "/../../" . $slug . ".etb");

foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempDir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
    $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
}

rmdir($tempDir);

echo $slug. '.etb';

function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

function copyFilesFromDirectory($sourceDir, $targetDir) {
    $handle = opendir($sourceDir);
    while(false !== ($entry = readdir($handle))){
        if(!is_dir($entry)){
            copy($sourceDir . "/" . $entry, $targetDir . "/" . $entry);
        }
    }
}

function slugGenerate($word) {
    $search = array(
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о',
        'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
        ' ', '_', '!', '?', '.', ',', '"', "'"
    );

    $replace = array(
        'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'j', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o',
        'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sh', '', 'i', '', 'e', 'you', 'ya',
        '-', '-', '', '', '', '', '', ''
    );

    return str_replace($search, $replace, mb_convert_case($word, MB_CASE_LOWER, "UTF-8"));
}

