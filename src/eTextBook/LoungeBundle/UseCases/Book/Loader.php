<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;
use Symfony\Component\Security\Acl\Exception\Exception;

class Loader {

    private $tmpDir;
    private $booksDir;

    public function __construct() {
        global $kernel;
        $this->tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir');
        $this->booksDir = $kernel->getContainer()->getParameter('books_dir');
    }

    public function load($bookSlug) {

        $this->dataExtract($bookSlug);

        $infoFile = $this->tmpDir . $bookSlug . '/book.info';

        if(is_file($infoFile)) {
            $bookData = json_decode(file_get_contents($infoFile));
        } else { throw new Exception('Info file not found, book archive is corrupted'); }

        return $this->setBookData($bookSlug, $bookData);

    }

    public function dataExtract($bookSlug) {

        $bookETBFile = $this->booksDir . $bookSlug . '.etb';

        if(!is_dir($this->tmpDir . $bookSlug)) {
            mkdir($this->tmpDir . $bookSlug, 0700, true);
        }

        if(is_file($bookETBFile)) {
            $zip = new \ZipArchive();
            $archive = $zip->open($bookETBFile);
            if($archive === true) {
                $zip->extractTo($this->tmpDir . $bookSlug);
                $zip->close();
            }
        } else {
            throw new Exception('etb file "' . $bookETBFile . '" not found!' );
        }
    }

    public function setBookData($bookSlug, $bookData) {
        $book = new Book();
        $book->setTitle(isset($bookData->title) ? $bookData->title : '');
        $book->setSlug($bookSlug);
        $book->setAuthors(isset($bookData->authors) ? $bookData->authors : '');
        $book->setEditor(isset($bookData->editor) ? $bookData->editor : '');
        $book->setIsbn(isset($bookData->isbn) ? $bookData->isbn : '');
        $book->setModules($bookData->modules);
        return $book;
    }

    public function setModules($book, $modules) {
        foreach($modules as $module) {

        }
        return $book;
    }
}