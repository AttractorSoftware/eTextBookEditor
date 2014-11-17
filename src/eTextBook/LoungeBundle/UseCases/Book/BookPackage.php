<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Lib\Transliterate;
use eTextBook\LoungeBundle\Lib\FileManager;
use Gedmo\Sluggable\Util as Sluggable;

class BookPackage {
    private $book;
    private $transliter;
    private $privateBooksFolderPath;
    private $tmpFolderPath;
    private $templateFolderPath;
    private $fileManager;

    public function __construct(Book $book) {
        $this->book = $book;
        $this->transliter = new Transliterate();
        $this->fileManager = new FileManager();
    }

    public function pack() {
        $bookBootstrapDir = $this->tmpFolderPath . $this->book->getSlug() . "/";
        $bookFile = $this->privateBooksFolderPath . $this->book->getSlug() . '.etb';
        if (is_file($bookFile)) { unlink($bookFile); }
        $this->fileManager->zip($bookBootstrapDir, $bookFile);
    }

    public function createBootstrapFiles() {
        $this->createBootstrapStructure();
        $this->copyBootstrapFiles();
        $this->updateBookInfoFile();
    }

    public function updateBookInfoFile() {
        $bookBootstrapDir = $this->tmpFolderPath . $this->book->getSlug() . "/";
        if(file_exists($bookBootstrapDir . 'book.info')) {
            $info = json_decode(file_get_contents($bookBootstrapDir . 'book.info'));
        } else { $info = array('modules' => array()); }

        $info['title'] = $this->book->getTitle();
        $info['authors'] = $this->book->getAuthors();
        $info['slug'] = $this->book->getSlug();
        $info['editor'] = $this->book->getEditor();
        $info['isbn'] = $this->book->getIsbn();
        $info['source'] = $this->book->getFile();
        file_put_contents($bookBootstrapDir . 'book.info', json_encode($info));
    }

    public function copyBootstrapFiles() {
        $bookBootstrapDir = $this->tmpFolderPath . $this->book->getSlug() . "/";
        copy($this->templateFolderPath . "/css/main-style.min.css", $bookBootstrapDir . 'css/main-style.min.css');
        copy($this->templateFolderPath . "/js/script.min.js", $bookBootstrapDir . 'js/script.min.js');
        $this->fileManager->copyFilesFromDirectory($this->templateFolderPath . "/fonts", $bookBootstrapDir . 'fonts');
        $this->fileManager->copyFilesFromDirectory($this->templateFolderPath . "/img", $bookBootstrapDir . 'img');
        if($this->book->getFile() == '') { //Check source book file (pdf, mobi, fb2 etc.)
            file_put_contents($bookBootstrapDir . 'index.html', $this->generateBookIndexFileContent());
        } else {
            $filePath = $this->tmpFolderPath . 'book/' . $this->book->getFile();
            if(file_exists($filePath)) {
                copy($filePath, $bookBootstrapDir . '/' . $this->book->getFile());
            }
        }
        if($this->book->getCover() != '') {
            $uploadedCoverLocation = $this->tmpFolderPath . 'cover/' . $this->book->getCover();
            if(file_exists($uploadedCoverLocation)) {
                copy($uploadedCoverLocation, $bookBootstrapDir . 'content/cover.png');
            }
        }
    }

    public function generateBookIndexFileContent() {
        $indexFileContent = file_get_contents($this->templateFolderPath . "/index.html");
        $indexFileContent = str_replace("-- book-title --", $this->book->getTitle(), $indexFileContent);
        return $indexFileContent;
    }

    public function createBootstrapStructure() {
        $bookBootstrapDir = $this->tmpFolderPath . $this->book->getSlug() . "/";

        !file_exists($bookBootstrapDir) ? mkdir($bookBootstrapDir, 0777, true) : false;
        !file_exists($bookBootstrapDir . 'css') ? mkdir($bookBootstrapDir . 'css', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'js') ? mkdir($bookBootstrapDir . 'js', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'img') ? mkdir($bookBootstrapDir . 'img', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'fonts') ? mkdir($bookBootstrapDir . 'fonts', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'modules') ? mkdir($bookBootstrapDir . 'modules', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'content/video') ? mkdir($bookBootstrapDir . 'content/video', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'content/audio') ? mkdir($bookBootstrapDir . 'content/audio', 0777, true) : false;
        !file_exists($bookBootstrapDir . 'content/img') ? mkdir($bookBootstrapDir . 'content/img', 0777, true) : false;
    }

    public function updateBookSlug() {
        $slug = Sluggable\Urlizer::urlize(
            $this->transliter->transliterate($this->book->getTitle(), 'ru'), '-'
        );
        $this->book->setSlug($this->book->getId() . "-" . $slug . "-" . $this->book->getVersion());
    }

    public function getBook() {
        return $this->book;
    }

    public function setTmpFolderPath($tmpFolderPath) {
        $this->tmpFolderPath = $tmpFolderPath;
    }

    public function setTemplateFolderPath($templateFolderPath) {
        $this->templateFolderPath = $templateFolderPath;
    }

    public function setPrivateBooksFolderPath($privateBooksFolderPath) {
        $this->privateBooksFolderPath = $privateBooksFolderPath;
    }

}