<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;

class CreateETBFile
{
    private $book;
    private $bookTmpDir;
    private $tmpDir;
    private $booksDir;

    public function __construct()
    {
        global $kernel;
        $this->tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir');
        $this->booksDir = $kernel->getContainer()->getParameter('books_dir');
        $this->templateDir = $kernel->getContainer()->getParameter('book_template_dir');
        $this->fileManager = $kernel->getContainer()->get('fileManager');
    }

    public function setBook(Book $book)
    {
        $this->book = $book;
        $this->bookTmpDir = $this->tmpDir . $this->book->getSlug() . '/';
    }

    public function execute()
    {
        if (!is_file($this->booksDir . $this->book->getSlug() . '.etb')) {
            $this->createStructure();
            $this->copyTemplateFiles();
            $this->createCover();
            $this->createInfoFile();
            $this->createIndexFile();
            $this->pack();
            return true;
        } else {
            return false;
        }
    }

    public function createCover()
    {
        if($this->book->getCover() != '') {
            copy($this->tmpDir . 'cover/' . $this->book->getCover(), $this->bookTmpDir . '/content/cover.png');
        }
    }

    public function createInfoFile()
    {
        $info = array(
            'title' => $this->book->getTitle()
            , 'authors' => $this->book->getAuthors()
            , 'editor' => $this->book->getEditor()
            , 'isbn' => $this->book->getIsbn()
            , 'modules' => array()
        );
        file_put_contents($this->bookTmpDir . 'book.info', json_encode($info));
    }


    public function createIndexFile()
    {
        $indexContent = file_get_contents($this->templateDir . 'index.html');
        $title = $this->book->getTitle();
        file_put_contents($this->bookTmpDir . 'index.html',
            str_replace(
                array("-- title --", "<!-- book-name -->"),
                array($title, $title),
                $indexContent
            ));
    }

    public function createStructure()
    {
        mkdir($this->bookTmpDir, 0777, true);
        mkdir($this->bookTmpDir . 'css', 0700, true);
        mkdir($this->bookTmpDir . 'js', 0700, true);
        mkdir($this->bookTmpDir . 'img', 0700, true);
        mkdir($this->bookTmpDir . 'fonts', 0700, true);
        mkdir($this->bookTmpDir . 'modules', 0700, true);
        mkdir($this->bookTmpDir . 'content/video', 0700, true);
        mkdir($this->bookTmpDir . 'content/audio', 0700, true);
        mkdir($this->bookTmpDir . 'content/img', 0700, true);
    }

    public function copyTemplateFiles()
    {
        copy($this->templateDir . "/css/main-style.min.css", $this->bookTmpDir . 'css/main-style.min.css');
        copy($this->templateDir . "/js/script.min.js", $this->bookTmpDir . 'js/script.min.js');
        $this->fileManager->copyFilesFromDirectory($this->templateDir . "/fonts", $this->bookTmpDir . 'fonts');
        $this->fileManager->copyFilesFromDirectory($this->templateDir . "/img", $this->bookTmpDir . 'img');
    }

    public function pack()
    {
        $bookFile = $this->booksDir . $this->book->getSlug() . '.etb';
        if (is_file($bookFile)) {
            unlink($bookFile);
        }
        $this->fileManager->zip($this->bookTmpDir, $bookFile);
    }
}