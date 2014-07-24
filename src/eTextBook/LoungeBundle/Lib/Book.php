<?php

namespace eTextBook\LoungeBundle\Lib;

class Book {

    private $filePath;
    private $slug;
    private $title;
    private $tmpDir;
    private $images = array();
    private $videos = array();
    private $audios = array();
    private $fileManager;

    function __construct($filePath = false) {
        if($filePath) {
            global $kernel;
            $this->fileManager = new FileManager();
            $this->filePath = $filePath;
            $this->tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir');
            $this->extractData();
            $this->parseSlug();
            $this->parseTitle();
            $this->modulesPath = $this->tmpDir . '/' . $this->getSlug() .'/modules/';
            $this->parseImages();
            $this->parseAudio();
            $this->parseVideo();
        }
    }

    public function parseBookContent($filePath) {
        $bookContent = file_get_contents($filePath);
        $bookContent = explode('<e-text-book>', $bookContent);
        $bookContent = explode('</e-text-book>', $bookContent[1]);
        $bookContent = $bookContent[0];
        $bookContent = str_replace('content/', '/tmp/' . $this->slug . '/content/', $bookContent);
        $bookContent = "<e-text-book>" . $bookContent . "</e-text-book>";
        return $bookContent;
    }

    public function getFirstModuleContent() {

        $modules = $this->fileManager->fileList($this->modulesPath);

        if(count($modules)) {
            sort($modules);
            $content = $this->getModuleContent($modules[0]);
        } else { $content = ''; }

        return $content;
    }

    public function getModuleContent($filePath) {
        return $this->parseBookContent($this->modulesPath . $filePath);
    }

    public function getModules() {
        $modules = $this->fileManager->fileList($this->tmpDir . '/' . $this->getSlug() . '/modules');
        sort($modules);
        return $modules;
    }

    public function extractData() {

        $zip = new \ZipArchive();
        $archive = $zip->open($this->filePath);

        if($archive === true) {
            $zip->extractTo($this->tmpDir);
            $zip->close();
        }

        return $archive;
    }

    public function parseImages() {
        $images = $this->fileManager->fileList($this->tmpDir . '/' . $this->slug . '/content/img');
        if(count($images)) {
            foreach($images as $img) {
                $img = explode('.', $img);
                $this->images[] = array(
                    'title' => $img[0]
                    ,'extension' => $img[1]
                );
            }
        }

    }

    public function parseAudio() {
        $audios = $this->fileManager->fileList($this->tmpDir . '/' . $this->slug . '/content/audio');
        if(count($audios)) {
            foreach($audios as $audio) {
                $audio = explode('.', $audio);
                $this->audios[] = array(
                    'title' => $audio[0]
                ,'extension' => $audio[1]
                );
            }
        }

    }

    public function parseVideo() {
        $videos = $this->fileManager->fileList($this->tmpDir . '/' . $this->slug . '/content/video');
        if(count($videos)) {
            foreach($videos as $video) {
                $video = explode('.', $video);
                $this->videos[] = array(
                    'title' => $video[0]
                ,'extension' => $video[1]
                );
            }
        }

    }

    public function parseTitle() {
        if(is_file($this->tmpDir . $this->slug . '/book.info')) {
            $bookInfo = file_get_contents($this->tmpDir . $this->slug . '/book.info');
            $bookInfo = explode('=+=', $bookInfo);
            $this->title = trim($bookInfo[1]);
        } else {
            die('info file "'. $this->tmpDir . $this->slug . '/book.info' .'" not found');
        }
    }

    public function getTitle() {
        return $this->title;
    }

    private function parseSlug() {
        $bookSlug = explode('/', $this->filePath);
        $bookSlug = end($bookSlug);
        $bookSlug = explode('.', $bookSlug);
        $this->slug = $bookSlug[0];
    }

    public function getSlug() {
        return $this->slug;
    }

    public function createModule($title, $templateDir) {
        $transliterate = new Transliterate();
        $slug = $transliterate->transliterate($title, 'ru');

        $indexContent = file_get_contents($templateDir . "/index.html");

        $content = '<e-text-book>
                <module>
                    <module-title>
                        <edit-element class="module-element">Название модуля</edit-element>
                        <view-element class="module-element">' . $title . '</view-element>
                    </module-title>
                    <module-background-image>&nbsp;</module-background-image>
                    <module-questions>
                        <edit-element class="module-element">Ключевые вопросы модуля</edit-element>
                        <view-element class="module-element"></view-element>
                    </module-questions>
                    <module-description>
                        <edit-element class="module-element">Описание модуля</edit-element>
                        <view-element class="module-element"></view-element>
                    </module-description>
                    <blocks></blocks>
                </module>
            </e-text-book>';

        file_put_contents(
            $this->tmpDir . '/' . $this->getSlug() . '/modules/' . $slug . '.html',
            str_replace(
                array("-- title --", "-- content --"),
                array($title, $content),
                $indexContent
            )
        ); return $slug;
    }

    public function getImages() {
        return $this->images;
    }

    public function getVideos() {
        return $this->videos;
    }

    public function getAudios() {
        return $this->audios;
    }

}