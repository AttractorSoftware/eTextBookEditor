<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\BookPackage;
use eTextBook\LoungeBundle\UseCases\Book\BookPublisher;

class BookPublisherTest extends eTextBookTestCase {
    private $book;
    private $package;

    public function __construct() {
        $currentDateTime = new \DateTime();
        $this->book = new Book();
        $this->book->setTitle('Unit test book');
        $this->book->setAuthors('Unit test authors');

        $client = static::createClient();
        $this->entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->persist($this->book);
        $this->entityManager->flush();

        $this->package = new BookPackage($this->book);
    }

    public function testCreateBook() {
        $publisher = new BookPublisher($this->package);
    }
}