<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\UpdateETBFile;
use eTextBook\LoungeBundle\Entity\Book;
use Gedmo\Sluggable\Util as Sluggable;
use eTextBook\LoungeBundle\Lib\Transliterate;


class UpdateETBFileTest extends eTextBookTestCase {

    public function testExecute() {
        $transliterate = new Transliterate();
        $book = new Book();
        $book->setTitle("Unit test book 31-07-2014-12-11-00");
        $book->setSlug(Sluggable\Urlizer::urlize($transliterate->transliterate($book->getTitle(), 'ru'), '-'));
        $book->setAuthors('Authorssss');
        $book->setIsbn('111-1111-1111-2');

        $createETBFile = new UpdateETBFile();
        $createETBFile->setBook($book);
        $createETBFile->execute();
    }
}
