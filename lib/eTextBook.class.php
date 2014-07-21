<?php

    include_once('Util.class.php');

    class eTextBook {

        private $filePath;
        private $slug;
        private $title;
        private $content;
        private $images = array();
        private $videos = array();
        private $audios = array();
        private $tmpDir;
        private $rootDir;

        public function __construct($filePath = false) {
            $this->filePath = $filePath;
            $this->tmpDir = Util::getRootDir() . 'tmp/';
            $this->rootDir = Util::getRootDir();

            if($filePath) {
                if($this->extractData()) {
                    $this->parseSlug();
                    $this->modulesPath = $this->tmpDir . $this->getSlug() .'/modules/';
                    if(is_file($this->tmpDir . $this->slug . '/index.html')) {
                        $this->content = $this->parseBookContent($this->tmpDir . $this->slug . '/index.html');
                    }
                    $this->parseTitle();
                    $this->parseImages();
                    $this->parseAudio();
                    $this->parseVideo();
                }
            }
        }

        public function parseImages() {
            $images = Util::fileList($this->tmpDir . '/' . $this->slug . '/content/img');
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
            $audios = Util::fileList($this->tmpDir . '/' . $this->slug . '/content/audio');
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
            $videos = Util::fileList($this->tmpDir . '/' . $this->slug . '/content/video');
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

        public function parseSlug() {
            $bookSlug = explode('.', $this->filePath);
            $this->slug = $bookSlug[0];
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

        public function parseTitle() {
            if(is_file($this->tmpDir . $this->slug . '/book.info')) {
                $bookInfo = file_get_contents($this->tmpDir . $this->slug . '/book.info');
                $bookInfo = explode('=+=', $bookInfo);

                $this->title = trim($bookInfo[1]);
            }
        }

        public function extractData() {

            $zip = new ZipArchive();
            $archive = $zip->open("books/".$this->filePath);

            if($archive === true) {
                $zip->extractTo($this->tmpDir);
                $zip->close();
            }

            return $archive;
        }

        public function getSlug() {
            return $this->slug;
        }

        public function setSlug($slug) {
            $this->slug = $slug;
        }

        public function getTitle() {
            return $this->title;
        }

        public function setTitle($title) {
            $this->title = $title;
        }

        public function getContent() {
            return $this->content;
        }

        public function setContent($content) {
            $this->content = $content;
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

        public function getModules() {
            $modules = Util::fileList(Util::getRootDir() . 'tmp/' . $this->getSlug() . '/modules');
            sort($modules);
            return $modules;
        }

        public function createModule($data) {

            $slug = Util::slugGenerate($data['title']);

            $indexContent = file_get_contents(Util::getRootDir() . "template/index.html");

            $content = '<e-text-book>
                <module>
                    <module-title>
                        <edit-element class="module-element">Название модуля</edit-element>
                        <view-element class="module-element">' . $data['title'] . '</view-element>
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
                Util::getRootDir() . 'tmp/' . $this->getSlug() . '/modules/' . $slug . '.html',
                str_replace(
                    array("-- title --", "-- content --"),
                    array($data['title'], $content),
                    $indexContent
                )
            );
        }

        public static function create($data) {

            $slug = Util::slugGenerate($data['title']);

            $rootDir = $tempDir = Util::getRootDir() . "/books/" . $slug;
            $tmpDir = Util::getRootDir() . "/tmp/" . $slug;
            mkdir($rootDir);
            $rootDir .= "/" . $slug;

            $templateDir = Util::getRootDir() . "/template";
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
            $indexFilePath = $rootDir . '/index.html';

            mkdir($rootDir);
            mkdir($cssDir);
            mkdir($jsDir);
            mkdir($imgDir);
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

            file_put_contents($infoFilePath, "title =+= " . $data['title']);

            $indexContent = file_get_contents($templateDir . "/index.html");

            $content = '<e-text-book></e-text-book>';

            file_put_contents(
                $indexFilePath,
                str_replace(
                    array("-- title --", "-- content --"),
                    array($data['title'], $content),
                    $indexContent
                )
            );

            if(is_file($rootDir . "/../../" . $slug . ".etb")) {
                unlink($rootDir . "/../../" . $slug . ".etb");
            }

            Util::zip($rootDir . "/../", $rootDir . "/../../" . $slug . ".etb");
            Util::removeDir($tempDir);

        }

        public function getFirstModuleContent() {

            $modules = Util::fileList($this->modulesPath);

            if(count($modules)) {
                sort($modules);
                $content = $this->getModuleContent($modules[0]);
            } else { $content = ''; }

            return $content;
        }

        public function getModuleContent($filePath) {
            return $this->parseBookContent($this->modulesPath . $filePath);
        }

    }