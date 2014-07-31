<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;

class UpdateETBFile {
    private $book;
    private $bookTmpDir;

    public function setBook(Book $book) {
        global $kernel;
        $this->book = $book;
        $this->bookTmpDir = $kernel->getContainer()->getParameter('book_tmp_dir')
            . $this->book->getSlug(). '/';
    }

    public function setModuleContent($moduleId, $content) {

    }

    public function addModule($moduleTitle) {

    }

    public function execute() {
        $this->updateInfoFile();
        $this->pack();
    }

    public function updateInfoFile() {
        $info = json_decode(file_get_contents($this->bookTmpDir . 'book.info'));
        $info->title = $this->book->getTitle();
        $info->authors = $this->book->getAuthors();
        $info->editor = $this->book->getEditor();
        $info->isbn = $this->book->getIsbn();
        file_put_contents($this->bookTmpDir . 'book.info', json_encode($info));
    }

    public function pack() {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');
        $bookFile =  $booksDir . $this->book->getSlug() . '.etb';
        if(is_file($bookFile)) { unlink($bookFile); }
        $fileManager->zip($this->bookTmpDir, $bookFile);
    }
}