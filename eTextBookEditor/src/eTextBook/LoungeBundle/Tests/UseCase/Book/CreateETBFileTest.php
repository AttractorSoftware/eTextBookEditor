<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\CreateETBFile;
use eTextBook\LoungeBundle\Entity\Book;
use Gedmo\Sluggable\Util as Sluggable;
use eTextBook\LoungeBundle\Lib\Transliterate;


class CreateETBFileTest extends eTextBookTestCase {

    public function testExecute() {
        $transliterate = new Transliterate();
        $book = new Book();
        $book->setTitle("Unit test book " . date('d-m-Y-H-i-s'));
        $book->setSlug(Sluggable\Urlizer::urlize($transliterate->transliterate($book->getTitle(), 'ru'), '-'));
        $book->setAuthors('Authors');
        $book->setIsbn('111-1111-1111-1');

        $createETBFile = new CreateETBFile();
        $createETBFile->setBook($book);
        $createETBFile->execute();
    }
}
