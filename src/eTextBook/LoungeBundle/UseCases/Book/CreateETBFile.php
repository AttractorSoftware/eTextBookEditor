<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;

class CreateETBFile {
    private $book;
    private $bookTmpDir;
    private $booksDir;

    public function __construct() {
        global $kernel;
        $this->bookTmpDir = $kernel->getContainer()->getParameter('book_tmp_dir');
        $this->booksDir = $kernel->getContainer()->getParameter('books_dir');
    }

    public function setBook(Book $book) {
        $this->book = $book;
        $this->bookTmpDir = $this->bookTmpDir . $this->book->getSlug(). '/';
    }

    public function execute() {
        if(!is_file($this->booksDir . $this->book->getSlug() . '.etb')) {
            $this->createStructure();
            $this->copyTemplateFiles();
            $this->createInfoFile();
            $this->pack();
            return true;
        } else { return false; }
    }

    public function createInfoFile() {
        $info = array(
            'title' => $this->book->getTitle()
            ,'authors' => $this->book->getAuthors()
            ,'editor' => $this->book->getEditor()
            ,'isbn' => $this->book->getIsbn()
            ,'modules' => array()
        );
        file_put_contents($this->bookTmpDir . 'book.info', json_encode($info));
    }

    public function createStructure() {
        mkdir($this->bookTmpDir, 0700, true);
        mkdir($this->bookTmpDir . 'css', 0700, true);
        mkdir($this->bookTmpDir . 'js', 0700, true);
        mkdir($this->bookTmpDir . 'img', 0700, true);
        mkdir($this->bookTmpDir . 'fonts', 0700, true);
        mkdir($this->bookTmpDir . 'modules', 0700, true);
        mkdir($this->bookTmpDir . 'content/video', 0700, true);
        mkdir($this->bookTmpDir . 'content/audio', 0700, true);
        mkdir($this->bookTmpDir . 'content/img', 0700, true);
    }

    public function copyTemplateFiles() {
        global $kernel;
        $templateDir = $kernel->getContainer()->getParameter('book_template_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');

        copy($templateDir . "/css/main-style.min.css", $this->bookTmpDir . 'css/main-style.min.css');
        copy($templateDir . "/js/script.min.js", $this->bookTmpDir . 'js/script.min.js');
        $fileManager->copyFilesFromDirectory($templateDir . "/fonts", $this->bookTmpDir . 'fonts');
        $fileManager->copyFilesFromDirectory($templateDir . "/img", $this->bookTmpDir . 'img');
    }

    public function pack() {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');
        $bookFile =  $booksDir . $this->book->getSlug() . '.etb';
        if(is_file($bookFile)) { unlink($bookFile); }
        $fileManager->zip($this->bookTmpDir, $bookFile);
        $fileManager->removeDir($this->bookTmpDir);
    }
}