<?php

namespace eTextBook\LoungeBundle\Lib;

class FileManager {

    public function copyFilesFromDirectory($sourceDir, $targetDir) {
        $handle = opendir($sourceDir);
        while(false !== ($entry = readdir($handle))){
            if(!is_dir($entry)){
                copy($sourceDir . "/" . $entry, $targetDir . "/" . $entry);
            }
        }
    }

    public function zip($source, $destination) {

        $sourceParts = explode('/', $source);

        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
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

    public function unzip($zipFile, $destinationPath) {
        $zip = new \ZipArchive();
        $archive = $zip->open($zipFile);
        if ($archive === true) {
            if(!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777);
            }
            $zip->extractTo($destinationPath);
            $zip->close();
        }
        return $archive;
    }

    public function fileList($dir) {
        $files = array();
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $file = explode('/', $path);
            $files[] = $file[count($file) - 1];
        }
        return $files;
    }

    public function removeDir($dir) {

        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }

        rmdir($dir);
    }

}