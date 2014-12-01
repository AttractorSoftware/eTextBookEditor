<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\BookPackage;

class BookPackageTest extends eTextBookTestCase {

    private $client;
    private $entityManager;
    private $bookTemplateFolderPath;
    private $bookTmpFolderPath;
    private $bookPrivateBooksFolderPath;

    public function __construct() {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->bookTmpFolderPath = $this->client->getContainer()->getParameter('book_tmp_dir');
        $this->bookTemplateFolderPath = $this->client->getContainer()->getParameter('book_template_dir');
        $this->bookPrivateBooksFolderPath = $this->client->getContainer()->getParameter('books_dir');
    }

    public function createFixtureBook() {
        $book = new Book();
        $book->setTitle('Unit test book');
        $book->setAuthors('Unit test authors');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $book;
    }

    public function createFixtureBookWithFile() {
        $book = new Book();
        $book->setTitle('Unit test book');
        $book->setAuthors('Unit test authors');
        $book->setFile('file-path');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $book;
    }

    public function testNewBookParams() {
        $package = new BookPackage($this->createFixtureBook());
        $package->updateBookSlug();

        $this->assertEquals(1, $package->getBook()->getVersion());
        $this->assertEquals(
            $package->getBook()->getId() . '-unit-test-book-1',
            $package->getBook()->getSlug()
        );
    }

    public function testCreateBootstrapFiles() {
        $package = new BookPackage($this->createFixtureBook());
        $package->updateBookSlug();

        $package->setTmpFolderPath($this->bookTmpFolderPath);
        $package->setTemplateFolderPath($this->bookTemplateFolderPath);
        $package->createBootstrapFiles();

        $this->assertEquals(
            true,
            file_exists($this->bookTmpFolderPath . $package->getBook()->getSlug())
        );
        $this->assertEquals(
            true,
            file_exists($this->bookTmpFolderPath . $package->getBook()->getSlug() . "/index.html")
        );
        $this->assertEquals(
            true,
            file_exists($this->bookTmpFolderPath . $package->getBook()->getSlug() . "/book.info")
        );
    }

    public function testCreateBootstrapFilesForBookWithFile() {
        $package = new BookPackage($this->createFixtureBookWithFile());
        $package->updateBookSlug();

        $package->setTmpFolderPath($this->bookTmpFolderPath);
        $package->setTemplateFolderPath($this->bookTemplateFolderPath);
        $package->createBootstrapFiles();

        $this->assertEquals(
            true,
            file_exists($this->bookTmpFolderPath . $package->getBook()->getSlug())
        );
        $this->assertEquals(
            false,
            file_exists($this->bookTmpFolderPath . $package->getBook()->getSlug() . "/index.html")
        );
        $this->assertEquals(
            true,
            file_exists($this->bookTmpFolderPath . $package->getBook()->getSlug() . "/book.info")
        );
    }

    public function testBookPack() {
        $package = new BookPackage($this->createFixtureBook());
        $package->updateBookSlug();

        $package->setTmpFolderPath($this->bookTmpFolderPath);
        $package->setTemplateFolderPath($this->bookTemplateFolderPath);
        $package->setBooksFolderPath($this->bookPrivateBooksFolderPath);
        $package->createBootstrapFiles();
        $package->pack();

        $this->assertEquals(
            true,
            file_exists($this->bookPrivateBooksFolderPath . $package->getBook()->getSlug() . '.etb')
        );
    }

    public function testBookUnpack() {
        $package = new BookPackage($this->createFixtureBook());
        $package->updateBookSlug();

        $package->setTmpFolderPath($this->bookTmpFolderPath);
        $package->setTemplateFolderPath($this->bookTemplateFolderPath);
        $package->setBooksFolderPath($this->bookPrivateBooksFolderPath);
        $package->createBootstrapFiles();
        $package->pack();

        $package->unpack();

        $this->assertEquals(
            true,
            file_exists($this->bookPrivateBooksFolderPath . $package->getBook()->getSlug() . '.etb')
        );
    }

}