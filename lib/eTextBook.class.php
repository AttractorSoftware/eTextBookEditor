<?php

    class eTextBook {

        private $filePath;
        private $slug;
        private $title;
        private $content;
        private $images = array();
        private $videos = array();
        private $audios = array();

        public function __construct($filePath = false) {
            $this->filePath = $filePath;
            if($filePath) {
                if($this->extractData()) {
                    $this->parseSlug();
                    $this->parseContent();
                    $this->parseTitle();
                    $this->parseImages();
                    $this->copyContent();
                }
            }
        }

        public function copyContent() {
            Util::copyFilesFromDirectory(
                "/tmp/ebook/" . $this->slug . '/content/' . $this->slug . '/img',
                Util::getRootDir() . 'content/'  . $this->slug . '/img'
            );

            Util::copyFilesFromDirectory(
                "/tmp/ebook/" . $this->slug . '/content/' . $this->slug . '/video',
                Util::getRootDir() . 'content/'  . $this->slug . '/video'
            );

            Util::copyFilesFromDirectory(
                "/tmp/ebook/" . $this->slug . '/content/' . $this->slug . '/audio',
                Util::getRootDir() . 'content/'  . $this->slug . '/audio'
            );
        }

        public function parseImages() {
            $images = Util::fileList("/tmp/ebook/" . $this->slug . '/content/' . $this->slug . '/img');
            foreach($images as $img) {
                $img = explode('.', $img);
                $this->images[] = array(
                    'title' => $img[0]
                    ,'extension' => $img[1]
                );
            }
        }

        public function parseSlug() {
            $bookSlug = explode('.', $this->filePath);
            $this->slug = $bookSlug[0];
        }

        public function parseContent() {
            $bookContent = file_get_contents('/tmp/ebook/' . $this->slug . '/index.html');
            $bookContent = explode('<e-text-book>', $bookContent);
            $bookContent = explode('</e-text-book>', $bookContent[1]);
            $bookContent = $bookContent[0];
            $bookContent = "<e-text-book>" . $bookContent . "</e-text-book>";

            $this->content = $bookContent;
        }

        public function parseTitle() {
            $bookInfo = file_get_contents('/tmp/ebook/' . $this->slug . '/book.info');
            $bookInfo = explode('=+=', $bookInfo);

            $this->title = trim($bookInfo[1]);
        }

        public function extractData() {

            $zip = new ZipArchive();
            $archive = $zip->open("books/".$this->filePath);

            if($archive === true) {
                $zip->extractTo('/tmp/ebook');
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