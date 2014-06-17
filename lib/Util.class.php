<?php

    class Util {

        public static function copyFilesFromDirectory($sourceDir, $targetDir) {
            $handle = opendir($sourceDir);
            while(false !== ($entry = readdir($handle))){
                if(!is_dir($entry)){
                    copy($sourceDir . "/" . $entry, $targetDir . "/" . $entry);
                }
            }
        }

        public static function slugGenerate($word) {
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

        public static function zip($source, $destination) {

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

        public static function fileList($dir) {
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                $file = explode('/', $path);
                $files[] = $file[count($file) - 1];
            }
            return $files;
        }

        public static function getRootDir() {
            return dirname(__FILE__).'/../';
        }

        public static function removeDir($dir) {

            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
            }

            rmdir($dir);
        }

    }