<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\BookPackage;
use eTextBook\LoungeBundle\UseCases\Book\BookPublisher;

class BookPublisherTest extends eTextBookTestCase {
    private $client;
    private $entityManager;
    private $bookTemplateFolderPath;
    private $bookTmpFolderPath;
    private $bookPrivateBooksFolderPath;
    private $bookPublishBooksFolderPath;

    public function __construct() {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->bookTmpFolderPath = $this->client->getContainer()->getParameter('book_tmp_dir');
        $this->bookTemplateFolderPath = $this->client->getContainer()->getParameter('book_template_dir');
        $this->bookPrivateBooksFolderPath = $this->client->getContainer()->getParameter('books_dir');
        $this->bookPublishBooksFolderPath = $this->client->getContainer()->getParameter('public_dir');
    }

    public function createFixtureBook() {
        $book = new Book();
        $book->setTitle('Unit test book');
        $book->setAuthors('Unit test authors');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $book;
    }

    public function testPublish() {
        $package = new BookPackage($this->createFixtureBook());
        $package->setTmpFolderPath($this->bookTmpFolderPath);
        $package->setTemplateFolderPath($this->bookTemplateFolderPath);
        $package->setBooksFolderPath($this->bookPrivateBooksFolderPath);
        $package->updateBookSlug();
        $package->createBootstrapFiles();
        $publisher = new BookPublisher($package);
        $publisher->setPublishBooksFolderPath($this->bookPublishBooksFolderPath);
        $publisher->publish();
    }


}