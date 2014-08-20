<?php

namespace eTextBook\LoungeBundle\Lib;

class Book
{

    private $filePath;
    private $slug;
    private $info;
    private $tmpDir;
    private $images = array();
    private $videos = array();
    private $audios = array();
    private $fileManager;

    function __construct($filePath = false)
    {
        if ($filePath) {
            global $kernel;
            $this->fileManager = new FileManager();
            $this->filePath = $filePath;
            $this->parseSlug();
            $this->tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir') . $this->getSlug();
            $this->extractData();
            $this->parseInfo();
            $this->modulesPath = $this->tmpDir . '/modules/';
            $this->parseImages();
            $this->parseAudio();
            $this->parseVideo();
        }
    }

    public function parseBookContent($filePath)
    {
        $bookContent = file_get_contents($filePath);
        $bookContent = explode('<e-text-book>', $bookContent);
        $bookContent = explode('</e-text-book>', $bookContent[1]);
        $bookContent = $bookContent[0];
//        $bookContent = str_replace('content/', '/tmp/' . $this->slug . '/content/', $bookContent);
        $bookContent = "<e-text-book>" . $bookContent . "</e-text-book>";

        return $bookContent;
    }

    public function getFirstModuleContent()
    {
        $modules = $this->info->modules;
        if (count($modules)) {
            $content = $this->getModuleContent($modules[0]->slug);
        } else {
            $content = '';
        }
        return $content;
    }

    public function getModuleContent($slug)
    {
        return $this->parseBookContent($this->modulesPath . $slug . '.html');
    }

    public function getModules()
    {
        return $this->info->modules;
    }

    public function extractData()
    {

        $zip = new \ZipArchive();
        $archive = $zip->open($this->filePath);

        if ($archive === true) {
            $zip->extractTo($this->tmpDir);
            $zip->close();
        }

        return $archive;
    }

    public function parseImages()
    {
        $images = $this->fileManager->fileList($this->tmpDir . '/content/img');
        if (count($images)) {
            foreach ($images as $img) {
                $img = explode('.', $img);
                $this->images[] = array(
                    'title' => $img[0]
                ,
                    'extension' => $img[1]
                );
            }
        }

    }

    public function parseAudio()
    {
        $audios = $this->fileManager->fileList($this->tmpDir . '/content/audio');
        if (count($audios)) {
            foreach ($audios as $audio) {
                $audio = explode('.', $audio);
                $this->audios[] = array(
                    'title' => $audio[0]
                ,
                    'extension' => $audio[1]
                );
            }
        }

    }

    public function parseVideo()
    {
        $videos = $this->fileManager->fileList($this->tmpDir . '/content/video');
        if (count($videos)) {
            foreach ($videos as $video) {
                $video = explode('.', $video);
                $this->videos[] = array(
                    'title' => $video[0]
                ,
                    'extension' => $video[1]
                );
            }
        }

    }

    public function parseInfo()
    {
        if (is_file($this->tmpDir . '/book.info')) {
            $this->info = json_decode(file_get_contents($this->tmpDir . '/book.info'));
        }
    }

    public function updateInfo()
    {
        file_put_contents($this->tmpDir . '/book.info', json_encode($this->info));
    }

    public function getTitle()
    {
        ;

        return $this->info->title;
    }

    private function parseSlug()
    {
        $bookSlug = explode('/', $this->filePath);
        $bookSlug = end($bookSlug);
        $bookSlug = explode('.', $bookSlug);
        $this->slug = $bookSlug[0];
    }

    public function getSlug()
    {
        return $this->slug;
    }


    public function getImages()
    {
        return $this->images;
    }

    public function getVideos()
    {
        return $this->videos;
    }

    public function getAudios()
    {
        return $this->audios;
    }

    public function getAuthors()
    {
        return isset($this->info->authors) ? $this->info->authors : '';
    }

    public function getEditor()
    {
        return isset($this->info->editor) ? $this->info->editor : '';
    }

    public function getISBN()
    {
        return isset($this->info->isbn) ? $this->info->isbn : '';
    }

}