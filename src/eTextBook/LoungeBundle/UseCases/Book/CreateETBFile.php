<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Lib\SummaryDom;

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
            $this->createBooksTempDir();
            $this->createStructure();
            $this->createSourceFile();
            $this->createInfoFile();
            $this->createCover();
            if ($this->book->getFile() == '') {
                $this->copyTemplateFiles();
                $this->createIndexFile();
            }
            $this->pack();
            $this->changeDirectoriesPermissions();

            return true;
        } else {
            return false;
        }
    }

    public function createCover()
    {
        if ($this->book->getCover() != '') {
            $uploadedCoverLocation = $this->tmpDir . 'cover/' . $this->book->getCover();
            $booksCoverLocation = $this->bookTmpDir . 'content/cover.png';
            copy($uploadedCoverLocation, $booksCoverLocation);
        }
    }

    public function createSourceFile()
    {
        if ($this->book->getFile() != '') {
            copy($this->tmpDir . 'book/' . $this->book->getFile(), $this->bookTmpDir . '/' . $this->book->getFile());
        }
    }

    public function createInfoFile()
    {
        $info = array(
            'title' => $this->book->getTitle(),
            'authors' => $this->book->getAuthors(),
            'slug' => $this->book->getSlug(),
            'editor' => $this->book->getEditor(),
            'isbn' => $this->book->getIsbn(),
            'modules' => array(),
            'source' => $this->book->getFile()
        );
        file_put_contents($this->bookTmpDir . 'book.info', json_encode($info));
    }


    public function createIndexFile()
    {
        $title = $this->book->getTitle();
        $templatePath = $this->templateDir . 'index.html';
        $indexContent = new SummaryDom();
        $indexContent->loadWithBreaks($templatePath);
        $indexContent->setBookAttributes($title);
        $indexContent->save($this->bookTmpDir . 'index.html');
        $indexContent ->destroy();
    }

    public function createStructure()
    {
        mkdir($this->bookTmpDir . 'css', 0777, true);
        mkdir($this->bookTmpDir . 'js', 0777, true);
        mkdir($this->bookTmpDir . 'img', 0777, true);
        mkdir($this->bookTmpDir . 'fonts', 0777, true);
        mkdir($this->bookTmpDir . 'modules', 0777, true);
        mkdir($this->bookTmpDir . 'content/video', 0777, true);
        mkdir($this->bookTmpDir . 'content/audio', 0777, true);
        mkdir($this->bookTmpDir . 'content/img', 0777, true);
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

    public function createBooksTempDir()
    {
        mkdir($this->bookTmpDir, 0777, true);
    }

    private function changeDirectoriesPermissions()
    {
        $fileIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->bookTmpDir));
        foreach ($fileIterator as $item) {
            if (!is_dir($item->getPathname())) {
                chmod($item->getPathname(), 0777);
            }
        }
    }
}