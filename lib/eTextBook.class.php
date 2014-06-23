<?php

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
                    $this->parseContent();
                    $this->parseTitle();
                    $this->parseImages();
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

        public function parseSlug() {
            $bookSlug = explode('.', $this->filePath);
            $this->slug = $bookSlug[0];
        }

        public function parseContent() {
            $bookContent = file_get_contents($this->tmpDir . $this->slug . '/index.html');
            $bookContent = explode('<e-text-book>', $bookContent);
            $bookContent = explode('</e-text-book>', $bookContent[1]);
            $bookContent = $bookContent[0];
            $bookContent = str_replace('content/', '/tmp/' . $this->slug . '/content/', $bookContent);
            $bookContent = "<e-text-book>" . $bookContent . "</e-text-book>";

            $this->content = $bookContent;
        }

        public function parseTitle() {
            $bookInfo = file_get_contents($this->tmpDir . $this->slug . '/book.info');
            $bookInfo = explode('=+=', $bookInfo);

            $this->title = trim($bookInfo[1]);
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

    }