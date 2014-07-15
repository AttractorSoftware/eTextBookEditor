<?php

    require '../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
    require '../lib/eTextBook.class.php';

    class eTextBookTest extends PHPUnit_Framework_TestCase {

        protected $book;

        protected function setUp() {
            $this->book = new eTextBook('new-book.etb');
        }

        public function testGetFirstModuleContent() {
            $this->assertEquals('<e-text-book></e-text-book>', $this->book->getFirstModuleContent());
        }
    }