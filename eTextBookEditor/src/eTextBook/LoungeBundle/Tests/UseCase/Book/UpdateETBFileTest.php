<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\CreateETBFile;
use eTextBook\LoungeBundle\UseCases\Book\UpdateETBFile;
use eTextBook\LoungeBundle\Entity\Book;
use Gedmo\Sluggable\Util as Sluggable;
use eTextBook\LoungeBundle\Lib\Transliterate;


class UpdateETBFileTest extends eTextBookTestCase {

    public function testExecute() {
        $transliterate = new Transliterate();

        $bookTitle = "Unit test book " . date('d-m-Y-H-i-s');
        $bookSlug = Sluggable\Urlizer::urlize($transliterate->transliterate($bookTitle, 'ru'), '-');

        $book = new Book();
        $book->setTitle($bookTitle);
        $book->setSlug($bookSlug);
        $book->setAuthors('Author');
        $book->setIsbn('111-1111-1111-2');

        $createETBFile = new CreateETBFile();
        $createETBFile->setBook($book);
        $createETBFile->execute();

        $book->setAuthors('Author, Author');
        $book->setTitle('With module');

        $updateETBFile = new UpdateETBFile();
        $updateETBFile->setBook($book);
        $updateETBFile->addModule('New module');
        $updateETBFile->execute();
    }
}
