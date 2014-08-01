<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\UseCases\Book\Loader;
use eTextBook\LoungeBundle\UseCases\Book\CreateETBFile as Creator;
use eTextBook\LoungeBundle\UseCases\Book\UpdateETBFile as Updater;


class LoaderTest extends eTextBookTestCase {
    public function testLoad() {

        $timeSlug = date('d-m-Y-H-i-s');
        $bookSlug = 'selenium-generate-book-'. $timeSlug;
        $bookTitle = 'Selenium generate book ' . $timeSlug;
        $bookAuthors = "Author A. A., Author B. B.";
        $bookEditor = "Editor E. E.";
        $bookISBN = "3-455-44433-223-33";

        $book = new Book();
        $book->setTitle($bookTitle);
        $book->setSlug($bookSlug);
        $book->setAuthors($bookAuthors);
        $book->setEditor($bookEditor);
        $book->setIsbn($bookISBN);

        $creator = new Creator();
        $creator->setBook($book);
        $creator->execute();

        $updater = new Updater();
        $updater->setBook($book);
        $updater->addModule('New module');

        $loader = new Loader();
        $book = $loader->load($bookSlug);

        $this->assertEquals($book->getTitle(), $bookTitle);
        $this->assertEquals($book->getSlug(), $bookSlug);
        $this->assertEquals($book->getAuthors(), $bookAuthors);
        $this->assertEquals($book->getEditor(), $bookEditor);
        $this->assertEquals($book->getIsbn(), $bookISBN);
        $bookModules = $book->getModules();
        $this->assertEquals($bookModules[0]->title, 'New module');
    }
}